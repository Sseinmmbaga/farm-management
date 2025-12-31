@extends('layouts.base')

@section('title', 'Edit Service Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-headset me-2"></i> Edit Service Request
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('service-requests.show', $serviceRequest) }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to Request
                            </a>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('service-requests.update', $serviceRequest) }}" id="serviceRequestForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="farmer_id" class="form-label">Farmer <span class="text-danger">*</span></label>
                                    <select name="farmer_id" id="farmer_id" class="form-select" required>
                                        <option value="">Select Farmer</option>
                                        @foreach($farmers as $farmer)
                                            <option value="{{ $farmer->id }}" {{ old('farmer_id', $serviceRequest->farmer_id) == $farmer->id ? 'selected' : '' }}>
                                                {{ $farmer->full_name }} ({{ $farmer->registration_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('farmer_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Request Type <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        @foreach($types as $key => $label)
                                            <option value="{{ $key }}" {{ old('type', $serviceRequest->type) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                        value="{{ old('title', $serviceRequest->title) }}" 
                                        placeholder="Brief summary of the request" required>
                                    @error('title')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-select" required>
                                        <option value="">Select Priority</option>
                                        @foreach($priorities as $key => $label)
                                            <option value="{{ $key }}" {{ old('priority', $serviceRequest->priority) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('priority')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $serviceRequest->description) }}</textarea>
                            <small class="text-muted">Provide detailed information about the service request.</small>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select" required>
                                        @foreach($statuses as $key => $label)
                                            <option value="{{ $key }}" {{ old('status', $serviceRequest->status) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="assigned_to" class="form-label">Assign To (Optional)</label>
                                    <select name="assigned_to" id="assigned_to" class="form-select">
                                        <option value="">Unassigned</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to', $serviceRequest->assigned_to) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="resolved_notes" class="form-label">Resolution Notes (Optional)</label>
                                    <textarea class="form-control" id="resolved_notes" name="resolved_notes" rows="2">{{ old('resolved_notes', $serviceRequest->resolved_notes) }}</textarea>
                                    @error('resolved_notes')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $serviceRequest->notes) }}</textarea>
                            @error('notes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Request #<strong>{{ $serviceRequest->request_number }}</strong> created on {{ $serviceRequest->created_at->format('d/m/Y') }} by {{ $serviceRequest->requestedBy->name ?? 'Unknown' }}.
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <div>
                                @if(in_array($serviceRequest->status, ['open', 'cancelled']))
                                    <button type="button" 
                                            class="btn btn-danger me-2" 
                                            onclick="confirmDelete('{{ route('service-requests.destroy', $serviceRequest) }}', '{{ $serviceRequest->request_number }}')">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                @endif
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Update Service Request
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal (same as index) -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete request <strong id="requestNumber"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, requestNumber) {
        document.getElementById('requestNumber').textContent = requestNumber;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endpush
@endsection