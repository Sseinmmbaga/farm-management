<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\StockItem;
use App\Models\Stock\StockTransaction;
use App\Enums\StockTransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = StockTransaction::with(['stockItem', 'farmer', 'farm'])
            ->when($request->search, fn($q, $search) => $q->where('reference_number', 'like', "%{$search}%"))
            ->when($request->type, fn($q, $type) => $q->where('transaction_type', $type))
            ->when($request->date_from, fn($q, $date) => $q->whereDate('transaction_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('transaction_date', '<=', $date))
            ->latest()
            ->paginate(20);

        return view('stock.transactions.index', compact('transactions'));
    }

    public function intake()
    {
        $stockItems = StockItem::active()->with('category')->get();
        return view('stock.transactions.intake', compact('stockItems'));
    }

    public function storeIntake(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => ['required', 'exists:stock_items,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'transaction_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $stockItem = StockItem::findOrFail($validated['stock_item_id']);
            $balanceBefore = $stockItem->quantity_on_hand;

            // Create transaction
            $transaction = StockTransaction::create([
                'stock_item_id' => $validated['stock_item_id'],
                'transaction_type' => StockTransactionType::INTAKE,
                'quantity' => $validated['quantity'],
                'unit_cost' => $validated['unit_cost'] ?? $stockItem->unit_cost,
                'total_cost' => ($validated['unit_cost'] ?? $stockItem->unit_cost) * $validated['quantity'],
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceBefore + $validated['quantity'],
                'reference_number' => $validated['reference_number'] ?? StockTransaction::generateReferenceNumber(),
                'transaction_date' => $validated['transaction_date'] ?? now(),
                'source' => $validated['source'] ?? 'Purchase',
                'notes' => $validated['notes'],
                'status' => 'completed',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Update stock item quantity
            $stockItem->adjustStock($validated['quantity'], 'add');
        });

        return redirect()
            ->route('stock.transactions.index')
            ->with('success', 'Stock intake recorded successfully.');
    }

    public function issuance()
    {
        $stockItems = StockItem::active()->with('category')->get();
        return view('stock.transactions.issuance', compact('stockItems'));
    }

    public function storeIssuance(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => ['required', 'exists:stock_items,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'destination' => ['required', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'transaction_date' => ['nullable', 'date'],
            'farmer_id' => ['nullable', 'exists:farmers,id'],
            'farm_id' => ['nullable', 'exists:farms,id'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $stockItem = StockItem::findOrFail($validated['stock_item_id']);
            $balanceBefore = $stockItem->quantity_on_hand;

            if ($balanceBefore < $validated['quantity']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => 'Insufficient stock. Available: ' . $balanceBefore,
                ]);
            }

            $transaction = StockTransaction::create([
                'stock_item_id' => $validated['stock_item_id'],
                'transaction_type' => StockTransactionType::ISSUANCE,
                'quantity' => $validated['quantity'],
                'unit_cost' => $stockItem->unit_cost,
                'total_cost' => $stockItem->unit_cost * $validated['quantity'],
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceBefore - $validated['quantity'],
                'reference_number' => $validated['reference_number'] ?? StockTransaction::generateReferenceNumber(),
                'transaction_date' => $validated['transaction_date'] ?? now(),
                'destination' => $validated['destination'],
                'farmer_id' => $validated['farmer_id'] ?? null,
                'farm_id' => $validated['farm_id'] ?? null,
                'notes' => $validated['notes'],
                'status' => 'completed',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $stockItem->adjustStock($validated['quantity'], 'subtract');
        });

        return redirect()
            ->route('stock.transactions.index')
            ->with('success', 'Stock issuance recorded successfully.');
    }
}