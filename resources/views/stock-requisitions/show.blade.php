@extends('layouts.base')

@section('title', 'Stock Requisition #' . $stockRequisition->requisition_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i> Stock Requisition #{{ $stockRequisition->requisition_number }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock-requisitions.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Requisition Details</h5>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Requisition Number:</th>
                                            <td><strong>{{ $stockRequisition->requisition_number }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Employee:</th>
                                            <td>
                                                {{ $stockRequisition->user->name ?? 'N/A' }}
                                                @if($stockRequisition->user)
                                                    <small class="text-muted d-block">({{ $stockRequisition->user->email }})</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Department:</th>
                                            <td>{{ $stockRequisition->department->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At:</th>
                                            <td>{{ $stockRequisition->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated:</th>
                                            <td>{{ $stockRequisition->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $stockRequisition->status_color }}">
                                                    {{ $stockRequisition->status_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($stockRequisition->approved_by)
                                        <tr>
                                            <th>Approved By:</th>
                                            <td>{{ $stockRequisition->approvedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Approved At:</th>
                                            <td>{{ $stockRequisition->approved_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($stockRequisition->rejected_by)
                                        <tr>
                                            <th>Rejected By:</th>
                                            <td>{{ $stockRequisition->rejectedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Rejected At:</th>
                                            <td>{{ $stockRequisition->rejected_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if($stockRequisition->issued_by)
                                        <tr>
                                            <th>Issued By:</th>
                                            <td>{{ $stockRequisition->issuedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Issued At:</th>
                                            <td>{{ $stockRequisition->issued_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Requested Date:</th>
                                            <td>{{ $stockRequisition->requested_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Required Date:</th>
                                            <td>{{ $stockRequisition->required_date ? $stockRequisition->required_date->format('d/m/Y') : 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Estimated Total:</th>
                                            <td>
                                                <strong class="text-success">{{ number_format($stockRequisition->estimated_total, 2) }}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h6>Purpose</h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $stockRequisition->purpose }}
                                </div>
                            </div>

                            @if($stockRequisition->notes)
                                <div class="mt-4">
                                    <h6>Additional Notes</h6>
                                    <div class="p-3 bg-light rounded">
                                        {{ $stockRequisition->notes }}
                                    </div>
                                </div>
                            @endif

                            @if($stockRequisition->rejection_reason)
                                <div class="mt-4">
                                    <h6>Rejection Reason</h6>
                                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                                        {{ $stockRequisition->rejection_reason }}
                                    </div>
                                </div>
                            @endif

                            @if($stockRequisition->issue_notes)
                                <div class="mt-4">
                                    <h6>Issue Notes</h6>
                                    <div class="p-3 bg-info bg-opacity-10 rounded">
                                        {{ $stockRequisition->issue_notes }}
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
                                        @if($stockRequisition->is_pending)
                                            @can('approve', $stockRequisition)
                                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                                    <i class="fas fa-check-circle me-1"></i> Approve Requisition
                                                </button>
                                            @endcan
                                            @can('reject', $stockRequisition)
                                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                    <i class="fas fa-times-circle me-1"></i> Reject Requisition
                                                </button>
                                            @endcan
                                        @endif

                                        @if($stockRequisition->is_approved || $stockRequisition->is_partially_issued)
                                            @can('issue', $stockRequisition)
                                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#issueModal">
                                                    <i class="fas fa-truck-loading me-1"></i> Issue Items
                                                </button>
                                            @endcan
                                        @endif

                                        @if($stockRequisition->is_editable)
                                            @can('update', $stockRequisition)
                                                <a href="{{ route('stock-requisitions.edit', $stockRequisition) }}" class="btn btn-outline-warning">
                                                    <i class="fas fa-edit me-1"></i> Edit Requisition
                                                </a>
                                            @endcan
                                            @can('delete', $stockRequisition)
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmDelete('{{ route('stock-requisitions.destroy', $stockRequisition) }}', '{{ $stockRequisition->requisition_number }}')">
                                                    <i class="fas fa-trash me-1"></i> Delete Requisition
                                                </button>
                                            @endcan
                                        @endif

                                        @if($stockRequisition->canBeCancelled())
                                            @can('cancel', $stockRequisition)
                                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                                    <i class="fas fa-ban me-1"></i> Cancel Requisition
                                                </button>
                                            @endcan
                                        @endif
                                    </div>

                                    <hr class="my-3">

                                    <div class="text-center">
                                        <small class="text-muted">Quick Links</small>
                                        <div class="mt-2">
                                            <a href="{{ route('users.show', $stockRequisition->user_id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-user me-1"></i> View Employee
                                            </a>
                                            <a href="{{ route('stock-requisitions.create') }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-plus me-1"></i> New Requisition
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-5">

                    <h5 class="mb-3">
                        <i class="fas fa-boxes me-2"></i> Requisition Items
                        <small class="text-muted">({{ $stockRequisition->items->count() }} items)</small>
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Quantity Requested</th>
                                    <th>Quantity Issued</th>
                                    <th>Unit</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockRequisition->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->stockItem->name ?? 'N/A' }}</strong>
                                            @if($item->stockItem)
                                                <small class="d-block text-muted">{{ $item->stockItem->code ?? '' }}</small>
                                            @endif
                                        </td>
                                        <td>{{ number_format($item->quantity_requested, 3) }}</td>
                                        <td>
                                            @if($item->quantity_issued !== null)
                                                {{ number_format($item->quantity_issued, 3) }}
                                            @else
                                                <span class="text-muted">Not issued</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->unit_of_measure }}</td>
                                        <td>
                                            @if($item->unit_price)
                                                {{ number_format($item->unit_price, 2) }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->total_price)
                                                <strong class="text-success">{{ number_format($item->total_price, 2) }}</strong>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->isFullyIssued())
                                                <span class="badge bg-success">Fully Issued</span>
                                            @elseif($item->isPartiallyIssued())
                                                <span class="badge bg-primary">Partially Issued</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                            <small class="d-block">{{ $item->issued_percentage }}%</small>
                                        </td>
                                        <td>{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">Totals:</th>
                                    <th>{{ number_format($stockRequisition->total_quantity_requested, 3) }}</th>
                                    <th>{{ number_format($stockRequisition->total_quantity_issued, 3) }}</th>
                                    <th colspan="2"></th>
                                    <th class="text-success">{{ number_format($stockRequisition->total_estimated_price, 2) }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
@if($stockRequisition->is_pending)
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Stock Requisition #{{ $stockRequisition->requisition_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('stock-requisitions.approve', $stockRequisition) }}">
                @csrf
                <div class="modal-body">
                    <p>You are about to approve this stock requisition with <strong>{{ $stockRequisition->items->count() }}</strong> items.</p>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Requisition</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Reject Modal -->
@if($stockRequisition->is_pending)
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Stock Requisition #{{ $stockRequisition->requisition_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('stock-requisitions.reject', $stockRequisition) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required 
                                  placeholder="Provide a clear reason for rejecting this requisition..."></textarea>
                        <small class="text-muted">This reason will be visible to the employee.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Requisition</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Issue Modal -->
@if($stockRequisition->is_approved || $stockRequisition->is_partially_issued)
<div class="modal fade" id="issueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Issue Items for #{{ $stockRequisition->requisition_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('stock-requisitions.issue', $stockRequisition) }}">
                @csrf
                <div class="modal-body">
                    <p>Enter the quantity issued for each item. Leave blank if not issued yet.</p>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Requested</th>
                                    <th>Already Issued</th>
                                    <th>Remaining</th>
                                    <th>Issue Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockRequisition->items as $item)
                                    <tr>
                                        <td>{{ $item->stockItem->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($item->quantity_requested, 3) }}</td>
                                        <td>{{ $item->quantity_issued ? number_format($item->quantity_issued, 3) : '0' }}</td>
                                        <td>{{ number_format($item->remaining_quantity, 3) }}</td>
                                        <td>
                                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                            <input type="number" step="0.001" min="0" max="{{ $item->remaining_quantity }}" 
                                                   class="form-control form-control-sm" 
                                                   name="items[{{ $loop->index }}][quantity_issued]" 
                                                   value="{{ $item->remaining_quantity }}" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3">
                        <label for="issue_notes" class="form-label">Issue Notes (Optional)</label>
                        <textarea class="form-control" id="issue_notes" name="issue_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Issue Items</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Cancel Modal -->
@if($stockRequisition->canBeCancelled())
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Stock Requisition #{{ $stockRequisition->requisition_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('stock-requisitions.cancel', $stockRequisition) }}">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to cancel this stock requisition? This will change its status to <strong>Cancelled</strong>.</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Cancellation Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4" required 
                                  placeholder="Provide a reason for cancellation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Cancel Requisition</button>
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
                <p>Are you sure you want to delete stock requisition <strong id="requisitionNumber"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Requisition</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, requisitionNumber) {
        document.getElementById('requisitionNumber').textContent = requisitionNumber;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endpush
@endsection