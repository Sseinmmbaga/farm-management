@extends('layouts.base')

@section('title', 'Edit Stock Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Stock Request: {{ $stockRequest->request_number }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock.requests.show', $stockRequest) }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to Details
                            </a>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('stock.requests.update', $stockRequest) }}" id="stockRequestForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="farmer_id" class="form-label">Farmer (Optional)</label>
                                    <select name="farmer_id" id="farmer_id" class="form-select">
                                        <option value="">Select Farmer</option>
                                        @foreach($farmers as $farmer)
                                            <option value="{{ $farmer->id }}" {{ old('farmer_id', $stockRequest->farmer_id) == $farmer->id ? 'selected' : '' }}>
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
                                            <option value="{{ $season->id }}" {{ old('season_id', $stockRequest->season_id) == $season->id ? 'selected' : '' }}>
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
                                        <option value="low" {{ old('priority', $stockRequest->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="normal" {{ old('priority', $stockRequest->priority) == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="high" {{ old('priority', $stockRequest->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority', $stockRequest->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="needed_by" class="form-label">Needed By</label>
                                    <input type="date" class="form-control" id="needed_by" name="needed_by" value="{{ old('needed_by', $stockRequest->needed_by ? $stockRequest->needed_by->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose</label>
                                    <input type="text" class="form-control" id="purpose" name="purpose" value="{{ old('purpose', $stockRequest->purpose) }}" placeholder="e.g., Seeding, Fertilizer">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes', $stockRequest->notes) }}</textarea>
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
                            @foreach($stockRequest->items as $index => $item)
                                <div class="item-row border rounded p-3 mb-3 bg-white">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <label class="form-label">Stock Item <span class="text-danger">*</span></label>
                                            <select name="items[{{ $index }}][stock_item_id]" class="form-select stock-item-select" required>
                                                <option value="">Select Item</option>
                                                @foreach($stockItems as $stockItem)
                                                    <option value="{{ $stockItem->id }}" 
                                                            data-unit="{{ $stockItem->unit }}"
                                                            {{ old("items.$index.stock_item_id", $item->stock_item_id) == $stockItem->id ? 'selected' : '' }}>
                                                        {{ $stockItem->name }} ({{ $stockItem->code }}) - {{ $stockItem->quantity_on_hand }} {{ $stockItem->unit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Quantity Requested <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0.01" class="form-control quantity" 
                                                   name="items[{{ $index }}][quantity_requested]" 
                                                   value="{{ old("items.$index.quantity_requested", $item->quantity_requested) }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Unit</label>
                                            <input type="text" class="form-control unit-display" 
                                                   value="{{ $item->stockItem->unit ?? '' }}" readonly>
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
                                            <textarea class="form-control" name="items[{{ $index }}][notes]" rows="1" placeholder="Item-specific notes">{{ old("items.$index.notes", $item->notes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
                                <i class="fas fa-save me-1"></i> Update Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template for new item row (hidden) -->
<template id="itemTemplate">
    <div class="item-row border rounded p-3 mb-3 bg-white">
        <div class="row align-items-center">
            <div class="col-md-5">
                <label class="form-label">Stock Item <span class="text-danger">*</span></label>
                <select name="items[][stock_item_id]" class="form-select stock-item-select" required>
                    <option value="">Select Item</option>
                    @foreach($stockItems as $item)
                        <option value="{{ $item->id }}" data-unit="{{ $item->unit }}">
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
        
        let itemIndex = {{ $stockRequest->items->count() }};
        
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
        
        // Attach unit display to existing items
        document.querySelectorAll('.stock-item-select').forEach(select => {
            const unitDisplay = select.closest('.item-row').querySelector('.unit-display');
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const unit = selectedOption.getAttribute('data-unit') || '';
                unitDisplay.value = unit;
            });
        });
        
        // Attach remove event to existing items
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.item-row').remove();
            });
        });
    });
</script>
@endpush
@endsection