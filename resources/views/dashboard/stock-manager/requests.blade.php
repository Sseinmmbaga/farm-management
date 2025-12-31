@extends('layouts.base')

@section('title', 'Stock Requests')

@push('styles')
<style>
    .filter-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .stats-row {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-item {
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        flex: 1;
        text-align: center;
        border-left: 4px solid;
    }
    .stat-item.total { border-left-color: #3498db; }
    .stat-item.pending-approval { border-left-color: #f39c12; }
    .stat-item.pending-fulfillment { border-left-color: #9b59b6; }
    .stat-item.fulfilled { border-left-color: #2ecc71; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .data-table {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    .text-purple { color: #9b59b6 !important; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Stock Requests</h1>
            <p class="text-muted mb-0">Manage stock requests from extension officers</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.stock') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Request
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-primary">{{ number_format($stats['total_requests']) }}</div>
            <div class="label">Total Requests</div>
        </div>
        <div class="stat-item pending-approval">
            <div class="number text-warning">{{ number_format($stats['pending_approval']) }}</div>
            <div class="label">Pending Approval</div>
        </div>
        <div class="stat-item pending-fulfillment">
            <div class="number text-purple">{{ number_format($stats['pending_fulfillment']) }}</div>
            <div class="label">Pending Fulfillment</div>
        </div>
        <div class="stat-item fulfilled">
            <div class="number text-success">{{ number_format($stats['fulfilled_this_month']) }}</div>
            <div class="label">Fulfilled This Month</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.stock.requests') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search request number, farmer..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="data-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Request #</th>
                        <th>Requested By</th>
                        <th>Farmer</th>
                        <th>Items</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Needed By</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td><strong>{{ $request->request_number }}</strong></td>
                        <td>
                            @if($request->requestedBy)
                                {{ $request->requestedBy->name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($request->farmer)
                                <div>{{ $request->farmer->full_name ?? $request->farmer->first_name . ' ' . $request->farmer->last_name }}</div>
                                <small class="text-muted">{{ $request->farmer->registration_number ?? 'N/A' }}</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $request->items->count() }} items</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $request->priority_color }}">
                                {{ $request->priority_display }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $request->status_color }}">
                                {{ $request->status_display }}
                            </span>
                        </td>
                        <td>
                            @if($request->needed_by)
                                {{ $request->needed_by->format('M d, Y') }}
                                @if($request->needed_by->isPast() && !$request->is_fulfilled)
                                    <span class="badge bg-danger ms-1">Overdue</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="#" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($request->is_submitted)
                                <a href="#" class="btn btn-outline-success" title="Approve">
                                    <i class="fas fa-check"></i>
                                </a>
                                @endif
                                @if($request->is_approved)
                                <a href="#" class="btn btn-outline-primary" title="Fulfill">
                                    <i class="fas fa-truck"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No stock requests found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests instanceof \Illuminate\Pagination\LengthAwarePaginator && $requests->hasPages())
        <div class="p-3 border-top">
            {{ $requests->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
