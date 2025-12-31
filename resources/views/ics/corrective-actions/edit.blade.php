@extends('layouts.base')

@section('title', 'Edit Corrective Action: ' . $correctiveAction->action_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Corrective Action: {{ $correctiveAction->action_number }}
                        </h4>
                        <a href="{{ route('corrective-actions.show', $correctiveAction) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-eye me-1"></i> View Details
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('corrective-actions.update', $correctiveAction) }}" id="editForm">
                        @csrf
                        @method('PUT')

                        <!-- Finding Reference (Read-only) -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>Finding Reference
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Finding</label>
                                    <div class="form-control bg-light">
                                        <a href="{{ route('findings.show', $correctiveAction->finding) }}" class="text-decoration-none">
                                            <strong>{{ $correctiveAction->finding->finding_number }}</strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $correctiveAction->finding->description }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Inspection</label>
                                    <div class="form-control bg-light">
                                        <a href="{{ route('inspections.show', $correctiveAction->finding->inspection) }}" class="text-decoration-none">
                                            {{ $correctiveAction->finding->inspection->inspection_number }}
                                        </a>
                                        <br>
                                        <small class="text-muted">
                                            {{ $correctiveAction->finding->inspection->farmer->first_name ?? '' }} 
                                            {{ $correctiveAction->finding->inspection->farmer->last_name ?? '' }}
                                        </small>
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
                                        <option value="correction" {{ old('action_type', $correctiveAction->action_type) == 'correction' ? 'selected' : '' }}>Correction</option>
                                        <option value="corrective_action" {{ old('action_type', $correctiveAction->action_type) == 'corrective_action' ? 'selected' : '' }}>Corrective Action</option>
                                        <option value="preventive_action" {{ old('action_type', $correctiveAction->action_type) == 'preventive_action' ? 'selected' : '' }}>Preventive Action</option>
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
                                        <option value="planned" {{ old('status', $correctiveAction->status) == 'planned' ? 'selected' : '' }}>Planned</option>
                                        <option value="in_progress" {{ old('status', $correctiveAction->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status', $correctiveAction->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="verified" {{ old('status', $correctiveAction->status) == 'verified' ? 'selected' : '' }}>Verified</option>
                                        <option value="ineffective" {{ old('status', $correctiveAction->status) == 'ineffective' ? 'selected' : '' }}>Ineffective</option>
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
                                          required>{{ old('description', $correctiveAction->description) }}</textarea>
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
                                          rows="3">{{ old('description_sw', $correctiveAction->description_sw) }}</textarea>
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
                                        <option value="1" {{ old('responsible_person_id', $correctiveAction->responsible_person_id) == '1' ? 'selected' : '' }}>John Doe (Inspector)</option>
                                        <option value="2" {{ old('responsible_person_id', $correctiveAction->responsible_person_id) == '2' ? 'selected' : '' }}>Jane Smith (Supervisor)</option>
                                        <option value="3" {{ old('responsible_person_id', $correctiveAction->responsible_person_id) == '3' ? 'selected' : '' }}>Robert Johnson (Farmer)</option>
                                        <option value="4" {{ old('responsible_person_id', $correctiveAction->responsible_person_id) == '4' ? 'selected' : '' }}>Mary Williams (Quality Manager)</option>
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
                                           value="{{ old('planned_date', $correctiveAction->planned_date ? $correctiveAction->planned_date->format('Y-m-d') : '') }}"
                                           required>
                                    @error('planned_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                        <option value="low" {{ old('priority', $correctiveAction->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $correctiveAction->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $correctiveAction->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ old('priority', $correctiveAction->priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="estimated_cost" class="form-label">Estimated Cost (TZS)</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="estimated_cost" 
                                           name="estimated_cost" 
                                           value="{{ old('estimated_cost', $correctiveAction->estimated_cost) }}"
                                           min="0"
                                           step="1000">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label for="resources_required" class="form-label">Resources Required</label>
                                <textarea class="form-control" 
                                          id="resources_required" 
                                          name="resources_required" 
                                          rows="2">{{ old('resources_required', $correctiveAction->resources_required) }}</textarea>
                            </div>
                        </div>

                        <!-- Verification Notes (if applicable) -->
                        @if($correctiveAction->status == 'completed' || $correctiveAction->status == 'verified')
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-clipboard-check me-2"></i>Verification Notes
                            </h5>
                            <div class="mb-3">
                                <label for="verification_notes" class="form-label">Verification Notes</label>
                                <textarea class="form-control @error('verification_notes') is-invalid @enderror" 
                                          id="verification_notes" 
                                          name="verification_notes" 
                                          rows="3">{{ old('verification_notes', $correctiveAction->verification_notes) }}</textarea>
                                @error('verification_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('corrective-actions.show', $correctiveAction) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i> Update Corrective Action
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete corrective action <strong>{{ $correctiveAction->action_number }}</strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('corrective-actions.destroy', $correctiveAction) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Action</button>
                </form>
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
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This is a preview of the updated corrective action details. Review before saving.
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Finding</th>
                        <td id="previewFinding">{{ $correctiveAction->finding->finding_number }}</td>
                    </tr>
                    <tr>
                        <th>Action Type</th>
                        <td id="previewType">{{ ucfirst(str_replace('_', ' ', $correctiveAction->action_type)) }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="previewStatus">{{ ucfirst($correctiveAction->status) }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td id="previewDescription">{{ $correctiveAction->description }}</td>
                    </tr>
                    <tr>
                        <th>Responsible Person</th>
                        <td id="previewResponsible">{{ $correctiveAction->responsiblePerson->name ?? 'Unassigned' }}</td>
                    </tr>
                    <tr>
                        <th>Planned Date</th>
                        <td id="previewPlannedDate">{{ $correctiveAction->planned_date ? $correctiveAction->planned_date->format('M d, Y') : 'Not set' }}</td>
                    </tr>
                    <tr>
                        <th>Priority</th>
                        <td id="previewPriority">{{ ucfirst($correctiveAction->priority ?? 'medium') }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="document.getElementById('editForm').submit()">
                    <i class="fas fa-check me-1"></i> Confirm & Update
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('action_type');
        const statusSelect = document.getElementById('status');
        const descriptionTextarea = document.getElementById('description');
        const responsibleSelect = document.getElementById('responsible_person_id');
        const plannedDateInput = document.getElementById('planned_date');
        const prioritySelect = document.getElementById('priority');

        function updatePreview() {
            document.getElementById('previewType').textContent = typeSelect.value ? typeSelect.options[typeSelect.selectedIndex].text : '';
            document.getElementById('previewStatus').textContent = statusSelect.value ? statusSelect.options[statusSelect.selectedIndex].text : '';
            document.getElementById('previewDescription').textContent = descriptionTextarea.value;
            document.getElementById('previewResponsible').textContent = responsibleSelect.value ? responsibleSelect.options[responsibleSelect.selectedIndex].text : '';
            document.getElementById('previewPlannedDate').textContent = plannedDateInput.value || 'Not set';
            document.getElementById('previewPriority').textContent = prioritySelect.value ? prioritySelect.options[prioritySelect.selectedIndex].text : '';
        }

        // Attach event listeners
        typeSelect.addEventListener('change', updatePreview);
        statusSelect.addEventListener('change', updatePreview);
        descriptionTextarea.addEventListener('input', updatePreview);
        responsibleSelect.addEventListener('change', updatePreview);
        plannedDateInput.addEventListener('change', updatePreview);
        prioritySelect.addEventListener('change', updatePreview);

        // Initialize preview
        updatePreview();
    });
</script>
@endpush
@endsection