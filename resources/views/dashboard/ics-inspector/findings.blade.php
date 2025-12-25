@extends('layouts.base')

@section('title', 'Findings')

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
    .stat-item.open { border-left-color: #f39c12; }
    .stat-item.in-progress { border-left-color: #3498db; }
    .stat-item.resolved { border-left-color: #2ecc71; }
    .stat-item.critical { border-left-color: #e74c3c; }
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
    .severity-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .severity-critical { background-color: #e74c3c; color: white; }
    .severity-major { background-color: #f39c12; color: white; }
    .severity-minor { background-color: #3498db; color: white; }
    .severity-observation { background-color: #6c757d; color: white; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Findings</h1>
            <p class="text-muted mb-0">Track and manage inspection findings</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.ics') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item open">
            <div class="number text-warning">{{ $stats['open'] }}</div>
            <div class="label">Open</div>
        </div>
        <div class="stat-item in-progress">
            <div class="number text-primary">{{ $stats['in_progress'] }}</div>
            <div class="label">In Progress</div>
        </div>
        <div class="stat-item resolved">
            <div class="number text-success">{{ $stats['resolved'] }}</div>
            <div class="label">Resolved</div>
        </div>
        <div class="stat-item critical">
            <div class="number text-danger">{{ $stats['critical'] }}</div>
            <div class="label">Critical Open</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.ics.findings') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Severity</label>
                <select name="severity" class="form-select">
                    <option value="">All Severities</option>
                    <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="major" {{ request('severity') === 'major' ? 'selected' : '' }}>Major</option>
                    <option value="minor" {{ request('severity') === 'minor' ? 'selected' : '' }}>Minor</option>
                    <option value="observation" {{ request('severity') === 'observation' ? 'selected' : '' }}>Observation</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by description or farmer..." value="{{ request('search') }}">
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
                        <th>Severity</th>
                        <th>Description</th>
                        <th>Farmer</th>
                        <th>Farm</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($findings as $finding)
                    <tr>
                        <td>#{{ $finding->id }}</td>
                        <td>
                            <span class="severity-badge severity-{{ $finding->severity }}">
                                {{ ucfirst($finding->severity) }}
                            </span>
                        </td>
                        <td>
                            {{ Str::limit($finding->description, 50) }}
                            @if($finding->is_overdue)
                            <br><span class="badge bg-danger">Overdue</span>
                            @endif
                        </td>
                        <td>{{ $finding->inspection->farmer->full_name ?? 'N/A' }}</td>
                        <td>{{ $finding->inspection->farm->name ?? 'N/A' }}</td>
                        <td>
                            {{ $finding->due_date ? $finding->due_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td>
                            @switch($finding->status)
                                @case('open')
                                    <span class="badge bg-warning text-dark">Open</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge bg-info">In Progress</span>
                                    @break
                                @case('resolved')
                                    <span class="badge bg-success">Resolved</span>
                                    @break
                                @case('closed')
                                    <span class="badge bg-secondary">Closed</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $finding->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            <a href="{{ route('inspections.show', $finding->inspection_id) }}" class="btn btn-sm btn-outline-info" title="View Inspection">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted mb-0">No findings found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($findings->hasPages())
        <div class="p-3 border-top">
            {{ $findings->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
