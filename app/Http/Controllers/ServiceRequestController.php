<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Farmers\Farmer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['farmer', 'requestedBy', 'assignedTo'])
            ->latest();

        // Filter by farmer if user is a farmer
        if (Auth::user()->role === 'farmer') {
            $farmer = Farmer::where('phone', Auth::user()->phone)
                ->orWhere('email', Auth::user()->email)
                ->first();
            if ($farmer) {
                $query->forFarmer($farmer->id);
            }
        }

        // Search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $requests = $query->paginate(20);

        return view('service-requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $farmers = Farmer::active()->get(['id', 'first_name', 'last_name', 'registration_number']);
        $users = User::active()->get(['id', 'name', 'email']);

        $types = [
            'support' => 'Support',
            'training' => 'Training',
            'maintenance' => 'Maintenance',
            'other' => 'Other',
        ];

        $priorities = [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];

        return view('service-requests.create', compact('farmers', 'users', 'types', 'priorities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => ['required', 'exists:farmers,id'],
            'type' => ['required', Rule::in(['support', 'training', 'maintenance', 'other'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        // Auto-generate request number is handled by model
        $serviceRequest = ServiceRequest::create([
            'farmer_id' => $validated['farmer_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'assigned_to' => $validated['assigned_to'] ?? null,
            'requested_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Service request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['farmer', 'requestedBy', 'assignedTo']);
        return view('service-requests.show', compact('serviceRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceRequest $serviceRequest)
    {
        // Only allow editing if status is open or in_progress (optional)
        if (!in_array($serviceRequest->status, ['open', 'in_progress'])) {
            abort(403, 'Cannot edit a request that is already resolved or closed.');
        }

        $farmers = Farmer::active()->get(['id', 'first_name', 'last_name', 'registration_number']);
        $users = User::active()->get(['id', 'name', 'email']);

        $types = [
            'support' => 'Support',
            'training' => 'Training',
            'maintenance' => 'Maintenance',
            'other' => 'Other',
        ];

        $priorities = [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];

        $statuses = [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
        ];

        return view('service-requests.edit', compact('serviceRequest', 'farmers', 'users', 'types', 'priorities', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        // Only allow updating if status is open or in_progress
        if (!in_array($serviceRequest->status, ['open', 'in_progress'])) {
            abort(403, 'Cannot update a request that is already resolved or closed.');
        }

        $validated = $request->validate([
            'farmer_id' => ['required', 'exists:farmers,id'],
            'type' => ['required', Rule::in(['support', 'training', 'maintenance', 'other'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['open', 'in_progress', 'resolved', 'closed', 'cancelled'])],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'resolved_notes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        // If status changed to resolved, set resolved_at timestamp
        if ($validated['status'] === 'resolved' && $serviceRequest->status !== 'resolved') {
            $validated['resolved_at'] = now();
        }

        $serviceRequest->update($validated);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Service request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceRequest $serviceRequest)
    {
        // Only allow deletion if status is open or cancelled (or maybe any)
        if (!in_array($serviceRequest->status, ['open', 'cancelled'])) {
            abort(403, 'Cannot delete a request that is already in progress, resolved or closed.');
        }

        $serviceRequest->delete();

        return redirect()
            ->route('service-requests.index')
            ->with('success', 'Service request deleted successfully.');
    }

    /**
     * Assign a service request to a user.
     */
    public function assign(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $serviceRequest->update([
            'assigned_to' => $validated['assigned_to'],
            'status' => 'in_progress',
            'notes' => $validated['notes'] ?? $serviceRequest->notes,
        ]);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Service request assigned successfully.');
    }

    /**
     * Resolve a service request.
     */
    public function resolve(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'resolved_notes' => ['required', 'string', 'min:10'],
        ]);

        $serviceRequest->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_notes' => $validated['resolved_notes'],
        ]);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Service request marked as resolved.');
    }

    /**
     * Close a service request.
     */
    public function close(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update([
            'status' => 'closed',
        ]);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Service request closed.');
    }

    /**
     * Cancel a service request.
     */
    public function cancel(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update([
            'status' => 'cancelled',
        ]);

        return redirect()
            ->route('service-requests.show', $serviceRequest)
            ->with('success', 'Service request cancelled.');
    }
}
