@extends('layouts.base')

@section('title', 'Compliance Standard Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-gavel me-2"></i> {{ $complianceStandard->name }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('compliance-standards.edit', $complianceStandard) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('compliance-standards.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i> Basic Information
                            </h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Code:</th>
                                    <td>
                                        <span class="badge bg-primary">{{ $complianceStandard->code }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Standard Name:</th>
                                    <td>{{ $complianceStandard->name }}</td>
                                </tr>
                                @if($complianceStandard->name_sw)
                                <tr>
                                    <th>Name (Swahili):</th>
                                    <td>{{ $complianceStandard->name_sw }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        @php
                                            $categoryLabels = [
                                                'organic' => 'Organic',
                                                'fair_trade' => 'Fair Trade',
                                                'environmental' => 'Environmental',
                                                'social' => 'Social',
                                                'quality' => 'Quality',
                                                'safety' => 'Safety',
                                                'other' => 'Other',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $complianceStandard->category === 'organic' ? 'success' : ($complianceStandard->category === 'fair_trade' ? 'info' : ($complianceStandard->category === 'environmental' ? 'primary' : 'secondary')) }}">
                                            {{ $categoryLabels[$complianceStandard->category] ?? ucfirst($complianceStandard->category) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Certification Body:</th>
                                    <td>{{ $complianceStandard->certification_body ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $complianceStandard->description ?? 'No description provided' }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-calendar-alt me-2"></i> Version & Dates
                            </h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Version:</th>
                                    <td>v{{ $complianceStandard->version }}</td>
                                </tr>
                                <tr>
                                    <th>Effective Date:</th>
                                    <td>
                                        @if($complianceStandard->effective_date)
                                            {{ \Carbon\Carbon::parse($complianceStandard->effective_date)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Expiry Date:</th>
                                    <td>
                                        @if($complianceStandard->expiry_date)
                                            {{ \Carbon\Carbon::parse($complianceStandard->expiry_date)->format('M d, Y') }}
                                            @php
                                                $expiry = \Carbon\Carbon::parse($complianceStandard->expiry_date);
                                                $today = \Carbon\Carbon::today();
                                            @endphp
                                            @if($expiry->isPast())
                                                <span class="badge bg-danger ms-2">Expired</span>
                                            @elseif($expiry->diffInDays($today) <= 30)
                                                <span class="badge bg-warning ms-2">Expiring Soon</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $complianceStandard->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $complianceStandard->updated_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                            
                            <h5 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-cogs me-2"></i> Settings
                            </h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Status:</th>
                                    <td>
                                        @if($complianceStandard->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i> Inactive
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Mandatory:</th>
                                    <td>
                                        @if($complianceStandard->is_mandatory)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Mandatory
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-info-circle me-1"></i> Optional
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @if($complianceStandard->createdBy)
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $complianceStandard->createdBy->name }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    <!-- Requirements Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-list-check me-2"></i> Requirements
                            </h5>
                            
                            @php
                                $requirements = $complianceStandard->requirements ? json_decode($complianceStandard->requirements, true) : [];
                            @endphp
                            
                            @if(count($requirements) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Requirement</th>
                                                <th>Mandatory</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($requirements as $req)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $req['requirement'] ?? $req['text'] ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($req['mandatory'] ?? false)
                                                            <span class="badge bg-danger">Yes</span>
                                                        @else
                                                            <span class="badge bg-secondary">No</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $req['description'] ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> No specific requirements defined for this standard.
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Related Checklists -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-clipboard-check me-2"></i> Related Checklists
                            </h5>
                            
                            @php
                                $checklists = $complianceStandard->checklists ?? [];
                            @endphp
                            
                            @if(count($checklists) > 0)
                                <div class="row">
                                    @foreach($checklists as $checklist)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title">
                                                        <a href="{{ route('checklists.show', $checklist) }}">{{ $checklist->name }}</a>
                                                    </h6>
                                                    <p class="card-text small text-muted">{{ $checklist->description ?? 'No description' }}</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-{{ $checklist->is_active ? 'success' : 'secondary' }}">
                                                            {{ $checklist->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                        <span class="badge bg-info">
                                                            {{ $checklist->items_count ?? 0 }} items
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i> No checklists associated with this standard yet.
                                    <a href="{{ route('checklists.create') }}" class="alert-link">Create a checklist</a>.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div class="btn-group">
                            <a href="{{ route('compliance-standards.edit', $complianceStandard) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Standard
                            </a>
                            <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="confirmDelete('{{ route('compliance-standards.destroy', $complianceStandard) }}', '{{ $complianceStandard->name }}')">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </div>
                        <a href="{{ route('compliance-standards.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Standards
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i> Quick Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Associated Checklists
                            <span class="badge bg-primary rounded-pill">{{ $complianceStandard->checklists_count ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Active Checklists
                            <span class="badge bg-success rounded-pill">{{ $complianceStandard->active_checklists_count ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Certified Farmers
                            <span class="badge bg-warning rounded-pill">{{ $complianceStandard->certified_farmers_count ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pending Inspections
                            <span class="badge bg-danger rounded-pill">{{ $complianceStandard->pending_inspections_count ?? 0 }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('checklists.create', ['compliance_standard_id' => $complianceStandard->id]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-clipboard-plus me-1"></i> Create Checklist
                        </a>
                        <a href="{{ route('inspections.create', ['standard_id' => $complianceStandard->id]) }}" class="btn btn-outline-success">
                            <i class="fas fa-clipboard-check me-1"></i> Schedule Inspection
                        </a>
                        <a href="{{ route('certifications.index', ['standard_id' => $complianceStandard->id]) }}" class="btn btn-outline-info">
                            <i class="fas fa-certificate me-1"></i> View Certifications
                        </a>
                        <button type="button" class="btn btn-outline-warning">
                            <i class="fas fa-file-export me-1"></i> Export Report
                        </button>
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
                <p class="text-danger"><small>This action cannot be undone and will affect all associated checklists and inspections.</small></p>
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
</script>
@endpush
@endsection