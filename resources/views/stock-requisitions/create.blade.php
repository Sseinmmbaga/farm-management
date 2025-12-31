@extends('layouts.base')

@section('title', 'Create Stock Requisition')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i> Create Stock Requisition
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock-requisitions.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('stock-requisitions.store') }}" id="stockRequisitionForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-select" required>
                                        <option value="">Select Employee</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department (Optional)</label>
                                    <select name="department_id" id="department_id" class="form-select">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                        value="{{ old('required_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    <small class="text-muted">If left blank, no specific date required.</small>
                                    @error('required_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="purpose" name="purpose" rows="2" required>{{ old('purpose') }}</textarea>
                                    <small class="text-muted">Describe the purpose of this requisition (10-1000 characters).</small>
                                    @error('purpose')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
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
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="addItemRow">
                                                <i class="fas fa-plus me-1"></i> Add Item
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Note:</strong> The requisition will be submitted for approval. You can edit it while it's still in draft or pending status.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Reminder:</strong> Ensure all item details are accurate before submission.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Submit Requisition
                            </button>
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
    .item-row:last-child {
        border-bottom: 2px solid #dee2e6;
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

        // Add initial row if none
        if (itemsBody.children.length === 0) {
            addItemRow();
        }

        addItemButton.addEventListener('click', addItemRow);

        function addItemRow(itemData = {}) {
            const rowIndex = itemsBody.children.length;
            const row = document.createElement('tr');
            row.className = 'item-row';
            row.innerHTML = `
                <td>
                    <select name="items[${rowIndex}][stock_item_id]" class="form-select item-select" required>
                        <option value="">Select Item</option>
                        ${stockItems.map(item => `
                            <option value="${item.id}" ${itemData.stock_item_id == item.id ? 'selected' : ''}>
                                ${item.name}
                            </option>
                        `).join('')}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.001" min="0.001" class="form-control quantity" 
                           name="items[${rowIndex}][quantity_requested]" 
                           value="${itemData.quantity_requested || ''}" required>
                </td>
                <td>
                    <input type="text" class="form-control unit" 
                           name="items[${rowIndex}][unit_of_measure]" 
                           value="${itemData.unit_of_measure || 'pieces'}" placeholder="pieces">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control price" 
                           name="items[${rowIndex}][unit_price]" 
                           value="${itemData.unit_price || ''}" placeholder="0.00">
                </td>
                <td>
                    <input type="text" class="form-control" 
                           name="items[${rowIndex}][notes]" 
                           value="${itemData.notes || ''}" placeholder="Optional notes">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            itemsBody.appendChild(row);

            // Attach event to remove button
            row.querySelector('.remove-row').addEventListener('click', function() {
                row.remove();
                reindexRows();
            });

            // Auto-fill unit when item selected
            row.querySelector('.item-select').addEventListener('change', function() {
                const selectedId = this.value;
                const selectedItem = stockItems.find(item => item.id == selectedId);
                if (selectedItem && selectedItem.unit) {
                    row.querySelector('.unit').value = selectedItem.unit;
                }
            });
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

        // Pre-populate with old input if validation failed
        @if(old('items'))
            const oldItems = @json(old('items'));
            itemsBody.innerHTML = '';
            oldItems.forEach(item => addItemRow(item));
        @endif
    });
</script>
@endpush