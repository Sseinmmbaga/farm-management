@extends('layouts.base')

@section('title', 'Edit Inspection')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Inspection: {{ $inspection->inspection_number }}
                        </h4>
                        <a href="{{ route('inspections.show', $inspection) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-eye me-1"></i> View Details
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('inspections.update', $inspection) }}" id="editForm">
                        @csrf
                        @method('PUT')

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
                                                {{ old('farmer_id', $inspection->farmer_id) == $farmer->id ? 'selected' : '' }}>
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
                                                {{ old('checklist_id', $inspection->checklist_id) == $checklist->id ? 'selected' : '' }}>
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
                                           value="{{ old('scheduled_date', $inspection->scheduled_date ? $inspection->scheduled_date->format('Y-m-d') : '') }}" 
                                           required>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Current Status</label>
                                    <div class="form-control bg-light">
                                        <span class="badge bg-{{ $inspection->status == 'scheduled' ? 'info' : ($inspection->status == 'in_progress' ? 'warning' : ($inspection->status == 'completed' ? 'success' : 'secondary')) }}">
                                            {{ $inspection->status_label }}
                                        </span>
                                        @if($inspection->isOverdue)
                                            <span class="badge bg-danger ms-2">Overdue</span>
                                        @endif
                                    </div>
                                    <div class="form-text">
                                        Status cannot be changed from this form. Use action buttons on inspection details page.
                                    </div>
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
                                          rows="3">{{ old('notes', $inspection->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Inspection Results (Read-only if completed) -->
                        @if($inspection->status == 'completed')
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-chart-bar me-2"></i>Inspection Results (Completed)
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Result</label>
                                    <div class="form-control bg-light">
                                        @if($inspection->result == 'passed')
                                            <span class="badge bg-success">Passed</span>
                                        @elseif($inspection->result == 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @elseif($inspection->result == 'conditional')
                                            <span class="badge bg-warning">Conditional</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Score</label>
                                    <div class="form-control bg-light">
                                        {{ $inspection->percentage_score ?? 'N/A' }}%
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Completion Date</label>
                                    <div class="form-control bg-light">
                                        {{ $inspection->completed_at ? $inspection->completed_at->format('M d, Y') : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('inspections.show', $inspection) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i> Update Inspection
                                    </button>
                                    @if($inspection->status == 'scheduled')
                                        <a href="{{ route('inspections.start', $inspection) }}" 
                                           class="btn btn-success"
                                           onclick="return confirm('Are you sure you want to start this inspection now?')">
                                            <i class="fas fa-play me-1"></i> Start Inspection
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
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
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete inspection <strong>{{ $inspection->inspection_number }}</strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    This action cannot be undone. All associated findings and responses will also be deleted.
                </p>
                <div class="mb-3">
                    <label for="delete_reason" class="form-label">Reason for Deletion (Optional)</label>
                    <textarea class="form-control" id="delete_reason" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('inspections.destroy', $inspection) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="reason" id="deleteReasonInput">
                    <button type="submit" class="btn btn-danger">Delete Inspection</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete modal reason sync
        const deleteModal = document.getElementById('deleteModal');
        const deleteReasonTextarea = document.getElementById('delete_reason');
        const deleteReasonInput = document.getElementById('deleteReasonInput');
        
        deleteModal.addEventListener('show.bs.modal', function () {
            deleteReasonTextarea.addEventListener('input', function() {
                deleteReasonInput.value = this.value;
            });
        });
    });
</script>
@endpush
@endsection