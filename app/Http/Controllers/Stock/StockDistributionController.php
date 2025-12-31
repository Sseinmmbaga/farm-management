<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\StockItem;
use App\Models\Stock\StockTransaction;
use App\Models\Stock\StockDistribution;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Models\Farms\Season;
use App\Enums\StockTransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockDistributionController extends Controller
{
    public function index()
    {
        $distributions = StockDistribution::with(['transaction.stockItem', 'farmer', 'season'])
            ->latest()
            ->paginate(20);

        return view('stock.distributions.index', compact('distributions'));
    }

    public function create()
    {
        $farmers = Farmer::active()->get(['id', 'first_name', 'last_name', 'registration_number']);
        $farms = Farm::active()->get(['id', 'name', 'farmer_id']);
        $seasons = Season::active()->get(['id', 'name', 'year']);
        $stockItems = StockItem::active()->with('category')->get();

        return view('stock.distributions.create', compact('farmers', 'farms', 'seasons', 'stockItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => ['required', 'exists:farmers,id'],
            'farm_id' => ['nullable', 'exists:farms,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'stock_item_id' => ['required', 'exists:stock_items,id'],
            'distribution_type' => ['required', Rule::in(['credit', 'cash', 'free'])],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date', 'after:today'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            // First, create stock transaction (issuance)
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
                'reference_number' => StockTransaction::generateReferenceNumber(),
                'transaction_date' => now(),
                'destination' => 'Farmer Distribution',
                'farmer_id' => $validated['farmer_id'],
                'farm_id' => $validated['farm_id'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'created_by' => auth()->id(),
            ]);

            // Update stock item quantity
            $stockItem->adjustStock($validated['quantity'], 'subtract');

            // Create distribution record
            $distribution = StockDistribution::create([
                'stock_transaction_id' => $transaction->id,
                'farmer_id' => $validated['farmer_id'],
                'farm_id' => $validated['farm_id'],
                'season_id' => $validated['season_id'] ?? null,
                'distribution_type' => $validated['distribution_type'],
                'quantity' => $validated['quantity'],
                'value' => $validated['value'] ?? ($stockItem->unit_cost * $validated['quantity']),
                'is_repaid' => false,
                'amount_repaid' => 0,
                'due_date' => $validated['due_date'] ?? null,
                'purpose' => $validated['purpose'] ?? null,
                'distributed_by' => auth()->id(),
            ]);

            // If distribution is cash, mark as repaid? Maybe not.
            if ($validated['distribution_type'] === 'cash') {
                $distribution->update([
                    'is_repaid' => true,
                    'amount_repaid' => $distribution->value,
                ]);
            }
        });

        return redirect()
            ->route('stock.distributions.index')
            ->with('success', 'Stock distribution recorded successfully.');
    }

    public function show($distribution)
    {
        $distribution = StockDistribution::with([
            'transaction.stockItem',
            'farmer',
            'farm',
            'season',
            'distributedBy',
        ])->findOrFail($distribution);

        return view('stock.distributions.show', compact('distribution'));
    }

    public function farmerDistributions($farmer)
    {
        $farmer = Farmer::with(['distributions.transaction.stockItem'])->findOrFail($farmer);
        $distributions = $farmer->distributions()->latest()->paginate(20);

        return view('stock.distributions.farmer', compact('farmer', 'distributions'));
    }
}