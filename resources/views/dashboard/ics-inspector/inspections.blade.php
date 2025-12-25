@extends('layouts.base')

@section('title', 'Inspections')

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
    .stat-item.scheduled { border-left-color: #f39c12; }
    .stat-item.completed { border-left-color: #2ecc71; }
    .stat-item.passed { border-left-color: #3498db; }
    .stat-item.failed { border-left-color: #e74c3c; }
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
            <h1 class="h3 mb-0">Inspections</h1>
            <p class="text-muted mb-0">Manage and track all inspections</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.ics') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('inspections.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Inspection
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item scheduled">
            <div class="number text-warning">{{ $stats['scheduled'] }}</div>
            <div class="label">Scheduled</div>
        </div>
        <div class="stat-item completed">
            <div class="number text-success">{{ $stats['completed'] }}</div>
            <div class="label">Completed</div>
        </div>
        <div class="stat-item passed">
            <div class="number text-primary">{{ $stats['passed'] }}</div>
            <div class="label">Passed</div>
        </div>
        <div class="stat-item failed">
            <div class="number text-danger">{{ $stats['failed'] }}</div>
            <div class="label">Failed</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.ics.inspections') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Result</label>
                <select name="result" class="form-select">
                    <option value="">All Results</option>
                    <option value="passed" {{ request('result') === 'passed' ? 'selected' : '' }}>Passed</option>
                    <option value="failed" {{ request('result') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="conditional" {{ request('result') === 'conditional' ? 'selected' : '' }}>Conditional</option>
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
                        <th>Inspection #</th>
                        <th>Farmer</th>
                        <th>Farm</th>
                        <th>Scheduled Date</th>
                        <th>Inspector</th>
                        <th>Status</th>
                        <th>Result</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspections as $inspection)
                    <tr>
                        <td><strong>{{ $inspection->inspection_number ?? '#'.$inspection->id }}</strong></td>
                        <td>{{ $inspection->farmer->full_name ?? 'N/A' }}</td>
                        <td>{{ $inspection->farm->name ?? 'N/A' }}</td>
                        <td>
                            {{ $inspection->scheduled_date ? $inspection->scheduled_date->format('M d, Y') : 'N/A' }}
                            @if($inspection->is_overdue)
                            <br><span class="badge bg-danger">Overdue</span>
                            @endif
                        </td>
                        <td>{{ $inspection->inspector->name ?? 'Unassigned' }}</td>
                        <td>
                            @switch($inspection->status)
                                @case('scheduled')
                                    <span class="badge bg-warning text-dark">Scheduled</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge bg-info">In Progress</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Completed</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-secondary">Cancelled</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $inspection->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if($inspection->result)
                                @switch($inspection->result)
                                    @case('passed')
                                        <span class="badge bg-success">Passed</span>
                                        @break
                                    @case('failed')
                                        <span class="badge bg-danger">Failed</span>
                                        @break
                                    @case('conditional')
                                        <span class="badge bg-warning text-dark">Conditional</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $inspection->result }}</span>
                                @endswitch
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('inspections.show', $inspection) }}" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($inspection->status === 'scheduled')
                            <a href="{{ route('inspections.edit', $inspection) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No inspections found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inspections->hasPages())
        <div class="p-3 border-top">
            {{ $inspections->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
