@extends('layouts.base')

@section('title', 'Edit Compliance Standard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Compliance Standard: {{ $complianceStandard->name }}
                        </h4>
                        <a href="{{ route('compliance-standards.show', $complianceStandard) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Details
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('compliance-standards.update', $complianceStandard) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-info-circle me-2"></i> Basic Information
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Standard Name *</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $complianceStandard->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="name_sw" class="form-label">Standard Name (Swahili)</label>
                                    <input type="text" 
                                           class="form-control @error('name_sw') is-invalid @enderror" 
                                           id="name_sw" 
                                           name="name_sw" 
                                           value="{{ old('name_sw', $complianceStandard->name_sw) }}">
                                    @error('name_sw')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code *</label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code', $complianceStandard->code) }}" 
                                           placeholder="e.g., ISO-9001"
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3">{{ old('description', $complianceStandard->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            required>
                                        <option value="">Select Category</option>
                                        <option value="organic" {{ old('category', $complianceStandard->category) == 'organic' ? 'selected' : '' }}>Organic</option>
                                        <option value="fair_trade" {{ old('category', $complianceStandard->category) == 'fair_trade' ? 'selected' : '' }}>Fair Trade</option>
                                        <option value="environmental" {{ old('category', $complianceStandard->category) == 'environmental' ? 'selected' : '' }}>Environmental</option>
                                        <option value="social" {{ old('category', $complianceStandard->category) == 'social' ? 'selected' : '' }}>Social</option>
                                        <option value="quality" {{ old('category', $complianceStandard->category) == 'quality' ? 'selected' : '' }}>Quality</option>
                                        <option value="safety" {{ old('category', $complianceStandard->category) == 'safety' ? 'selected' : '' }}>Safety</option>
                                        <option value="other" {{ old('category', $complianceStandard->category) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="certification_body" class="form-label">Certification Body</label>
                                    <input type="text" 
                                           class="form-control @error('certification_body') is-invalid @enderror" 
                                           id="certification_body" 
                                           name="certification_body" 
                                           value="{{ old('certification_body', $complianceStandard->certification_body) }}">
                                    @error('certification_body')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-calendar-alt me-2"></i> Version & Dates
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="version" class="form-label">Version *</label>
                                    <input type="number" 
                                           class="form-control @error('version') is-invalid @enderror" 
                                           id="version" 
                                           name="version" 
                                           value="{{ old('version', $complianceStandard->version) }}" 
                                           min="1"
                                           required>
                                    @error('version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="effective_date" class="form-label">Effective Date</label>
                                    <input type="date"
                                           class="form-control @error('effective_date') is-invalid @enderror"
                                           id="effective_date"
                                           name="effective_date"
                                           value="{{ old('effective_date', $complianceStandard->effective_date ? \Carbon\Carbon::parse($complianceStandard->effective_date)->format('Y-m-d') : '') }}">
                                    @error('effective_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="date"
                                           class="form-control @error('expiry_date') is-invalid @enderror"
                                           id="expiry_date"
                                           name="expiry_date"
                                           value="{{ old('expiry_date', $complianceStandard->expiry_date ? \Carbon\Carbon::parse($complianceStandard->expiry_date)->format('Y-m-d') : '') }}">
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <h5 class="mb-3 mt-4 border-bottom pb-2">
                                    <i class="fas fa-cogs me-2"></i> Settings
                                </h5>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input @error('is_mandatory') is-invalid @enderror" 
                                               type="checkbox" 
                                               id="is_mandatory" 
                                               name="is_mandatory" 
                                               value="1"
                                               {{ old('is_mandatory', $complianceStandard->is_mandatory) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_mandatory">
                                            Mandatory Standard
                                        </label>
                                        @error('is_mandatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">If checked, this standard is mandatory for certification.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', $complianceStandard->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Inactive standards will not be available for selection.</small>
                                </div>
                                
                                <h5 class="mb-3 mt-4 border-bottom pb-2">
                                    <i class="fas fa-list-check me-2"></i> Requirements
                                </h5>

                                <div class="mb-3">
                                    <div id="requirements-container">
                                        <!-- Requirements will be added here dynamically -->
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-requirement">
                                        <i class="fas fa-plus me-1"></i> Add Requirement
                                    </button>
                                    @error('requirements')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <input type="hidden" name="requirements" id="requirements-json">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('compliance-standards.show', $complianceStandard) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update Standard
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let requirementCount = 0;
    const existingRequirements = @json($complianceStandard->requirements ?? []);

    function addRequirement(text = '', mandatory = false) {
        requirementCount++;
        const container = document.getElementById('requirements-container');
        const div = document.createElement('div');
        div.className = 'requirement-item card mb-2';
        div.id = `requirement-${requirementCount}`;
        div.innerHTML = `
            <div class="card-body p-2">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <input type="text"
                               class="form-control form-control-sm requirement-text"
                               placeholder="Enter requirement..."
                               value="${text}">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox"
                                   class="form-check-input requirement-mandatory"
                                   id="mandatory-${requirementCount}"
                                   ${mandatory ? 'checked' : ''}>
                            <label class="form-check-label small" for="mandatory-${requirementCount}">
                                Mandatory
                            </label>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRequirement(${requirementCount})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
        updateRequirementsJson();
    }

    function removeRequirement(id) {
        const element = document.getElementById(`requirement-${id}`);
        if (element) {
            element.remove();
            updateRequirementsJson();
        }
    }

    function updateRequirementsJson() {
        const items = document.querySelectorAll('.requirement-item');
        const requirements = [];
        items.forEach((item, index) => {
            const text = item.querySelector('.requirement-text').value.trim();
            const mandatory = item.querySelector('.requirement-mandatory').checked;
            if (text) {
                requirements.push({
                    id: index + 1,
                    requirement: text,
                    mandatory: mandatory
                });
            }
        });
        document.getElementById('requirements-json').value = requirements.length > 0 ? JSON.stringify(requirements) : '';
    }

    document.getElementById('add-requirement').addEventListener('click', function() {
        addRequirement();
    });

    document.getElementById('requirements-container').addEventListener('input', updateRequirementsJson);
    document.getElementById('requirements-container').addEventListener('change', updateRequirementsJson);

    // Load existing requirements
    if (existingRequirements && Array.isArray(existingRequirements) && existingRequirements.length > 0) {
        existingRequirements.forEach(req => {
            addRequirement(req.requirement || '', req.mandatory || false);
        });
    } else {
        // Initialize with one empty requirement if none exist
        addRequirement();
    }
</script>
@endpush
@endsection