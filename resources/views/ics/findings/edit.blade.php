@extends('layouts.base')

@section('title', 'Edit Finding: ' . $finding->finding_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Finding: {{ $finding->finding_number }}
                        </h4>
                        <a href="{{ route('findings.show', $finding) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-eye me-1"></i> View Details
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('findings.update', $finding) }}" id="editForm">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
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
                                        <option value="documentation" {{ old('finding_type', $finding->finding_type) == 'documentation' ? 'selected' : '' }}>Documentation Issue</option>
                                        <option value="procedure" {{ old('finding_type', $finding->finding_type) == 'procedure' ? 'selected' : '' }}>Procedure Non-Compliance</option>
                                        <option value="safety" {{ old('finding_type', $finding->finding_type) == 'safety' ? 'selected' : '' }}>Safety Violation</option>
                                        <option value="quality" {{ old('finding_type', $finding->finding_type) == 'quality' ? 'selected' : '' }}>Quality Defect</option>
                                        <option value="environmental" {{ old('finding_type', $finding->finding_type) == 'environmental' ? 'selected' : '' }}>Environmental Concern</option>
                                        <option value="social" {{ old('finding_type', $finding->finding_type) == 'social' ? 'selected' : '' }}>Social Compliance Issue</option>
                                        <option value="other" {{ old('finding_type', $finding->finding_type) == 'other' ? 'selected' : '' }}>Other</option>
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
                                        <option value="observation" {{ old('severity', $finding->severity) == 'observation' ? 'selected' : '' }}>Observation</option>
                                        <option value="minor" {{ old('severity', $finding->severity) == 'minor' ? 'selected' : '' }}>Minor</option>
                                        <option value="major" {{ old('severity', $finding->severity) == 'major' ? 'selected' : '' }}>Major</option>
                                        <option value="critical" {{ old('severity', $finding->severity) == 'critical' ? 'selected' : '' }}>Critical</option>
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
                                          required>{{ old('description', $finding->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                              rows="3">{{ old('evidence', $finding->evidence) }}</textarea>
                                    @error('evidence')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="recommendation" class="form-label">Recommendation</label>
                                    <textarea class="form-control @error('recommendation') is-invalid @enderror" 
                                              id="recommendation" 
                                              name="recommendation" 
                                              rows="3">{{ old('recommendation', $finding->recommendation) }}</textarea>
                                    @error('recommendation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status & Due Date -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-calendar-day me-2"></i>Status & Due Date
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="status" class="form-label">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="open" {{ old('status', $finding->status) == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="in_progress" {{ old('status', $finding->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="resolved" {{ old('status', $finding->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="closed" {{ old('status', $finding->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="due_date" class="form-label">Due Date for Resolution</label>
                                    <input type="date" 
                                           class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" 
                                           name="due_date" 
                                           value="{{ old('due_date', $finding->due_date ? $finding->due_date->format('Y-m-d') : '') }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Inspection Reference (Read-only) -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-clipboard-check me-2"></i>Inspection Reference
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Inspection</label>
                                    <div class="form-control bg-light">
                                        <a href="{{ route('inspections.show', $finding->inspection) }}" class="text-decoration-none">
                                            <strong>{{ $finding->inspection->inspection_number }}</strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $finding->inspection->checklist->name ?? 'N/A' }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Farmer</label>
                                    <div class="form-control bg-light">
                                        <strong>{{ $finding->inspection->farmer->first_name ?? '' }} {{ $finding->inspection->farmer->last_name ?? '' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $finding->inspection->farmer->registration_number ?? '' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('findings.show', $finding) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i> Update Finding
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
                <p>Are you sure you want to delete finding <strong>{{ $finding->finding_number }}</strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    This action cannot be undone. All associated corrective actions will also be deleted.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('findings.destroy', $finding) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Finding</button>
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
                <h5 class="modal-title">Finding Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This is a preview of the updated finding details. Review before saving.
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Finding Type</th>
                        <td id="previewType">{{ $finding->finding_type }}</td>
                    </tr>
                    <tr>
                        <th>Severity</th>
                        <td id="previewSeverity">{{ ucfirst($finding->severity) }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td id="previewDescription">{{ $finding->description }}</td>
                    </tr>
                    <tr>
                        <th>Evidence</th>
                        <td id="previewEvidence">{{ $finding->evidence ?: 'None' }}</td>
                    </tr>
                    <tr>
                        <th>Recommendation</th>
                        <td id="previewRecommendation">{{ $finding->recommendation ?: 'None' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="previewStatus">{{ ucfirst($finding->status) }}</td>
                    </tr>
                    <tr>
                        <th>Due Date</th>
                        <td id="previewDueDate">{{ $finding->due_date ? $finding->due_date->format('M d, Y') : 'Not set' }}</td>
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
        const typeSelect = document.getElementById('finding_type');
        const severitySelect = document.getElementById('severity');
        const descriptionTextarea = document.getElementById('description');
        const evidenceTextarea = document.getElementById('evidence');
        const recommendationTextarea = document.getElementById('recommendation');
        const statusSelect = document.getElementById('status');
        const dueDateInput = document.getElementById('due_date');

        function updatePreview() {
            document.getElementById('previewType').textContent = typeSelect.value ? typeSelect.options[typeSelect.selectedIndex].text : '';
            document.getElementById('previewSeverity').textContent = severitySelect.value ? severitySelect.options[severitySelect.selectedIndex].text : '';
            document.getElementById('previewDescription').textContent = descriptionTextarea.value;
            document.getElementById('previewEvidence').textContent = evidenceTextarea.value || 'None';
            document.getElementById('previewRecommendation').textContent = recommendationTextarea.value || 'None';
            document.getElementById('previewStatus').textContent = statusSelect.value ? statusSelect.options[statusSelect.selectedIndex].text : '';
            document.getElementById('previewDueDate').textContent = dueDateInput.value || 'Not set';
        }

        // Attach event listeners
        typeSelect.addEventListener('change', updatePreview);
        severitySelect.addEventListener('change', updatePreview);
        descriptionTextarea.addEventListener('input', updatePreview);
        evidenceTextarea.addEventListener('input', updatePreview);
        recommendationTextarea.addEventListener('input', updatePreview);
        statusSelect.addEventListener('change', updatePreview);
        dueDateInput.addEventListener('change', updatePreview);

        // Initialize preview
        updatePreview();
    });
</script>
@endpush
@endsection