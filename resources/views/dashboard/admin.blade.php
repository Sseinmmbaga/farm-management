@extends('layouts.base')

@section('title', 'Dashboard Overview')

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
    .card-danger { border-left: 4px solid #e74c3c; }
    .card-info { border-left: 4px solid #17a2b8; }
    .card-purple { border-left: 4px solid #9b59b6; }

    .recent-activity {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .activity-item {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-time {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .badge-organic { background-color: #2ecc71; color: white; }
    .badge-conversion { background-color: #f39c12; color: white; }
    .badge-conventional { background-color: #95a5a6; color: white; }

    .alert-card {
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: transform 0.2s;
    }
    .alert-card:hover {
        transform: translateX(5px);
    }
    .alert-card i {
        font-size: 1.2rem;
        margin-right: 12px;
    }
    .alert-card.alert-danger { background-color: #fee2e2; color: #dc2626; }
    .alert-card.alert-warning { background-color: #fef3c7; color: #d97706; }
    .alert-card.alert-info { background-color: #dbeafe; color: #2563eb; }

    .week-stat {
        text-align: center;
        padding: 15px;
        border-radius: 8px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    .week-stat .number {
        font-size: 1.8rem;
        font-weight: bold;
        color: #2c3e50;
    }
    .week-stat .label {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .bg-purple { background-color: #9b59b6 !important; }
    .btn-outline-purple {
        color: #9b59b6;
        border-color: #9b59b6;
    }
    .btn-outline-purple:hover {
        background-color: #9b59b6;
        color: white;
    }

    .region-bar {
        height: 8px;
        border-radius: 4px;
        background: linear-gradient(90deg, #3498db, #2ecc71);
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <h1 class="h3 mb-0">Dashboard Overview</h1>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-primary" id="refreshBtn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <a href="{{ route('farmers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Farmer
            </a>
        </div>
    </div>

    <!-- Core Metrics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['total_farmers']) }}</div>
                <div class="stats-label">Total Farmers</div>
                <div class="mt-2">
                    <small class="text-success">
                        <i class="fas fa-user-check"></i> {{ number_format($stats['active_farmers']) }} active
                    </small>
                    <br>
                    <small class="text-warning">
                        <i class="fas fa-clock"></i> {{ number_format($stats['pending_farmers']) }} pending
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success">
                    <i class="fas fa-tractor"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['total_farms']) }}</div>
                <div class="stats-label">Total Farms</div>
                <div class="mt-2">
                    <small class="text-success">
                        <i class="fas fa-leaf"></i> {{ number_format($stats['organic_farms']) }} organic certified
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['pending_inspections']) }}</div>
                <div class="stats-label">Pending Inspections</div>
                <div class="mt-2">
                    <small class="text-info">
                        <i class="fas fa-calendar-alt"></i> {{ number_format($stats['upcoming_trainings']) }} upcoming trainings
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card card-danger">
                <div class="stats-icon text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['low_stock_items']) }}</div>
                <div class="stats-label">Low Stock Items</div>
                <div class="mt-2">
                    <small class="text-info">
                        <i class="fas fa-user-tie"></i> {{ number_format($stats['total_users']) }} system users
                    </small>
                    <br>
                    <small class="text-success">
                        <i class="fas fa-user-check"></i> {{ number_format($stats['active_users']) }} active
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats Row -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Quick Stats</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded p-2 me-3">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Extension Officers</h6>
                                    <p class="mb-0 text-muted">
                                        {{ number_format($extensionOfficers) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded p-2 me-3">
                                    <i class="fas fa-chart-line text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Farmers per Officer</h6>
                                    <p class="mb-0 text-muted">
                                        {{ $farmersPerOfficer }}:1 ratio
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded p-2 me-3">
                                    <i class="fas fa-map-marked-alt text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Total Cultivated Area</h6>
                                    <p class="mb-0 text-muted">
                                        {{ number_format($totalCultivatedArea, 1) }} hectares
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-purple rounded p-2 me-3">
                                    <i class="fas fa-leaf text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Organic Conversion Rate</h6>
                                    <p class="mb-0 text-muted">
                                        {{ $organicConversionRate }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts & This Week Stats Row -->
    <div class="row mt-3">
        <!-- Alerts Panel -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-bell text-warning me-2"></i>Alerts & Notifications
                    </h5>
                    @if(count($alerts) > 0)
                        @foreach($alerts as $alert)
                        <a href="{{ $alert['link'] }}" class="alert-card alert-{{ $alert['type'] }}">
                            <i class="{{ $alert['icon'] }}"></i>
                            <span>{{ $alert['message'] }}</span>
                        </a>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">No alerts - everything looks good!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- This Week Stats -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-calendar-week text-primary me-2"></i>This Week's Activity
                    </h5>
                    <div class="row">
                        <div class="col-3">
                            <div class="week-stat">
                                <div class="number text-primary">{{ $thisWeekStats['new_farmers'] }}</div>
                                <div class="label">New Farmers</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="week-stat">
                                <div class="number text-success">{{ $thisWeekStats['new_farms'] }}</div>
                                <div class="label">New Farms</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="week-stat">
                                <div class="number text-info">{{ $thisWeekStats['completed_inspections'] }}</div>
                                <div class="label">Inspections</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="week-stat">
                                <div class="number text-warning">{{ $thisWeekStats['logs_recorded'] }}</div>
                                <div class="label">Logs</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="recent-activity">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Recent Activity</h4>
                    <a href="{{ route('logs.index') }}" class="btn btn-sm btn-outline-primary">View All Logs</a>
                </div>
                
                <!-- Recent Farmer Registrations -->
                @if($recentFarmers->count() > 0)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Recent Farmer Registrations</h6>
                    @foreach($recentFarmers as $farmer)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $farmer->full_name }}</strong>
                                <p class="mb-0">
                                    Reg: {{ $farmer->registration_number }}
                                    @if($farmer->village)
                                    • {{ $farmer->village->name }}
                                    @endif
                                </p>
                                <small class="text-muted">
                                    @if($farmer->extensionOfficer)
                                    Assigned to: {{ $farmer->extensionOfficer->name }}
                                    @endif
                                </small>
                            </div>
                            <div class="activity-time">
                                {{ $farmer->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                
                <!-- Recent Inspections -->
                @if($recentInspections->count() > 0)
                <div>
                    <h6 class="text-muted mb-2">Recent Inspections</h6>
                    @foreach($recentInspections as $inspection)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>
                                    @if($inspection->farmer)
                                    {{ $inspection->farmer->full_name }}
                                    @else
                                    Unknown Farmer
                                    @endif
                                </strong>
                                <p class="mb-0">
                                    Inspection #{{ $inspection->id }}
                                    • Status: <span class="badge bg-{{ $inspection->status === 'completed' ? 'success' : ($inspection->status === 'scheduled' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($inspection->status) }}
                                    </span>
                                </p>
                                <small class="text-muted">
                                    @if($inspection->inspector)
                                    Inspector: {{ $inspection->inspector->name }}
                                    @endif
                                </small>
                            </div>
                            <div class="activity-time">
                                {{ $inspection->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                
                <!-- Recent Activity Logs -->
                @if($recentActivityLogs->count() > 0)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Recent Activity Logs</h6>
                    @foreach($recentActivityLogs->take(5) as $log)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $log->type ? $log->type->label() : 'Activity' }}</strong>
                                <p class="mb-0">
                                    {{ $log->name ?? $log->description ?? 'Log entry' }}
                                    @if($log->farmer)
                                    • {{ $log->farmer->full_name }}
                                    @endif
                                </p>
                                @if($log->farm)
                                <small class="text-muted">
                                    Farm: {{ $log->farm->name }}
                                </small>
                                @endif
                            </div>
                            <div class="activity-time">
                                {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($recentFarmers->count() == 0 && $recentInspections->count() == 0 && $recentActivityLogs->count() == 0)
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No recent activity to display</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="recent-activity mb-4">
                <h4>Quick Actions</h4>
                <div class="mt-3">
                    <a href="{{ route('farmers.create') }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="fas fa-user-plus"></i> Add New Farmer
                    </a>
                    <a href="{{ route('farms.create') }}" class="btn btn-outline-success w-100 mb-2">
                        <i class="fas fa-farm"></i> Register New Farm
                    </a>
                    <a href="{{ route('inspections.create') }}" class="btn btn-outline-warning w-100 mb-2">
                        <i class="fas fa-clipboard-check"></i> Schedule Inspection
                    </a>
                    <a href="#" class="btn btn-outline-info w-100 mb-2">
                        <i class="fas fa-file-export"></i> Generate Report
                    </a>
                    <a href="{{ route('dashboard.admin.statistics') }}" class="btn btn-outline-purple w-100">
                        <i class="fas fa-chart-pie"></i> View Statistics
                    </a>
                </div>
            </div>
            
            <!-- Certification Overview -->
            <div class="recent-activity">
                <h4>Certification Overview</h4>
                <div class="mt-3">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>
                                <span class="badge badge-organic me-1">●</span>
                                Organic Certified
                            </span>
                            <span>{{ number_format($certificationStats['organic_farmers']) }} ({{ $stats['total_farmers'] > 0 ? round(($certificationStats['organic_farmers'] / $stats['total_farmers']) * 100, 1) : 0 }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['total_farmers'] > 0 ? ($certificationStats['organic_farmers'] / $stats['total_farmers']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>
                                <span class="badge badge-conversion me-1">●</span>
                                In Conversion
                            </span>
                            <span>{{ number_format($certificationStats['in_conversion_farmers']) }} ({{ $stats['total_farmers'] > 0 ? round(($certificationStats['in_conversion_farmers'] / $stats['total_farmers']) * 100, 1) : 0 }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['total_farmers'] > 0 ? ($certificationStats['in_conversion_farmers'] / $stats['total_farmers']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>
                                <span class="badge badge-conventional me-1">●</span>
                                Conventional
                            </span>
                            <span>{{ number_format($certificationStats['conventional_farmers']) }} ({{ $stats['total_farmers'] > 0 ? round(($certificationStats['conventional_farmers'] / $stats['total_farmers']) * 100, 1) : 0 }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-secondary" style="width: {{ $stats['total_farmers'] > 0 ? ($certificationStats['conventional_farmers'] / $stats['total_farmers']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('farmers.index') }}?certification_status=organic" class="btn btn-sm btn-outline-success me-2">
                            <i class="fas fa-leaf"></i> Organic Farmers
                        </a>
                        <a href="{{ route('farmers.index') }}?certification_status=in-conversion" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-hourglass-half"></i> In Conversion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info Row -->
    <div class="row mt-4">
        <!-- Regional Distribution -->
        <div class="col-md-4">
            <div class="recent-activity h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Regional Distribution</h4>
                    <a href="{{ route('dashboard.admin.statistics') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-map"></i> View All
                    </a>
                </div>
                @if($farmersByRegion->count() > 0)
                    @php $maxFarmers = $farmersByRegion->max('farmers_count') ?: 1; @endphp
                    @foreach($farmersByRegion as $region)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $region->name }}</span>
                            <span class="text-muted">{{ number_format($region->farmers_count) }} farmers</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="region-bar" style="width: {{ ($region->farmers_count / $maxFarmers) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-map-marked-alt fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No regional data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Inspections -->
        <div class="col-md-4">
            <div class="recent-activity h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Upcoming Inspections</h4>
                    <a href="{{ route('inspections.index') }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-list"></i> View All
                    </a>
                </div>
                @if($upcomingInspections->count() > 0)
                    @foreach($upcomingInspections as $inspection)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>
                                    @if($inspection->farmer)
                                    {{ $inspection->farmer->full_name }}
                                    @else
                                    Inspection #{{ $inspection->id }}
                                    @endif
                                </strong>
                                <p class="mb-0 small">
                                    <i class="fas fa-calendar text-muted"></i>
                                    {{ $inspection->scheduled_date ? $inspection->scheduled_date->format('M d, Y') : 'TBD' }}
                                </p>
                                @if($inspection->inspector)
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> {{ $inspection->inspector->name }}
                                </small>
                                @endif
                            </div>
                            <span class="badge bg-warning">
                                {{ $inspection->scheduled_date ? $inspection->scheduled_date->diffForHumans() : 'Pending' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-check fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No upcoming inspections</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Trainings -->
        <div class="col-md-4">
            <div class="recent-activity h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Upcoming Trainings</h4>
                    <a href="{{ route('training.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-list"></i> View All
                    </a>
                </div>
                @if($upcomingTrainings->count() > 0)
                    @foreach($upcomingTrainings as $training)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $training->title ?? $training->name ?? 'Training Session' }}</strong>
                                <p class="mb-0 small">
                                    <i class="fas fa-calendar text-muted"></i>
                                    {{ $training->scheduled_date ? $training->scheduled_date->format('M d, Y') : 'TBD' }}
                                </p>
                                @if($training->location)
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> {{ $training->location }}
                                </small>
                                @endif
                            </div>
                            <span class="badge bg-info">
                                {{ $training->scheduled_date ? $training->scheduled_date->diffForHumans() : 'Pending' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chalkboard-teacher fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No upcoming trainings</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Refresh button
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
