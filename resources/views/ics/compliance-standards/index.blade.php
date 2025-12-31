@extends('layouts.base')

@section('title', 'Compliance Standards Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-gavel me-2"></i> Compliance Standards
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('compliance-standards.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Add New Standard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Standards</h6>
                                            <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-gavel fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Active</h6>
                                            <h3 class="mb-0">{{ $stats['active'] ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Mandatory</h6>
                                            <h3 class="mb-0">{{ $stats['mandatory'] ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Categories</h6>
                                            <h3 class="mb-0">{{ $stats['categories'] ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-tags fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('compliance-standards.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by name, code, or description..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <option value="organic" {{ request('category') == 'organic' ? 'selected' : '' }}>Organic</option>
                                <option value="fair_trade" {{ request('category') == 'fair_trade' ? 'selected' : '' }}>Fair Trade</option>
                                <option value="environmental" {{ request('category') == 'environmental' ? 'selected' : '' }}>Environmental</option>
                                <option value="social" {{ request('category') == 'social' ? 'selected' : '' }}>Social</option>
                                <option value="quality" {{ request('category') == 'quality' ? 'selected' : '' }}>Quality</option>
                                <option value="safety" {{ request('category') == 'safety' ? 'selected' : '' }}>Safety</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="is_active" class="form-select" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="is_mandatory" class="form-select" onchange="this.form.submit()">
                                <option value="">Mandatory/Optional</option>
                                <option value="1" {{ request('is_mandatory') === '1' ? 'selected' : '' }}>Mandatory</option>
                                <option value="0" {{ request('is_mandatory') === '0' ? 'selected' : '' }}>Optional</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        @if(request('search') || request('category') || request('is_active') || request('is_mandatory'))
                            <div class="col-md-2">
                                <a href="{{ route('compliance-standards.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Compliance Standards Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Standard Name</th>
                                    <th>Category</th>
                                    <th>Certification Body</th>
                                    <th>Version</th>
                                    <th>Effective Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($complianceStandards as $standard)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $standard->code }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($standard->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $standard->name }}</strong>
                                                    @if($standard->name_sw)
                                                        <br>
                                                        <small class="text-muted">{{ $standard->name_sw }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $categoryLabels = [
                                                    'organic' => 'Organic',
                                                    'fair_trade' => 'Fair Trade',
                                                    'environmental' => 'Environmental',
                                                    'social' => 'Social',
                                                    'quality' => 'Quality',
                                                    'safety' => 'Safety',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $standard->category === 'organic' ? 'success' : ($standard->category === 'fair_trade' ? 'info' : ($standard->category === 'environmental' ? 'primary' : 'secondary')) }}">
                                                {{ $categoryLabels[$standard->category] ?? ucfirst($standard->category) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $standard->certification_body ?? 'N/A' }}
                                        </td>
                                        <td>
                                            v{{ $standard->version }}
                                        </td>
                                        <td>
                                            {{ $standard->effective_date ? \Carbon\Carbon::parse($standard->effective_date)->format('M d, Y') : 'N/A' }}
                                            @if($standard->expiry_date)
                                                <br>
                                                <small class="text-muted">Expires: {{ \Carbon\Carbon::parse($standard->expiry_date)->format('M d, Y') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($standard->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Inactive
                                                </span>
                                            @endif
                                            @if($standard->is_mandatory)
                                                <span class="badge bg-warning ms-1">
                                                    <i class="fas fa-exclamation-triangle me-1"></i> Mandatory
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('compliance-standards.show', $standard) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('compliance-standards.edit', $standard) }}"
                                                   class="btn btn-outline-warning"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        title="Delete"
                                                        onclick="confirmDelete('{{ route('compliance-standards.destroy', $standard) }}', '{{ $standard->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-gavel fa-3x mb-3"></i>
                                                <h5>No compliance standards found</h5>
                                                <p>Start by adding your first compliance standard</p>
                                                <a href="{{ route('compliance-standards.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> Add New Standard
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($complianceStandards->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $complianceStandards->firstItem() }} to {{ $complianceStandards->lastItem() }} of {{ $complianceStandards->total() }} standards
                                </div>
                                <div>
                                    {{ $complianceStandards->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Bulk Actions & Export -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                Select All
                            </label>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('export.index') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-file-csv me-1"></i> Export to CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete compliance standard <strong id="standardName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Standard</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, name) {
        document.getElementById('standardName').textContent = name;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="selected_standards[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endpush
@endsection