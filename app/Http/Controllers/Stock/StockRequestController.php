<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Stock\StockRequest;
use App\Models\Stock\StockRequestItem;
use App\Models\Stock\StockItem;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Season;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockRequestController extends Controller
{
    public function index()
    {
        $requests = StockRequest::with(['farmer', 'requestedBy', 'items.stockItem'])
            ->latest()
            ->paginate(20);

        return view('stock.requests.index', compact('requests'));
    }

    public function create()
    {
        $farmers = Farmer::active()->get(['id', 'first_name', 'last_name', 'registration_number']);
        $seasons = Season::active()->get(['id', 'name', 'year']);
        $stockItems = StockItem::active()->with('category')->get();

        return view('stock.requests.create', compact('farmers', 'seasons', 'stockItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => ['nullable', 'exists:farmers,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'needed_by' => ['nullable', 'date', 'after:today'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.stock_item_id' => ['required', 'exists:stock_items,id'],
            'items.*.quantity_requested' => ['required', 'numeric', 'min:0.01'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $stockRequest = StockRequest::create([
                'request_number' => $this->generateRequestNumber(),
                'requested_by' => auth()->id(),
                'farmer_id' => $validated['farmer_id'] ?? null,
                'season_id' => $validated['season_id'] ?? null,
                'status' => 'draft',
                'priority' => $validated['priority'],
                'needed_by' => $validated['needed_by'] ?? null,
                'purpose' => $validated['purpose'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                StockRequestItem::create([
                    'stock_request_id' => $stockRequest->id,
                    'stock_item_id' => $item['stock_item_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('stock.requests.index')
            ->with('success', 'Stock request created successfully.');
    }

    public function show($requestId)
    {
        $stockRequest = StockRequest::with([
            'farmer',
            'season',
            'requestedBy',
            'approvedBy',
            'fulfilledBy',
            'items.stockItem',
        ])->findOrFail($requestId);

        return view('stock.requests.show', compact('stockRequest'));
    }

    public function edit($id)
    {
        $stockRequest = StockRequest::with('items')->findOrFail($id);
        if (!$stockRequest->is_draft) {
            abort(403, 'Only draft requests can be edited.');
        }

        $farmers = Farmer::active()->get(['id', 'first_name', 'last_name', 'registration_number']);
        $seasons = Season::active()->get(['id', 'name', 'year']);
        $stockItems = StockItem::active()->with('category')->get();

        return view('stock.requests.edit', compact('stockRequest', 'farmers', 'seasons', 'stockItems'));
    }

    public function update(Request $request, $id)
    {
        $stockRequest = StockRequest::with('items')->findOrFail($id);
        if (!$stockRequest->is_draft) {
            abort(403, 'Only draft requests can be updated.');
        }

        $validated = $request->validate([
            'farmer_id' => ['nullable', 'exists:farmers,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'needed_by' => ['nullable', 'date', 'after:today'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.stock_item_id' => ['required', 'exists:stock_items,id'],
            'items.*.quantity_requested' => ['required', 'numeric', 'min:0.01'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($stockRequest, $validated) {
            $stockRequest->update([
                'farmer_id' => $validated['farmer_id'] ?? null,
                'season_id' => $validated['season_id'] ?? null,
                'priority' => $validated['priority'],
                'needed_by' => $validated['needed_by'] ?? null,
                'purpose' => $validated['purpose'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete existing items and create new ones
            $stockRequest->items()->delete();
            foreach ($validated['items'] as $item) {
                StockRequestItem::create([
                    'stock_request_id' => $stockRequest->id,
                    'stock_item_id' => $item['stock_item_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('stock.requests.show', $stockRequest)
            ->with('success', 'Stock request updated successfully.');
    }

    public function destroy($id)
    {
        $stockRequest = StockRequest::findOrFail($id);
        if (!$stockRequest->is_draft) {
            abort(403, 'Only draft requests can be deleted.');
        }

        $stockRequest->delete();

        return redirect()
            ->route('stock.requests.index')
            ->with('success', 'Stock request deleted successfully.');
    }

    public function approve($requestId)
    {
        $stockRequest = StockRequest::with('items')->findOrFail($requestId);
        if (!$stockRequest->is_submitted) {
            abort(403, 'Only submitted requests can be approved.');
        }

        $validated = request()->validate([
            'approval_notes' => ['nullable', 'string'],
            'items' => ['sometimes', 'array'],
            'items.*.quantity_approved' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($stockRequest, $validated) {
            // Update quantities approved if provided
            if (isset($validated['items'])) {
                foreach ($validated['items'] as $itemId => $itemData) {
                    $requestItem = $stockRequest->items->where('id', $itemId)->first();
                    if ($requestItem && isset($itemData['quantity_approved'])) {
                        $requestItem->update([
                            'quantity_approved' => $itemData['quantity_approved'],
                        ]);
                    }
                }
            }

            $stockRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('stock.requests.show', $stockRequest)
            ->with('success', 'Stock request approved successfully.');
    }

    public function reject($requestId)
    {
        $stockRequest = StockRequest::findOrFail($requestId);
        if (!$stockRequest->is_submitted) {
            abort(403, 'Only submitted requests can be rejected.');
        }

        $validated = request()->validate([
            'approval_notes' => ['required', 'string', 'max:500'],
        ]);

        $stockRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return redirect()
            ->route('stock.requests.show', $stockRequest)
            ->with('success', 'Stock request rejected.');
    }

    public function fulfill($requestId)
    {
        $stockRequest = StockRequest::with('items.stockItem')->findOrFail($requestId);
        if (!$stockRequest->is_approved) {
            abort(403, 'Only approved requests can be fulfilled.');
        }

        DB::transaction(function () use ($stockRequest) {
            foreach ($stockRequest->items as $item) {
                $quantity = $item->quantity_approved ?? $item->quantity_requested;
                if ($quantity <= 0) {
                    continue;
                }

                $stockItem = $item->stockItem;
                $balanceBefore = $stockItem->quantity_on_hand;

                if ($balanceBefore < $quantity) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'quantity' => "Insufficient stock for {$stockItem->name}. Available: {$balanceBefore}",
                    ]);
                }

                // Create stock transaction
                $transaction = \App\Models\Stock\StockTransaction::create([
                    'stock_item_id' => $stockItem->id,
                    'transaction_type' => \App\Enums\StockTransactionType::ISSUANCE,
                    'quantity' => $quantity,
                    'unit_cost' => $stockItem->unit_cost,
                    'total_cost' => $stockItem->unit_cost * $quantity,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore - $quantity,
                    'reference_number' => \App\Models\Stock\StockTransaction::generateReferenceNumber(),
                    'transaction_date' => now(),
                    'destination' => 'Request Fulfillment',
                    'farmer_id' => $stockRequest->farmer_id,
                    'farm_id' => null,
                    'notes' => "Fulfillment of request #{$stockRequest->request_number}",
                    'status' => 'completed',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'created_by' => auth()->id(),
                ]);

                // Update stock item
                $stockItem->adjustStock($quantity, 'subtract');

                // Update fulfilled quantity
                $item->update([
                    'quantity_fulfilled' => $quantity,
                ]);
            }

            $stockRequest->update([
                'status' => 'fulfilled',
                'fulfilled_by' => auth()->id(),
                'fulfilled_at' => now(),
            ]);
        });

        return redirect()
            ->route('stock.requests.show', $stockRequest)
            ->with('success', 'Stock request fulfilled successfully.');
    }

    public function submit($requestId)
    {
        $stockRequest = StockRequest::findOrFail($requestId);
        if (!$stockRequest->is_draft) {
            abort(403, 'Only draft requests can be submitted.');
        }

        $stockRequest->update([
            'status' => 'submitted',
        ]);

        return redirect()
            ->route('stock.requests.show', $stockRequest)
            ->with('success', 'Stock request submitted for approval.');
    }

    public function cancel($requestId)
    {
        $stockRequest = StockRequest::findOrFail($requestId);
        if ($stockRequest->is_fulfilled || $stockRequest->is_cancelled) {
            abort(403, 'Cannot cancel a fulfilled or already cancelled request.');
        }

        $stockRequest->update([
            'status' => 'cancelled',
        ]);

        return redirect()
            ->route('stock.requests.show', $stockRequest)
            ->with('success', 'Stock request cancelled.');
    }

    private function generateRequestNumber(): string
    {
        $prefix = 'SR';
        $year = now()->format('Y');
        $month = now()->format('m');
        $day = now()->format('d');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return "{$prefix}{$year}{$month}{$day}-{$random}";
    }
}