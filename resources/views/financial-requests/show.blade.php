@extends('layouts.base')

@section('title', 'Financial Request #' . $financialRequest->request_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i> Financial Request #{{ $financialRequest->request_number }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('financial-requests.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Financial Request Details</h5>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Request Number:</th>
                                            <td><strong>{{ $financialRequest->request_number }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Employee:</th>
                                            <td>
                                                {{ $financialRequest->user->name ?? 'N/A' }}
                                                @if($financialRequest->user)
                                                    <small class="text-muted d-block">({{ $financialRequest->user->email }})</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Request Type:</th>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $financialRequest->type_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Amount:</th>
                                            <td>
                                                <strong class="text-success">{{ $financialRequest->formatted_amount }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Currency:</th>
                                            <td>{{ $financialRequest->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At:</th>
                                            <td>{{ $financialRequest->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated:</th>
                                            <td>{{ $financialRequest->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $financialRequest->status_color }}">
                                                    {{ $financialRequest->status_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($financialRequest->approved_by)
                                        <tr>
                                            <th>Approved By:</th>
                                            <td>{{ $financialRequest->approvedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Approved At:</th>
                                            <td>{{ $financialRequest->approved_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($financialRequest->rejected_by)
                                        <tr>
                                            <th>Rejected By:</th>
                                            <td>{{ $financialRequest->rejectedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Rejected At:</th>
                                            <td>{{ $financialRequest->rejected_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($financialRequest->disbursed_by)
                                        <tr>
                                            <th>Disbursed By:</th>
                                            <td>{{ $financialRequest->disbursedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Disbursed At:</th>
                                            <td>{{ $financialRequest->disbursed_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Total Repayment:</th>
                                            <td>
                                                @if($financialRequest->total_repayment_amount)
                                                    {{ $financialRequest->currency_symbol }} {{ number_format($financialRequest->total_repayment_amount, 2) }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Amount Repaid:</th>
                                            <td>
                                                <strong class="{{ $financialRequest->is_fully_repaid ? 'text-success' : 'text-warning' }}">
                                                    {{ $financialRequest->currency_symbol }} {{ number_format($financialRequest->amount_repaid, 2) }}
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Balance:</th>
                                            <td>
                                                <strong>{{ $financialRequest->currency_symbol }} {{ number_format($financialRequest->balance, 2) }}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h6>Purpose</h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $financialRequest->purpose }}
                                </div>
                            </div>

                            @if($financialRequest->approval_notes)
                                <div class="mt-4">
                                    <h6>Approval Notes</h6>
                                    <div class="p-3 bg-success bg-opacity-10 rounded">
                                        {{ $financialRequest->approval_notes }}
                                    </div>
                                </div>
                            @endif

                            @if($financialRequest->rejection_reason)
                                <div class="mt-4">
                                    <h6>Rejection Reason</h6>
                                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                                        {{ $financialRequest->rejection_reason }}
                                    </div>
                                </div>
                            @endif

                            @if($financialRequest->disbursement_notes)
                                <div class="mt-4">
                                    <h6>Disbursement Notes</h6>
                                    <div class="p-3 bg-info bg-opacity-10 rounded">
                                        {{ $financialRequest->disbursement_notes }}
                                    </div>
                                </div>
                            @endif

                            @if($financialRequest->repayment_schedule)
                                <div class="mt-4">
                                    <h6>Repayment Schedule</h6>
                                    <div class="p-3 bg-light rounded">
                                        <pre class="mb-0">{{ json_encode($financialRequest->repayment_schedule, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @endif

                            @if($financialRequest->notes)
                                <div class="mt-4">
                                    <h6>Additional Notes</h6>
                                    <div class="p-3 bg-light rounded">
                                        {{ $financialRequest->notes }}
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
                                        @if($financialRequest->is_pending)
                                            @can('approve', $financialRequest)
                                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                                    <i class="fas fa-check-circle me-1"></i> Approve Request
                                                </button>
                                            @endcan
                                            @can('reject', $financialRequest)
                                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                    <i class="fas fa-times-circle me-1"></i> Reject Request
                                                </button>
                                            @endcan
                                            @can('update', $financialRequest)
                                                <a href="{{ route('financial-requests.edit', $financialRequest) }}" class="btn btn-outline-warning">
                                                    <i class="fas fa-edit me-1"></i> Edit Request
                                                </a>
                                            @endcan
                                            @can('delete', $financialRequest)
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmDelete('{{ route('financial-requests.destroy', $financialRequest) }}', '{{ $financialRequest->request_number }}')">
                                                    <i class="fas fa-trash me-1"></i> Delete Request
                                                </button>
                                            @endcan
                                        @endif

                                        @if($financialRequest->is_approved)
                                            @can('disburse', $financialRequest)
                                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#disburseModal">
                                                    <i class="fas fa-hand-holding-usd me-1"></i> Disburse Funds
                                                </button>
                                            @endcan
                                            @can('cancel', $financialRequest)
                                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                                    <i class="fas fa-ban me-1"></i> Cancel Request
                                                </button>
                                            @endcan
                                        @endif

                                        @if($financialRequest->is_disbursed || $financialRequest->is_approved)
                                            @can('recordRepayment', $financialRequest)
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#repaymentModal">
                                                    <i class="fas fa-money-bill-wave me-1"></i> Record Repayment
                                                </button>
                                            @endcan
                                        @endif

                                        @if($financialRequest->is_pending || $financialRequest->is_approved)
                                            @can('cancel', $financialRequest)
                                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                                    <i class="fas fa-ban me-1"></i> Cancel Request
                                                </button>
                                            @endcan
                                        @endif
                                    </div>

                                    <hr class="my-3">

                                    <div class="text-center">
                                        <small class="text-muted">Quick Links</small>
                                        <div class="mt-2">
                                            <a href="{{ route('users.show', $financialRequest->user_id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-user me-1"></i> View Employee
                                            </a>
                                            <a href="{{ route('financial-requests.create') }}" class="btn btn-sm btn-outline-secondary">
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
@if($financialRequest->is_pending)
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Financial Request #{{ $financialRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('financial-requests.approve', $financialRequest) }}">
                @csrf
                <div class="modal-body">
                    <p>You are about to approve this financial request of <strong>{{ $financialRequest->formatted_amount }}</strong>.</p>
                    <div class="mb-3">
                        <label for="total_repayment_amount" class="form-label">Total Repayment Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="total_repayment_amount" name="total_repayment_amount" 
                            step="0.01" min="{{ $financialRequest->amount }}" required>
                        <small class="text-muted">Must be equal to or greater than the requested amount.</small>
                    </div>
                    <div class="mb-3">
                        <label for="repayment_end_date" class="form-label">Repayment End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="repayment_end_date" name="repayment_end_date" 
                            min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Reject Modal -->
@if($financialRequest->is_pending)
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Financial Request #{{ $financialRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('financial-requests.reject', $financialRequest) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required 
                                  placeholder="Provide a clear reason for rejecting this request..."></textarea>
                        <small class="text-muted">This reason will be visible to the employee.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Disburse Modal -->
@if($financialRequest->is_approved)
<div class="modal fade" id="disburseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Disburse Funds for #{{ $financialRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('financial-requests.disburse', $financialRequest) }}">
                @csrf
                <div class="modal-body">
                    <p>Confirm disbursement of <strong>{{ $financialRequest->formatted_amount }}</strong> to <strong>{{ $financialRequest->user->name }}</strong>.</p>
                    <div class="mb-3">
                        <label for="disbursement_notes" class="form-label">Disbursement Notes (Optional)</label>
                        <textarea class="form-control" id="disbursement_notes" name="disbursement_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Disburse Funds</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Record Repayment Modal -->
@if($financialRequest->is_disbursed || $financialRequest->is_approved)
<div class="modal fade" id="repaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Repayment for #{{ $financialRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('financial-requests.recordRepayment', $financialRequest) }}">
                @csrf
                <div class="modal-body">
                    <p>Current balance: <strong>{{ $financialRequest->currency_symbol }} {{ number_format($financialRequest->balance, 2) }}</strong></p>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Repayment Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="amount" name="amount" 
                            step="0.01" min="1" max="{{ $financialRequest->balance }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="repayment_date" class="form-label">Repayment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="repayment_date" name="repayment_date" 
                            max="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Repayment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Cancel Modal -->
@if(in_array($financialRequest->status, ['pending', 'approved']))
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Financial Request #{{ $financialRequest->request_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('financial-requests.cancel', $financialRequest) }}">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to cancel this financial request? This will change its status to <strong>Cancelled</strong>.</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Cancellation Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4" required 
                                  placeholder="Provide a reason for cancellation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Cancel Request</button>
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
                <p>Are you sure you want to delete financial request <strong id="requestNumber"></strong>?</p>
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