@extends('layouts.base')

@section('title', 'Create New Activity Log')

@push('styles')
<style>
    .form-section {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .field-conditional {
        display: none;
    }
    .field-conditional.show {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-journal-plus me-2"></i> Create New Activity Log
                        </h4>
                        <a href="{{ route('logs.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back to Logs
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('logs.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Basic Information -->
                        <div class="form-section">
                            <h5 class="mb-3 border-bottom pb-2">
                                <i class="bi bi-info-circle me-2"></i> Basic Information
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Log Type *</label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" 
                                            name="type" 
                                            required>
                                        <option value="">Select Log Type</option>
                                        @foreach($logTypes as $type)
                                            <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                                {{ $type->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="log_date" class="form-label">Log Date *</label>
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
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label d-block">Flagged</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="is_flagged" name="is_flagged" value="1" {{ old('is_flagged') ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger" for="is_flagged">
                                            <i class="bi bi-flag-fill"></i> Mark as Flagged
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Associated Entities -->
                        <div class="form-section">
                            <h5 class="mb-3 border-bottom pb-2">
                                <i class="bi bi-link-45deg me-2"></i> Associated Entities
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="farmer_id" class="form-label">Farmer</label>
                                    <select class="form-select @error('farmer_id') is-invalid @enderror" 
                                            id="farmer_id" 
                                            name="farmer_id">
                                        <option value="">Select Farmer (Optional)</option>
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
                                    <label for="farm_id" class="form-label">Farm</label>
                                    <select class="form-select @error('farm_id') is-invalid @enderror" 
                                            id="farm_id" 
                                            name="farm_id">
                                        <option value="">Select Farm (Optional)</option>
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

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="season_id" class="form-label">Season</label>
                                    <select class="form-select @error('season_id') is-invalid @enderror" 
                                            id="season_id" 
                                            name="season_id">
                                        <option value="">Select Season (Optional)</option>
                                        @foreach($seasons as $season)
                                            <option value="{{ $season->id }}" {{ old('season_id') == $season->id ? 'selected' : '' }}>
                                                {{ $season->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('season_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="field_id" class="form-label">Field (Optional)</label>
                                    <select class="form-select @error('field_id') is-invalid @enderror" 
                                            id="field_id" 
                                            name="field_id">
                                        <option value="">Select Field</option>
                                        @foreach($fields as $field)
                                            <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : '' }}>
                                                {{ $field->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('field_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Type-Specific Fields (Dynamic) -->
                        <div class="form-section">
                            <h5 class="mb-3 border-bottom pb-2">
                                <i class="bi bi-gear me-2"></i> Type‑Specific Details
                            </h5>
                            <div id="type-specific-fields">
                                <!-- Fields will be injected via JavaScript based on selected type -->
                                <p class="text-muted">Select a log type above to see additional fields.</p>
                            </div>
                        </div>

                        <!-- Images & Attachments -->
                        <div class="form-section">
                            <h5 class="mb-3 border-bottom pb-2">
                                <i class="bi bi-images me-2"></i> Images & Attachments
                            </h5>
                            <div class="mb-3">
                                <label for="images" class="form-label">Upload Images</label>
                                <input type="file" 
                                       class="form-control @error('images') is-invalid @enderror" 
                                       id="images" 
                                       name="images[]" 
                                       multiple 
                                       accept="image/*">
                                <small class="text-muted">You can select multiple images (max 5).</small>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-section">
                            <h5 class="mb-3 border-bottom pb-2">
                                <i class="bi bi-pencil me-2"></i> Additional Notes
                            </h5>
                            <div class="mb-3">
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('logs.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Create Log
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
    // Show/hide type-specific fields based on selected log type
    document.getElementById('type').addEventListener('change', function() {
        const type = this.value;
        const container = document.getElementById('type-specific-fields');
        
        // Clear previous fields
        container.innerHTML = '';
        
        if (!type) {
            container.innerHTML = '<p class="text-muted">Select a log type above to see additional fields.</p>';
            return;
        }
        
        // Define fields for each type (simplified example)
        const fields = {
            seeding: [
                { label: 'Seed Variety', name: 'seed_variety', type: 'text', placeholder: 'e.g., Hybrid Maize 123' },
                { label: 'Planting Method', name: 'planting_method', type: 'select', options: ['Direct seeding', 'Transplanting', 'Broadcast'] },
                { label: 'Seed Quantity (kg)', name: 'seed_quantity', type: 'number', step: '0.01' },
                { label: 'Spacing (cm)', name: 'spacing', type: 'text', placeholder: 'Row x Plant spacing' },
            ],
            input: [
                { label: 'Material Type', name: 'material_type', type: 'text', placeholder: 'e.g., Fertilizer, Pesticide' },
                { label: 'Quantity', name: 'quantity', type: 'number', step: '0.01' },
                { label: 'Unit', name: 'unit', type: 'select', options: ['kg', 'liters', 'bags', 'pieces'] },
                { label: 'Application Method', name: 'application_method', type: 'text' },
            ],
            observation: [
                { label: 'Observation Type', name: 'observation_type', type: 'select', options: ['Pest', 'Disease', 'Growth Stage', 'Weather'] },
                { label: 'Severity', name: 'severity', type: 'select', options: ['Low', 'Medium', 'High'] },
                { label: 'Recommendation', name: 'recommendation', type: 'textarea', rows: 2 },
            ],
            harvest: [
                { label: 'Harvested Crop', name: 'harvested_crop', type: 'text' },
                { label: 'Yield Amount', name: 'yield_amount', type: 'number', step: '0.01' },
                { label: 'Yield Unit', name: 'yield_unit', type: 'select', options: ['kg', 'tons', 'bags', 'crates'] },
                { label: 'Quality Grade', name: 'quality_grade', type: 'select', options: ['A', 'B', 'C', 'D'] },
            ],
            activity: [
                { label: 'Activity Category', name: 'activity_category', type: 'text' },
                { label: 'Duration (hours)', name: 'duration', type: 'number', step: '0.5' },
                { label: 'Labor Count', name: 'labor_count', type: 'number' },
            ],
            training: [
                { label: 'Training Topic', name: 'training_topic', type: 'text' },
                { label: 'Trainer', name: 'trainer', type: 'text' },
                { label: 'Participant Count', name: 'participant_count', type: 'number' },
            ],
            inspection: [
                { label: 'Inspection Type', name: 'inspection_type', type: 'text' },
                { label: 'Inspector', name: 'inspector', type: 'text' },
                { label: 'Findings', name: 'findings', type: 'textarea', rows: 2 },
            ]
        };
        
        const typeFields = fields[type] || [];
        
        if (typeFields.length === 0) {
            container.innerHTML = '<p class="text-muted">No additional fields for this log type.</p>';
            return;
        }
        
        // Create a row with columns
        let html = '<div class="row">';
        typeFields.forEach((field, index) => {
            const colClass = 'col-md-' + (field.type === 'textarea' ? '12' : '6');
            html += `<div class="${colClass} mb-3">`;
            html += `<label for="${field.name}" class="form-label">${field.label}</label>`;
            
            if (field.type === 'select') {
                html += `<select class="form-select" id="${field.name}" name="${field.name}">`;
                html += '<option value="">Select</option>';
                field.options.forEach(opt => {
                    html += `<option value="${opt}">${opt}</option>`;
                });
                html += '</select>';
            } else if (field.type === 'textarea') {
                html += `<textarea class="form-control" id="${field.name}" name="${field.name}" rows="${field.rows || 3}" placeholder="${field.placeholder || ''}"></textarea>`;
            } else {
                html += `<input type="${field.type}" class="form-control" id="${field.name}" name="${field.name}" placeholder="${field.placeholder || ''}" step="${field.step || ''}">`;
            }
            
            html += '</div>';
            // Ensure proper row wrapping
            if ((index + 1) % 2 === 0 && field.type !== 'textarea') {
                html += '</div><div class="row">';
            }
        });
        html += '</div>';
        container.innerHTML = html;
    });
    
    // Trigger change on page load if type already selected (e.g., after validation error)
    @if(old('type'))
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('type').dispatchEvent(new Event('change'));
        });
    @endif
</script>
@endpush