@extends('layouts.base')

@section('title', 'Schedule Internal Inspection')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i> Schedule Internal Inspection
                        </h4>
                        <a href="{{ route('inspections.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Inspections
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('inspections.store') }}" id="inspectionForm">
                        @csrf

                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="farmer_id" class="form-label">
                                        Farmer <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('farmer_id') is-invalid @enderror" 
                                            id="farmer_id" 
                                            name="farmer_id" 
                                            required>
                                        <option value="">Select Farmer</option>
                                        @foreach($farmers as $farmer)
                                            <option value="{{ $farmer->id }}" 
                                                {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                                {{ $farmer->first_name }} {{ $farmer->last_name }}
                                                ({{ $farmer->registration_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('farmer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="checklist_id" class="form-label">
                                        Inspection Checklist <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('checklist_id') is-invalid @enderror" 
                                            id="checklist_id" 
                                            name="checklist_id" 
                                            required>
                                        <option value="">Select Checklist</option>
                                        @foreach($checklists as $checklist)
                                            <option value="{{ $checklist->id }}" 
                                                {{ old('checklist_id') == $checklist->id ? 'selected' : '' }}>
                                                {{ $checklist->name }} ({{ $checklist->code }})
                                                - {{ $checklist->items_count ?? 0 }} items
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('checklist_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Scheduling -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>Scheduling
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="scheduled_date" class="form-label">
                                        Scheduled Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('scheduled_date') is-invalid @enderror" 
                                           id="scheduled_date" 
                                           name="scheduled_date" 
                                           value="{{ old('scheduled_date', date('Y-m-d', strtotime('+1 day'))) }}" 
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           required>
                                    <div class="form-text">Inspection must be scheduled at least one day in advance.</div>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="scheduled_time" class="form-label">Preferred Time</label>
                                    <input type="time" 
                                           class="form-control" 
                                           id="scheduled_time" 
                                           name="scheduled_time" 
                                           value="{{ old('scheduled_time', '09:00') }}">
                                    <div class="form-text">Approximate start time (optional).</div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Additional Information
                            </h5>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes / Special Instructions</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Provide any specific instructions for the inspector or farmer.
                                </div>
                            </div>
                        </div>

                        <!-- Risk Assessment (Optional) -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>Risk Assessment (Optional)
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Expected Risk Level</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="risk_level" id="risk_low" value="low" checked>
                                        <label class="form-check-label" for="risk_low">
                                            <span class="badge bg-success">Low</span> - Routine inspection
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="risk_level" id="risk_medium" value="medium">
                                        <label class="form-check-label" for="risk_medium">
                                            <span class="badge bg-warning">Medium</span> - Previous minor issues
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="risk_level" id="risk_high" value="high">
                                        <label class="form-check-label" for="risk_high">
                                            <span class="badge bg-danger">High</span> - Previous major non-conformities
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label for="risk_notes" class="form-label">Risk Notes</label>
                                    <textarea class="form-control" 
                                              id="risk_notes" 
                                              name="risk_notes" 
                                              rows="3"
                                              placeholder="Any specific risks to be aware of during inspection...">{{ old('risk_notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('inspections.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus me-1"></i> Schedule Inspection
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
                <h5 class="modal-title">Inspection Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This is a preview of the inspection details. Review before scheduling.
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Farmer</th>
                        <td id="previewFarmer">-</td>
                    </tr>
                    <tr>
                        <th>Checklist</th>
                        <td id="previewChecklist">-</td>
                    </tr>
                    <tr>
                        <th>Scheduled Date</th>
                        <td id="previewDate">-</td>
                    </tr>
                    <tr>
                        <th>Time</th>
                        <td id="previewTime">-</td>
                    </tr>
                    <tr>
                        <th>Risk Level</th>
                        <td id="previewRisk">Low</td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td id="previewNotes">-</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('inspectionForm').submit()">
                    <i class="fas fa-check me-1"></i> Confirm & Schedule
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const farmerSelect = document.getElementById('farmer_id');
        const checklistSelect = document.getElementById('checklist_id');
        const dateInput = document.getElementById('scheduled_date');
        const timeInput = document.getElementById('scheduled_time');
        const notesTextarea = document.getElementById('notes');
        const riskRadios = document.querySelectorAll('input[name="risk_level"]');
        const riskNotes = document.getElementById('risk_notes');

        function updatePreview() {
            // Farmer
            const farmerOption = farmerSelect.selectedOptions[0];
            document.getElementById('previewFarmer').textContent = farmerOption.text || '-';

            // Checklist
            const checklistOption = checklistSelect.selectedOptions[0];
            document.getElementById('previewChecklist').textContent = checklistOption.text || '-';

            // Date
            document.getElementById('previewDate').textContent = dateInput.value || '-';

            // Time
            document.getElementById('previewTime').textContent = timeInput.value || 'Not specified';

            // Risk Level
            let selectedRisk = 'Low';
            for (const radio of riskRadios) {
                if (radio.checked) {
                    selectedRisk = radio.value;
                    break;
                }
            }
            document.getElementById('previewRisk').textContent = selectedRisk.charAt(0).toUpperCase() + selectedRisk.slice(1);

            // Notes
            document.getElementById('previewNotes').textContent = notesTextarea.value || 'None';
        }

        // Attach event listeners
        farmerSelect.addEventListener('change', updatePreview);
        checklistSelect.addEventListener('change', updatePreview);
        dateInput.addEventListener('change', updatePreview);
        timeInput.addEventListener('change', updatePreview);
        notesTextarea.addEventListener('input', updatePreview);
        riskRadios.forEach(radio => radio.addEventListener('change', updatePreview));
        riskNotes.addEventListener('input', updatePreview);

        // Initialize preview
        updatePreview();
    });
</script>
@endpush
@endsection