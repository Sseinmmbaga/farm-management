@extends('layouts.base')

@section('title', 'Production Plans')

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
    .stat-item.draft { border-left-color: #6c757d; }
    .stat-item.active { border-left-color: #3498db; }
    .stat-item.completed { border-left-color: #2ecc71; }
    .stat-item.cancelled { border-left-color: #e74c3c; }
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
    .progress-bar-container {
        height: 8px;
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background-color: #2ecc71;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Production Plans</h1>
            <p class="text-muted mb-0">Manage production planning and targets</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.production') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Plan
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item draft">
            <div class="number text-secondary">{{ $stats['draft'] }}</div>
            <div class="label">Draft</div>
        </div>
        <div class="stat-item active">
            <div class="number text-primary">{{ $stats['active'] }}</div>
            <div class="label">Active</div>
        </div>
        <div class="stat-item completed">
            <div class="number text-success">{{ $stats['completed'] }}</div>
            <div class="label">Completed</div>
        </div>
        <div class="stat-item cancelled">
            <div class="number text-danger">{{ $stats['cancelled'] }}</div>
            <div class="label">Cancelled</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.production.plans') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Season</label>
                <select name="season" class="form-select">
                    <option value="">All Seasons</option>
                    <option value="2024" {{ request('season') === '2024' ? 'selected' : '' }}>2024</option>
                    <option value="2023" {{ request('season') === '2023' ? 'selected' : '' }}>2023</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Crop Type</label>
                <select name="crop" class="form-select">
                    <option value="">All Crops</option>
                    <option value="cotton" {{ request('crop') === 'cotton' ? 'selected' : '' }}>Cotton</option>
                    <option value="organic" {{ request('crop') === 'organic' ? 'selected' : '' }}>Organic Cotton</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search plans..." value="{{ request('search') }}">
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
                        <th>Plan ID</th>
                        <th>Name</th>
                        <th>Season</th>
                        <th>Target (tons)</th>
                        <th>Progress</th>
                        <th>Farms</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr>
                        <td><strong>#{{ $plan->id ?? 'N/A' }}</strong></td>
                        <td>
                            {{ $plan->name ?? 'N/A' }}
                            @if($plan->description)
                            <br><small class="text-muted">{{ Str::limit($plan->description, 40) }}</small>
                            @endif
                        </td>
                        <td>{{ $plan->season ?? 'N/A' }}</td>
                        <td>{{ number_format($plan->target_quantity ?? 0, 1) }}</td>
                        <td style="min-width: 120px;">
                            @php
                                $progress = $plan->target_quantity > 0
                                    ? min(100, round(($plan->achieved_quantity ?? 0) / $plan->target_quantity * 100))
                                    : 0;
                            @endphp
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: {{ $progress }}%"></div>
                            </div>
                            <small class="text-muted">{{ $progress }}% ({{ number_format($plan->achieved_quantity ?? 0, 1) }} tons)</small>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $plan->farms_count ?? 0 }} farms</span>
                        </td>
                        <td>
                            @switch($plan->status ?? 'draft')
                                @case('draft')
                                    <span class="badge bg-secondary">Draft</span>
                                    @break
                                @case('active')
                                    <span class="badge bg-primary">Active</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Completed</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $plan->status }}</span>
                            @endswitch
                        </td>
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
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No production plans found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($plans instanceof \Illuminate\Pagination\LengthAwarePaginator && $plans->hasPages())
        <div class="p-3 border-top">
            {{ $plans->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
