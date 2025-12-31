@extends('layouts.base')

@section('title', 'Stock Request Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i> Stock Request: {{ $stockRequest->request_number }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock.requests.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Status</h6>
                                    <span class="badge bg-{{ $stockRequest->status_color }} fs-6 py-2 px-3">
                                        {{ $stockRequest->status_display }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Priority</h6>
                                    <span class="badge bg-{{ $stockRequest->priority_color }} fs-6 py-2 px-3">
                                        {{ $stockRequest->priority_display }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Created By</h6>
                                    <strong>{{ $stockRequest->requestedBy->name ?? 'Unknown' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Created At</h6>
                                    <strong>{{ $stockRequest->created_at->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Request Details</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th>Farmer</th>
                                                <td>{{ $stockRequest->farmer->full_name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Season</th>
                                                <td>{{ $stockRequest->season->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Needed By</th>
                                                <td>{{ $stockRequest->needed_by ? $stockRequest->needed_by->format('d/m/Y') : 'Not specified' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Purpose</th>
                                                <td>{{ $stockRequest->purpose ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Notes</th>
                                                <td>{{ $stockRequest->notes ?? '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Approval Details</h5>
                                </div>
                                <div class="card-body">
                                    @if($stockRequest->is_approved || $stockRequest->is_rejected)
                                        <table class="table table-sm">
                                            <tbody>
                                                <tr>
                                                    <th>Approved/Rejected By</th>
                                                    <td>{{ $stockRequest->approvedBy->name ?? 'Unknown' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date</th>
                                                    <td>{{ $stockRequest->approved_at ? $stockRequest->approved_at->format('d/m/Y H:i') : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Notes</th>
                                                    <td>{{ $stockRequest->approval_notes ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-muted mb-0">Not yet approved or rejected.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">
                        <i class="fas fa-boxes me-2"></i> Request Items
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Code</th>
                                    <th>Unit</th>
                                    <th class="text-end">Requested Qty</th>
                                    <th class="text-end">Approved Qty</th>
                                    <th class="text-end">Fulfilled Qty</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockRequest->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->stockItem->name }}</strong>
                                        </td>
                                        <td>{{ $item->stockItem->code }}</td>
                                        <td>{{ $item->stockItem->unit }}</td>
                                        <td class="text-end">{{ number_format($item->quantity_requested, 2) }}</td>
                                        <td class="text-end">
                                            @if($item->quantity_approved)
                                                <span class="badge bg-success">{{ number_format($item->quantity_approved, 2) }}</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($item->quantity_fulfilled)
                                                <span class="badge bg-primary">{{ number_format($item->quantity_fulfilled, 2) }}</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-light">
                                    <td colspan="3" class="text-end"><strong>Totals</strong></td>
                                    <td class="text-end"><strong>{{ number_format($stockRequest->total_requested_quantity, 2) }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($stockRequest->total_approved_quantity, 2) }}</strong></td>
                                    <td class="text-end"><strong>{{ number_format($stockRequest->total_fulfilled_quantity, 2) }}</strong></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        @if($stockRequest->is_draft)
                            <a href="{{ route('stock.requests.edit', $stockRequest) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Request
                            </a>
                            <form action="{{ route('stock.requests.submit', $stockRequest) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-paper-plane me-1"></i> Submit for Approval
                                </button>
                            </form>
                        @endif
                        
                        @if($stockRequest->is_submitted && auth()->user()->can('approve_stock_requests'))
                            <a href="{{ route('stock.requests.approve', $stockRequest) }}" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i> Approve Request
                            </a>
                            <a href="{{ route('stock.requests.reject', $stockRequest) }}" class="btn btn-danger">
                                <i class="fas fa-times-circle me-1"></i> Reject Request
                            </a>
                        @endif
                        
                        @if($stockRequest->is_approved && auth()->user()->can('fulfill_stock_requests'))
                            <form action="{{ route('stock.requests.fulfill', $stockRequest) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-truck me-1"></i> Fulfill Request
                                </button>
                            </form>
                        @endif
                        
                        @if($stockRequest->is_draft)
                            <form action="{{ route('stock.requests.destroy', $stockRequest) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this request?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-trash me-1"></i> Delete Request
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection