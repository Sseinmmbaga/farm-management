@extends('layouts.base')

@section('title', 'Edit Stock Item')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Stock Item: {{ $stockItem->name }}
                        </h4>
                        <a href="{{ route('stock.show', $stockItem) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Item
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('stock.update', $stockItem) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-info-circle me-2"></i> Basic Information
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category *</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $stockItem->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Item Name *</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $stockItem->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="name_sw" class="form-label">Jina la Kigiriki (Swahili)</label>
                                    <input type="text" 
                                           class="form-control @error('name_sw') is-invalid @enderror" 
                                           id="name_sw" 
                                           name="name_sw" 
                                           value="{{ old('name_sw', $stockItem->name_sw) }}" 
                                           placeholder="Optional Swahili name">
                                    @error('name_sw')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="code" class="form-label">Item Code *</label>
                                        <input type="text" 
                                               class="form-control @error('code') is-invalid @enderror" 
                                               id="code" 
                                               name="code" 
                                               value="{{ old('code', $stockItem->code) }}" 
                                               required>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Unique identifier for the item</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sku" class="form-label">SKU (Optional)</label>
                                        <input type="text" 
                                               class="form-control @error('sku') is-invalid @enderror" 
                                               id="sku" 
                                               name="sku" 
                                               value="{{ old('sku', $stockItem->sku) }}">
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Stock Keeping Unit</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3">{{ old('description', $stockItem->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-balance-scale me-2"></i> Quantities & Units
                                </h5>
                                
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="quantity_on_hand" class="form-label">On Hand</label>
                                        <input type="number" 
                                               step="0.01" 
                                               class="form-control @error('quantity_on_hand') is-invalid @enderror" 
                                               id="quantity_on_hand" 
                                               name="quantity_on_hand" 
                                               value="{{ old('quantity_on_hand', $stockItem->quantity_on_hand) }}">
                                        @error('quantity_on_hand')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="quantity_reserved" class="form-label">Reserved</label>
                                        <input type="number" 
                                               step="0.01" 
                                               class="form-control @error('quantity_reserved') is-invalid @enderror" 
                                               id="quantity_reserved" 
                                               name="quantity_reserved" 
                                               value="{{ old('quantity_reserved', $stockItem->quantity_reserved) }}">
                                        @error('quantity_reserved')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="unit" class="form-label">Unit *</label>
                                        <select class="form-select @error('unit') is-invalid @enderror" 
                                                id="unit" 
                                                name="unit" 
                                                required>
                                            <option value="">Select Unit</option>
                                            @foreach($units as $key => $label)
                                                <option value="{{ $key }}" {{ old('unit', $stockItem->unit) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('unit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="reorder_level" class="form-label">Reorder Level</label>
                                        <input type="number" 
                                               step="0.01" 
                                               class="form-control @error('reorder_level') is-invalid @enderror" 
                                               id="reorder_level" 
                                               name="reorder_level" 
                                               value="{{ old('reorder_level', $stockItem->reorder_level) }}">
                                        @error('reorder_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Minimum quantity before reorder</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="reorder_quantity" class="form-label">Reorder Quantity</label>
                                        <input type="number" 
                                               step="0.01" 
                                               class="form-control @error('reorder_quantity') is-invalid @enderror" 
                                               id="reorder_quantity" 
                                               name="reorder_quantity" 
                                               value="{{ old('reorder_quantity', $stockItem->reorder_quantity) }}">
                                        @error('reorder_quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Quantity to reorder</small>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3 mt-4 border-bottom pb-2">
                                    <i class="fas fa-money-bill-wave me-2"></i> Pricing
                                </h5>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="unit_cost" class="form-label">Unit Cost ({{ config('units.currency', 'TZS') }})</label>
                                        <input type="number" 
                                               step="0.01" 
                                               class="form-control @error('unit_cost') is-invalid @enderror" 
                                               id="unit_cost" 
                                               name="unit_cost" 
                                               value="{{ old('unit_cost', $stockItem->unit_cost) }}">
                                        @error('unit_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="unit_price" class="form-label">Unit Price ({{ config('units.currency', 'TZS') }})</label>
                                        <input type="number" 
                                               step="0.01" 
                                               class="form-control @error('unit_price') is-invalid @enderror" 
                                               id="unit_price" 
                                               name="unit_price" 
                                               value="{{ old('unit_price', $stockItem->unit_price) }}">
                                        @error('unit_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select class="form-select @error('currency') is-invalid @enderror" 
                                            id="currency" 
                                            name="currency">
                                        <option value="TZS" {{ old('currency', $stockItem->currency) == 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling</option>
                                        <option value="USD" {{ old('currency', $stockItem->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="EUR" {{ old('currency', $stockItem->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-industry me-2"></i> Manufacturer & Brand
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="brand" class="form-label">Brand</label>
                                    <input type="text" 
                                           class="form-control @error('brand') is-invalid @enderror" 
                                           id="brand" 
                                           name="brand" 
                                           value="{{ old('brand', $stockItem->brand) }}">
                                    @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="manufacturer" class="form-label">Manufacturer</label>
                                    <input type="text" 
                                           class="form-control @error('manufacturer') is-invalid @enderror" 
                                           id="manufacturer" 
                                           name="manufacturer" 
                                           value="{{ old('manufacturer', $stockItem->manufacturer) }}">
                                    @error('manufacturer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="warehouse_location" class="form-label">Warehouse Location</label>
                                        <input type="text" 
                                               class="form-control @error('warehouse_location') is-invalid @enderror" 
                                               id="warehouse_location" 
                                               name="warehouse_location" 
                                               value="{{ old('warehouse_location', $stockItem->warehouse_location) }}">
                                        @error('warehouse_location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bin_location" class="form-label">Bin Location</label>
                                        <input type="text" 
                                               class="form-control @error('bin_location') is-invalid @enderror" 
                                               id="bin_location" 
                                               name="bin_location" 
                                               value="{{ old('bin_location', $stockItem->bin_location) }}">
                                        @error('bin_location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-tags me-2"></i> Additional Settings
                                </h5>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('is_organic_approved') is-invalid @enderror" 
                                                   type="checkbox" 
                                                   role="switch" 
                                                   id="is_organic_approved" 
                                                   name="is_organic_approved" 
                                                   {{ old('is_organic_approved', $stockItem->is_organic_approved) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_organic_approved">Organic Approved</label>
                                            @error('is_organic_approved')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('requires_batch_tracking') is-invalid @enderror" 
                                                   type="checkbox" 
                                                   role="switch" 
                                                   id="requires_batch_tracking" 
                                                   name="requires_batch_tracking" 
                                                   {{ old('requires_batch_tracking', $stockItem->requires_batch_tracking) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="requires_batch_tracking">Requires Batch Tracking</label>
                                            @error('requires_batch_tracking')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                               type="checkbox" 
                                               role="switch" 
                                               id="is_active" 
                                               name="is_active" 
                                               {{ old('is_active', $stockItem->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Item Image</label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Max file size: 2MB. Supported formats: JPG, PNG, GIF.</small>
                                    @if($stockItem->image)
                                        <div class="mt-2">
                                            <small>Current image:</small>
                                            <img src="{{ asset('storage/' . $stockItem->image) }}" alt="{{ $stockItem->name }}" class="img-thumbnail" style="max-width: 100px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('stock.show', $stockItem) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i> Update Stock Item
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate code if left empty
    document.getElementById('code').addEventListener('blur', function() {
        if (!this.value.trim()) {
            // You can implement auto-generation via AJAX if needed
            // For now, leave empty
        }
    });
</script>
@endpush
@endsection