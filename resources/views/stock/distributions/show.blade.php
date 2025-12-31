@extends('layouts.base')

@section('title', 'Distribution Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('stock.index') }}">Stock Inventory</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stock.distributions.index') }}">Distributions</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Distribution Details
                        @php
                            $typeColors = [
                                'credit' => 'warning',
                                'cash' => 'success',
                                'free' => 'info',
                            ];
                        @endphp
                        <span class="badge bg-{{ $typeColors[$distribution->distribution_type] ?? 'secondary' }} ms-2">
                            {{ ucfirst($distribution->distribution_type) }}
                        </span>
                    </h1>
                    <p class="text-muted mb-0">
                        Created {{ $distribution->created_at->format('M d, Y H:i') }}
                        ({{ $distribution->created_at->diffForHumans() }})
                    </p>
                </div>
                <div class="btn-group">
                    @if($distribution->farmer)
                        <a href="{{ route('stock.distributions.farmer', $distribution->farmer) }}" class="btn btn-info">
                            <i class="fas fa-user me-1"></i> Farmer History
                        </a>
                    @endif
                    <a href="{{ route('stock.distributions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Main Info -->
                <div class="col-lg-8">
                    <!-- Farmer & Farm Info -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>
                                Farmer Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Farmer Name:</th>
                                            <td>
                                                @if($distribution->farmer)
                                                    <strong>{{ $distribution->farmer->first_name }} {{ $distribution->farmer->last_name }}</strong>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Registration #:</th>
                                            <td>{{ $distribution->farmer->registration_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td>{{ $distribution->farmer->phone ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Farm:</th>
                                            <td>{{ $distribution->farm->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Season:</th>
                                            <td>
                                                @if($distribution->season)
                                                    {{ $distribution->season->name }} {{ $distribution->season->year }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Distributed By:</th>
                                            <td>{{ $distribution->distributedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Item Info -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-box me-2"></i>
                                Stock Item Details
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($distribution->transaction && $distribution->transaction->stockItem)
                                @php $stockItem = $distribution->transaction->stockItem; @endphp
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Item Name:</th>
                                                <td>
                                                    <a href="{{ route('stock.show', $stockItem) }}">
                                                        <strong>{{ $stockItem->name }}</strong>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Item Code:</th>
                                                <td>{{ $stockItem->code }}</td>
                                            </tr>
                                            <tr>
                                                <th>Category:</th>
                                                <td>
                                                    @if($stockItem->category)
                                                        <span class="badge bg-secondary">{{ $stockItem->category->name }}</span>
                                                    @else
                                                        <span class="text-muted">Uncategorized</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Quantity:</th>
                                                <td>
                                                    <span class="h4 text-primary">{{ number_format($distribution->quantity, 2) }}</span>
                                                    {{ $stockItem->unit }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Unit Cost:</th>
                                                <td>TZS {{ number_format($distribution->transaction->unit_cost ?? 0, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Reference:</th>
                                                <td>{{ $distribution->transaction->reference_number ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <p>Stock item information not available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Purpose & Notes -->
                    @if($distribution->purpose || $distribution->notes)
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-clipboard me-2"></i>
                                    Additional Information
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($distribution->purpose)
                                    <div class="mb-3">
                                        <label class="text-muted">Purpose</label>
                                        <p class="mb-0">
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $distribution->purpose)) }}</span>
                                        </p>
                                    </div>
                                @endif
                                @if($distribution->notes)
                                    <div>
                                        <label class="text-muted">Notes</label>
                                        <p class="mb-0">{{ $distribution->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Payment & Status -->
                <div class="col-lg-4">
                    <!-- Payment Status -->
                    <div class="card mb-4">
                        <div class="card-header bg-{{ $typeColors[$distribution->distribution_type] ?? 'secondary' }} {{ $distribution->distribution_type == 'warning' ? 'text-dark' : 'text-white' }}">
                            <h5 class="mb-0">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Payment Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="display-6 text-{{ $typeColors[$distribution->distribution_type] ?? 'secondary' }}">
                                    @if($distribution->distribution_type == 'credit')
                                        <i class="fas fa-credit-card"></i>
                                    @elseif($distribution->distribution_type == 'cash')
                                        <i class="fas fa-money-bill"></i>
                                    @else
                                        <i class="fas fa-gift"></i>
                                    @endif
                                </div>
                                <h4 class="mt-2">{{ ucfirst($distribution->distribution_type) }} Distribution</h4>
                            </div>

                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-tag me-2"></i> Total Value</span>
                                    <span class="badge bg-primary rounded-pill fs-6">
                                        TZS {{ number_format($distribution->value, 2) }}
                                    </span>
                                </div>

                                @if($distribution->distribution_type === 'credit')
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-check-circle me-2"></i> Amount Repaid</span>
                                        <span class="badge bg-success rounded-pill">
                                            TZS {{ number_format($distribution->amount_repaid, 2) }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-clock me-2"></i> Outstanding</span>
                                        <span class="badge bg-{{ $distribution->is_repaid ? 'success' : 'danger' }} rounded-pill">
                                            TZS {{ number_format($distribution->value - $distribution->amount_repaid, 2) }}
                                        </span>
                                    </div>
                                    @if($distribution->due_date)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-calendar me-2"></i> Due Date</span>
                                            <span class="badge bg-{{ $distribution->due_date->isPast() && !$distribution->is_repaid ? 'danger' : 'info' }} rounded-pill">
                                                {{ $distribution->due_date->format('M d, Y') }}
                                            </span>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            @if($distribution->distribution_type === 'credit')
                                <hr>
                                <div class="text-center">
                                    @if($distribution->is_repaid)
                                        <span class="badge bg-success fs-5 py-2 px-3">
                                            <i class="fas fa-check-circle me-2"></i> Fully Repaid
                                        </span>
                                    @elseif($distribution->due_date && $distribution->due_date->isPast())
                                        <span class="badge bg-danger fs-5 py-2 px-3">
                                            <i class="fas fa-exclamation-triangle me-2"></i> Overdue
                                        </span>
                                    @else
                                        <span class="badge bg-warning fs-5 py-2 px-3">
                                            <i class="fas fa-clock me-2"></i> Pending Repayment
                                        </span>
                                    @endif
                                </div>

                                @if(!$distribution->is_repaid)
                                    <div class="progress mt-3" style="height: 20px;">
                                        @php
                                            $percentage = $distribution->value > 0 ? min(100, ($distribution->amount_repaid / $distribution->value) * 100) : 0;
                                        @endphp
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">Repayment progress</small>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($distribution->distribution_type === 'credit' && !$distribution->is_repaid)
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                                        <i class="fas fa-plus-circle me-2"></i> Record Payment
                                    </button>
                                @endif
                                @if($distribution->farmer)
                                    <a href="{{ route('stock.distributions.farmer', $distribution->farmer) }}" class="btn btn-outline-info">
                                        <i class="fas fa-history me-2"></i> View Farmer History
                                    </a>
                                @endif
                                <a href="{{ route('stock.distributions.create') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i> New Distribution
                                </a>
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i> Print Receipt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal (for credit distributions) -->
@if($distribution->distribution_type === 'credit' && !$distribution->is_repaid)
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Record Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Outstanding Amount:</strong> TZS {{ number_format($distribution->value - $distribution->amount_repaid, 2) }}
                </div>
                <form id="paymentForm">
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount (TZS)</label>
                        <input type="number"
                               class="form-control"
                               id="payment_amount"
                               step="0.01"
                               min="0.01"
                               max="{{ $distribution->value - $distribution->amount_repaid }}"
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="payment_notes" rows="2"></textarea>
                    </div>
                </form>
                <p class="text-muted small">Note: Payment recording functionality needs to be implemented in the controller.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" disabled>
                    <i class="fas fa-check me-1"></i> Record Payment
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<style>
@media print {
    .btn, .card-header, .breadcrumb, nav {
        display: none !important;
    }
    .card {
        border: 1px solid #ddd !important;
    }
}
</style>
@endsection
