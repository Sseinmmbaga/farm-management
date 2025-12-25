@extends('layouts.base')

@section('title', 'ICS Inspector Dashboard')

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
    .stats-card:hover { transform: translateY(-5px); }
    .stats-icon { font-size: 2.5rem; margin-bottom: 15px; }
    .stats-number { font-size: 2rem; font-weight: bold; margin-bottom: 5px; }
    .stats-label { color: #6c757d; font-size: 0.9rem; }
    .card-primary { border-left: 4px solid #3498db; }
    .card-success { border-left: 4px solid #2ecc71; }
    .card-warning { border-left: 4px solid #f39c12; }
    .card-danger { border-left: 4px solid #e74c3c; }
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
    .activity-item:last-child { border-bottom: none; }
    .severity-critical { background-color: #e74c3c; }
    .severity-major { background-color: #f39c12; }
    .severity-minor { background-color: #3498db; }
    .week-stat {
        text-align: center;
        padding: 15px;
        border-radius: 8px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    .week-stat .number { font-size: 1.8rem; font-weight: bold; color: #2c3e50; }
    .week-stat .label { font-size: 0.85rem; color: #6c757d; }
    .compliance-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: bold;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <h1 class="h3 mb-0">ICS Inspector Dashboard</h1>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.ics.inspections') }}" class="btn btn-outline-warning">
                <i class="fas fa-clipboard-check"></i> Inspections
            </a>
            <a href="{{ route('dashboard.ics.findings') }}" class="btn btn-outline-danger">
                <i class="fas fa-exclamation-triangle"></i> Findings
            </a>
            <a href="{{ route('inspections.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Inspection
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['pending_inspections']) }}</div>
                <div class="stats-label">Pending Inspections</div>
                @if($overdueInspections > 0)
                <small class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $overdueInspections }} overdue</small>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['completed_this_month']) }}</div>
                <div class="stats-label">Completed This Month</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-danger">
                <div class="stats-icon text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-number">{{ number_format($stats['total_findings']) }}</div>
                <div class="stats-label">Open Findings</div>
                @if($stats['critical_findings'] > 0)
                <small class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ $stats['critical_findings'] }} critical</small>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stats-number">{{ $stats['compliance_rate'] }}%</div>
                <div class="stats-label">Compliance Rate</div>
            </div>
        </div>
    </div>

    <!-- This Week Stats -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-calendar-week text-warning me-2"></i>This Week's Activity</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="week-stat">
                                <div class="number text-success">{{ $thisWeekStats['inspections_completed'] }}</div>
                                <div class="label">Inspections Completed</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="week-stat">
                                <div class="number text-primary">{{ $thisWeekStats['findings_resolved'] }}</div>
                                <div class="label">Findings Resolved</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row mt-4">
        <!-- Upcoming Inspections -->
        <div class="col-md-6">
            <div class="recent-activity h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt text-warning me-2"></i>Upcoming Inspections</h5>
                    <a href="{{ route('dashboard.ics.inspections') }}" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                @if($upcomingInspections->count() > 0)
                    @foreach($upcomingInspections as $inspection)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $inspection->farmer->full_name ?? 'Unknown Farmer' }}</strong>
                                <p class="mb-0 small">
                                    @if($inspection->farm)
                                    <i class="fas fa-tractor text-muted"></i> {{ $inspection->farm->name }}
                                    @endif
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> {{ $inspection->scheduled_date->format('M d, Y') }}
                                </small>
                            </div>
                            <span class="badge bg-warning text-dark">
                                {{ $inspection->scheduled_date->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No upcoming inspections</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Open Findings -->
        <div class="col-md-6">
            <div class="recent-activity h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Open Findings</h5>
                    <a href="{{ route('dashboard.ics.findings') }}" class="btn btn-sm btn-outline-danger">View All</a>
                </div>
                @if($recentFindings->count() > 0)
                    @foreach($recentFindings as $finding)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge severity-{{ $finding->severity }} me-2">{{ ucfirst($finding->severity) }}</span>
                                <strong>{{ Str::limit($finding->description, 40) }}</strong>
                                <p class="mb-0 small text-muted">
                                    @if($finding->inspection && $finding->inspection->farmer)
                                    Farmer: {{ $finding->inspection->farmer->full_name }}
                                    @endif
                                </p>
                            </div>
                            <small class="text-muted">{{ $finding->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted">No open findings</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Findings by Severity -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="recent-activity">
                <h5 class="mb-3"><i class="fas fa-chart-pie text-primary me-2"></i>Findings by Severity</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <div class="h2 text-danger mb-1">{{ $findingsBySeverity['critical'] }}</div>
                            <span class="badge bg-danger">Critical</span>
                            <p class="text-muted small mb-0 mt-2">Immediate action required</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <div class="h2 text-warning mb-1">{{ $findingsBySeverity['major'] }}</div>
                            <span class="badge bg-warning text-dark">Major</span>
                            <p class="text-muted small mb-0 mt-2">Action within 30 days</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <div class="h2 text-info mb-1">{{ $findingsBySeverity['minor'] }}</div>
                            <span class="badge bg-info">Minor</span>
                            <p class="text-muted small mb-0 mt-2">Action within 90 days</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
