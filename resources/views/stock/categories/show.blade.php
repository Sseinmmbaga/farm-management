@extends('layouts.base')

@section('title', 'Stock Category Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header with Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('stock-categories.index') }}">Stock Categories</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-tag me-2"></i>
                        {{ $category->name }}
                        @if($category->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </h1>
                    <p class="text-muted mb-0">Category Code: {{ $category->code }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('stock-categories.edit', $category) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('stock-categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Categories
                    </a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="row">
                <!-- Left Column: Category Info -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Category Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Name:</th>
                                            <td>{{ $category->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Swahili Name:</th>
                                            <td>{{ $category->name_sw ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Code:</th>
                                            <td><strong class="text-primary">{{ $category->code }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Parent Category:</th>
                                            <td>
                                                @if($category->parent)
                                                    <a href="{{ route('stock-categories.show', $category->parent) }}" class="badge bg-secondary text-decoration-none">
                                                        <i class="fas fa-level-up-alt me-1"></i> {{ $category->parent->name }}
                                                    </a>
                                                @else
                                                    <span class="badge bg-light text-dark">Root Category</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Sort Order:</th>
                                            <td>{{ $category->sort_order }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                @if($category->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created:</th>
                                            <td>
                                                {{ $category->created_at->format('M d, Y') }}
                                                <br>
                                                <small class="text-muted">{{ $category->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated:</th>
                                            <td>
                                                {{ $category->updated_at->format('M d, Y') }}
                                                <br>
                                                <small class="text-muted">{{ $category->updated_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            @if($category->description)
                                <div class="mt-3">
                                    <h6>Description:</h6>
                                    <div class="alert alert-light">
                                        {{ $category->description }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Sub‑Categories -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-sitemap me-2"></i>
                                Sub‑Categories
                                <span class="badge bg-light text-dark ms-2">{{ $category->children->count() }}</span>
                            </h5>
                            <a href="{{ route('stock-categories.create', ['parent_id' => $category->id]) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i> Add Sub‑Category
                            </a>
                        </div>
                        <div class="card-body">
                            @if($category->children->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Name</th>
                                                <th>Items Count</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($category->children as $child)
                                                <tr>
                                                    <td>{{ $child->code }}</td>
                                                    <td>
                                                        <a href="{{ route('stock-categories.show', $child) }}" class="text-decoration-none">
                                                            <i class="fas fa-folder me-1"></i> {{ $child->name }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $child->items_count ?? $child->items->count() }}</span>
                                                    </td>
                                                    <td>
                                                        @if($child->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('stock-categories.show', $child) }}" class="btn btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('stock-categories.edit', $child) }}" class="btn btn-outline-warning">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-sitemap fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No sub‑categories under this category.</p>
                                    <a href="{{ route('stock-categories.create', ['parent_id' => $category->id]) }}" class="btn btn-success mt-2">
                                        <i class="fas fa-plus me-1"></i> Add First Sub‑Category
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Stats & Actions -->
                <div class="col-lg-4">
                    <!-- Statistics Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                Statistics
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="display-6 text-primary">{{ $category->items_count ?? $category->items->count() }}</div>
                                    <small class="text-muted">Stock Items</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="display-6 text-success">{{ $category->children->count() }}</div>
                                    <small class="text-muted">Sub‑Categories</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stock Items Preview -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-boxes me-2"></i>
                                Recent Stock Items
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($category->items->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($category->items->take(5) as $item)
                                        <a href="{{ route('stock.show', $item) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $item->name }}</h6>
                                                <small class="text-muted">{{ number_format($item->quantity_available, 2) }} {{ $item->unit }}</small>
                                            </div>
                                            <small class="text-muted">Code: {{ $item->code }}</small>
                                        </a>
                                    @endforeach
                                </div>
                                @if($category->items->count() > 5)
                                    <div class="text-center mt-2">
                                        <a href="{{ route('stock.index', ['category_id' => $category->id]) }}" class="btn btn-outline-warning btn-sm">
                                            View All Items
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-boxes fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No stock items in this category.</p>
                                    <a href="{{ route('stock.create', ['category_id' => $category->id]) }}" class="btn btn-warning mt-2">
                                        <i class="fas fa-plus me-1"></i> Add Stock Item
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    
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
                                <a href="{{ route('stock-categories.edit', $category) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit me-2"></i> Edit Category
                                </a>
                                <a href="{{ route('stock.create', ['category_id' => $category->id]) }}" class="btn btn-outline-success">
                                    <i class="fas fa-plus-circle me-2"></i> Add Stock Item
                                </a>
                                <a href="{{ route('stock-categories.create', ['parent_id' => $category->id]) }}" class="btn btn-outline-info">
                                    <i class="fas fa-plus-square me-2"></i> Add Sub‑Category
                                </a>
                                <a href="{{ route('stock.index', ['category_id' => $category->id]) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-list me-2"></i> View All Items
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection