@extends('layouts.base')

@section('title', 'Harvest Records')

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
    .stat-item.pending { border-left-color: #f39c12; }
    .stat-item.in-progress { border-left-color: #3498db; }
    .stat-item.completed { border-left-color: #2ecc71; }
    .stat-item.total { border-left-color: #34495e; }
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
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Harvest Records</h1>
            <p class="text-muted mb-0">Track and manage harvest data</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.production') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i> Record Harvest
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item pending">
            <div class="number text-warning">{{ $stats['pending'] }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-item in-progress">
            <div class="number text-primary">{{ $stats['in_progress'] }}</div>
            <div class="label">In Progress</div>
        </div>
        <div class="stat-item completed">
            <div class="number text-success">{{ $stats['completed'] }}</div>
            <div class="label">Completed</div>
        </div>
        <div class="stat-item total">
            <div class="number text-dark">{{ number_format($stats['total_quantity'], 1) }}</div>
            <div class="label">Total Yield (tons)</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.production.harvests') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
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
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
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
                        <th>Harvest ID</th>
                        <th>Farm</th>
                        <th>Farmer</th>
                        <th>Date</th>
                        <th>Quantity (kg)</th>
                        <th>Quality Grade</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($harvests as $harvest)
                    <tr>
                        <td><strong>#{{ $harvest->id ?? 'N/A' }}</strong></td>
                        <td>{{ $harvest->farm->name ?? 'N/A' }}</td>
                        <td>{{ $harvest->farmer->full_name ?? 'N/A' }}</td>
                        <td>{{ $harvest->harvest_date ? $harvest->harvest_date->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ number_format($harvest->quantity ?? 0) }}</td>
                        <td>
                            @switch($harvest->quality_grade ?? 'unknown')
                                @case('A')
                                    <span class="badge bg-success">Grade A</span>
                                    @break
                                @case('B')
                                    <span class="badge bg-info">Grade B</span>
                                    @break
                                @case('C')
                                    <span class="badge bg-warning text-dark">Grade C</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $harvest->quality_grade ?? 'N/A' }}</span>
                            @endswitch
                        </td>
                        <td>
                            @switch($harvest->status ?? 'pending')
                                @case('pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge bg-info">In Progress</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Completed</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $harvest->status }}</span>
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
                            <i class="fas fa-leaf fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No harvest records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($harvests instanceof \Illuminate\Pagination\LengthAwarePaginator && $harvests->hasPages())
        <div class="p-3 border-top">
            {{ $harvests->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
