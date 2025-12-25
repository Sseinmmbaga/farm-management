@extends('layouts.base')

@section('title', 'Supervisor Dashboard')

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
    .recent-activity {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .activity-item {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    .activity-item:last-child {
        border-bottom: none;
    }
    .officer-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.2s;
        border: 1px solid #eee;
    }
    .officer-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
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
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <h1 class="h3 mb-0">Supervisor Dashboard</h1>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.supervisor.team') }}" class="btn btn-outline-primary">
                <i class="fas fa-users"></i> View Team
            </a>
            <a href="{{ route('dashboard.supervisor.data-review') }}" class="btn btn-primary">
                <i class="fas fa-clipboard-check"></i> Review Data
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['total_extension_officers']) }}</div>
                <div class="stats-label">Extension Officers</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['total_farmers']) }}</div>
                <div class="stats-label">Total Farmers</div>
                <small class="text-success">{{ number_format($stats['active_farmers']) }} active</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['pending_approvals']) }}</div>
                <div class="stats-label">Pending Approvals</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-info">
                <div class="stats-icon text-info">
                    <i class="fas fa-tractor"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['total_farms']) }}</div>
                <div class="stats-label">Total Farms</div>
            </div>
        </div>
    </div>

    <!-- This Week Stats -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-calendar-week text-primary me-2"></i>This Week's Activity</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="week-stat">
                                <div class="number text-primary">{{ $thisWeekStats['new_farmers'] }}</div>
                                <div class="label">New Farmers Registered</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="week-stat">
                                <div class="number text-success">{{ $thisWeekStats['new_farms'] }}</div>
                                <div class="label">New Farms Added</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row mt-4">
        <!-- Team Overview -->
        <div class="col-md-6">
            <div class="recent-activity h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-user-tie text-primary me-2"></i>Extension Officers</h5>
                    <a href="{{ route('dashboard.supervisor.team') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                @if($officersWithStats->count() > 0)
                    @foreach($officersWithStats->take(5) as $officer)
                    <div class="officer-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($officer->name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $officer->name }}</strong>
                                    <p class="mb-0 small text-muted">{{ $officer->email }}</p>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">{{ $officer->farmers_count }} farmers</span>
                                <br>
                                <small class="text-muted">{{ $officer->farms_count }} farms</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No extension officers found</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="col-md-6">
            <div class="recent-activity h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-clock text-warning me-2"></i>Pending Approvals</h5>
                    <a href="{{ route('dashboard.supervisor.data-review') }}?status=pending" class="btn btn-sm btn-outline-warning">Review All</a>
                </div>
                @if($pendingApprovals->count() > 0)
                    @foreach($pendingApprovals as $farmer)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $farmer->full_name }}</strong>
                                <p class="mb-0 small">
                                    <span class="text-muted">Reg:</span> {{ $farmer->registration_number }}
                                    @if($farmer->village)
                                    <span class="ms-2"><i class="fas fa-map-marker-alt"></i> {{ $farmer->village->name }}</span>
                                    @endif
                                </p>
                                @if($farmer->extensionOfficer)
                                <small class="text-muted">By: {{ $farmer->extensionOfficer->name }}</small>
                                @endif
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning text-dark">Pending</span>
                                <br>
                                <small class="text-muted">{{ $farmer->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted">No pending approvals</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Farmers -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="recent-activity">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-user-plus text-success me-2"></i>Recently Registered Farmers</h5>
                    <a href="{{ route('farmers.index') }}" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                @if($recentFarmers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Farmer</th>
                                <th>Reg. Number</th>
                                <th>Village</th>
                                <th>Extension Officer</th>
                                <th>Registered</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentFarmers as $farmer)
                            <tr>
                                <td><strong>{{ $farmer->full_name }}</strong></td>
                                <td>{{ $farmer->registration_number }}</td>
                                <td>{{ $farmer->village->name ?? 'N/A' }}</td>
                                <td>{{ $farmer->extensionOfficer->name ?? 'N/A' }}</td>
                                <td>{{ $farmer->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $farmer->status === 'active' ? 'success' : ($farmer->status === 'pending' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($farmer->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent farmer registrations</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
