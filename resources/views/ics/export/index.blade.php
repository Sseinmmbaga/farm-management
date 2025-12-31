@extends('layouts.base')

@section('title', 'Export Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #0c2461 0%, #4a69bd 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="display-6 mb-2 text-white">
                                <i class="fas fa-file-export me-3"></i>Export Reports
                            </h1>
                            <p class="lead text-white mb-0">
                                Generate and download comprehensive reports in PDF, Excel, or CSV format.
                            </p>
                        </div>
                        <div class="col-lg-4 text-end">
                            <a href="{{ route('ics-reports.summary') }}" class="btn btn-light">
                                <i class="fas fa-chart-pie me-2"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="row">
        <!-- Inspections Export -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-primary h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-list me-2"></i> Inspections Export
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Export inspection data including checklist responses, findings, and compliance scores.
                    </p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Data Included:</span>
                            <span class="badge bg-primary">Inspection Details</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Format:</span>
                            <div>
                                <span class="badge bg-danger">PDF</span>
                                <span class="badge bg-success">Excel</span>
                                <span class="badge bg-info">CSV</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Maximum Records:</span>
                            <span>10,000</span>
                        </li>
                    </ul>
                    <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#exportInspectionsModal">
                        <i class="fas fa-download me-2"></i> Configure Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Findings Export -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i> Findings Export
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Export non-conformities and findings with severity levels, corrective actions, and status.
                    </p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Data Included:</span>
                            <span class="badge bg-warning">Findings Details</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Format:</span>
                            <div>
                                <span class="badge bg-danger">PDF</span>
                                <span class="badge bg-success">Excel</span>
                                <span class="badge bg-info">CSV</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Maximum Records:</span>
                            <span>5,000</span>
                        </li>
                    </ul>
                    <button class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#exportFindingsModal">
                        <i class="fas fa-download me-2"></i> Configure Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Corrective Actions Export -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-success h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i> Corrective Actions Export
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Export corrective actions with status, responsible persons, due dates, and verification details.
                    </p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Data Included:</span>
                            <span class="badge bg-success">Action Details</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Format:</span>
                            <div>
                                <span class="badge bg-danger">PDF</span>
                                <span class="badge bg-success">Excel</span>
                                <span class="badge bg-info">CSV</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Maximum Records:</span>
                            <span>3,000</span>
                        </li>
                    </ul>
                    <button class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#exportCorrectiveActionsModal">
                        <i class="fas fa-download me-2"></i> Configure Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Certifications Export -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-info h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-certificate me-2"></i> Certifications Export
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Export farmer certifications with status, expiry dates, inspection history, and compliance standards.
                    </p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Data Included:</span>
                            <span class="badge bg-info">Certification Details</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Format:</span>
                            <div>
                                <span class="badge bg-danger">PDF</span>
                                <span class="badge bg-success">Excel</span>
                                <span class="badge bg-info">CSV</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Maximum Records:</span>
                            <span>2,000</span>
                        </li>
                    </ul>
                    <button class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#exportCertificationsModal">
                        <i class="fas fa-download me-2"></i> Configure Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Compliance Reports Export -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-danger h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i> Compliance Reports Export
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Export comprehensive compliance reports with charts, statistics, and trend analysis.
                    </p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Data Included:</span>
                            <span class="badge bg-danger">Charts & Stats</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Format:</span>
                            <div>
                                <span class="badge bg-danger">PDF</span>
                                <span class="badge bg-success">Excel</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Maximum Records:</span>
                            <span>All Data</span>
                        </li>
                    </ul>
                    <a href="{{ route('ics-reports.compliance') }}?export=pdf" class="btn btn-outline-danger w-100">
                        <i class="fas fa-download me-2"></i> Generate PDF Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Bulk Data Export -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-secondary h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-database me-2"></i> Bulk Data Export
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Export all ICS data in a single archive for backup, migration, or external analysis.
                    </p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Data Included:</span>
                            <span class="badge bg-secondary">All Modules</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Format:</span>
                            <div>
                                <span class="badge bg-success">ZIP Archive</span>
                                <span class="badge bg-info">JSON</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Maximum Records:</span>
                            <span>Full Database</span>
                        </li>
                    </ul>
                    <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#bulkExportModal">
                        <i class="fas fa-download me-2"></i> Configure Bulk Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Exports -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i> Recent Exports
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Export Date</th>
                                    <th>Report Type</th>
                                    <th>Format</th>
                                    <th>Records</th>
                                    <th>File Size</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Dec 30, 2025 14:30</td>
                                    <td>Inspections Report</td>
                                    <td><span class="badge bg-success">Excel</span></td>
                                    <td>1,245</td>
                                    <td>2.4 MB</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Dec 28, 2025 10:15</td>
                                    <td>Findings Report</td>
                                    <td><span class="badge bg-danger">PDF</span></td>
                                    <td>567</td>
                                    <td>1.8 MB</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Dec 25, 2025 16:45</td>
                                    <td>Compliance Report</td>
                                    <td><span class="badge bg-info">CSV</span></td>
                                    <td>3,890</td>
                                    <td>4.2 MB</td>
                                    <td><span class="badge bg-warning">Expired</span></td>
                                    <td>
                                        <span class="text-muted">Link expired</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for each export type -->
@include('ics.export.modals.inspections')
@include('ics.export.modals.findings')
@include('ics.export.modals.corrective-actions')
@include('ics.export.modals.certifications')
@include('ics.export.modals.bulk')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple form validation for export forms
        const exportForms = document.querySelectorAll('.export-form');
        exportForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const format = this.querySelector('select[name="format"]').value;
                if (!format) {
                    e.preventDefault();
                    alert('Please select an export format.');
                    return false;
                }
            });
        });
    });
</script>
@endpush
@endsection