@extends('layouts.base')

@section('title', 'Create Stock Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i> Create Stock Request
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock.requests.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('stock.requests.store') }}" id="stockRequestForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="farmer_id" class="form-label">Farmer (Optional)</label>
                                    <select name="farmer_id" id="farmer_id" class="form-select">
                                        <option value="">Select Farmer</option>
                                        @foreach($farmers as $farmer)
                                            <option value="{{ $farmer->id }}" {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                                {{ $farmer->full_name }} ({{ $farmer->registration_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="season_id" class="form-label">Season (Optional)</label>
                                    <select name="season_id" id="season_id" class="form-select">
                                        <option value="">Select Season</option>
                                        @foreach($seasons as $season)
                                            <option value="{{ $season->id }}" {{ old('season_id') == $season->id ? 'selected' : '' }}>
                                                {{ $season->name }} ({{ $season->year }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-select" required>
                                        <option value="">Select Priority</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="needed_by" class="form-label">Needed By</label>
                                    <input type="date" class="form-control" id="needed_by" name="needed_by" value="{{ old('needed_by') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose</label>
                                    <input type="text" class="form-control" id="purpose" name="purpose" value="{{ old('purpose') }}" placeholder="e.g., Seeding, Fertilizer">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3">
                            <i class="fas fa-boxes me-2"></i> Request Items
                            <button type="button" class="btn btn-sm btn-primary float-end" id="addItemBtn">
                                <i class="fas fa-plus me-1"></i> Add Item
                            </button>
                        </h5>
                        
                        <div id="itemsContainer">
                            <!-- Items will be added dynamically -->
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Add at least one item to the request.
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Save as Draft
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template for item row (hidden) -->
<template id="itemTemplate">
    <div class="item-row border rounded p-3 mb-3 bg-white">
        <div class="row align-items-center">
            <div class="col-md-5">
                <label class="form-label">Stock Item <span class="text-danger">*</span></label>
                <select name="items[][stock_item_id]" class="form-select stock-item-select" required>
                    <option value="">Select Item</option>
                    @foreach($stockItems as $item)
                        <option value="{{ $item->id }}" data-unit="{{ $item->unit }}" data-price="{{ $item->unit_price }}">
                            {{ $item->name }} ({{ $item->code }}) - {{ $item->quantity_on_hand }} {{ $item->unit }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity Requested <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" class="form-control quantity" name="items[][quantity_requested]" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <input type="text" class="form-control unit-display" readonly>
            </div>
            <div class="col-md-1">
                <label class="form-label invisible">Actions</label>
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <label class="form-label">Notes (Optional)</label>
                <textarea class="form-control" name="items[][notes]" rows="1" placeholder="Item-specific notes"></textarea>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemsContainer = document.getElementById('itemsContainer');
        const addItemBtn = document.getElementById('addItemBtn');
        const itemTemplate = document.getElementById('itemTemplate');
        
        let itemIndex = 0;
        
        function addItem() {
            const clone = itemTemplate.content.cloneNode(true);
            const newRow = clone.querySelector('.item-row');
            
            // Update field names with index
            const stockItemSelect = newRow.querySelector('.stock-item-select');
            const quantityInput = newRow.querySelector('.quantity');
            const unitDisplay = newRow.querySelector('.unit-display');
            
            stockItemSelect.name = `items[${itemIndex}][stock_item_id]`;
            quantityInput.name = `items[${itemIndex}][quantity_requested]`;
            newRow.querySelector('textarea').name = `items[${itemIndex}][notes]`;
            
            // Update unit when stock item changes
            stockItemSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const unit = selectedOption.getAttribute('data-unit') || '';
                unitDisplay.value = unit;
            });
            
            // Remove item
            newRow.querySelector('.remove-item').addEventListener('click', function() {
                newRow.remove();
            });
            
            itemsContainer.appendChild(newRow);
            itemIndex++;
        }
        
        addItemBtn.addEventListener('click', addItem);
        
        // Add first item automatically
        addItem();
    });
</script>
@endpush
@endsection