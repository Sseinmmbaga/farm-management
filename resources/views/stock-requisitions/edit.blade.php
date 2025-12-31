@extends('layouts.base')

@section('title', 'Edit Stock Requisition #' . $stockRequisition->requisition_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Stock Requisition #{{ $stockRequisition->requisition_number }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock-requisitions.show', $stockRequisition) }}" class="btn btn-light">
                                <i class="fas fa-eye me-1"></i> View Details
                            </a>
                            <a href="{{ route('stock-requisitions.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('stock-requisitions.update', $stockRequisition) }}" id="stockRequisitionForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-select" required {{ $stockRequisition->is_editable ? '' : 'disabled' }}>
                                        <option value="">Select Employee</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $stockRequisition->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(!$stockRequisition->is_editable)
                                        <input type="hidden" name="user_id" value="{{ $stockRequisition->user_id }}">
                                        <small class="text-muted">Employee cannot be changed after approval.</small>
                                    @endif
                                    @error('user_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department (Optional)</label>
                                    <select name="department_id" id="department_id" class="form-select" {{ $stockRequisition->is_editable ? '' : 'disabled' }}>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $stockRequisition->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(!$stockRequisition->is_editable)
                                        <input type="hidden" name="department_id" value="{{ $stockRequisition->department_id }}">
                                        <small class="text-muted">Department cannot be changed after approval.</small>
                                    @endif
                                    @error('department_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="required_date" class="form-label">Required By Date (Optional)</label>
                                    <input type="date" class="form-control" id="required_date" name="required_date" 
                                        value="{{ old('required_date', $stockRequisition->required_date ? $stockRequisition->required_date->format('Y-m-d') : '') }}" 
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}" {{ $stockRequisition->is_editable ? '' : 'disabled' }}>
                                    @if(!$stockRequisition->is_editable)
                                        <input type="hidden" name="required_date" value="{{ $stockRequisition->required_date ? $stockRequisition->required_date->format('Y-m-d') : '' }}">
                                        <small class="text-muted">Required date cannot be changed after approval.</small>
                                    @endif
                                    <small class="text-muted">If left blank, no specific date required.</small>
                                    @error('required_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="purpose" name="purpose" rows="2" required {{ $stockRequisition->is_editable ? '' : 'disabled' }}>{{ old('purpose', $stockRequisition->purpose) }}</textarea>
                                    @if(!$stockRequisition->is_editable)
                                        <input type="hidden" name="purpose" value="{{ $stockRequisition->purpose }}">
                                        <small class="text-muted">Purpose cannot be changed after approval.</small>
                                    @endif
                                    <small class="text-muted">Describe the purpose of this requisition (10-1000 characters).</small>
                                    @error('purpose')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" {{ $stockRequisition->is_editable ? '' : 'disabled' }}>{{ old('notes', $stockRequisition->notes) }}</textarea>
                            @if(!$stockRequisition->is_editable)
                                <input type="hidden" name="notes" value="{{ $stockRequisition->notes }}">
                                <small class="text-muted">Notes cannot be changed after approval.</small>
                            @endif
                            @error('notes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">
                            <i class="fas fa-boxes me-2"></i> Requisition Items <span class="text-danger">*</span>
                            <small class="text-muted">Add at least one item</small>
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30%">Item</th>
                                        <th width="15%">Quantity</th>
                                        <th width="15%">Unit</th>
                                        <th width="15%">Unit Price (Optional)</th>
                                        <th width="20%">Notes (Optional)</th>
                                        <th width="5%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <!-- Dynamic rows will be inserted here -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end">
                                            @if($stockRequisition->is_editable)
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="addItemRow">
                                                    <i class="fas fa-plus me-1"></i> Add Item
                                                </button>
                                            @else
                                                <small class="text-muted">Items cannot be changed after approval.</small>
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> Stock requisition details cannot be changed after approval (except for notes). 
                            If you need to make changes after approval, please cancel the requisition and create a new one.
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Current Status:</strong> 
                            <span class="badge bg-{{ $stockRequisition->status_color }}">
                                {{ $stockRequisition->status_display }}
                            </span>
                            @if($stockRequisition->approved_at)
                                <br>Approved by {{ $stockRequisition->approvedBy->name ?? 'N/A' }} on {{ $stockRequisition->approved_at->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            @if($stockRequisition->is_editable)
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Update Requisition
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" disabled>
                                    <i class="fas fa-ban me-1"></i> Cannot Edit (Already Approved)
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .item-row {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemsBody = document.getElementById('itemsBody');
        const addItemButton = document.getElementById('addItemRow');
        const stockItems = @json($stockItems->map(function($item) {
            return ['id' => $item->id, 'name' => $item->name, 'unit' => $item->unit_of_measure ?? 'pieces'];
        }));

        // Load existing items
        const existingItems = @json($stockRequisition->items->map(function($item) {
            return [
                'id' => $item->id,
                'stock_item_id' => $item->stock_item_id,
                'quantity_requested' => $item->quantity_requested,
                'unit_of_measure' => $item->unit_of_measure,
                'unit_price' => $item->unit_price,
                'notes' => $item->notes,
            ];
        }));

        function addItemRow(itemData = {}, isNew = false) {
            const rowIndex = itemsBody.children.length;
            const row = document.createElement('tr');
            row.className = 'item-row';
            row.innerHTML = `
                <td>
                    <select name="items[${rowIndex}][stock_item_id]" class="form-select item-select" required {{ $stockRequisition->is_editable ? '' : 'disabled' }}>
                        <option value="">Select Item</option>
                        ${stockItems.map(item => `
                            <option value="${item.id}" ${itemData.stock_item_id == item.id ? 'selected' : ''}>
                                ${item.name}
                            </option>
                        `).join('')}
                    </select>
                    ${!isNew && itemData.id ? `<input type="hidden" name="items[${rowIndex}][id]" value="${itemData.id}">` : ''}
                </td>
                <td>
                    <input type="number" step="0.001" min="0.001" class="form-control quantity" 
                           name="items[${rowIndex}][quantity_requested]" 
                           value="${itemData.quantity_requested || ''}" required {{ $stockRequisition->is_editable ? '' : 'disabled' }}>
                </td>
                <td>
                    <input type="text" class="form-control unit" 
                           name="items[${rowIndex}][unit_of_measure]" 
                           value="${itemData.unit_of_measure || 'pieces'}" placeholder="pieces" {{ $stockRequisition->is_editable ? '' : 'disabled' }}>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control price" 
                           name="items[${rowIndex}][unit_price]" 
                           value="${itemData.unit_price || ''}" placeholder="0.00" {{ $stockRequisition->is_editable ? '' : 'disabled' }}>
                </td>
                <td>
                    <input type="text" class="form-control" 
                           name="items[${rowIndex}][notes]" 
                           value="${itemData.notes || ''}" placeholder="Optional notes" {{ $stockRequisition->is_editable ? '' : 'disabled' }}>
                </td>
                <td class="text-center">
                    @if($stockRequisition->is_editable)
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            `;
            itemsBody.appendChild(row);

            // Attach event to remove button
            if ({{ $stockRequisition->is_editable ? 'true' : 'false' }}) {
                row.querySelector('.remove-row').addEventListener('click', function() {
                    row.remove();
                    reindexRows();
                });
            }

            // Auto-fill unit when item selected
            if ({{ $stockRequisition->is_editable ? 'true' : 'false' }}) {
                row.querySelector('.item-select').addEventListener('change', function() {
                    const selectedId = this.value;
                    const selectedItem = stockItems.find(item => item.id == selectedId);
                    if (selectedItem && selectedItem.unit) {
                        row.querySelector('.unit').value = selectedItem.unit;
                    }
                });
            }
        }

        function reindexRows() {
            const rows = itemsBody.querySelectorAll('.item-row');
            rows.forEach((row, index) => {
                // Update all input names with new index
                row.querySelectorAll('[name]').forEach(input => {
                    const name = input.getAttribute('name');
                    const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
                    input.setAttribute('name', newName);
                });
            });
        }

        // Populate existing items
        existingItems.forEach(item => addItemRow(item, false));

        // If no items (should not happen), add one empty row
        if (existingItems.length === 0 && {{ $stockRequisition->is_editable ? 'true' : 'false' }}) {
            addItemRow();
        }

        // Add item button
        if (addItemButton) {
            addItemButton.addEventListener('click', () => addItemRow({}, true));
        }

        // Pre-populate with old input if validation failed
        @if(old('items'))
            const oldItems = @json(old('items'));
            itemsBody.innerHTML = '';
            oldItems.forEach(item => addItemRow(item, true));
        @endif
    });
</script>
@endpush