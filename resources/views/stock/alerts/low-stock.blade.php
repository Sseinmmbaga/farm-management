@extends('layouts.base')

@section('title', 'Low Stock Alerts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alerts
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock.critical') }}" class="btn btn-danger">
                                <i class="fas fa-skull-crossbones me-1"></i> Critical
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

                <div class="card-body bg-warning bg-opacity-10">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Low Stock Items:</strong> These items are below their reorder level but not yet critical. Consider placing orders soon.
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
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $item->code }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-exclamation"></i>
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
                                            <span class="fw-bold text-warning">{{ number_format($item->quantity_available, 2) }}</span>
                                            <br>
                                            <small class="text-muted">{{ $item->unit }}</small>
                                        </td>
                                        <td>{{ number_format($item->reorder_level, 2) }}</td>
                                        <td>{{ number_format($item->reorder_quantity, 2) }}</td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Low Stock
                                            </span>
                                            @php
                                                $shortage = $item->reorder_level - $item->quantity_available;
                                            @endphp
                                            <br>
                                            <small class="text-danger">Short by {{ number_format($shortage, 2) }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('stock.show', $item) }}" class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('stock.transactions.intake') }}?stock_item_id={{ $item->id }}" class="btn btn-outline-success" title="Record Intake">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-success">
                                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                                <h5>No Low Stock Items</h5>
                                                <p class="mb-0">All stock items are at healthy levels. Great job!</p>
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
