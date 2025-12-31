@extends('layouts.base')

@section('title', 'Out of Stock Alerts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-times-circle me-2"></i> Out of Stock Items
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock.low-stock') }}" class="btn btn-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i> Low Stock
                            </a>
                            <a href="{{ route('stock.critical') }}" class="btn btn-danger">
                                <i class="fas fa-skull-crossbones me-1"></i> Critical
                            </a>
                            <a href="{{ route('stock.index') }}" class="btn btn-light">
                                <i class="fas fa-boxes me-1"></i> All Stock
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body bg-dark bg-opacity-10">
                    <div class="alert alert-dark mb-0">
                        <i class="fas fa-ban me-2"></i>
                        <strong>Out of Stock Items:</strong> These items have zero available quantity. They cannot be issued until restocked.
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>On Hand</th>
                                    <th>Reserved</th>
                                    <th>Available</th>
                                    <th>Reorder Qty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockItems as $item)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $item->code }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-times"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $item->name }}</strong>
                                                    @if($item->name_sw)
                                                        <br><small class="text-muted">{{ $item->name_sw }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->category)
                                                <span class="badge bg-secondary">{{ $item->category->name }}</span>
                                            @else
                                                <span class="text-muted">Uncategorized</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($item->quantity_on_hand, 2) }}</td>
                                        <td>{{ number_format($item->quantity_reserved, 2) }}</td>
                                        <td>
                                            <span class="badge bg-dark fs-6">
                                                <i class="fas fa-times-circle me-1"></i> 0
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $item->unit }}</small>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">{{ number_format($item->reorder_quantity, 2) }}</span>
                                            <br>
                                            <small class="text-muted">{{ $item->unit }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('stock.show', $item) }}" class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('stock.transactions.intake') }}?stock_item_id={{ $item->id }}" class="btn btn-success" title="Record Intake - URGENT">
                                                    <i class="fas fa-plus"></i> Restock Now
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-success">
                                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                                <h5>No Out of Stock Items</h5>
                                                <p class="mb-0">All items have stock available. Excellent inventory management!</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($stockItems->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $stockItems->firstItem() }} to {{ $stockItems->lastItem() }} of {{ $stockItems->total() }} items
                                </div>
                                <div>
                                    {{ $stockItems->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quick Stats -->
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h5 class="mb-0 text-dark">{{ $stockItems->total() }}</h5>
                            <small class="text-muted">Out of Stock Items</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-0 text-primary">{{ $stockItems->sum('reorder_quantity') }}</h5>
                            <small class="text-muted">Total Reorder Quantity Needed</small>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('stock.transactions.intake') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus-circle me-1"></i> Quick Intake
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
