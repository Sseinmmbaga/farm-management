@extends('layouts.base')

@section('title', 'Compliance Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #6a89cc 0%, #b8e994 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="display-6 mb-2 text-white">
                                <i class="fas fa-chart-bar me-3"></i>Compliance Reports
                            </h1>
                            <p class="lead text-white mb-0">
                                Monitor compliance trends, identify non-conformities, and track corrective actions across all inspections.
                            </p>
                        </div>
                        <div class="col-lg-4 text-end">
                            <div class="btn-group">
                                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-download me-2"></i> Export
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('ics-reports.compliance') }}?export=pdf"><i class="fas fa-file-pdf me-2"></i> PDF Report</a></li>
                                    <li><a class="dropdown-item" href="{{ route('ics-reports.compliance') }}?export=excel"><i class="fas fa-file-excel me-2"></i> Excel Data</a></li>
                                    <li><a class="dropdown-item" href="{{ route('ics-reports.compliance') }}?export=csv"><i class="fas fa-file-csv me-2"></i> CSV Export</a></li>
                                </ul>
                                <a href="{{ route('ics-reports.summary') }}" class="btn btn-outline-light">
                                    <i class="fas fa-chart-pie me-2"></i> Summary Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i> Filter Reports
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('ics-reports.compliance') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="period" class="form-label">Reporting Period</label>
                                <select class="form-select" id="period" name="period">
                                    <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                    <option value="this_quarter" {{ request('period') == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                                    <option value="last_quarter" {{ request('period') == 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
                                    <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>This Year</option>
                                    <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="from_date" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="to_date" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="checklist" class="form-label">Checklist</label>
                                <select class="form-select" id="checklist" name="checklist">
                                    <option value="">All Checklists</option>
                                    <!-- Populate with real checklists -->
                                    <option value="1" {{ request('checklist') == '1' ? 'selected' : '' }}>Internal Control System</option>
                                    <option value="2" {{ request('checklist') == '2' ? 'selected' : '' }}>Social & Environmental</option>
                                    <option value="3" {{ request('checklist') == '3' ? 'selected' : '' }}>Quality Management</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-9">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="include_findings" name="include_findings" value="1" {{ request('include_findings') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="include_findings">Include Findings Details</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="include_actions" name="include_actions" value="1" {{ request('include_actions') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="include_actions">Include Corrective Actions</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="group_by_farmer" name="group_by_farmer" value="1" {{ request('group_by_farmer') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="group_by_farmer">Group by Farmer</label>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i> Apply Filters
                                </button>
                                <a href="{{ route('ics-reports.compliance') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Total Inspections</h6>
                            <h3 class="fw-bold">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-success fw-semibold">
                            <i class="fas fa-arrow-up me-1"></i> {{ $stats['compliance_rate'] ?? 0 }}%
                        </span>
                        <span class="text-muted">Compliance Rate</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Compliant Items</h6>
                            <h3 class="fw-bold">{{ $stats['compliant'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-success fw-semibold">
                            <i class="fas fa-arrow-up me-1"></i> {{ $stats['compliant_percentage'] ?? 0 }}%
                        </span>
                        <span class="text-muted">of total items</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Non-Compliant Items</h6>
                            <h3 class="fw-bold">{{ $stats['non_compliant'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-danger fw-semibold">
                            <i class="fas fa-arrow-up me-1"></i> {{ $stats['non_compliant_percentage'] ?? 0 }}%
                        </span>
                        <span class="text-muted">of total items</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Average Score</h6>
                            <h3 class="fw-bold">{{ $stats['average_score'] ?? 0 }}/100</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line fa-2x text-info"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-info fw-semibold">
                            <i class="fas fa-chart-bar me-1"></i> Score Trend
                        </span>
                        <span class="text-muted">over period</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Visualizations -->
    <div class="row mb-4">
        <!-- Compliance Trend Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i> Compliance Trend (Last 12 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="complianceTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <!-- Compliance by Checklist -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i> Compliance by Checklist
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="checklistComplianceChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Data -->
    <div class="row">
        <!-- Top Non-Compliant Items -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i> Top Non-Compliant Items
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Checklist Item</th>
                                    <th>Non-Compliance Count</th>
                                    <th>Severity</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_non_compliant_items ?? [] as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ $item['count'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item['severity'] == 'critical' ? 'danger' : ($item['severity'] == 'major' ? 'warning' : 'info') }}">
                                            {{ ucfirst($item['severity']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item['trend'] == 'up')
                                            <i class="fas fa-arrow-up text-danger"></i> Increasing
                                        @elseif($item['trend'] == 'down')
                                            <i class="fas fa-arrow-down text-success"></i> Decreasing
                                        @else
                                            <i class="fas fa-minus text-muted"></i> Stable
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No non-compliant items found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Farmer Compliance Ranking -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i> Farmer Compliance Ranking
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Farmer</th>
                                    <th>Inspections</th>
                                    <th>Compliance Score</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($farmer_ranking ?? [] as $index => $farmer)
                                <tr>
                                    <td>
                                        @if($index == 0)
                                            <span class="badge bg-warning">1st</span>
                                        @elseif($index == 1)
                                            <span class="badge bg-secondary">2nd</span>
                                        @elseif($index == 2)
                                            <span class="badge bg-danger">3rd</span>
                                        @else
                                            <span class="text-muted">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $farmer['name'] }}</strong><br>
                                        <small class="text-muted">{{ $farmer['farm_name'] ?? 'No farm' }}</small>
                                    </td>
                                    <td>{{ $farmer['inspections_count'] }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $farmer['score'] >= 80 ? 'success' : ($farmer['score'] >= 60 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $farmer['score'] }}%;"
                                                 aria-valuenow="{{ $farmer['score'] }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $farmer['score'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($farmer['score'] >= 80)
                                            <span class="badge bg-success">Compliant</span>
                                        @elseif($farmer['score'] >= 60)
                                            <span class="badge bg-warning">Partially Compliant</span>
                                        @else
                                            <span class="badge bg-danger">Non-Compliant</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No farmer data available.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Reports -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i> Generated Reports
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                    <h5>Monthly Compliance Report</h5>
                                    <p class="text-muted">Detailed monthly analysis of compliance metrics</p>
                                    <a href="#" class="btn btn-outline-danger">
                                        <i class="fas fa-download me-2"></i> Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                                    <h5>Findings & Actions Report</h5>
                                    <p class="text-muted">Export all findings and corrective actions</p>
                                    <a href="#" class="btn btn-outline-success">
                                        <i class="fas fa-download me-2"></i> Download Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                                    <h5>Performance Dashboard</h5>
                                    <p class="text-muted">Interactive dashboard with live data</p>
                                    <a href="{{ route('ics-reports.summary') }}" class="btn btn-outline-warning">
                                        <i class="fas fa-external-link-alt me-2"></i> Open Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Compliance Trend Chart
        const trendCtx = document.getElementById('complianceTrendChart').getContext('2d');
        const trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Compliance Rate (%)',
                    data: [85, 88, 82, 90, 87, 92, 89, 91, 88, 90, 93, 95],
                    borderColor: '#6a89cc',
                    backgroundColor: 'rgba(106, 137, 204, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Non-Compliance Rate (%)',
                    data: [15, 12, 18, 10, 13, 8, 11, 9, 12, 10, 7, 5],
                    borderColor: '#f78fb3',
                    backgroundColor: 'rgba(247, 143, 179, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Percentage (%)'
                        }
                    }
                }
            }
        });

        // Checklist Compliance Chart
        const checklistCtx = document.getElementById('checklistComplianceChart').getContext('2d');
        const checklistChart = new Chart(checklistCtx, {
            type: 'doughnut',
            data: {
                labels: ['Internal Control', 'Social & Env', 'Quality', 'Safety', 'Documentation'],
                datasets: [{
                    data: [85, 72, 90, 68, 95],
                    backgroundColor: [
                        '#6a89cc',
                        '#b8e994',
                        '#f78fb3',
                        '#f6b93b',
                        '#78e08f'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed}% compliant`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection