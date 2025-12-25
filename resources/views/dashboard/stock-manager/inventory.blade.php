@extends('layouts.base')

@section('title', 'Inventory Management')

@push('styles')
<style>
    .filter-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .stats-row {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-item {
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        flex: 1;
        text-align: center;
        border-left: 4px solid;
    }
    .stat-item.total { border-left-color: #3498db; }
    .stat-item.in-stock { border-left-color: #2ecc71; }
    .stat-item.low-stock { border-left-color: #f39c12; }
    .stat-item.out-of-stock { border-left-color: #e74c3c; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .data-table {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Inventory Management</h1>
            <p class="text-muted mb-0">Manage stock items and inventory levels</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.stock') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Item
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-primary">{{ $stats['total_items'] }}</div>
            <div class="label">Total Items</div>
        </div>
        <div class="stat-item in-stock">
            <div class="number text-success">{{ $stats['in_stock'] }}</div>
            <div class="label">In Stock</div>
        </div>
        <div class="stat-item low-stock">
            <div class="number text-warning">{{ $stats['low_stock'] }}</div>
            <div class="label">Low Stock</div>
        </div>
        <div class="stat-item out-of-stock">
            <div class="number text-danger">{{ $stats['out_of_stock'] }}</div>
            <div class="label">Out of Stock</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.stock.inventory') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <option value="seeds">Seeds</option>
                    <option value="fertilizers">Fertilizers</option>
                    <option value="equipment">Equipment</option>
                    <option value="packaging">Packaging</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search items..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="data-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Reorder Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td><strong>{{ $item->code ?? 'N/A' }}</strong></td>
                        <td>{{ $item->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($item->category ?? 'N/A') }}</td>
                        <td>{{ number_format($item->quantity ?? 0) }}</td>
                        <td>{{ $item->unit ?? 'N/A' }}</td>
                        <td>{{ number_format($item->reorder_level ?? 0) }}</td>
                        <td>
                            @if(($item->quantity ?? 0) == 0)
                                <span class="badge bg-danger">Out of Stock</span>
                            @elseif(($item->quantity ?? 0) <= ($item->reorder_level ?? 0))
                                <span class="badge bg-warning text-dark">Low Stock</span>
                            @else
                                <span class="badge bg-success">In Stock</span>
                            @endif
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No inventory items found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator && $items->hasPages())
        <div class="p-3 border-top">
            {{ $items->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
