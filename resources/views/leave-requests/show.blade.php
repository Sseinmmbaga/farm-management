@extends('layouts.base')

@section('title', 'Leave Request #' . $leaveRequest->request_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i> Leave Request #{{ $leaveRequest->request_number }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('leave-requests.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Leave Details</h5>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Request Number:</th>
                                            <td><strong>{{ $leaveRequest->request_number }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Employee:</th>
                                            <td>
                                                {{ $leaveRequest->user->name ?? 'N/A' }}
                                                @if($leaveRequest->user)
                                                    <small class="text-muted d-block">({{ $leaveRequest->user->email }})</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Leave Type:</th>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $leaveRequest->type_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Start Date:</th>
                                            <td>{{ $leaveRequest->start_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>End Date:</th>
                                            <td>{{ $leaveRequest->end_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Duration:</th>
                                            <td><span class="badge bg-info">{{ $leaveRequest->days }} day(s)</span></td>
                                        </tr>
                                        <tr>
                                            <th>Created At:</th>
                                            <td>{{ $leaveRequest->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $leaveRequest->status_color }}">
                                                    {{ $leaveRequest->status_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($leaveRequest->approved_by)
                                        <tr>
                                            <th>Approved By:</th>
                                            <td>{{ $leaveRequest->approvedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Approved At:</th>
                                            <td>{{ $leaveRequest->approved_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($leaveRequest->rejected_by)
                                        <tr>
                                            <th>Rejected By:</th>
                                            <td>{{ $leaveRequest->rejectedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Rejected At:</th>
                                            <td>{{ $leaveRequest->rejected_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($leaveRequest->cancelled_by)
                                        <tr>
                                            <th>Cancelled By:</th>
                                            <td>{{ $leaveRequest->cancelledBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cancelled At:</th>
                                            <td>{{ $leaveRequest->cancelled_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Medical Certificate:</th>
                                            <td>
                                                @if($leaveRequest->has_sick_sheet)
                                                    <a href="{{ asset('storage/' . $leaveRequest->sick_sheet_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file-medical"></i> View Certificate
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Leave Balance Before:</th>
                                            <td>{{ $leaveRequest->leave_balance_before ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Leave Balance After:</th>
                                            <td>{{ $leaveRequest->leave_balance_after ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h6>Reason for Leave</h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $leaveRequest->reason }}
                                </div>
                            </div>

                            @if($leaveRequest->approval_notes)
                                <div class="mt-4">
                                    <h6>Approval Notes</h6>
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        {{ $leaveRequest->approval_notes }}
                                    </div>
                                </div>
                            @endif

                            @if($leaveRequest->rejection_reason)
                                <div class="mt-4">
                                    <h6>Rejection Reason</h6>
                                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                                        {{ $leaveRequest->rejection_reason }}
                                    </div>
                                </div>
                            @endif

                            @if($leaveRequest->cancellation_reason)
                                <div class="mt-4">
                                    <h6>Cancellation Reason</h6>
                                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                                        {{ $leaveRequest->cancellation_reason }}
                                    </div>
                                </div>
                            @endif

                            @if($leaveRequest->notes)
                                <div class="mt-4">
                                    <h6>Additional Notes</h6>
                                    <div class="p-3 bg-light rounded">
                                        {{ $leaveRequest->notes }}
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
                                        @if($leaveRequest->is_pending)
                                            @can('approve', $leaveRequest)
                                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                                    <i class="fas fa-check-circle me-1"></i> Approve Leave
                                                </button>
                                            @endcan
                                            @can('reject', $leaveRequest)
                                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                    <i class="fas fa-times-circle me-1"></i> Reject Leave
                                                </button>
                                            @endcan
                                            @can('update', $leaveRequest)
                                                <a href="{{ route('leave-requests.edit', $leaveRequest) }}" class="btn btn-outline-warning">
                                                    <i class="fas fa-edit me-1"></i> Edit Request
                                                </a>
                                            @endcan
                                            @can('delete', $leaveRequest)
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmDelete('{{ route('leave-requests.destroy', $leaveRequest) }}', '{{ $leaveRequest->request_number }}')">
                                                    <i class="fas fa-trash me-1"></i> Delete Request
                                                </button>
                                            @endcan
                                        @endif

                                        @if($leaveRequest->is_approved)
                                            @can('cancel', $leaveRequest)
                                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                                    <i class="fas fa-ban me-1"></i> Cancel Leave
                                                </button>
                                            @endcan
                                            @can('markAsTaken', $leaveRequest)
                                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#takenModal">
                                                    <i class="fas fa-calendar-check me-1"></i> Mark as Taken
                                                </button>
                                            @endcan
                                        @endif

                                        @if($leaveRequest->is_pending || $leaveRequest->is_approved)
                                            @can('cancel', $leaveRequest)
                                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                                    <i class="fas fa-ban me-1"></i> Cancel Leave
                                                </button>
                                            @endcan
                                        @endif
                                    </div>

                                    <hr class="my-3">

                                    <div class="text-center">
                                        <small class="text-muted">Quick Links</small>
                                        <div class="mt-2">
                                            <a href="{{ route('users.show', $leaveRequest->user_id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-user me-1"></i> View Employee
                                            </a>
                                            <a href="{{ route('leave-requests.create') }}" class="btn btn-sm btn-outline-secondary">
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

<!-- Approve Modal -->
@if($leaveRequest->is_pending)
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Leave Request #{{ $leaveRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest) }}">
                @csrf
                <div class="modal-body">
                    <p>You are about to approve this leave request. This will deduct <strong>{{ $leaveRequest->days }} day(s)</strong> from the employee's leave balance.</p>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3" 
                                  placeholder="Add any notes for the employee..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Reject Modal -->
@if($leaveRequest->is_pending)
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Leave Request #{{ $leaveRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('leave-requests.reject', $leaveRequest) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required 
                                  placeholder="Provide a clear reason for rejecting this leave request..."></textarea>
                        <small class="text-muted">This reason will be visible to the employee.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Cancel Modal -->
@if(in_array($leaveRequest->status, ['pending', 'approved']))
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Leave Request #{{ $leaveRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('leave-requests.cancel', $leaveRequest) }}">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to cancel this leave request? This will change its status to <strong>Cancelled</strong>.</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Cancellation Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4" required 
                                  placeholder="Provide a reason for cancellation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Cancel Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Mark as Taken Modal -->
@if($leaveRequest->is_approved)
<div class="modal fade" id="takenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Leave as Taken #{{ $leaveRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('leave-requests.markAsTaken', $leaveRequest) }}">
                @csrf
                <div class="modal-body">
                    <p>Confirm that the employee has taken the approved leave from <strong>{{ $leaveRequest->start_date->format('d/m/Y') }}</strong> to <strong>{{ $leaveRequest->end_date->format('d/m/Y') }}</strong>.</p>
                    <p class="text-muted">This will update the status to <strong>Taken</strong> and cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Mark as Taken</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete leave request <strong id="requestNumber"></strong>?</p>
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