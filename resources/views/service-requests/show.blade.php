@extends('layouts.base')

@section('title', 'Service Request #' . $serviceRequest->request_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-headset me-2"></i> Service Request #{{ $serviceRequest->request_number }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('service-requests.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">{{ $serviceRequest->title }}</h5>
                            <p class="text-muted">{{ $serviceRequest->description }}</p>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Request Number:</th>
                                            <td><strong>{{ $serviceRequest->request_number }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Farmer:</th>
                                            <td>
                                                {{ $serviceRequest->farmer->full_name ?? 'N/A' }}
                                                @if($serviceRequest->farmer)
                                                    <small class="text-muted d-block">({{ $serviceRequest->farmer->registration_number }})</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Requested By:</th>
                                            <td>{{ $serviceRequest->requestedBy->name ?? 'Unknown' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At:</th>
                                            <td>{{ $serviceRequest->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated:</th>
                                            <td>{{ $serviceRequest->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Type:</th>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $serviceRequest->type_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $serviceRequest->status_color }}">
                                                    {{ $serviceRequest->status_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Priority:</th>
                                            <td>
                                                <span class="badge bg-{{ $serviceRequest->priority_color }}">
                                                    {{ $serviceRequest->priority_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Assigned To:</th>
                                            <td>
                                                {{ $serviceRequest->assignedTo->name ?? 'Unassigned' }}
                                                @if($serviceRequest->assignedTo)
                                                    <small class="text-muted d-block">({{ $serviceRequest->assignedTo->email }})</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Resolved At:</th>
                                            <td>{{ $serviceRequest->resolved_at ? $serviceRequest->resolved_at->format('d/m/Y H:i') : 'Not resolved' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($serviceRequest->notes)
                                <div class="mt-4">
                                    <h6>Additional Notes</h6>
                                    <div class="p-3 bg-light rounded">
                                        {{ $serviceRequest->notes }}
                                    </div>
                                </div>
                            @endif

                            @if($serviceRequest->resolved_notes)
                                <div class="mt-4">
                                    <h6>Resolution Notes</h6>
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        {{ $serviceRequest->resolved_notes }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        @if(in_array($serviceRequest->status, ['open', 'in_progress']))
                                            <a href="{{ route('service-requests.edit', $serviceRequest) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-edit me-1"></i> Edit Request
                                            </a>
                                        @endif

                                        @if($serviceRequest->status === 'open')
                                            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#assignModal">
                                                <i class="fas fa-user-check me-1"></i> Assign Request
                                            </button>
                                        @endif

                                        @if($serviceRequest->status === 'in_progress')
                                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#resolveModal">
                                                <i class="fas fa-check-circle me-1"></i> Mark as Resolved
                                            </button>
                                        @endif

                                        @if(in_array($serviceRequest->status, ['open', 'in_progress']))
                                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#closeModal">
                                                <i class="fas fa-lock me-1"></i> Close Request
                                            </button>
                                        @endif

                                        @if(in_array($serviceRequest->status, ['open', 'in_progress']))
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                                <i class="fas fa-ban me-1"></i> Cancel Request
                                            </button>
                                        @endif

                                        @if(in_array($serviceRequest->status, ['open', 'cancelled']))
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmDelete('{{ route('service-requests.destroy', $serviceRequest) }}', '{{ $serviceRequest->request_number }}')">
                                                <i class="fas fa-trash me-1"></i> Delete Request
                                            </button>
                                        @endif
                                    </div>

                                    <hr class="my-3">

                                    <div class="text-center">
                                        <small class="text-muted">Quick Links</small>
                                        <div class="mt-2">
                                            <a href="{{ route('farmers.show', $serviceRequest->farmer_id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-user me-1"></i> View Farmer
                                            </a>
                                            <a href="{{ route('service-requests.create') }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-plus me-1"></i> New Request
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for actions -->
@if($serviceRequest->status === 'open')
<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Request #{{ $serviceRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('service-requests.assign', $serviceRequest) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assign_user" class="form-label">Assign to User</label>
                        <select name="assigned_to" id="assign_user" class="form-select" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assign_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="assign_notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($serviceRequest->status === 'in_progress')
<!-- Resolve Modal -->
<div class="modal fade" id="resolveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resolve Request #{{ $serviceRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('service-requests.resolve', $serviceRequest) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="resolved_notes" class="form-label">Resolution Notes <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="resolved_notes" name="resolved_notes" rows="4" required 
                                  placeholder="Describe how the request was resolved..."></textarea>
                        <small class="text-muted">This will mark the request as resolved.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Resolved</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Close Modal -->
<div class="modal fade" id="closeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Close Request #{{ $serviceRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('service-requests.close', $serviceRequest) }}">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to close this request? This will change its status to <strong>Closed</strong>.</p>
                    <p class="text-muted">Closed requests cannot be reopened.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-secondary">Close Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Request #{{ $serviceRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('service-requests.cancel', $serviceRequest) }}">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to cancel this request? This will change its status to <strong>Cancelled</strong>.</p>
                    <p class="text-muted">Cancelled requests cannot be reopened.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Cancel Request</button>
                </div>
            </form>
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