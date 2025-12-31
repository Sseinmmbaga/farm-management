@extends('layouts.base')

@section('title', 'Report Non-Conformity (Finding)')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> Report Non-Conformity (Finding)
                        </h4>
                        <a href="{{ route('findings.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Findings
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('findings.store') }}" id="findingForm">
                        @csrf

                        <!-- Inspection Selection -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-clipboard-check me-2"></i>Inspection Details
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="inspection_id" class="form-label">
                                        Inspection <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('inspection_id') is-invalid @enderror" 
                                            id="inspection_id" 
                                            name="inspection_id" 
                                            required>
                                        <option value="">Select Inspection</option>
                                        @foreach($inspections as $inspection)
                                            <option value="{{ $inspection->id }}" 
                                                {{ old('inspection_id') == $inspection->id ? 'selected' : '' }}>
                                                {{ $inspection->inspection_number }} - 
                                                {{ $inspection->farmer->first_name }} {{ $inspection->farmer->last_name }}
                                                ({{ $inspection->checklist->name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('inspection_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Select the inspection where this non-conformity was identified.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="checklist_item_id" class="form-label">Checklist Item (Optional)</label>
                                    <select class="form-select @error('checklist_item_id') is-invalid @enderror" 
                                            id="checklist_item_id" 
                                            name="checklist_item_id">
                                        <option value="">Not specific to any item</option>
                                        <!-- Will be populated via AJAX based on selected inspection -->
                                    </select>
                                    @error('checklist_item_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        If this finding relates to a specific checklist item, select it.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Finding Details -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Finding Details
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="finding_type" class="form-label">
                                        Finding Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('finding_type') is-invalid @enderror" 
                                            id="finding_type" 
                                            name="finding_type" 
                                            required>
                                        <option value="">Select Type</option>
                                        <option value="documentation" {{ old('finding_type') == 'documentation' ? 'selected' : '' }}>Documentation Issue</option>
                                        <option value="procedure" {{ old('finding_type') == 'procedure' ? 'selected' : '' }}>Procedure Non-Compliance</option>
                                        <option value="safety" {{ old('finding_type') == 'safety' ? 'selected' : '' }}>Safety Violation</option>
                                        <option value="quality" {{ old('finding_type') == 'quality' ? 'selected' : '' }}>Quality Defect</option>
                                        <option value="environmental" {{ old('finding_type') == 'environmental' ? 'selected' : '' }}>Environmental Concern</option>
                                        <option value="social" {{ old('finding_type') == 'social' ? 'selected' : '' }}>Social Compliance Issue</option>
                                        <option value="other" {{ old('finding_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('finding_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="severity" class="form-label">
                                        Severity <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('severity') is-invalid @enderror" 
                                            id="severity" 
                                            name="severity" 
                                            required>
                                        <option value="">Select Severity</option>
                                        <option value="observation" {{ old('severity') == 'observation' ? 'selected' : '' }}>Observation</option>
                                        <option value="minor" {{ old('severity') == 'minor' ? 'selected' : '' }}>Minor</option>
                                        <option value="major" {{ old('severity') == 'major' ? 'selected' : '' }}>Major</option>
                                        <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('severity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-3">
                                <label for="description" class="form-label">
                                    Description <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          placeholder="Describe the non-conformity in detail..."
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Provide a clear, concise description of the issue, including what was observed, when, and where.
                                </div>
                            </div>
                        </div>

                        <!-- Evidence & Recommendation -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-clipboard-list me-2"></i>Evidence & Recommendation
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="evidence" class="form-label">Evidence / Reference</label>
                                    <textarea class="form-control @error('evidence') is-invalid @enderror" 
                                              id="evidence" 
                                              name="evidence" 
                                              rows="3"
                                              placeholder="Photo references, document numbers, witness statements...">{{ old('evidence') }}</textarea>
                                    @error('evidence')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Provide any evidence that supports this finding.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="recommendation" class="form-label">Recommendation</label>
                                    <textarea class="form-control @error('recommendation') is-invalid @enderror" 
                                              id="recommendation" 
                                              name="recommendation" 
                                              rows="3"
                                              placeholder="Recommended corrective actions...">{{ old('recommendation') }}</textarea>
                                    @error('recommendation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Suggest how this non-conformity should be addressed.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Due Date & Follow-up -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-calendar-day me-2"></i>Due Date & Follow-up
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="due_date" class="form-label">Due Date for Resolution</label>
                                    <input type="date" 
                                           class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" 
                                           name="due_date" 
                                           value="{{ old('due_date', date('Y-m-d', strtotime('+14 days'))) }}"
                                           min="{{ date('Y-m-d') }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Set a target date for resolving this finding.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Assign to</label>
                                    <select class="form-select" name="assigned_to">
                                        <option value="">Self (Inspector)</option>
                                        <!-- Would be populated with users -->
                                    </select>
                                    <div class="form-text">
                                        Optionally assign this finding to another staff member.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('findings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-save me-1"></i> Report Finding
                                    </button>
                                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#previewModal">
                                        <i class="fas fa-eye me-1"></i> Preview
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

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Finding Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This is a preview of the finding details. Review before submitting.
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Inspection</th>
                        <td id="previewInspection">-</td>
                    </tr>
                    <tr>
                        <th>Finding Type</th>
                        <td id="previewType">-</td>
                    </tr>
                    <tr>
                        <th>Severity</th>
                        <td id="previewSeverity">-</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td id="previewDescription">-</td>
                    </tr>
                    <tr>
                        <th>Evidence</th>
                        <td id="previewEvidence">-</td>
                    </tr>
                    <tr>
                        <th>Recommendation</th>
                        <td id="previewRecommendation">-</td>
                    </tr>
                    <tr>
                        <th>Due Date</th>
                        <td id="previewDueDate">-</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('findingForm').submit()">
                    <i class="fas fa-check me-1"></i> Confirm & Report
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inspectionSelect = document.getElementById('inspection_id');
        const checklistItemSelect = document.getElementById('checklist_item_id');
        const typeSelect = document.getElementById('finding_type');
        const severitySelect = document.getElementById('severity');
        const descriptionTextarea = document.getElementById('description');
        const evidenceTextarea = document.getElementById('evidence');
        const recommendationTextarea = document.getElementById('recommendation');
        const dueDateInput = document.getElementById('due_date');

        // Load checklist items when inspection changes
        inspectionSelect.addEventListener('change', function() {
            const inspectionId = this.value;
            checklistItemSelect.innerHTML = '<option value="">Not specific to any item</option>';
            
            if (inspectionId) {
                // In a real application, you would fetch via AJAX
                // For now, we'll simulate with a placeholder
                checklistItemSelect.disabled = true;
                checklistItemSelect.innerHTML += '<option value="">Loading items...</option>';
                
                // Simulate API call
                setTimeout(() => {
                    checklistItemSelect.disabled = false;
                    checklistItemSelect.innerHTML = '<option value="">Not specific to any item</option>';
                    // Add some dummy items
                    for (let i = 1; i <= 5; i++) {
                        const option = document.createElement('option');
                        option.value = i;
                        option.textContent = `Checklist Item ${i} - Requirement description`;
                        checklistItemSelect.appendChild(option);
                    }
                }, 300);
            }
        });

        // Preview update function
        function updatePreview() {
            // Inspection
            const inspectionOption = inspectionSelect.selectedOptions[0];
            document.getElementById('previewInspection').textContent = inspectionOption.text || '-';

            // Type
            document.getElementById('previewType').textContent = typeSelect.value ? typeSelect.options[typeSelect.selectedIndex].text : '-';

            // Severity
            document.getElementById('previewSeverity').textContent = severitySelect.value ? severitySelect.options[severitySelect.selectedIndex].text : '-';

            // Description
            document.getElementById('previewDescription').textContent = descriptionTextarea.value || '-';

            // Evidence
            document.getElementById('previewEvidence').textContent = evidenceTextarea.value || 'None';

            // Recommendation
            document.getElementById('previewRecommendation').textContent = recommendationTextarea.value || 'None';

            // Due Date
            document.getElementById('previewDueDate').textContent = dueDateInput.value || 'Not set';
        }

        // Attach event listeners
        inspectionSelect.addEventListener('change', updatePreview);
        typeSelect.addEventListener('change', updatePreview);
        severitySelect.addEventListener('change', updatePreview);
        descriptionTextarea.addEventListener('input', updatePreview);
        evidenceTextarea.addEventListener('input', updatePreview);
        recommendationTextarea.addEventListener('input', updatePreview);
        dueDateInput.addEventListener('change', updatePreview);

        // Initialize preview
        updatePreview();
    });
</script>
@endpush
@endsection