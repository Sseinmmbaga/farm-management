@extends('layouts.base')

@section('title', 'Certifications Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #4a69bd 0%, #38ada9 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="display-6 mb-2 text-white">
                                <i class="fas fa-certificate me-3"></i>Certifications Management
                            </h1>
                            <p class="lead text-white mb-0">
                                Manage farmer certifications, track compliance status, and monitor certification lifecycle.
                            </p>
                        </div>
                        <div class="col-lg-4 text-end">
                            <div class="btn-group">
                                <a href="#" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                                    <i class="fas fa-sync me-2"></i> Bulk Update
                                </a>
                                <a href="#" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="fas fa-upload me-2"></i> Import
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h2 class="fw-bold text-success">{{ $stats['certified'] ?? 0 }}</h2>
                    <p class="mb-0 text-muted">Certified</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h2 class="fw-bold text-info">{{ $stats['in_conversion'] ?? 0 }}</h2>
                    <p class="mb-0 text-muted">In Conversion</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h2 class="fw-bold text-warning">{{ $stats['expiring_soon'] ?? 0 }}</h2>
                    <p class="mb-0 text-muted">Expiring Soon</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-0 bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <h2 class="fw-bold text-danger">{{ $stats['expired'] ?? 0 }}</h2>
                    <p class="mb-0 text-muted">Expired</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-0 bg-secondary bg-opacity-10">
                <div class="card-body text-center">
                    <h2 class="fw-bold text-secondary">{{ $stats['suspended'] ?? 0 }}</h2>
                    <p class="mb-0 text-muted">Suspended</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h2 class="fw-bold text-primary">{{ $stats['total'] ?? 0 }}</h2>
                    <p class="mb-0 text-muted">Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i> Filter Certifications
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('certifications.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="certified" {{ request('status') == 'certified' ? 'selected' : '' }}>Certified</option>
                                    <option value="in-conversion" {{ request('status') == 'in-conversion' ? 'selected' : '' }}>In Conversion</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>Revoked</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="farmer" class="form-label">Farmer</label>
                                <input type="text" class="form-control" id="farmer" name="farmer" value="{{ request('farmer') }}" placeholder="Search by farmer name...">
                            </div>
                            <div class="col-md-3">
                                <label for="certificate_number" class="form-label">Certificate Number</label>
                                <input type="text" class="form-control" id="certificate_number" name="certificate_number" value="{{ request('certificate_number') }}" placeholder="e.g., CERT-2024-001">
                            </div>
                            <div class="col-md-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <select class="form-select" id="expiry_date" name="expiry_date">
                                    <option value="">Any Expiry</option>
                                    <option value="expiring_soon" {{ request('expiry_date') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon (30 days)</option>
                                    <option value="expired" {{ request('expiry_date') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="valid" {{ request('expiry_date') == 'valid' ? 'selected' : '' }}>Valid (Not expired)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="has_inspection" name="has_inspection" value="1" {{ request('has_inspection') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_inspection">Has Recent Inspection</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="needs_renewal" name="needs_renewal" value="1" {{ request('needs_renewal') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="needs_renewal">Needs Renewal</label>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i> Apply Filters
                                </button>
                                <a href="{{ route('certifications.index') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
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
                            <i class="fas fa-list me-2"></i> All Certifications
                        </h5>
                        <div>
                            <span class="badge bg-light text-dark fs-6">
                                Showing {{ $certifications->firstItem() ?? 0 }} - {{ $certifications->lastItem() ?? 0 }} of {{ $certifications->total() }} certifications
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Certificate #</th>
                                    <th>Farmer</th>
                                    <th>Standard</th>
                                    <th>Status</th>
                                    <th>Certification Date</th>
                                    <th>Expiry Date</th>
                                    <th>Last Inspection</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certifications as $cert)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="cert-checkbox" value="{{ $cert->id }}">
                                    </td>
                                    <td>
                                        <strong>{{ $cert->certificate_number }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('certifications.farmer', $cert->farmer) }}" class="text-decoration-none">
                                            <strong>{{ $cert->farmer->first_name }} {{ $cert->farmer->last_name }}</strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $cert->farmer->farm_name ?? 'No farm' }}</small>
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
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('certifications.farmer', $cert->farmer) }}" class="btn btn-outline-info" title="View Farmer Certifications">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $cert->id }}">
                                                        <i class="fas fa-sync text-primary me-2"></i> Update Status
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#renewModal{{ $cert->id }}">
                                                        <i class="fas fa-redo text-success me-2"></i> Renew Certificate
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#notesModal{{ $cert->id }}">
                                                        <i class="fas fa-edit text-info me-2"></i> Edit Notes
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $cert->id }}">
                                                        <i class="fas fa-trash me-2"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                                        <h5>No certifications found</h5>
                                        <p class="text-muted">Try adjusting your filters or add new certifications.</p>
                                        <a href="#" class="btn btn-primary">Add First Certification</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($certifications->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $certifications->firstItem() }} to {{ $certifications->lastItem() }} of {{ $certifications->total() }} entries
                        </div>
                        <div>
                            {{ $certifications->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span id="selectedCount">0</span> certifications selected
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkStatusModal">
                                <i class="fas fa-sync me-2"></i> Update Status
                            </button>
                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#bulkRenewModal">
                                <i class="fas fa-redo me-2"></i> Renew Selected
                            </button>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                                <i class="fas fa-trash me-2"></i> Delete Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Bulk Update Status Modal -->
<div class="modal fade" id="bulkStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('certifications.bulk-update-status') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="certification_ids" id="bulkStatusIds">
                    <div class="mb-3">
                        <label for="bulk_status" class="form-label">New Status</label>
                        <select class="form-select" id="bulk_status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="certified">Certified</option>
                            <option value="in-conversion">In Conversion</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                            <option value="revoked">Revoked</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bulk_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="bulk_notes" name="notes" rows="3"></textarea>
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

<!-- Individual Status Update Modals (Generated Dynamically) -->
@foreach($certifications as $cert)
<div class="modal fade" id="updateStatusModal{{ $cert->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status: {{ $cert->certificate_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('certifications.update-status', $cert->farmer) }}">
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
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.cert-checkbox');
        const selectedCount = document.getElementById('selectedCount');
        const bulkStatusIds = document.getElementById('bulkStatusIds');

        // Select all functionality
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            checkboxes.forEach(cb => cb.checked = isChecked);
            updateSelectedCount();
        });

        // Update selected count
        function updateSelectedCount() {
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            selectedCount.textContent = selected.length;
            
            // Update hidden input with comma-separated IDs
            bulkStatusIds.value = selected.map(cb => cb.value).join(',');
        }

        // Attach change event to each checkbox
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        // Initialize count
        updateSelectedCount();
    });
</script>
@endpush
@endsection