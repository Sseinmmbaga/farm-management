@extends('layouts.base')

@section('title', 'Record Stock Intake')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('stock.index') }}">Stock Inventory</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stock.transactions.index') }}">Transactions</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Record Intake</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-arrow-down text-success me-2"></i>
                        Record Stock Intake
                    </h1>
                    <p class="text-muted mb-0">Add new stock to inventory</p>
                </div>
                <a href="{{ route('stock.transactions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Transactions
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-plus-circle me-2"></i>
                                Intake Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('stock.transactions.intake.store') }}" id="intakeForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="stock_item_id" class="form-label">Stock Item <span class="text-danger">*</span></label>
                                        <select name="stock_item_id" id="stock_item_id" class="form-select @error('stock_item_id') is-invalid @enderror" required>
                                            <option value="">Select Stock Item</option>
                                            @foreach($stockItems as $item)
                                                <option value="{{ $item->id }}"
                                                        data-unit="{{ $item->unit }}"
                                                        data-cost="{{ $item->unit_cost }}"
                                                        data-current="{{ $item->quantity_on_hand }}"
                                                        {{ old('stock_item_id') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->name }} ({{ $item->code }})
                                                    @if($item->category) - {{ $item->category->name }} @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('stock_item_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number"
                                                   name="quantity"
                                                   id="quantity"
                                                   class="form-control @error('quantity') is-invalid @enderror"
                                                   step="0.01"
                                                   min="0.01"
                                                   value="{{ old('quantity') }}"
                                                   required>
                                            <span class="input-group-text" id="unit-display">units</span>
                                        </div>
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="unit_cost" class="form-label">Unit Cost</label>
                                        <div class="input-group">
                                            <span class="input-group-text">TZS</span>
                                            <input type="number"
                                                   name="unit_cost"
                                                   id="unit_cost"
                                                   class="form-control @error('unit_cost') is-invalid @enderror"
                                                   step="0.01"
                                                   min="0"
                                                   value="{{ old('unit_cost') }}"
                                                   placeholder="Leave blank to use default">
                                        </div>
                                        <small class="text-muted">Leave blank to use the item's default unit cost</small>
                                        @error('unit_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="transaction_date" class="form-label">Transaction Date</label>
                                        <input type="date"
                                               name="transaction_date"
                                               id="transaction_date"
                                               class="form-control @error('transaction_date') is-invalid @enderror"
                                               value="{{ old('transaction_date', date('Y-m-d')) }}">
                                        @error('transaction_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="reference_number" class="form-label">Reference Number</label>
                                        <input type="text"
                                               name="reference_number"
                                               id="reference_number"
                                               class="form-control @error('reference_number') is-invalid @enderror"
                                               value="{{ old('reference_number') }}"
                                               placeholder="Auto-generated if left blank">
                                        <small class="text-muted">e.g., Invoice number, PO number, etc.</small>
                                        @error('reference_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="source" class="form-label">Source/Supplier</label>
                                        <input type="text"
                                               name="source"
                                               id="source"
                                               class="form-control @error('source') is-invalid @enderror"
                                               value="{{ old('source') }}"
                                               placeholder="e.g., Supplier name, Warehouse transfer">
                                        @error('source')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes"
                                              id="notes"
                                              class="form-control @error('notes') is-invalid @enderror"
                                              rows="3"
                                              placeholder="Additional notes about this intake...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('stock.transactions.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check me-1"></i> Record Intake
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calculator me-2"></i>
                                Transaction Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="selectedItemInfo" class="d-none">
                                <div class="mb-3">
                                    <label class="text-muted">Selected Item</label>
                                    <h5 id="selectedItemName">-</h5>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted">Current Stock</label>
                                    <h5 id="currentStock">0 <span id="currentUnit">units</span></h5>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label class="text-muted">Quantity to Add</label>
                                    <h5 id="quantityToAdd" class="text-success">+0</h5>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted">New Stock Level</label>
                                    <h4 id="newStockLevel" class="text-primary">0</h4>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label class="text-muted">Total Cost</label>
                                    <h4 id="totalCost" class="text-success">TZS 0.00</h4>
                                </div>
                            </div>
                            <div id="noItemSelected">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-box-open fa-3x mb-3"></i>
                                    <p>Select a stock item to see transaction summary</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Quick Tips
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Intake adds stock to inventory
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Unit cost is used for valuation
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Reference helps track purchases
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-2"></i>
                                    All intakes are logged for audit
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stockItemSelect = document.getElementById('stock_item_id');
    const quantityInput = document.getElementById('quantity');
    const unitCostInput = document.getElementById('unit_cost');
    const unitDisplay = document.getElementById('unit-display');

    // Summary elements
    const selectedItemInfo = document.getElementById('selectedItemInfo');
    const noItemSelected = document.getElementById('noItemSelected');
    const selectedItemName = document.getElementById('selectedItemName');
    const currentStock = document.getElementById('currentStock');
    const currentUnit = document.getElementById('currentUnit');
    const quantityToAdd = document.getElementById('quantityToAdd');
    const newStockLevel = document.getElementById('newStockLevel');
    const totalCost = document.getElementById('totalCost');

    function updateSummary() {
        const selectedOption = stockItemSelect.options[stockItemSelect.selectedIndex];

        if (stockItemSelect.value) {
            const unit = selectedOption.dataset.unit || 'units';
            const cost = parseFloat(selectedOption.dataset.cost) || 0;
            const current = parseFloat(selectedOption.dataset.current) || 0;
            const qty = parseFloat(quantityInput.value) || 0;
            const unitCost = parseFloat(unitCostInput.value) || cost;

            // Update unit display
            unitDisplay.textContent = unit;
            currentUnit.textContent = unit;

            // Show summary
            selectedItemInfo.classList.remove('d-none');
            noItemSelected.classList.add('d-none');

            // Update values
            selectedItemName.textContent = selectedOption.text.split(' (')[0];
            currentStock.innerHTML = current.toFixed(2) + ' <span id="currentUnit">' + unit + '</span>';
            quantityToAdd.textContent = '+' + qty.toFixed(2);
            newStockLevel.textContent = (current + qty).toFixed(2) + ' ' + unit;
            totalCost.textContent = 'TZS ' + (qty * unitCost).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            // Update unit cost placeholder
            if (!unitCostInput.value) {
                unitCostInput.placeholder = 'Default: ' + cost.toFixed(2);
            }
        } else {
            selectedItemInfo.classList.add('d-none');
            noItemSelected.classList.remove('d-none');
            unitDisplay.textContent = 'units';
        }
    }

    stockItemSelect.addEventListener('change', updateSummary);
    quantityInput.addEventListener('input', updateSummary);
    unitCostInput.addEventListener('input', updateSummary);

    // Initial update
    updateSummary();
});
</script>
@endpush
@endsection
