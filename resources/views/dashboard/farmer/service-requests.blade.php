@extends('layouts.base')

@section('title', 'My Service Requests')

@push('styles')
<style>
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
    .stat-item.open { border-left-color: #e74c3c; }
    .stat-item.in-progress { border-left-color: #f39c12; }
    .stat-item.resolved { border-left-color: #27ae60; }
    .stat-item.total { border-left-color: #3498db; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .request-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #eee;
    }
    .request-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .status-open { background-color: #ffeaea; color: #c0392b; }
    .status-in-progress { background-color: #fff4e6; color: #e67e22; }
    .status-resolved { background-color: #e6f7ee; color: #27ae60; }
    .status-closed { background-color: #f0f0f0; color: #7f8c8d; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">My Service Requests</h1>
            <p class="text-muted mb-0">View and track your submitted service requests</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('service-requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Request
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item open">
            <div class="number text-danger">{{ $stats['open'] }}</div>
            <div class="label">Open</div>
        </div>
        <div class="stat-item in-progress">
            <div class="number text-warning">{{ $stats['in_progress'] }}</div>
            <div class="label">In Progress</div>
        </div>
        <div class="stat-item resolved">
            <div class="number text-success">{{ $stats['resolved'] }}</div>
            <div class="label">Resolved</div>
        </div>
        <div class="stat-item total">
            <div class="number text-primary">{{ $stats['total'] }}</div>
            <div class="label">Total Requests</div>
        </div>
    </div>

    <!-- Requests List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Requests</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($requests as $request)
                    <div class="request-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $request->title ?? 'Service Request #' . $request->id }}</h6>
                                <p class="text-muted mb-2 small">{{ $request->description ?? 'No description' }}</p>
                                <div class="d-flex gap-3 align-items-center">
                                    <span class="request-status status-{{ $request->status }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                    <span class="text-muted small">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $request->created_at->format('M d, Y') }}
                                    </span>
                                    @if($request->assignedTo)
                                    <span class="text-muted small">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $request->assignedTo->name }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('service-requests.show', $request) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Service Requests</h5>
                        <p class="text-muted">You haven't submitted any service requests yet.</p>
                        <a href="{{ route('service-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Your First Request
                        </a>
                    </div>
                    @endforelse
                </div>
                @if($requests instanceof \Illuminate\Pagination\LengthAwarePaginator && $requests->hasPages())
                <div class="card-footer">
                    {{ $requests->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection