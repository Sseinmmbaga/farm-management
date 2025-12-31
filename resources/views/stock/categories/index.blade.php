@extends('layouts.base')

@section('title', 'Stock Categories')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-tags me-2"></i> Stock Categories
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock-categories.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Add New Category
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Search & Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('stock-categories.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by name, code..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select name="parent_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Parent Categories</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="active" class="form-select" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="true" {{ request('active') == 'true' ? 'selected' : '' }}>Active</option>
                                <option value="false" {{ request('active') == 'false' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        @if(request('search') || request('parent_id') || request('active'))
                            <div class="col-md-1">
                                <a href="{{ route('stock-categories.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
                
                <!-- Categories Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Swahili Name</th>
                                    <th>Parent Category</th>
                                    <th>Items Count</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $category->code }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($category->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $category->name }}</strong>
                                                    @if($category->description)
                                                        <br><small class="text-muted">{{ Str::limit($category->description, 40) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $category->name_sw ?? '-' }}
                                        </td>
                                        <td>
                                            @if($category->parent)
                                                <span class="badge bg-secondary">{{ $category->parent->name }}</span>
                                            @else
                                                <span class="badge bg-light text-dark">Root</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-box me-1"></i>
                                                {{ $category->items_count ?? $category->items->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('stock-categories.show', $category) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('stock-categories.edit', $category) }}" 
                                                   class="btn btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        title="Delete"
                                                        onclick="confirmDelete('{{ route('stock-categories.destroy', $category) }}', '{{ $category->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-tags fa-3x mb-3"></i>
                                                <h5>No categories found</h5>
                                                <p>Start by adding your first stock category</p>
                                                <a href="{{ route('stock-categories.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> Add New Category
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($categories->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} categories
                                </div>
                                <div>
                                    {{ $categories->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
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
                <p>Are you sure you want to delete category <strong id="categoryName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, name) {
        document.getElementById('categoryName').textContent = name;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endpush
@endsection