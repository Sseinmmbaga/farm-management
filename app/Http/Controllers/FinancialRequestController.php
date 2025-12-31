<?php

namespace App\Http\Controllers;

use App\Models\FinancialRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FinancialRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FinancialRequest::with(['user', 'approvedBy', 'rejectedBy', 'disbursedBy'])
            ->latest();

        // Filter by user if not admin/manager
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'manager' && Auth::user()->role !== 'supervisor') {
            $query->forUser(Auth::id());
        }

        // Search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->type($request->type);
        }

        // Filter by amount range
        if ($request->has('min_amount') && $request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->has('max_amount') && $request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        $financialRequests = $query->paginate(20);

        return view('financial-requests.index', compact('financialRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::active()->get(['id', 'name', 'email']);
        $requestTypes = [
            'loan' => 'Loan',
            'salary_advance' => 'Salary Advance',
            'imprest' => 'Imprest',
            'other' => 'Other',
        ];
        $currencies = [
            'TZS' => 'Tanzanian Shilling (TZS)',
            'USD' => 'US Dollar (USD)',
            'EUR' => 'Euro (EUR)',
        ];

        return view('financial-requests.create', compact('users', 'requestTypes', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', Rule::in(['loan', 'salary_advance', 'imprest', 'other'])],
            'amount' => ['required', 'numeric', 'min:1000', 'max:100000000'],
            'currency' => ['required', Rule::in(['TZS', 'USD', 'EUR'])],
            'purpose' => ['required', 'string', 'min:10', 'max:1000'],
            'repayment_installments' => ['nullable', 'integer', 'min:1', 'max:60'],
            'repayment_start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string'],
        ]);

        $financialRequest = FinancialRequest::create([
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'purpose' => $validated['purpose'],
            'repayment_installments' => $validated['repayment_installments'] ?? null,
            'repayment_start_date' => $validated['repayment_start_date'] ?? null,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('financial-requests.show', $financialRequest)
            ->with('success', 'Financial request submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FinancialRequest $financialRequest)
    {
        $financialRequest->load(['user', 'approvedBy', 'rejectedBy', 'disbursedBy']);
        return view('financial-requests.show', compact('financialRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinancialRequest $financialRequest)
    {
        // Only allow editing if pending
        if (!$financialRequest->isPending) {
            abort(403, 'Cannot edit a financial request that is already processed.');
        }

        $users = User::active()->get(['id', 'name', 'email']);
        $requestTypes = [
            'loan' => 'Loan',
            'salary_advance' => 'Salary Advance',
            'imprest' => 'Imprest',
            'other' => 'Other',
        ];
        $currencies = [
            'TZS' => 'Tanzanian Shilling (TZS)',
            'USD' => 'US Dollar (USD)',
            'EUR' => 'Euro (EUR)',
        ];

        return view('financial-requests.edit', compact('financialRequest', 'users', 'requestTypes', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinancialRequest $financialRequest)
    {
        // Only allow updating if pending
        if (!$financialRequest->isPending) {
            abort(403, 'Cannot update a financial request that is already processed.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', Rule::in(['loan', 'salary_advance', 'imprest', 'other'])],
            'amount' => ['required', 'numeric', 'min:1000', 'max:100000000'],
            'currency' => ['required', Rule::in(['TZS', 'USD', 'EUR'])],
            'purpose' => ['required', 'string', 'min:10', 'max:1000'],
            'repayment_installments' => ['nullable', 'integer', 'min:1', 'max:60'],
            'repayment_start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string'],
        ]);

        $financialRequest->update([
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'purpose' => $validated['purpose'],
            'repayment_installments' => $validated['repayment_installments'] ?? null,
            'repayment_start_date' => $validated['repayment_start_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('financial-requests.show', $financialRequest)
            ->with('success', 'Financial request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinancialRequest $financialRequest)
    {
        // Only allow deletion if pending
        if (!$financialRequest->isPending) {
            abort(403, 'Cannot delete a financial request that is already processed.');
        }

        $financialRequest->delete();

        return redirect()
            ->route('financial-requests.index')
            ->with('success', 'Financial request deleted successfully.');
    }

    /**
     * Approve a financial request.
     */
    public function approve(Request $request, FinancialRequest $financialRequest)
    {
        if (!$financialRequest->isPending) {
            abort(403, 'Financial request is not pending.');
        }

        $validated = $request->validate([
            'approval_notes' => ['nullable', 'string', 'max:500'],
            'total_repayment_amount' => ['required', 'numeric', 'min:' . $financialRequest->amount],
            'repayment_end_date' => ['required', 'date', 'after_or_equal:today'],
            'repayment_schedule' => ['nullable', 'json'],
        ]);

        $financialRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? null,
            'total_repayment_amount' => $validated['total_repayment_amount'],
            'repayment_end_date' => $validated['repayment_end_date'],
            'repayment_schedule' => $validated['repayment_schedule'] ?? null,
        ]);

        return redirect()
            ->route('financial-requests.show', $financialRequest)
            ->with('success', 'Financial request approved successfully.');
    }

    /**
     * Reject a financial request.
     */
    public function reject(Request $request, FinancialRequest $financialRequest)
    {
        if (!$financialRequest->isPending) {
            abort(403, 'Financial request is not pending.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $financialRequest->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()
            ->route('financial-requests.show', $financialRequest)
            ->with('success', 'Financial request rejected.');
    }

    /**
     * Disburse funds (mark as disbursed).
     */
    public function disburse(Request $request, FinancialRequest $financialRequest)
    {
        if (!$financialRequest->isApproved) {
            abort(403, 'Only approved financial requests can be disbursed.');
        }

        $validated = $request->validate([
            'disbursement_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $financialRequest->update([
            'status' => 'disbursed',
            'disbursed_by' => Auth::id(),
            'disbursed_at' => now(),
            'disbursement_notes' => $validated['disbursement_notes'] ?? null,
        ]);

        return redirect()
            ->route('financial-requests.show', $financialRequest)
            ->with('success', 'Funds disbursed successfully.');
    }

    /**
     * Record a repayment.
     */
    public function recordRepayment(Request $request, FinancialRequest $financialRequest)
    {
        if (!$financialRequest->isDisbursed && !$financialRequest->isApproved) {
            abort(403, 'Repayments can only be recorded for disbursed or approved requests.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:' . $financialRequest->balance],
            'repayment_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string'],
        ]);

        $newAmountRepaid = $financialRequest->amount_repaid + $validated['amount'];
        $status = $newAmountRepaid >= $financialRequest->total_repayment_amount ? 'repaid' : $financialRequest->status;

        $financialRequest->update([
            'amount_repaid' => $newAmountRepaid,
            'last_repayment_date' => $validated['repayment_date'],
            'status' => $status,
            'notes' => $financialRequest->notes . "\nRepayment recorded: " . $validated['amount'] . ' on ' . $validated['repayment_date'] . '. ' . ($validated['notes'] ?? ''),
        ]);

        return redirect()
            ->route('financial-requests.show', $financialRequest)
            ->with('success', 'Repayment recorded successfully.');
    }

    /**
     * Cancel a financial request.
     */
    public function cancel(Request $request, FinancialRequest $financialRequest)
    {
        if (!in_array($financialRequest->status, ['pending', 'approved'])) {
            abort(403, 'Financial request cannot be cancelled at this stage.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $financialRequest->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return redirect()
            ->route('financial-requests.show', $financialRequest)
            ->with('success', 'Financial request cancelled.');
    }
}