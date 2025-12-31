@extends('layouts.base')

@section('title', 'Extension Officer Dashboard')

@push('styles')
<style>
    .stats-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stats-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .card-primary { border-left: 4px solid #3498db; }
    .card-success { border-left: 4px solid #2ecc71; }
    .card-warning { border-left: 4px solid #f39c12; }
    .card-info { border-left: 4px solid #17a2b8; }
    .metric-card {
        background: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 15px;
    }
    .metric-value {
        font-size: 1.8rem;
        font-weight: bold;
    }
    .progress-thin {
        height: 6px;
        border-radius: 3px;
    }
    .task-item, .visit-item {
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 3px solid;
    }
    .task-item { border-left-color: #f39c12; }
    .visit-item { border-left-color: #27ae60; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <h1 class="h3 mb-0">Extension Officer Dashboard</h1>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-success" id="refreshBtn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <a href="{{ route('farmers.create') }}" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Add Farmer
            </a>
        </div>
    </div>

    <p class="lead mb-4">Welcome! Manage farmers and collect field data here.</p>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">{{ $myFarmers ?? 0 }}</div>
                <div class="stats-label">My Farmers</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success">
                    <i class="fas fa-tractor"></i>
                </div>
                <div class="stats-number">{{ $totalFarms ?? 0 }}</div>
                <div class="stats-label">Total Farms</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stats-number">{{ $pendingRecords ?? 0 }}</div>
                <div class="stats-label">Pending Records</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card card-info">
                <div class="stats-icon text-info">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stats-number">{{ $fieldVisits ?? 0 }}</div>
                <div class="stats-label">Field Visits This Month</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('farmers.create') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-user-plus"></i> Add New Farmer
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('farms.create') }}" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-plus-circle"></i> Register Farm
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('farm-records.new.create') }}" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-file-alt"></i> New Farmer Record
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('farm-records.existing.create') }}" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-edit"></i> Existing Farmer Record
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>My Farmers</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">View and manage your assigned farmers</p>
                    <a href="{{ route('farmers.index') }}" class="btn btn-primary">View All Farmers</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-tractor me-2"></i>Farm Records</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Manage farm records and data collection</p>
                    <a href="{{ route('farm-records.index') }}" class="btn btn-success">View Farm Records</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-map-marked-alt me-2"></i>Map View</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">View farms on interactive map</p>
                    <a href="{{ route('farms.map.all') }}" class="btn btn-info">Open Map</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Farmer History</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">View farmer registration history</p>
                    <a href="{{ route('farm-records.new.history') }}" class="btn btn-warning">View History</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics Section -->
    @if(isset($performanceMetrics))
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Visit Completion Rate -->
                        <div class="col-md-3">
                            <div class="metric-card text-center">
                                <div class="metric-value text-success">{{ $performanceMetrics['visit_completion_rate'] }}%</div>
                                <div class="text-muted small mb-2">Visit Completion Rate</div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-success" style="width: {{ $performanceMetrics['visit_completion_rate'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $performanceMetrics['completed_visits'] }}/{{ $performanceMetrics['total_scheduled_visits'] }} this month</small>
                            </div>
                        </div>

                        <!-- Task Completion Rate -->
                        <div class="col-md-3">
                            <div class="metric-card text-center">
                                <div class="metric-value text-primary">{{ $performanceMetrics['task_completion_rate'] }}%</div>
                                <div class="text-muted small mb-2">Task Completion Rate</div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-primary" style="width: {{ $performanceMetrics['task_completion_rate'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $performanceMetrics['completed_tasks'] }}/{{ $performanceMetrics['total_tasks'] }} this month</small>
                            </div>
                        </div>

                        <!-- Certification Rate -->
                        <div class="col-md-3">
                            <div class="metric-card text-center">
                                <div class="metric-value text-warning">{{ $performanceMetrics['certification_rate'] }}%</div>
                                <div class="text-muted small mb-2">Organic Certification Rate</div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-warning" style="width: {{ $performanceMetrics['certification_rate'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $performanceMetrics['organic_farmers'] }} organic, {{ $performanceMetrics['in_conversion_farmers'] }} in conversion</small>
                            </div>
                        </div>

                        <!-- Training Participation -->
                        <div class="col-md-3">
                            <div class="metric-card text-center">
                                <div class="metric-value text-info">{{ $performanceMetrics['training_participation_rate'] }}%</div>
                                <div class="text-muted small mb-2">Training Participation</div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-info" style="width: {{ $performanceMetrics['training_participation_rate'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $performanceMetrics['farmers_with_training'] }} farmers trained this year</small>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Progress -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Monthly Activity Progress</span>
                                <span class="badge bg-{{ $performanceMetrics['activity_progress'] >= 80 ? 'success' : ($performanceMetrics['activity_progress'] >= 50 ? 'warning' : 'danger') }}">
                                    {{ $performanceMetrics['logs_this_month'] }}/{{ $performanceMetrics['monthly_target'] }} logs
                                </span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $performanceMetrics['activity_progress'] >= 80 ? 'success' : ($performanceMetrics['activity_progress'] >= 50 ? 'warning' : 'danger') }}"
                                     style="width: {{ $performanceMetrics['activity_progress'] }}%">
                                    {{ $performanceMetrics['activity_progress'] }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tasks and Visits Section -->
    <div class="row mt-4">
        <!-- My Tasks -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-tasks me-2"></i>My Tasks</h5>
                    <a href="{{ route('tasks.my-tasks') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @forelse($myTasks ?? [] as $task)
                    <div class="task-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $task->title }}</strong>
                                <div class="text-muted small">
                                    <span class="badge bg-{{ $task->priority_color }}">{{ $task->priority_label }}</span>
                                    @if($task->planned_end_date)
                                    <span class="ms-2">
                                        <i class="fas fa-clock"></i> Due {{ $task->planned_end_date->format('M d') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <span class="badge bg-{{ $task->status_color }}">{{ $task->status_label }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p class="mb-0">No active tasks</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Upcoming Visits -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-calendar-check me-2"></i>Upcoming Visits</h5>
                    <a href="{{ route('visits.index') }}" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body">
                    @forelse($upcomingVisits ?? [] as $visit)
                    <div class="visit-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $visit->farm?->display_name ?? 'Farm Visit' }}</strong>
                                <div class="text-muted small">
                                    <i class="fas fa-user"></i> {{ $visit->farmer?->full_name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-info">{{ $visit->purpose_label }}</span>
                                <div class="small text-muted mt-1">
                                    {{ $visit->scheduled_date?->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                        <p class="mb-0">No upcoming visits</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                    location.reload();
                }, 1000);
            });
        }
    });
</script>
@endpush
