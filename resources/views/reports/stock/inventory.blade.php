@extends('layouts.base')

@section('title', 'Stock Inventory Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-box me-2"></i>
                        Inventory Report
                    </h1>
                    <p class="text-muted mb-0">Detailed view of all stock items</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.stock.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Overview
                    </a>
                    <a href="{{ route('reports.stock.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="row mb-4">
                <div class="col-md-6 col-sm-6 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1">Total Quantity (filtered)</h6>
                            <h2 class="mb-0">{{ number_format($totalQuantity, 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1">Total Value (filtered)</h6>
                            <h2 class="mb-0">TZS {{ number_format($totalValue, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.stock.inventory') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Stock Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="in" {{ request('status') === 'in' ? 'selected' : '' }}>In Stock</option>
                                    <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="critical" {{ request('status') === 'critical' ? 'selected' : '' }}>Critical Stock</option>
                                    <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Organic Approved</label>
                                <select name="organic" class="form-select">
                                    <option value="">All Items</option>
                                    <option value="1" {{ request('organic') === '1' ? 'selected' : '' }}>Organic Only</option>
                                    <option value="0" {{ request('organic') === '0' ? 'selected' : '' }}>Non-Organic Only</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Search items..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('reports.stock.inventory') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Inventory Items ({{ $items->total() }} total)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th class="text-end">Qty on Hand</th>
                                    <th class="text-end">Available</th>
                                    <th class="text-end">Reorder Level</th>
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end">Stock Value</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Organic</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    @php
                                        $statusClass = 'success';
                                        $statusText = 'In Stock';
                                        if ($item->quantity_on_hand <= 0) {
                                            $statusClass = 'danger';
                                            $statusText = 'Out of Stock';
                                        } elseif ($item->quantity_on_hand <= ($item->reorder_level * 0.5)) {
                                            $statusClass = 'danger';
                                            $statusText = 'Critical';
                                        } elseif ($item->quantity_on_hand <= $item->reorder_level) {
                                            $statusClass = 'warning';
                                            $statusText = 'Low Stock';
                                        }
                                    @endphp
                                    <tr class="{{ $statusClass === 'danger' ? 'table-danger' : ($statusClass === 'warning' ? 'table-warning' : '') }}">
                                        <td><code>{{ $item->code ?? 'N/A' }}</code></td>
                                        <td>
                                            <strong>{{ $item->name }}</strong>
                                            @if($item->description)
                                                <br><small class="text-muted">{{ Str::limit($item->description, 40) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->category?->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($item->quantity_on_hand, 2) }} {{ $item->unit }}</td>
                                        <td class="text-end">{{ number_format($item->quantity_available ?? $item->quantity_on_hand, 2) }} {{ $item->unit }}</td>
                                        <td class="text-end">{{ number_format($item->reorder_level, 2) }} {{ $item->unit }}</td>
                                        <td class="text-end">{{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->quantity_on_hand * $item->unit_cost, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($item->is_organic_approved)
                                                <span class="badge bg-success"><i class="fas fa-leaf"></i> Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                                            No stock items found matching your criteria
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($items->hasPages())
                <div class="card-footer">
                    {{ $items->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
