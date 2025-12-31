@extends('layouts.base')

@section('title', 'Findings Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i> Findings Report
                        </h4>
                        <div>
                            <button class="btn btn-light btn-sm" onclick="window.print()">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                            <a href="{{ route('findings.index') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-list me-1"></i> Back to Findings
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Report Header -->
                <div class="card-body border-bottom">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Non-Conformities Analysis Report</h5>
                            <p class="text-muted mb-0">
                                Generated on {{ now()->format('F d, Y') }} • 
                                Covers findings from {{ now()->subMonths(3)->format('M d, Y') }} to {{ now()->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-success fs-6 px-3 py-2">
                                <i class="fas fa-file-alt me-1"></i> Official Report
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Executive Summary -->
                <div class="card-body">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-chart-pie me-2"></i> Executive Summary
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h1 class="display-5 text-primary">{{ $stats['total'] }}</h1>
                                    <p class="text-muted mb-0">Total Findings</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <h1 class="display-5 text-danger">{{ $stats['open'] }}</h1>
                                    <p class="text-muted mb-0">Open Findings</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h1 class="display-5 text-warning">{{ $stats['in_progress'] }}</h1>
                                    <p class="text-muted mb-0">In Progress</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h1 class="display-5 text-success">{{ $stats['resolved'] }}</h1>
                                    <p class="text-muted mb-0">Resolved</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Severity Distribution -->
                <div class="card-body border-top">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i> Severity Distribution
                    </h5>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="table-light">
                                            <th>Severity</th>
                                            <th>Count</th>
                                            <th>Percentage</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span class="badge bg-danger">Critical</span>
                                            </td>
                                            <td>{{ $stats['critical'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['critical'] / $stats['total']) * 100, 1) : 0 }}%</td>
                                            <td>
                                                @php
                                                    $criticalOpen = $recentFindings->where('severity', 'critical')->where('status', 'open')->count();
                                                @endphp
                                                {{ $criticalOpen }} open
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="badge bg-warning">Major</span>
                                            </td>
                                            <td>{{ $stats['major'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['major'] / $stats['total']) * 100, 1) : 0 }}%</td>
                                            <td>Mostly in progress</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">Minor</span>
                                            </td>
                                            <td>{{ $stats['minor'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['minor'] / $stats['total']) * 100, 1) : 0 }}%</td>
                                            <td>Mostly resolved</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">Observation</span>
                                            </td>
                                            <td>{{ $stats['observation'] }}</td>
                                            <td>{{ $stats['total'] > 0 ? round(($stats['observation'] / $stats['total']) * 100, 1) : 0 }}%</td>
                                            <td>Informational only</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Key Insights</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <strong>{{ $stats['resolved'] }}</strong> findings resolved
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-clock text-warning me-2"></i>
                                            <strong>{{ $stats['in_progress'] }}</strong> findings in progress
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-exclamation-circle text-danger me-2"></i>
                                            <strong>{{ $stats['open'] }}</strong> findings require attention
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-calendar-alt text-info me-2"></i>
                                            Average resolution time: 14 days
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Findings -->
                <div class="card-body border-top">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-history me-2"></i> Recent Findings (Last 10)
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Finding #</th>
                                    <th>Inspection</th>
                                    <th>Farmer</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Reported</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentFindings as $finding)
                                    <tr>
                                        <td>
                                            <a href="{{ route('findings.show', $finding) }}" class="text-decoration-none">
                                                <strong>{{ $finding->finding_number }}</strong>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('inspections.show', $finding->inspection) }}" class="text-decoration-none">
                                                {{ $finding->inspection->inspection_number }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $finding->inspection->farmer->first_name }} {{ $finding->inspection->farmer->last_name }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $finding->severity == 'critical' ? 'danger' : ($finding->severity == 'major' ? 'warning' : 'info') }}">
                                                {{ ucfirst($finding->severity) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $finding->status == 'open' ? 'danger' : ($finding->status == 'in_progress' ? 'warning' : 'success') }}">
                                                {{ ucfirst($finding->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $finding->created_at->format('M d, Y') }}
                                        </td>
                                        <td>
                                            @if($finding->due_date)
                                                {{ $finding->due_date->format('M d, Y') }}
                                                @if($finding->due_date->isPast())
                                                    <br>
                                                    <small class="text-danger">Overdue</small>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-3">
                                            No findings found in the selected period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="card-body border-top bg-light">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-lightbulb me-2"></i> Recommendations & Next Steps
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fas fa-tasks me-2"></i> Immediate Actions
                                    </h6>
                                    <ul>
                                        <li>Address all critical findings within 7 days</li>
                                        <li>Review overdue findings and assign resources</li>
                                        <li>Schedule follow-up inspections for major non-conformities</li>
                                        <li>Update corrective action plans for in-progress findings</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-chart-line me-2"></i> Strategic Improvements
                                    </h6>
                                    <ul>
                                        <li>Implement preventive measures for recurring minor findings</li>
                                        <li>Enhance inspector training on common non-conformities</li>
                                        <li>Improve documentation requirements for evidence collection</li>
                                        <li>Automate finding tracking and notification system</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Footer -->
                <div class="card-footer text-muted">
                    <div class="row">
                        <div class="col-md-6">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                This report is generated automatically by the ICS Compliance System.
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small>
                                Report ID: FINDINGS-{{ now()->format('Ymd-His') }} | 
                                Generated by: {{ Auth::user()->name ?? 'System' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
@push('styles')
<style>
    @media print {
        .card-header, .btn, .dropdown, .nav-tabs {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-body, .card-footer {
            padding: 0 !important;
            border: none !important;
        }
        .table th, .table td {
            border: 1px solid #dee2e6 !important;
        }
    }
</style>
@endpush
@endsection