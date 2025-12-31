@extends('layouts.base')

@section('title', 'Create Corrective Action')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-tools me-2"></i> Create Corrective Action
                        </h4>
                        <a href="{{ route('corrective-actions.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Actions
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('corrective-actions.store') }}" id="actionForm">
                        @csrf

                        <!-- Finding Selection -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>Related Finding
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="finding_id" class="form-label">
                                        Finding <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('finding_id') is-invalid @enderror" 
                                            id="finding_id" 
                                            name="finding_id" 
                                            required>
                                        <option value="">Select Finding</option>
                                        @foreach($findings as $finding)
                                            <option value="{{ $finding->id }}" 
                                                {{ old('finding_id') == $finding->id ? 'selected' : '' }}>
                                                {{ $finding->finding_number }} - 
                                                {{ $finding->description }}
                                                (Farmer: {{ $finding->inspection->farmer->first_name }} {{ $finding->inspection->farmer->last_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('finding_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Select the finding that this corrective action addresses.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Details -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Action Details
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="action_type" class="form-label">
                                        Action Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('action_type') is-invalid @enderror" 
                                            id="action_type" 
                                            name="action_type" 
                                            required>
                                        <option value="">Select Type</option>
                                        <option value="correction" {{ old('action_type') == 'correction' ? 'selected' : '' }}>Correction</option>
                                        <option value="corrective_action" {{ old('action_type') == 'corrective_action' ? 'selected' : '' }}>Corrective Action</option>
                                        <option value="preventive_action" {{ old('action_type') == 'preventive_action' ? 'selected' : '' }}>Preventive Action</option>
                                    </select>
                                    @error('action_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="">Select Status</option>
                                        <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="verified" {{ old('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                        <option value="ineffective" {{ old('status') == 'ineffective' ? 'selected' : '' }}>Ineffective</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-3">
                                <label for="description" class="form-label">
                                    Description (English) <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          placeholder="Describe the corrective action in detail..."
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mt-3">
                                <label for="description_sw" class="form-label">
                                    Description (Swahili) <span class="text-muted">(Optional)</span>
                                </label>
                                <textarea class="form-control @error('description_sw') is-invalid @enderror" 
                                          id="description_sw" 
                                          name="description_sw" 
                                          rows="3"
                                          placeholder="Elezea hatua ya kurekebisha kwa Kiswahili...">{{ old('description_sw') }}</textarea>
                                @error('description_sw')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Responsible Person & Planning -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-user-tie me-2"></i>Responsible Person & Planning
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="responsible_person_id" class="form-label">
                                        Responsible Person <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('responsible_person_id') is-invalid @enderror" 
                                            id="responsible_person_id" 
                                            name="responsible_person_id" 
                                            required>
                                        <option value="">Select Responsible Person</option>
                                        <!-- In a real app, you would populate with users from database -->
                                        <option value="1" {{ old('responsible_person_id') == '1' ? 'selected' : '' }}>John Doe (Inspector)</option>
                                        <option value="2" {{ old('responsible_person_id') == '2' ? 'selected' : '' }}>Jane Smith (Supervisor)</option>
                                        <option value="3" {{ old('responsible_person_id') == '3' ? 'selected' : '' }}>Robert Johnson (Farmer)</option>
                                        <option value="4" {{ old('responsible_person_id') == '4' ? 'selected' : '' }}>Mary Williams (Quality Manager)</option>
                                    </select>
                                    @error('responsible_person_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="planned_date" class="form-label">
                                        Planned Completion Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('planned_date') is-invalid @enderror" 
                                           id="planned_date" 
                                           name="planned_date" 
                                           value="{{ old('planned_date', date('Y-m-d', strtotime('+7 days'))) }}"
                                           min="{{ date('Y-m-d') }}"
                                           required>
                                    @error('planned_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Set the target date for completing this action.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-clipboard-list me-2"></i>Additional Information
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }} selected>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="estimated_cost" class="form-label">Estimated Cost (TZS)</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="estimated_cost" 
                                           name="estimated_cost" 
                                           value="{{ old('estimated_cost') }}"
                                           min="0"
                                           step="1000">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label for="resources_required" class="form-label">Resources Required</label>
                                <textarea class="form-control" 
                                          id="resources_required" 
                                          name="resources_required" 
                                          rows="2"
                                          placeholder="List any resources needed (materials, equipment, personnel)...">{{ old('resources_required') }}</textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('corrective-actions.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Create Corrective Action
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
                <h5 class="modal-title">Corrective Action Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This is a preview of the corrective action details. Review before submitting.
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Finding</th>
                        <td id="previewFinding">-</td>
                    </tr>
                    <tr>
                        <th>Action Type</th>
                        <td id="previewType">-</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="previewStatus">-</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td id="previewDescription">-</td>
                    </tr>
                    <tr>
                        <th>Responsible Person</th>
                        <td id="previewResponsible">-</td>
                    </tr>
                    <tr>
                        <th>Planned Date</th>
                        <td id="previewPlannedDate">-</td>
                    </tr>
                    <tr>
                        <th>Priority</th>
                        <td id="previewPriority">Medium</td>
                    </tr>
                    <tr>
                        <th>Resources Required</th>
                        <td id="previewResources">None specified</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('actionForm').submit()">
                    <i class="fas fa-check me-1"></i> Confirm & Create
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const findingSelect = document.getElementById('finding_id');
        const typeSelect = document.getElementById('action_type');
        const statusSelect = document.getElementById('status');
        const descriptionTextarea = document.getElementById('description');
        const responsibleSelect = document.getElementById('responsible_person_id');
        const plannedDateInput = document.getElementById('planned_date');
        const prioritySelect = document.getElementById('priority');
        const resourcesTextarea = document.getElementById('resources_required');

        // Preview update function
        function updatePreview() {
            // Finding
            const findingOption = findingSelect.selectedOptions[0];
            document.getElementById('previewFinding').textContent = findingOption.text || '-';

            // Type
            document.getElementById('previewType').textContent = typeSelect.value ? typeSelect.options[typeSelect.selectedIndex].text : '-';

            // Status
            document.getElementById('previewStatus').textContent = statusSelect.value ? statusSelect.options[statusSelect.selectedIndex].text : '-';

            // Description
            document.getElementById('previewDescription').textContent = descriptionTextarea.value || '-';

            // Responsible Person
            document.getElementById('previewResponsible').textContent = responsibleSelect.value ? responsibleSelect.options[responsibleSelect.selectedIndex].text : '-';

            // Planned Date
            document.getElementById('previewPlannedDate').textContent = plannedDateInput.value || '-';

            // Priority
            document.getElementById('previewPriority').textContent = prioritySelect.value ? prioritySelect.options[prioritySelect.selectedIndex].text : '-';

            // Resources
            document.getElementById('previewResources').textContent = resourcesTextarea.value || 'None specified';
        }

        // Attach event listeners
        findingSelect.addEventListener('change', updatePreview);
        typeSelect.addEventListener('change', updatePreview);
        statusSelect.addEventListener('change', updatePreview);
        descriptionTextarea.addEventListener('input', updatePreview);
        responsibleSelect.addEventListener('change', updatePreview);
        plannedDateInput.addEventListener('change', updatePreview);
        prioritySelect.addEventListener('change', updatePreview);
        resourcesTextarea.addEventListener('input', updatePreview);

        // Initialize preview
        updatePreview();
    });
</script>
@endpush
@endsection