@extends('layouts.base')

@section('title', 'Stock Item Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header with Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('stock.index') }}">Stock Inventory</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $stockItem->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-box me-2"></i>
                        {{ $stockItem->name }}
                        <span class="badge bg-{{ $stockItem->stock_status_color }} ms-2">
                            <i class="fas fa-circle me-1"></i> {{ $stockItem->stock_status }}
                        </span>
                        @if($stockItem->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </h1>
                    <p class="text-muted mb-0">Item Code: {{ $stockItem->code }} @if($stockItem->sku) | SKU: {{ $stockItem->sku }} @endif</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('stock.edit', $stockItem) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('stock.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Inventory
                    </a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="row">
                <!-- Left Column: Item Info -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Item Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Category:</th>
                                            <td>
                                                @if($stockItem->category)
                                                    <span class="badge bg-secondary">{{ $stockItem->category->name }}</span>
                                                @else
                                                    <span class="text-muted">Uncategorized</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Name (Swahili):</th>
                                            <td>{{ $stockItem->name_sw ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description:</th>
                                            <td>{{ $stockItem->description ?? 'No description' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Brand:</th>
                                            <td>{{ $stockItem->brand ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Manufacturer:</th>
                                            <td>{{ $stockItem->manufacturer ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Organic Approved:</th>
                                            <td>
                                                @if($stockItem->is_organic_approved)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Unit:</th>
                                            <td>{{ $stockItem->unit }}</td>
                                        </tr>
                                        <tr>
                                            <th>Warehouse Location:</th>
                                            <td>{{ $stockItem->warehouse_location ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bin Location:</th>
                                            <td>{{ $stockItem->bin_location ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Batch Tracking:</th>
                                            <td>
                                                @if($stockItem->requires_batch_tracking)
                                                    <span class="badge bg-info">Required</span>
                                                @else
                                                    <span class="badge bg-secondary">Not Required</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created:</th>
                                            <td>
                                                {{ $stockItem->created_at->format('M d, Y') }}
                                                <br>
                                                <small class="text-muted">{{ $stockItem->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated:</th>
                                            <td>
                                                {{ $stockItem->updated_at->format('M d, Y') }}
                                                <br>
                                                <small class="text-muted">{{ $stockItem->updated_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quantities & Pricing -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-balance-scale me-2"></i>
                                        Quantities
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="display-6 text-primary">{{ number_format($stockItem->quantity_on_hand, 2) }}</div>
                                            <small class="text-muted">On Hand</small>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="display-6 text-warning">{{ number_format($stockItem->quantity_reserved, 2) }}</div>
                                            <small class="text-muted">Reserved</small>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="display-6 text-success">{{ number_format($stockItem->quantity_available, 2) }}</div>
                                            <small class="text-muted">Available</small>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="display-6 text-danger">{{ number_format($stockItem->reorder_level, 2) }}</div>
                                            <small class="text-muted">Reorder Level</small>
                                        </div>
                                    </div>
                                    <div class="progress mb-2" style="height: 20px;">
                                        @php
                                            $percentage = $stockItem->reorder_level > 0 ? min(100, ($stockItem->quantity_available / $stockItem->reorder_level) * 100) : 0;
                                            $color = $stockItem->is_out_of_stock ? 'danger' : ($stockItem->is_critical_stock ? 'warning' : ($stockItem->is_low_stock ? 'info' : 'success'));
                                        @endphp
                                        <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">Stock level relative to reorder level.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Pricing
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="50%">Unit Cost:</th>
                                            <td class="text-end">{{ number_format($stockItem->unit_cost, 2) }} {{ $stockItem->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th>Unit Price:</th>
                                            <td class="text-end">{{ number_format($stockItem->unit_price, 2) }} {{ $stockItem->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reorder Quantity:</th>
                                            <td class="text-end">{{ number_format($stockItem->reorder_quantity, 2) }} {{ $stockItem->unit }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Stock Value:</th>
                                            <td class="text-end fw-bold text-primary">
                                                {{ number_format($stockItem->stock_value, 2) }} {{ $stockItem->currency }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Transactions -->
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-exchange-alt me-2"></i>
                                Recent Transactions
                                <span class="badge bg-light text-dark ms-2">{{ $stockItem->transactions->count() }}</span>
                            </h5>
                            <a href="#" class="btn btn-light btn-sm">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            @if($stockItem->transactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Quantity</th>
                                                <th>Reference</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stockItem->transactions->take(5) as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $transaction->type === 'intake' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($transaction->type) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ number_format($transaction->quantity, 2) }} {{ $stockItem->unit }}</td>
                                                    <td>{{ $transaction->reference ?? 'N/A' }}</td>
                                                    <td>{{ Str::limit($transaction->notes, 30) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($stockItem->transactions->count() > 5)
                                    <div class="text-center mt-2">
                                        <a href="#" class="btn btn-outline-dark btn-sm">
                                            View {{ $stockItem->transactions->count() - 5 }} more transactions
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-exchange-alt fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No transactions recorded for this item.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Stats & Actions -->
                <div class="col-lg-4">
                    <!-- Stock Status Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-{{ $stockItem->stock_status_color }} text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Stock Status
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="display-4 text-{{ $stockItem->stock_status_color }}">
                                    <i class="fas fa-{{ $stockItem->is_out_of_stock ? 'times-circle' : ($stockItem->is_critical_stock ? 'exclamation-triangle' : ($stockItem->is_low_stock ? 'exclamation-circle' : 'check-circle')) }}"></i>
                                </div>
                                <h4 class="mt-2">{{ $stockItem->stock_status }}</h4>
                                <p class="text-muted">
                                    @if($stockItem->is_out_of_stock)
                                        This item is out of stock. Consider replenishing.
                                    @elseif($stockItem->is_critical_stock)
                                        Stock is critically low. Immediate action required.
                                    @elseif($stockItem->is_low_stock)
                                        Stock is below reorder level. Plan to reorder.
                                    @else
                                        Stock is at healthy levels.
                                    @endif
                                </p>
                            </div>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-box-open me-2"></i> Available Quantity</span>
                                    <span class="badge bg-primary rounded-pill">{{ number_format($stockItem->quantity_available, 2) }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-exclamation-triangle me-2"></i> Reorder Level</span>
                                    <span class="badge bg-warning rounded-pill">{{ number_format($stockItem->reorder_level, 2) }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-shopping-cart me-2"></i> Reorder Quantity</span>
                                    <span class="badge bg-info rounded-pill">{{ number_format($stockItem->reorder_quantity, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Batches (if batch tracking) -->
                    @if($stockItem->requires_batch_tracking && $stockItem->batches->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-layer-group me-2"></i>
                                    Active Batches
                                    <span class="badge bg-light text-dark ms-2">{{ $stockItem->batches->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    @foreach($stockItem->batches->take(3) as $batch)
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $batch->batch_number }}</h6>
                                                <small class="text-muted">{{ number_format($batch->quantity, 2) }} {{ $stockItem->unit }}</small>
                                            </div>
                                            <small class="text-muted">Expires: {{ $batch->expiry_date ? $batch->expiry_date->format('M d, Y') : 'No expiry' }}</small>
                                        </div>
                                    @endforeach
                                </div>
                                @if($stockItem->batches->count() > 3)
                                    <div class="text-center mt-2">
                                        <a href="#" class="btn btn-outline-warning btn-sm">
                                            View All Batches
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                                    <i class="fas fa-plus-circle me-2"></i> Adjust Stock
                                </a>
                                <a href="#" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                                    <i class="fas fa-exchange-alt me-2"></i> Add Transaction
                                </a>
                                <a href="#" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reorderModal">
                                    <i class="fas fa-shopping-cart me-2"></i> Create Reorder
                                </a>
                                <a href="{{ route('stock.reports.summary') }}" class="btn btn-outline-info">
                                    <i class="fas fa-chart-bar me-2"></i> View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals (placeholder) -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Stock adjustment functionality will be implemented soon.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Transaction entry functionality will be implemented soon.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reorderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Reorder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Reorder creation functionality will be implemented soon.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .progress-bar {
        transition: width 0.6s ease;
    }
</style>
@endsection