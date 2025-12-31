@extends('layouts.base')

@section('title', 'Stock Distributions')

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
    .stat-item.this-month { border-left-color: #2ecc71; }
    .stat-item.credit { border-left-color: #f39c12; }
    .stat-item.farmers { border-left-color: #9b59b6; }
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
    .badge-credit { background-color: #f39c12; }
    .badge-cash { background-color: #3498db; }
    .badge-free { background-color: #2ecc71; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Stock Distributions</h1>
            <p class="text-muted mb-0">Manage stock distributions to farmers</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.stock') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Distribution
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-primary">{{ number_format($stats['total_distributions']) }}</div>
            <div class="label">Total Distributions</div>
        </div>
        <div class="stat-item this-month">
            <div class="number text-success">{{ number_format($stats['this_month']) }}</div>
            <div class="label">This Month</div>
        </div>
        <div class="stat-item credit">
            <div class="number text-warning">{{ number_format($stats['credit_outstanding'], 2) }}</div>
            <div class="label">Credit Outstanding</div>
        </div>
        <div class="stat-item farmers">
            <div class="number text-purple">{{ number_format($stats['farmers_served']) }}</div>
            <div class="label">Farmers Served</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.stock.distributions') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Distribution Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                    <option value="cash" {{ request('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="free" {{ request('type') == 'free' ? 'selected' : '' }}>Free</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search farmer..." value="{{ request('search') }}">
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
                        <th>ID</th>
                        <th>Farmer</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distributions as $distribution)
                    <tr>
                        <td><strong>#{{ $distribution->id }}</strong></td>
                        <td>
                            @if($distribution->farmer)
                                <div>{{ $distribution->farmer->full_name ?? $distribution->farmer->first_name . ' ' . $distribution->farmer->last_name }}</div>
                                <small class="text-muted">{{ $distribution->farmer->registration_number ?? 'N/A' }}</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $distribution->transaction?->stockItem?->name ?? 'N/A' }}</td>
                        <td>{{ number_format($distribution->quantity, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $distribution->distribution_type }}">
                                {{ $distribution->distribution_type_display }}
                            </span>
                        </td>
                        <td>{{ number_format($distribution->value, 2) }}</td>
                        <td>
                            @if($distribution->distribution_type == 'credit')
                                @if($distribution->is_fully_repaid)
                                    <span class="badge bg-success">Repaid</span>
                                @else
                                    <span class="badge bg-warning text-dark">Outstanding</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td>{{ $distribution->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No distributions found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($distributions instanceof \Illuminate\Pagination\LengthAwarePaginator && $distributions->hasPages())
        <div class="p-3 border-top">
            {{ $distributions->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
