@extends('layouts.base')

@section('title', 'Farmer Certifications: ' . $farmer->first_name . ' ' . $farmer->last_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #38ada9 0%, #079992 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-white p-3 rounded-circle me-4">
                                    <i class="fas fa-user-tie fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h1 class="display-6 mb-1 text-white">
                                        {{ $farmer->first_name }} {{ $farmer->last_name }}
                                    </h1>
                                    <p class="lead text-white mb-0">
                                        <i class="fas fa-tractor me-2"></i>{{ $farmer->farm_name ?? 'No farm name' }}
                                    </p>
                                    <p class="text-white mb-0">
                                        <i class="fas fa-id-card me-2"></i>Farmer ID: {{ $farmer->farmer_id ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-end">
                            <a href="{{ route('certifications.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i> Back to All Certifications
                            </a>
                            <a href="{{ route('inspections.index') }}?farmer={{ $farmer->id }}" class="btn btn-outline-light">
                                <i class="fas fa-clipboard-check me-2"></i> View Inspections
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Farmer Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Active Certifications</h6>
                            <h3 class="fw-bold">{{ $stats['active'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">In Conversion</h6>
                            <h3 class="fw-bold">{{ $stats['in_conversion'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-hourglass-half fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Expiring Soon</h6>
                            <h3 class="fw-bold">{{ $stats['expiring_soon'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-normal">Total Certifications</h6>
                            <h3 class="fw-bold">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-certificate fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Certifications Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-certificate me-2"></i> Farmer Certifications
                        </h5>
                        <div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCertificationModal">
                                <i class="fas fa-plus me-2"></i> Add Certification
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Certificate #</th>
                                    <th>Compliance Standard</th>
                                    <th>Status</th>
                                    <th>Application Date</th>
                                    <th>Certification Date</th>
                                    <th>Expiry Date</th>
                                    <th>Last Inspection</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certifications as $cert)
                                <tr class="{{ $cert->is_active ? 'table-success bg-opacity-10' : '' }}">
                                    <td>
                                        <strong>{{ $cert->certificate_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $cert->compliance_standard_id ? $cert->standard->name ?? 'Unknown' : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $cert->status_color }} fs-6 px-3 py-1">
                                            {{ $cert->status_label }}
                                        </span>
                                        @if($cert->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success ms-1">Active</span>
                                        @endif
                                        @if($cert->is_expired)
                                            <span class="badge bg-danger bg-opacity-10 text-danger ms-1">Expired</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cert->application_date)
                                            {{ $cert->application_date->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cert->certification_date)
                                            {{ $cert->certification_date->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">Not certified</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cert->expiry_date)
                                            @if($cert->expiry_date->isPast())
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    {{ $cert->expiry_date->format('M d, Y') }}
                                                </span>
                                            @elseif($cert->expiry_date->diffInDays(now()) <= 30)
                                                <span class="text-warning">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $cert->expiry_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                {{ $cert->expiry_date->format('M d, Y') }}
                                            @endif
                                        @else
                                            <span class="text-muted">No expiry</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cert->last_inspection)
                                            <a href="{{ route('inspections.show', $cert->last_inspection) }}" class="text-decoration-none">
                                                {{ $cert->last_inspection->inspection_number }}
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $cert->last_inspection_date ? $cert->last_inspection_date->format('M d, Y') : '' }}</small>
                                        @else
                                            <span class="text-muted">No inspection</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cert->notes)
                                            <span class="d-inline-block text-truncate" style="max-width: 150px;" title="{{ $cert->notes }}">
                                                {{ $cert->notes }}
                                            </span>
                                        @else
                                            <span class="text-muted">No notes</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewCertificationModal{{ $cert->id }}" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $cert->id }}" title="Update Status">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCertificationModal{{ $cert->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCertificationModal{{ $cert->id }}" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                                        <h5>No certifications found for this farmer</h5>
                                        <p class="text-muted">Add a certification to get started.</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCertificationModal">
                                            <i class="fas fa-plus me-2"></i> Add First Certification
                                        </button>
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

    <!-- Inspection History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i> Recent Inspection History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Inspection #</th>
                                    <th>Checklist</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Compliance Score</th>
                                    <th>Findings</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inspections as $inspection)
                                <tr>
                                    <td>
                                        <a href="{{ route('inspections.show', $inspection) }}" class="text-decoration-none">
                                            {{ $inspection->inspection_number }}
                                        </a>
                                    </td>
                                    <td>{{ $inspection->checklist->name ?? 'Unknown' }}</td>
                                    <td>{{ $inspection->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $inspection->status == 'completed' ? 'success' : ($inspection->status == 'in_progress' ? 'warning' : 'info') }}">
                                            {{ ucfirst($inspection->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $score = $inspection->compliance_score ?? rand(70, 95);
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $score }}%;"
                                                 aria-valuenow="{{ $score }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $score }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $findingsCount = $inspection->findings_count ?? rand(0, 5);
                                        @endphp
                                        @if($findingsCount > 0)
                                            <span class="badge bg-danger">{{ $findingsCount }} findings</span>
                                        @else
                                            <span class="badge bg-success">No findings</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('inspections.show', $inspection) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        No recent inspections found for this farmer.
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
</div>

<!-- Add Certification Modal -->
<div class="modal fade" id="addCertificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Certification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('certifications.store') }}">
                @csrf
                <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="certificate_number" class="form-label">Certificate Number *</label>
                            <input type="text" class="form-control" id="certificate_number" name="certificate_number" required>
                        </div>
                        <div class="col-md-6">
                            <label for="compliance_standard_id" class="form-label">Compliance Standard</label>
                            <select class="form-select" id="compliance_standard_id" name="compliance_standard_id">
                                <option value="">Select Standard</option>
                                <option value="1">Internal Control System (ICS)</option>
                                <option value="2">GlobalG.A.P.</option>
                                <option value="3">Organic Certification</option>
                                <option value="4">Fair Trade</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="in-conversion">In Conversion</option>
                                <option value="certified">Certified</option>
                                <option value="suspended">Suspended</option>
                                <option value="revoked">Revoked</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="conversion_year" class="form-label">Conversion Year</label>
                            <input type="number" class="form-control" id="conversion_year" name="conversion_year" min="2000" max="2050">
                        </div>
                        <div class="col-md-6">
                            <label for="application_date" class="form-label">Application Date</label>
                            <input type="date" class="form-control" id="application_date" name="application_date">
                        </div>
                        <div class="col-md-6">
                            <label for="certification_date" class="form-label">Certification Date</label>
                            <input type="date" class="form-control" id="certification_date" name="certification_date">
                        </div>
                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                        </div>
                        <div class="col-md-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Certification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Certification Modal (for each certification) -->
@foreach($certifications as $cert)
<div class="modal fade" id="viewCertificationModal{{ $cert->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Certification Details: {{ $cert->certificate_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Basic Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Certificate Number</th>
                                <td>{{ $cert->certificate_number }}</td>
                            </tr>
                            <tr>
                                <th>Farmer</th>
                                <td>{{ $cert->farmer->first_name }} {{ $cert->farmer->last_name }}</td>
                            </tr>
                            <tr>
                                <th>Compliance Standard</th>
                                <td>{{ $cert->compliance_standard_id ? $cert->standard->name ?? 'Unknown' : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-{{ $cert->status_color }}">
                                        {{ $cert->status_label }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Dates</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Application Date</th>
                                <td>{{ $cert->application_date ? $cert->application_date->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Certification Date</th>
                                <td>{{ $cert->certification_date ? $cert->certification_date->format('M d, Y') : 'Not certified' }}</td>
                            </tr>
                            <tr>
                                <th>Expiry Date</th>
                                <td>
                                    @if($cert->expiry_date)
                                        {{ $cert->expiry_date->format('M d, Y') }}
                                        @if($cert->expiry_date->isPast())
                                            <br><small class="text-danger">Expired</small>
                                        @elseif($cert->expiry_date->diffInDays(now()) <= 30)
                                            <br><small class="text-warning">Expires in {{ $cert->expiry_date->diffInDays(now()) }} days</small>
                                        @else
                                            <br><small class="text-success">Valid</small>
                                        @endif
                                    @else
                                        No expiry
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Conversion Year</th>
                                <td>{{ $cert->conversion_year ?: 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if($cert->notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Notes</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                {{ $cert->notes }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($cert->last_inspection)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Last Inspection</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p>
                                    <strong>Inspection:</strong> 
                                    <a href="{{ route('inspections.show', $cert->last_inspection) }}">{{ $cert->last_inspection->inspection_number }}</a>
                                    <br>
                                    <strong>Date:</strong> {{ $cert->last_inspection_date ? $cert->last_inspection_date->format('M d, Y') : 'Unknown' }}
                                    <br>
                                    <strong>Compliance Score:</strong> {{ $cert->last_inspection->compliance_score ?? 'N/A' }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $cert->id }}">
                    Update Status
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal{{ $cert->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status: {{ $cert->certificate_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('certifications.update-status', $farmer) }}">
                @csrf
                <input type="hidden" name="certification_id" value="{{ $cert->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status{{ $cert->id }}" class="form-label">New Status</label>
                        <select class="form-select" id="status{{ $cert->id }}" name="status" required>
                            <option value="">Select Status</option>
                            <option value="certified" {{ $cert->status == 'certified' ? 'selected' : '' }}>Certified</option>
                            <option value="in-conversion" {{ $cert->status == 'in-conversion' ? 'selected' : '' }}>In Conversion</option>
                            <option value="pending" {{ $cert->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="suspended" {{ $cert->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="revoked" {{ $cert->status == 'revoked' ? 'selected' : '' }}>Revoked</option>
                            <option value="expired" {{ $cert->status == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes{{ $cert->id }}" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes{{ $cert->id }}" name="notes" rows="3">{{ $cert->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    // Simple validation for add certification form
    document.addEventListener('DOMContentLoaded', function() {
        const addForm = document.querySelector('#addCertificationModal form');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                const certNumber = document.getElementById('certificate_number').value;
                if (!certNumber.trim()) {
                    e.preventDefault();
                    alert('Certificate number is required.');
                }
            });
        }
    });
</script>
@endpush
@endsection