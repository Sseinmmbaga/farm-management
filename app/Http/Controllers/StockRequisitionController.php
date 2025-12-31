<?php

namespace App\Http\Controllers;

use App\Models\StockRequisition;
use App\Models\StockRequisitionItem;
use App\Models\User;
use App\Models\Department;
use App\Models\Stock\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->authorize('viewAny', StockRequisition::class);

        $requisitions = StockRequisition::with(['user', 'department', 'approvedBy'])
            ->latest()
            ->paginate(20);

        return view('stock-requisitions.index', compact('requisitions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', StockRequisition::class);

        $users = User::active()->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $stockItems = StockItem::active()->orderBy('name')->get();

        return view('stock-requisitions.create', compact('users', 'departments', 'stockItems'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', StockRequisition::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'required_date' => 'nullable|date|after:today',
            'purpose' => 'required|string|min:10|max:1000',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.quantity_requested' => 'required|numeric|min:0.001',
            'items.*.unit_of_measure' => 'nullable|string|max:50',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $requisition = StockRequisition::create([
                'user_id' => $validated['user_id'],
                'department_id' => $validated['department_id'],
                'status' => StockRequisition::STATUS_PENDING,
                'requested_date' => now(),
                'required_date' => $validated['required_date'] ?? null,
                'purpose' => $validated['purpose'],
                'notes' => $validated['notes'],
            ]);

            $totalEstimated = 0;
            foreach ($validated['items'] as $itemData) {
                $unitPrice = $itemData['unit_price'] ?? 0;
                $quantity = $itemData['quantity_requested'];
                $totalPrice = $unitPrice * $quantity;

                $requisition->items()->create([
                    'stock_item_id' => $itemData['stock_item_id'],
                    'quantity_requested' => $quantity,
                    'unit_of_measure' => $itemData['unit_of_measure'] ?? 'pieces',
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'notes' => $itemData['notes'] ?? null,
                ]);

                $totalEstimated += $totalPrice;
            }

            $requisition->update(['estimated_total' => $totalEstimated]);

            DB::commit();

            return redirect()
                ->route('stock-requisitions.show', $requisition)
                ->with('success', 'Stock requisition created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create requisition: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StockRequisition  $stockRequisition
     * @return \Illuminate\View\View
     */
    public function show(StockRequisition $stockRequisition)
    {
        $this->authorize('view', $stockRequisition);

        $stockRequisition->load([
            'user',
            'department',
            'approvedBy',
            'rejectedBy',
            'issuedBy',
            'items.stockItem'
        ]);

        return view('stock-requisitions.show', compact('stockRequisition'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StockRequisition  $stockRequisition
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(StockRequisition $stockRequisition)
    {
        $this->authorize('update', $stockRequisition);

        if (!$stockRequisition->isEditable()) {
            return redirect()
                ->route('stock-requisitions.show', $stockRequisition)
                ->with('warning', 'This requisition cannot be edited because it is already approved or issued.');
        }

        $users = User::active()->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $stockItems = StockItem::active()->orderBy('name')->get();

        return view('stock-requisitions.edit', compact('stockRequisition', 'users', 'departments', 'stockItems'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StockRequisition  $stockRequisition
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, StockRequisition $stockRequisition)
    {
        $this->authorize('update', $stockRequisition);

        if (!$stockRequisition->isEditable()) {
            return redirect()
                ->route('stock-requisitions.show', $stockRequisition)
                ->with('error', 'This requisition cannot be updated because it is already approved or issued.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'required_date' => 'nullable|date|after:today',
            'purpose' => 'required|string|min:10|max:1000',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:stock_requisition_items,id',
            'items.*.stock_item_id' => 'required|exists:stock_items,id',
            'items.*.quantity_requested' => 'required|numeric|min:0.001',
            'items.*.unit_of_measure' => 'nullable|string|max:50',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $stockRequisition->update([
                'user_id' => $validated['user_id'],
                'department_id' => $validated['department_id'],
                'required_date' => $validated['required_date'] ?? null,
                'purpose' => $validated['purpose'],
                'notes' => $validated['notes'],
            ]);

            $existingItemIds = $stockRequisition->items->pluck('id')->toArray();
            $updatedItemIds = [];

            $totalEstimated = 0;
            foreach ($validated['items'] as $itemData) {
                $unitPrice = $itemData['unit_price'] ?? 0;
                $quantity = $itemData['quantity_requested'];
                $totalPrice = $unitPrice * $quantity;

                if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                    $item = StockRequisitionItem::find($itemData['id']);
                    $item->update([
                        'stock_item_id' => $itemData['stock_item_id'],
                        'quantity_requested' => $quantity,
                        'unit_of_measure' => $itemData['unit_of_measure'] ?? 'pieces',
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                    $updatedItemIds[] = $item->id;
                } else {
                    $item = $stockRequisition->items()->create([
                        'stock_item_id' => $itemData['stock_item_id'],
                        'quantity_requested' => $quantity,
                        'unit_of_measure' => $itemData['unit_of_measure'] ?? 'pieces',
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                    $updatedItemIds[] = $item->id;
                }

                $totalEstimated += $totalPrice;
            }

            // Delete items that were not updated
            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($itemsToDelete)) {
                StockRequisitionItem::whereIn('id', $itemsToDelete)->delete();
            }

            $stockRequisition->update(['estimated_total' => $totalEstimated]);

            DB::commit();

            return redirect()
                ->route('stock-requisitions.show', $stockRequisition)
                ->with('success', 'Stock requisition updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update requisition: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StockRequisition  $stockRequisition
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(StockRequisition $stockRequisition)
    {
        $this->authorize('delete', $stockRequisition);

        if (!$stockRequisition->isEditable()) {
            return redirect()
                ->route('stock-requisitions.show', $stockRequisition)
                ->with('error', 'This requisition cannot be deleted because it is already approved or issued.');
        }

        $stockRequisition->delete();

        return redirect()
            ->route('stock-requisitions.index')
            ->with('success', 'Stock requisition deleted successfully.');
    }

    /**
     * Approve a stock requisition.
     *
     * @param  \App\Models\StockRequisition  $stockRequisition
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(StockRequisition $stockRequisition)
    {
        $this->authorize('approve', $stockRequisition);

        if (!$stockRequisition->canBeApproved()) {
            return back()->with('error', 'This requisition cannot be approved.');
        }

        $stockRequisition->update([
            'status' => StockRequisition::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Stock requisition approved successfully.');
    }

    /**
     * Reject a stock requisition.
     *
     * @param  \App\Models\StockRequisition  $stockRequisition
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, StockRequisition $stockRequisition)
    {
        $this->authorize('reject', $stockRequisition);

        if (!$stockRequisition->canBeRejected()) {
            return back()->with('error', 'This requisition cannot be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ]);

        $stockRequisition->update([
            'status' => StockRequisition::STATUS_REJECTED,
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Stock requisition rejected successfully.');
    }

    /**
     * Issue a stock requisition (full or partial).
     *
     * @param  \App\Models\StockRequisition  $stockRequisition
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function issue(Request $request, StockRequisition $stockRequisition)
    {
        $this->authorize('issue', $stockRequisition);

        if (!$stockRequisition->canBeIssued()) {
            return back()->with('error', 'This requisition cannot be issued.');
        }

        $validated = $request->validate([
            'issue_notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:stock_requisition_items,id',
            'items.*.quantity_issued' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $allItemsFullyIssued = true;
            foreach ($validated['items'] as $itemData) {
                $item = StockRequisitionItem::find($itemData['id']);
                if (!$item || $item->stock_requisition_id !== $stockRequisition->id) {
                    continue;
                }

                $quantityIssued = $itemData['quantity_issued'];
                $item->update(['quantity_issued' => $quantityIssued]);

                if ($quantityIssued < $item->quantity_requested) {
                    $allItemsFullyIssued = false;
                }
            }

            $newStatus = $allItemsFullyIssued
                ? StockRequisition::STATUS_ISSUED
                : StockRequisition::STATUS_PARTIALLY_ISSUED;

            $stockRequisition->update([
                'status' => $newStatus,
                'issued_by' => Auth::id(),
                'issued_at' => now(),
                'issue_notes' => $validated['issue_notes'] ?? null,
            ]);

            DB::commit();

            return back()->with('success', 'Stock requisition issued successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to issue requisition: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a stock requisition.
     *
     * @param  \App\Models\StockRequisition  $stockRequisition
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request, StockRequisition $stockRequisition)
    {
        $this->authorize('cancel', $stockRequisition);

        if (!$stockRequisition->canBeCancelled()) {
            return back()->with('error', 'This requisition cannot be cancelled.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|min:5|max:500',
        ]);

        $stockRequisition->update([
            'status' => StockRequisition::STATUS_CANCELLED,
            'notes' => ($stockRequisition->notes ?? '') . "\nCancelled: " . $validated['cancellation_reason'],
        ]);

        return back()->with('success', 'Stock requisition cancelled successfully.');
    }

    /**
     * Add an item to a requisition (AJAX).
     *
     * @param  \App\Models\StockRequisition  $stockRequisition
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItem(Request $request, StockRequisition $stockRequisition)
    {
        $this->authorize('update', $stockRequisition);

        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'quantity_requested' => 'required|numeric|min:0.001',
            'unit_of_measure' => 'nullable|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $item = $stockRequisition->items()->create($validated);

        return response()->json([
            'success' => true,
            'item' => $item->load('stockItem'),
        ]);
    }

    /**
     * Remove an item from a requisition (AJAX).
     *
     * @param  \App\Models\StockRequisition  $stockRequisition
     * @param  \App\Models\StockRequisitionItem  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItem(StockRequisition $stockRequisition, StockRequisitionItem $item)
    {
        $this->authorize('update', $stockRequisition);

        if ($item->stock_requisition_id !== $stockRequisition->id) {
            return response()->json(['error' => 'Item does not belong to this requisition.'], 403);
        }

        $item->delete();

        return response()->json(['success' => true]);
    }
}
