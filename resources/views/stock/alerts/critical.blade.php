@extends('layouts.base')

@section('title', 'Critical Stock Alerts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-skull-crossbones me-2"></i> Critical Stock Alerts
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock.low-stock') }}" class="btn btn-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i> Low Stock
                            </a>
                            <a href="{{ route('stock.out-of-stock') }}" class="btn btn-dark">
                                <i class="fas fa-times-circle me-1"></i> Out of Stock
                            </a>
                            <a href="{{ route('stock.index') }}" class="btn btn-light">
                                <i class="fas fa-boxes me-1"></i> All Stock
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body bg-danger bg-opacity-10">
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Critical Stock Items:</strong> These items are at critically low levels (less than 25% of reorder level). Immediate action required!
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
                                    <th>Available</th>
                                    <th>Reorder Level</th>
                                    <th>Reorder Qty</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockItems as $item)
                                    <tr class="table-danger">
                                        <td>
                                            <strong class="text-primary">{{ $item->code }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-exclamation-circle"></i>
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
                                        <td>
                                            <span class="fw-bold text-danger">{{ number_format($item->quantity_available, 2) }}</span>
                                            <br>
                                            <small class="text-muted">{{ $item->unit }}</small>
                                        </td>
                                        <td>{{ number_format($item->reorder_level, 2) }}</td>
                                        <td>{{ number_format($item->reorder_quantity, 2) }}</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-skull-crossbones me-1"></i> CRITICAL
                                            </span>
                                            @php
                                                $percentage = $item->reorder_level > 0 ? ($item->quantity_available / $item->reorder_level) * 100 : 0;
                                            @endphp
                                            <br>
                                            <small class="text-danger fw-bold">Only {{ number_format($percentage, 1) }}% of reorder level</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('stock.show', $item) }}" class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('stock.transactions.intake') }}?stock_item_id={{ $item->id }}" class="btn btn-success" title="Record Intake - URGENT">
                                                    <i class="fas fa-plus"></i> Restock
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-success">
                                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                                <h5>No Critical Stock Items</h5>
                                                <p class="mb-0">No items are at critical levels. Stock management is on track!</p>
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
            </div>
        </div>
    </div>
</div>
@endsection
