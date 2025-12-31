@extends('layouts.base')

@section('title', 'Seeds Received')

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
    .stat-item.total { border-left-color: #27ae60; }
    .stat-item.quantity { border-left-color: #2ecc71; }
    .stat-item.pending { border-left-color: #f39c12; }
    .stat-item.value { border-left-color: #3498db; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .distribution-table th {
        border-top: none;
        font-weight: 600;
        color: #555;
        background: #f8f9fa;
    }
    .distribution-table td {
        vertical-align: middle;
    }
    .distribution-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .badge-delivered { background-color: #e6f7ee; color: #27ae60; }
    .badge-pending { background-color: #fff4e6; color: #e67e22; }
    .badge-partial { background-color: #e6f0ff; color: #3498db; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Seeds Received</h1>
            <p class="text-muted mb-0">Track your seed distributions and stock received</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('dashboard.farmer.distributions') }}" class="btn btn-outline-primary">
                <i class="fas fa-box"></i> View All Distributions
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-success">{{ $stats['total_deliveries'] }}</div>
            <div class="label">Total Deliveries</div>
        </div>
        <div class="stat-item quantity">
            <div class="number text-success">{{ number_format($stats['total_quantity']) }}</div>
            <div class="label">Total Seeds (kg)</div>
        </div>
        <div class="stat-item pending">
            <div class="number text-warning">{{ $stats['pending_deliveries'] }}</div>
            <div class="label">Pending Deliveries</div>
        </div>
        <div class="stat-item value">
            <div class="number text-primary">{{ number_format($stats['total_value']) }}</div>
            <div class="label">Estimated Value (TZS)</div>
        </div>
    </div>

    <!-- Distribution Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Seed Distributions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover distribution-table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Seed Type</th>
                            <th>Quantity</th>
                            <th>Farm</th>
                            <th>Season</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($distributions as $dist)
                        <tr>
                            <td>{{ $dist->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-seedling text-success me-2"></i>
                                    {{ $dist->transaction?->stock_item?->name ?? 'Seed' }}
                                </div>
                            </td>
                            <td><strong>{{ number_format($dist->quantity) }} {{ $dist->transaction?->stock_item?->unit ?? 'kg' }}</strong></td>
                            <td>{{ $dist->farm?->name ?? 'N/A' }}</td>
                            <td>{{ $dist->season?->name ?? 'N/A' }}</td>
                            <td>
                                <span class="distribution-badge badge-{{ $dist->is_repaid ? 'delivered' : ($dist->distribution_type === 'credit' ? 'pending' : 'delivered') }}">
                                    {{ ucfirst($dist->distribution_type) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No Seed Distributions</h6>
                                <p class="text-muted small mb-0">You haven't received any seed distributions yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($distributions instanceof \Illuminate\Pagination\LengthAwarePaginator && $distributions->hasPages())
        <div class="card-footer">
            {{ $distributions->links() }}
        </div>
        @endif
    </div>

    <!-- Upcoming Deliveries -->
    @if($upcomingDeliveries->count() > 0)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-calendar-check text-success me-2"></i> Upcoming Scheduled Deliveries</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <thead>
                        <tr>
                            <th>Expected Date</th>
                            <th>Request #</th>
                            <th>Items</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingDeliveries as $request)
                        <tr>
                            <td>{{ $request->needed_by?->format('M d, Y') ?? 'N/A' }}</td>
                            <td>{{ $request->request_number }}</td>
                            <td>
                                @foreach($request->items->take(2) as $item)
                                    {{ $item->stockItem?->name ?? 'Item' }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                                @if($request->items->count() > 2)
                                    +{{ $request->items->count() - 2 }} more
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $request->status === 'approved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($distributions->isEmpty() && $upcomingDeliveries->isEmpty())
    <div class="text-center py-5 mt-4">
        <i class="fas fa-seedling fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">No Seed Distributions</h5>
        <p class="text-muted">You haven't received any seed distributions yet.</p>
        <p class="text-muted small">Contact your extension officer for information about cotton and sesame seed programs.</p>
    </div>
    @endif
@endsection
