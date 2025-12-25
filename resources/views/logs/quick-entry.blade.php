@extends('layouts.base')

@section('title', 'Quick Log Entry')

@push('styles')
<style>
    .quick-entry-card {
        max-width: 800px;
        margin: 0 auto;
    }
    .type-option {
        border: 2px solid #dee2e6;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
    }
    .type-option:hover {
        border-color: #3498db;
        background: #f8f9fa;
    }
    .type-option.active {
        border-color: #3498db;
        background: #e3f2fd;
    }
    .type-option i {
        font-size: 2rem;
        margin-bottom: 10px;
        display: block;
    }
    .type-option.seeding i { color: #28a745; }
    .type-option.input i { color: #17a2b8; }
    .type-option.observation i { color: #ffc107; }
    .type-option.harvest i { color: #fd7e14; }
    .type-option.activity i { color: #6c757d; }
    .type-option.training i { color: #6f42c1; }
    .type-option.inspection i { color: #e83e8c; }
    .hidden-field {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card quick-entry-card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-lightning me-2"></i> Quick Log Entry
                        </h4>
                        <a href="{{ route('logs.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back to Logs
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('logs.store') }}">
                        @csrf

                        <!-- Step 1: Choose Log Type -->
                        <div class="mb-5">
                            <h5 class="mb-3">1. Select Activity Type</h5>
                            <div class="row g-3">
                                @foreach($logTypes as $type)
                                <div class="col-6 col-md-3">
                                    <div class="type-option {{ $type->value }} {{ old('type') == $type->value ? 'active' : '' }}"
                                         data-type="{{ $type->value }}">
                                        <i class="{{ $type->icon() }}"></i>
                                        <div class="fw-medium">{{ $type->label() }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="type" id="selectedType" value="{{ old('type') }}">
                            @error('type')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Step 2: Basic Details -->
                        <div id="basicFields" class="{{ old('type') ? '' : 'hidden-field' }}">
                            <h5 class="mb-3">2. Enter Details</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Log Name *</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="e.g., Seeding of Maize Field" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="log_date" class="form-label">Date *</label>
                                    <input type="date" 
                                           class="form-control @error('log_date') is-invalid @enderror" 
                                           id="log_date" 
                                           name="log_date" 
                                           value="{{ old('log_date', date('Y-m-d')) }}" 
                                           required>
                                    @error('log_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label">Description (Optional)</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="2">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Quick Associations -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <label for="farmer_id" class="form-label">Farmer (Optional)</label>
                                    <select class="form-select @error('farmer_id') is-invalid @enderror" 
                                            id="farmer_id" 
                                            name="farmer_id">
                                        <option value="">Select Farmer</option>
                                        @foreach($farmers as $farmer)
                                            <option value="{{ $farmer->id }}" {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                                {{ $farmer->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('farmer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="farm_id" class="form-label">Farm (Optional)</label>
                                    <select class="form-select @error('farm_id') is-invalid @enderror" 
                                            id="farm_id" 
                                            name="farm_id">
                                        <option value="">Select Farm</option>
                                        @foreach($farms as $farm)
                                            <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>
                                                {{ $farm->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('farm_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Type-specific quick fields (optional) -->
                            <div id="quickExtraFields" class="mt-4">
                                <!-- Will be filled by JavaScript -->
                            </div>

                            <!-- Status -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status">
                                        <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Completed</option>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_flagged" name="is_flagged" value="1" {{ old('is_flagged') ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger" for="is_flagged">
                                            <i class="bi bi-flag-fill"></i> Flag this log
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Submit -->
                        <div class="d-flex justify-content-between mt-5">
                            <a href="{{ route('logs.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn" {{ old('type') ? '' : 'disabled' }}>
                                <i class="bi bi-save me-1"></i> Save Log
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Type selection
    document.querySelectorAll('.type-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            document.querySelectorAll('.type-option').forEach(opt => opt.classList.remove('active'));
            // Add active to clicked
            this.classList.add('active');
            
            const type = this.dataset.type;
            document.getElementById('selectedType').value = type;
            
            // Show basic fields
            document.getElementById('basicFields').classList.remove('hidden-field');
            // Enable submit button
            document.getElementById('submitBtn').removeAttribute('disabled');
            
            // Update quick extra fields based on type
            updateQuickExtraFields(type);
        });
    });
    
    // If type already selected (e.g., after validation error), trigger click
    @if(old('type'))
        document.addEventListener('DOMContentLoaded', function() {
            const type = '{{ old('type') }}';
            const option = document.querySelector(`.type-option[data-type="${type}"]`);
            if (option) {
                option.click();
            }
        });
    @endif
    
    function updateQuickExtraFields(type) {
        const container = document.getElementById('quickExtraFields');
        container.innerHTML = '';
        
        const fields = {
            seeding: [
                { label: 'Seed Variety', name: 'seed_variety', placeholder: 'e.g., Hybrid Maize' },
                { label: 'Seed Quantity (kg)', name: 'seed_quantity', type: 'number', step: '0.01' }
            ],
            input: [
                { label: 'Material Type', name: 'material_type', placeholder: 'Fertilizer, Pesticide, etc.' },
                { label: 'Quantity', name: 'quantity', type: 'number', step: '0.01' }
            ],
            observation: [
                { label: 'Observation Type', name: 'observation_type', placeholder: 'Pest, Disease, Growth' },
                { label: 'Severity', name: 'severity', type: 'select', options: ['Low', 'Medium', 'High'] }
            ],
            harvest: [
                { label: 'Harvested Crop', name: 'harvested_crop', placeholder: 'e.g., Maize, Tomatoes' },
                { label: 'Yield Amount', name: 'yield_amount', type: 'number', step: '0.01' }
            ]
        };
        
        const typeFields = fields[type] || [];
        if (typeFields.length === 0) return;
        
        let html = '<h6 class="mb-3">Additional Details (Optional)</h6><div class="row g-3">';
        typeFields.forEach(field => {
            html += `<div class="col-md-6">
                <label class="form-label">${field.label}</label>`;
            if (field.type === 'select') {
                html += `<select class="form-select" name="${field.name}">
                    <option value="">Select</option>`;
                field.options.forEach(opt => html += `<option value="${opt}">${opt}</option>`);
                html += `</select>`;
            } else {
                html += `<input type="${field.type || 'text'}" class="form-control" name="${field.name}" placeholder="${field.placeholder || ''}" step="${field.step || ''}">`;
            }
            html += `</div>`;
        });
        html += '</div>';
        container.innerHTML = html;
    }
</script>
@endpush