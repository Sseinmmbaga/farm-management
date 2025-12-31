<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['user', 'approvedBy', 'rejectedBy', 'cancelledBy'])
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

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $leaveRequests = $query->paginate(20);

        return view('leave-requests.index', compact('leaveRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::active()->get(['id', 'name', 'email']);
        $leaveTypes = [
            'annual' => 'Annual Leave',
            'sick' => 'Sick Leave',
            'personal' => 'Personal Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'study' => 'Study Leave',
        ];

        return view('leave-requests.create', compact('users', 'leaveTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', Rule::in(['annual', 'sick', 'personal', 'maternity', 'paternity', 'study'])],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:10'],
            'is_sick_sheet' => ['nullable', 'boolean'],
            'sick_sheet_file' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
            'notes' => ['nullable', 'string'],
        ]);

        // Calculate days
        $start = \Carbon\Carbon::parse($validated['start_date']);
        $end = \Carbon\Carbon::parse($validated['end_date']);
        $days = $start->diffInDays($end) + 1;

        // Handle sick sheet file upload
        $sickSheetPath = null;
        if ($request->hasFile('sick_sheet_file')) {
            $sickSheetPath = $request->file('sick_sheet_file')->store('sick_sheets', 'public');
        }

        $leaveRequest = LeaveRequest::create([
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days' => $days,
            'reason' => $validated['reason'],
            'is_sick_sheet' => $validated['is_sick_sheet'] ?? false,
            'sick_sheet_file' => $sickSheetPath,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('leave-requests.show', $leaveRequest)
            ->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load(['user', 'approvedBy', 'rejectedBy', 'cancelledBy']);
        return view('leave-requests.show', compact('leaveRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        // Only allow editing if pending
        if (!$leaveRequest->isPending) {
            abort(403, 'Cannot edit a leave request that is already processed.');
        }

        $users = User::active()->get(['id', 'name', 'email']);
        $leaveTypes = [
            'annual' => 'Annual Leave',
            'sick' => 'Sick Leave',
            'personal' => 'Personal Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'study' => 'Study Leave',
        ];

        return view('leave-requests.edit', compact('leaveRequest', 'users', 'leaveTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        // Only allow updating if pending
        if (!$leaveRequest->isPending) {
            abort(403, 'Cannot update a leave request that is already processed.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', Rule::in(['annual', 'sick', 'personal', 'maternity', 'paternity', 'study'])],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:10'],
            'is_sick_sheet' => ['nullable', 'boolean'],
            'sick_sheet_file' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
            'notes' => ['nullable', 'string'],
        ]);

        // Calculate days
        $start = \Carbon\Carbon::parse($validated['start_date']);
        $end = \Carbon\Carbon::parse($validated['end_date']);
        $days = $start->diffInDays($end) + 1;

        // Handle sick sheet file upload
        $sickSheetPath = $leaveRequest->sick_sheet_file;
        if ($request->hasFile('sick_sheet_file')) {
            $sickSheetPath = $request->file('sick_sheet_file')->store('sick_sheets', 'public');
        }

        $leaveRequest->update([
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days' => $days,
            'reason' => $validated['reason'],
            'is_sick_sheet' => $validated['is_sick_sheet'] ?? false,
            'sick_sheet_file' => $sickSheetPath,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('leave-requests.show', $leaveRequest)
            ->with('success', 'Leave request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        // Only allow deletion if pending
        if (!$leaveRequest->isPending) {
            abort(403, 'Cannot delete a leave request that is already processed.');
        }

        $leaveRequest->delete();

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'Leave request deleted successfully.');
    }

    /**
     * Approve a leave request.
     */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        // Only allow approval if pending
        if (!$leaveRequest->isPending) {
            abort(403, 'Leave request is not pending.');
        }

        $validated = $request->validate([
            'approval_notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Calculate leave balance before and after (placeholder logic)
        $leaveBalanceBefore = 20; // Example: fetch from user's leave balance table
        $days = $leaveRequest->days;
        $leaveBalanceAfter = $leaveBalanceBefore - $days;

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? null,
            'leave_balance_before' => $leaveBalanceBefore,
            'leave_balance_after' => $leaveBalanceAfter,
        ]);

        return redirect()
            ->route('leave-requests.show', $leaveRequest)
            ->with('success', 'Leave request approved successfully.');
    }

    /**
     * Reject a leave request.
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if (!$leaveRequest->isPending) {
            abort(403, 'Leave request is not pending.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()
            ->route('leave-requests.show', $leaveRequest)
            ->with('success', 'Leave request rejected.');
    }

    /**
     * Cancel a leave request (by requester or admin).
     */
    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        // Allow cancellation if pending or approved but not taken
        if (!in_array($leaveRequest->status, ['pending', 'approved'])) {
            abort(403, 'Leave request cannot be cancelled at this stage.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $leaveRequest->update([
            'status' => 'cancelled',
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return redirect()
            ->route('leave-requests.show', $leaveRequest)
            ->with('success', 'Leave request cancelled.');
    }

    /**
     * Mark leave as taken (after actual leave).
     */
    public function markAsTaken(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'approved') {
            abort(403, 'Only approved leave requests can be marked as taken.');
        }

        $leaveRequest->update([
            'status' => 'taken',
        ]);

        return redirect()
            ->route('leave-requests.show', $leaveRequest)
            ->with('success', 'Leave marked as taken.');
    }
}