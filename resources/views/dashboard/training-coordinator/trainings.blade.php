@extends('layouts.base')

@section('title', 'Trainings')

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
    .stat-item.in-progress { border-left-color: #3498db; }
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
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Training Sessions</h1>
            <p class="text-muted mb-0">Manage all training sessions</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.training') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('dashboard.training.calendar') }}" class="btn btn-outline-info">
                <i class="fas fa-calendar-alt"></i> Calendar
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Training
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item scheduled">
            <div class="number text-warning">{{ $stats['scheduled'] }}</div>
            <div class="label">Scheduled</div>
        </div>
        <div class="stat-item in-progress">
            <div class="number text-primary">{{ $stats['in_progress'] }}</div>
            <div class="label">In Progress</div>
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
        <form method="GET" action="{{ route('dashboard.training.trainings') }}" class="row g-3">
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
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <option value="organic_farming" {{ request('category') === 'organic_farming' ? 'selected' : '' }}>Organic Farming</option>
                    <option value="pest_management" {{ request('category') === 'pest_management' ? 'selected' : '' }}>Pest Management</option>
                    <option value="soil_health" {{ request('category') === 'soil_health' ? 'selected' : '' }}>Soil Health</option>
                    <option value="water_management" {{ request('category') === 'water_management' ? 'selected' : '' }}>Water Management</option>
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
                        <th>Training</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Trainer</th>
                        <th>Participants</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainings as $training)
                    <tr>
                        <td>
                            <strong>{{ $training->title ?? 'N/A' }}</strong>
                            @if($training->description)
                            <br><small class="text-muted">{{ Str::limit($training->description, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ ucwords(str_replace('_', ' ', $training->category ?? 'N/A')) }}</td>
                        <td>
                            {{ $training->scheduled_date ? $training->scheduled_date->format('M d, Y') : 'N/A' }}
                            @if($training->scheduled_time)
                            <br><small class="text-muted">{{ $training->scheduled_time }}</small>
                            @endif
                        </td>
                        <td>{{ $training->location ?? 'TBD' }}</td>
                        <td>{{ $training->trainer->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $training->participants_count ?? 0 }} registered</span>
                            @if($training->max_participants)
                            <br><small class="text-muted">Max: {{ $training->max_participants }}</small>
                            @endif
                        </td>
                        <td>
                            @switch($training->status ?? 'scheduled')
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
                                    <span class="badge bg-danger">Cancelled</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $training->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(($training->status ?? 'scheduled') === 'scheduled')
                            <a href="#" class="btn btn-sm btn-outline-success" title="Record Attendance">
                                <i class="fas fa-user-check"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No training sessions found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trainings instanceof \Illuminate\Pagination\LengthAwarePaginator && $trainings->hasPages())
        <div class="p-3 border-top">
            {{ $trainings->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
