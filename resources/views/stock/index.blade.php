@extends('layouts.base')

@section('title', 'Stock Inventory')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-boxes me-2"></i> Stock Inventory
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock.low-stock') }}" class="btn btn-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i> Low Stock
                            </a>
                            <a href="{{ route('stock.critical') }}" class="btn btn-danger">
                                <i class="fas fa-skull-crossbones me-1"></i> Critical
                            </a>
                            <a href="{{ route('stock.out-of-stock') }}" class="btn btn-dark">
                                <i class="fas fa-times-circle me-1"></i> Out of Stock
                            </a>
                            <a href="{{ route('stock.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Add New Item
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Summary Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Items</h6>
                                            <h3 class="mb-0">{{ $stats['total_items'] }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-boxes fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">In Stock</h6>
                                            <h3 class="mb-0">{{ $stats['in_stock'] }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Low Stock</h6>
                                            <h3 class="mb-0">{{ $stats['low_stock'] }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Out of Stock</h6>
                                            <h3 class="mb-0">{{ $stats['out_of_stock'] }}</h3>
                                        </div>
                                        <div class="bg-danger text-white rounded-circle p-3">
                                            <i class="fas fa-times-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Search & Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('stock.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by name, code, SKU..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select name="category_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                                <option value="critical" {{ request('status') == 'critical' ? 'selected' : '' }}>Critical</option>
                                <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        @if(request('search') || request('category_id') || request('status'))
                            <div class="col-md-1">
                                <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
                
                <!-- Stock Items Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">
                                        <input class="form-check-input" type="checkbox" id="selectAllHeader">
                                    </th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Reorder Level</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockItems as $item)
                                    <tr>
                                        <td>
                                            <input class="form-check-input item-checkbox" type="checkbox" name="selected_items[]" value="{{ $item->id }}">
                                        </td>
                                        <td>
                                            <strong class="text-primary">{{ $item->code }}</strong>
                                            @if($item->sku)
                                                <br><small class="text-muted">SKU: {{ $item->sku }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($item->name, 0, 1) }}
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
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ number_format($item->quantity_on_hand, 2) }}</span>
                                                <small class="text-muted">Available: {{ number_format($item->quantity_available, 2) }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $item->unit }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item->stock_status_color }}">
                                                <i class="fas fa-circle me-1"></i> {{ $item->stock_status }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ number_format($item->reorder_level, 2) }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('stock.show', $item) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('stock.edit', $item) }}" 
                                                   class="btn btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        title="Delete"
                                                        onclick="confirmDelete('{{ route('stock.destroy', $item) }}', '{{ $item->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-boxes fa-3x mb-3"></i>
                                                <h5>No stock items found</h5>
                                                <p>Start by adding your first stock item</p>
                                                <a href="{{ route('stock.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> Add New Item
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
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
                
                <!-- Bulk Actions & Export -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-2" id="selectedCount">0 items selected</span>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('stock.reports.summary') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-chart-bar me-1"></i> Summary Report
                            </a>
                            <a href="{{ route('stock.export.csv') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-file-csv me-1"></i> Export to CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete stock item <strong id="itemName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, name) {
        document.getElementById('itemName').textContent = name;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Select All functionality
    const selectAllHeader = document.getElementById('selectAllHeader');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectedCountEl = document.getElementById('selectedCount');

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        selectedCountEl.textContent = checkedCount + ' item' + (checkedCount !== 1 ? 's' : '') + ' selected';
    }

    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }

    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update header checkbox state
            const allChecked = document.querySelectorAll('.item-checkbox:checked').length === itemCheckboxes.length;
            if (selectAllHeader) {
                selectAllHeader.checked = allChecked;
            }
            updateSelectedCount();
        });
    });
</script>
@endpush
@endsection