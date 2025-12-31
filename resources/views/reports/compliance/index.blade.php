@extends('layouts.base')

@section('title', 'Compliance Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Compliance Report
                    </h1>
                    <p class="text-muted mb-0">Overview of ICS compliance inspections and certifications</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.compliance.inspections') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list-alt me-1"></i> Inspections
                    </a>
                    <a href="{{ route('reports.compliance.findings') }}" class="btn btn-outline-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i> Findings
                    </a>
                    <a href="{{ route('reports.compliance.certifications') }}" class="btn btn-outline-success">
                        <i class="fas fa-certificate me-1"></i> Certifications
                    </a>
                    <a href="{{ route('reports.compliance.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
                </div>
            </div>

            <!-- Inspection Stats Row -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Inspections</h6>
                                    <h2 class="mb-0">{{ number_format($totalInspections) }}</h2>
                                    <small class="text-muted">{{ $completedInspections }} completed</small>
                                </div>
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-clipboard-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Passed Inspections</h6>
                                    <h2 class="mb-0 text-success">{{ number_format($passedInspections) }}</h2>
                                    <small class="text-success">{{ $passRate }}% pass rate</small>
                                </div>
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Failed Inspections</h6>
                                    <h2 class="mb-0 text-danger">{{ number_format($failedInspections) }}</h2>
                                    <small class="text-danger">Need attention</small>
                                </div>
                                <div class="bg-danger text-white rounded-circle p-3">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Pass Rate</h6>
                                    <h2 class="mb-0">{{ $passRate }}%</h2>
                                    <small class="text-muted">Overall compliance</small>
                                </div>
                                <div class="bg-info text-white rounded-circle p-3">
                                    <i class="fas fa-percent fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certification Stats Row -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Certifications</h6>
                                    <h2 class="mb-0">{{ number_format($totalCertifications) }}</h2>
                                </div>
                                <div class="bg-secondary text-white rounded-circle p-3">
                                    <i class="fas fa-certificate fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Active Certifications</h6>
                                    <h2 class="mb-0 text-success">{{ number_format($activeCertifications) }}</h2>
                                </div>
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-award fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">In Conversion</h6>
                                    <h2 class="mb-0 text-warning">{{ number_format($inConversionCertifications) }}</h2>
                                </div>
                                <div class="bg-warning text-white rounded-circle p-3">
                                    <i class="fas fa-sync-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Expiring Soon</h6>
                                    <h2 class="mb-0 text-danger">{{ number_format($expiringSoonCertifications) }}</h2>
                                    <small class="text-danger">Within 30 days</small>
                                </div>
                                <div class="bg-danger text-white rounded-circle p-3">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Findings Stats Row -->
            <div class="row mb-4">
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card border-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Findings</h6>
                                    <h2 class="mb-0">{{ number_format($totalFindings) }}</h2>
                                </div>
                                <div class="bg-secondary text-white rounded-circle p-3">
                                    <i class="fas fa-search fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card border-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Critical Findings</h6>
                                    <h2 class="mb-0 text-danger">{{ number_format($criticalFindings) }}</h2>
                                </div>
                                <div class="bg-danger text-white rounded-circle p-3">
                                    <i class="fas fa-exclamation-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Non-Compliant Items</h6>
                                    <h2 class="mb-0 text-warning">{{ number_format($nonCompliantFindings) }}</h2>
                                </div>
                                <div class="bg-warning text-white rounded-circle p-3">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Inspection Trend Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Monthly Inspection Trend
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="inspectionTrendChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Inspections -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-search me-2"></i>
                                Recent Inspections
                            </h5>
                            <a href="{{ route('reports.compliance.inspections') }}" class="btn btn-sm btn-light">
                                View All
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Farmer</th>
                                            <th>Checklist</th>
                                            <th class="text-center">Result</th>
                                            <th class="text-end">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentInspections as $inspection)
                                            <tr>
                                                <td>{{ $inspection->farmer?->full_name ?? 'N/A' }}</td>
                                                <td>{{ Str::limit($inspection->checklist?->name ?? 'N/A', 25) }}</td>
                                                <td class="text-center">
                                                    @if($inspection->result === 'passed')
                                                        <span class="badge bg-success">Passed</span>
                                                    @elseif($inspection->result === 'failed')
                                                        <span class="badge bg-danger">Failed</span>
                                                    @elseif($inspection->result === 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($inspection->result ?? 'Unknown') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ $inspection->inspection_date?->format('M d, Y') ?? 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                                    <p class="mb-0">No inspections found</p>
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

            <!-- Quick Links -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-link me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="{{ route('reports.compliance.inspections') }}" class="btn btn-outline-primary w-100 py-3">
                                        <i class="fas fa-clipboard-list fa-2x mb-2 d-block"></i>
                                        View All Inspections
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="{{ route('reports.compliance.findings') }}" class="btn btn-outline-warning w-100 py-3">
                                        <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                                        View Findings
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="{{ route('reports.compliance.certifications') }}" class="btn btn-outline-success w-100 py-3">
                                        <i class="fas fa-certificate fa-2x mb-2 d-block"></i>
                                        View Certifications
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="{{ route('reports.compliance.export') }}" class="btn btn-outline-secondary w-100 py-3">
                                        <i class="fas fa-file-csv fa-2x mb-2 d-block"></i>
                                        Export Report
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Inspection Trend Chart
    const inspectionTrendCtx = document.getElementById('inspectionTrendChart').getContext('2d');
    const inspectionTrendData = @json($inspectionTrend);

    new Chart(inspectionTrendCtx, {
        type: 'line',
        data: {
            labels: Object.keys(inspectionTrendData).map(key => {
                const [year, month] = key.split('-');
                const date = new Date(year, month - 1);
                return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
            }),
            datasets: [{
                label: 'Inspections',
                data: Object.values(inspectionTrendData),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
