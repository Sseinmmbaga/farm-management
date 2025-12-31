@extends('layouts.base')

@section('title', 'ICS Summary Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="display-6 mb-2 text-white">
                                <i class="fas fa-chart-pie me-3"></i>ICS Summary Report
                            </h1>
                            <p class="lead text-white mb-0">
                                Overview of all inspection activities, compliance status, and key metrics.
                            </p>
                        </div>
                        <div class="col-lg-4 text-end">
                            <a href="{{ route('ics-reports.compliance') }}" class="btn btn-light">
                                <i class="fas fa-chart-bar me-2"></i> Compliance Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Total Inspections</h6>
                            <h2 class="fw-bold mb-0">{{ $stats['total'] }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Scheduled</h6>
                            <h2 class="fw-bold mb-0">{{ $stats['scheduled'] }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-alt fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">In Progress</h6>
                            <h2 class="fw-bold mb-0">{{ $stats['in_progress'] }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-spinner fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Completed</h6>
                            <h2 class="fw-bold mb-0">{{ $stats['completed'] }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Cancelled</h6>
                            <h2 class="fw-bold mb-0">{{ $stats['cancelled'] }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Inspection Status Overview</h6>
                    <div class="progress" style="height: 30px;">
                        @php
                            $total = $stats['total'] > 0 ? $stats['total'] : 1;
                            $scheduledPct = ($stats['scheduled'] / $total) * 100;
                            $inProgressPct = ($stats['in_progress'] / $total) * 100;
                            $completedPct = ($stats['completed'] / $total) * 100;
                            $cancelledPct = ($stats['cancelled'] / $total) * 100;
                        @endphp
                        <div class="progress-bar bg-warning" style="width: {{ $scheduledPct }}%" title="Scheduled">
                            @if($scheduledPct > 10) {{ $stats['scheduled'] }} @endif
                        </div>
                        <div class="progress-bar bg-info" style="width: {{ $inProgressPct }}%" title="In Progress">
                            @if($inProgressPct > 10) {{ $stats['in_progress'] }} @endif
                        </div>
                        <div class="progress-bar bg-success" style="width: {{ $completedPct }}%" title="Completed">
                            @if($completedPct > 10) {{ $stats['completed'] }} @endif
                        </div>
                        <div class="progress-bar bg-danger" style="width: {{ $cancelledPct }}%" title="Cancelled">
                            @if($cancelledPct > 10) {{ $stats['cancelled'] }} @endif
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small text-muted">
                        <span><i class="fas fa-square text-warning me-1"></i> Scheduled</span>
                        <span><i class="fas fa-square text-info me-1"></i> In Progress</span>
                        <span><i class="fas fa-square text-success me-1"></i> Completed</span>
                        <span><i class="fas fa-square text-danger me-1"></i> Cancelled</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Inspections -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i> Recent Inspections
                        </h5>
                        <a href="{{ route('inspections.index') }}" class="btn btn-outline-primary btn-sm">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Inspection #</th>
                                    <th>Farmer</th>
                                    <th>Inspector</th>
                                    <th>Scheduled Date</th>
                                    <th>Status</th>
                                    <th>Result</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentInspections as $inspection)
                                <tr>
                                    <td>
                                        <strong>{{ $inspection->inspection_number ?? 'INS-' . $inspection->id }}</strong>
                                    </td>
                                    <td>
                                        @if($inspection->farmer)
                                            {{ $inspection->farmer->first_name }} {{ $inspection->farmer->last_name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($inspection->inspector)
                                            {{ $inspection->inspector->name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($inspection->scheduled_date)
                                            {{ $inspection->scheduled_date->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">Not scheduled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'scheduled' => 'warning',
                                                'in_progress' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                            $color = $statusColors[$inspection->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">
                                            {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($inspection->result)
                                            @php
                                                $resultColors = [
                                                    'pending' => 'secondary',
                                                    'passed' => 'success',
                                                    'failed' => 'danger',
                                                    'conditional' => 'warning',
                                                ];
                                                $resultColor = $resultColors[$inspection->result] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $resultColor }}">
                                                {{ ucfirst($inspection->result) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('inspections.show', $inspection) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                            <h5>No inspections found</h5>
                                            <p>Start by scheduling your first inspection</p>
                                            <a href="{{ route('inspections.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus-circle me-1"></i> Schedule Inspection
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('inspections.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus-circle me-2"></i> New Inspection
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('ics-reports.compliance') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-chart-bar me-2"></i> Compliance Report
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('ics-reports.findings') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-exclamation-triangle me-2"></i> Findings Report
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('export.index') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-download me-2"></i> Export Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
