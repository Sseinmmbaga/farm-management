@extends('layouts.base')

@section('title', 'Record Stock Issuance')

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
                            <li class="breadcrumb-item active" aria-current="page">Record Issuance</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-arrow-up text-warning me-2"></i>
                        Record Stock Issuance
                    </h1>
                    <p class="text-muted mb-0">Issue stock from inventory</p>
                </div>
                <a href="{{ route('stock.transactions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Transactions
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-minus-circle me-2"></i>
                                Issuance Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('stock.transactions.issuance.store') }}" id="issuanceForm">
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
                                                        data-available="{{ $item->quantity_available }}"
                                                        data-current="{{ $item->quantity_on_hand }}"
                                                        {{ old('stock_item_id') == $item->id ? 'selected' : '' }}
                                                        {{ $item->quantity_available <= 0 ? 'disabled' : '' }}>
                                                    {{ $item->name }} ({{ $item->code }})
                                                    - Available: {{ number_format($item->quantity_available, 2) }} {{ $item->unit }}
                                                    @if($item->quantity_available <= 0) [OUT OF STOCK] @endif
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
                                        <small class="text-muted" id="available-hint">Select an item to see available quantity</small>
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="destination"
                                               id="destination"
                                               class="form-control @error('destination') is-invalid @enderror"
                                               value="{{ old('destination') }}"
                                               placeholder="e.g., Field use, Farmer distribution, Transfer"
                                               required>
                                        @error('destination')
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
                                        <small class="text-muted">e.g., Delivery note number, Request ID</small>
                                        @error('reference_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="farmer_id" class="form-label">Farmer (Optional)</label>
                                        <select name="farmer_id" id="farmer_id" class="form-select @error('farmer_id') is-invalid @enderror">
                                            <option value="">Select Farmer (if applicable)</option>
                                            {{-- Farmers will be loaded from controller if available --}}
                                        </select>
                                        <small class="text-muted">For farmer-related issuances. Use Distributions for formal tracking.</small>
                                        @error('farmer_id')
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
                                              placeholder="Additional notes about this issuance...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('stock.transactions.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-warning" id="submitBtn">
                                        <i class="fas fa-check me-1"></i> Record Issuance
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
                                    <label class="text-muted">Available Stock</label>
                                    <h5 id="availableStock" class="text-success">0 <span id="availableUnit">units</span></h5>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label class="text-muted">Quantity to Issue</label>
                                    <h5 id="quantityToIssue" class="text-danger">-0</h5>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted">Remaining Stock</label>
                                    <h4 id="remainingStock" class="text-primary">0</h4>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label class="text-muted">Total Value</label>
                                    <h4 id="totalValue" class="text-warning">TZS 0.00</h4>
                                </div>

                                <!-- Warning for low stock -->
                                <div id="lowStockWarning" class="alert alert-warning d-none">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Warning:</strong> This will bring stock below safe levels!
                                </div>

                                <!-- Error for insufficient stock -->
                                <div id="insufficientStockError" class="alert alert-danger d-none">
                                    <i class="fas fa-times-circle me-2"></i>
                                    <strong>Error:</strong> Insufficient stock available!
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
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                    Issuance reduces stock levels
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Cannot exceed available quantity
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-users text-info me-2"></i>
                                    Use Distributions for farmer tracking
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-2"></i>
                                    All issuances are logged for audit
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
    const unitDisplay = document.getElementById('unit-display');
    const availableHint = document.getElementById('available-hint');
    const submitBtn = document.getElementById('submitBtn');

    // Summary elements
    const selectedItemInfo = document.getElementById('selectedItemInfo');
    const noItemSelected = document.getElementById('noItemSelected');
    const selectedItemName = document.getElementById('selectedItemName');
    const availableStock = document.getElementById('availableStock');
    const availableUnit = document.getElementById('availableUnit');
    const quantityToIssue = document.getElementById('quantityToIssue');
    const remainingStock = document.getElementById('remainingStock');
    const totalValue = document.getElementById('totalValue');
    const lowStockWarning = document.getElementById('lowStockWarning');
    const insufficientStockError = document.getElementById('insufficientStockError');

    function updateSummary() {
        const selectedOption = stockItemSelect.options[stockItemSelect.selectedIndex];

        if (stockItemSelect.value) {
            const unit = selectedOption.dataset.unit || 'units';
            const cost = parseFloat(selectedOption.dataset.cost) || 0;
            const available = parseFloat(selectedOption.dataset.available) || 0;
            const qty = parseFloat(quantityInput.value) || 0;

            // Update unit display
            unitDisplay.textContent = unit;
            availableUnit.textContent = unit;

            // Update available hint
            availableHint.innerHTML = 'Max available: <strong>' + available.toFixed(2) + '</strong> ' + unit;
            quantityInput.max = available;

            // Show summary
            selectedItemInfo.classList.remove('d-none');
            noItemSelected.classList.add('d-none');

            // Update values
            selectedItemName.textContent = selectedOption.text.split(' (')[0];
            availableStock.innerHTML = available.toFixed(2) + ' <span id="availableUnit">' + unit + '</span>';
            quantityToIssue.textContent = '-' + qty.toFixed(2);
            const remaining = available - qty;
            remainingStock.textContent = remaining.toFixed(2) + ' ' + unit;
            totalValue.textContent = 'TZS ' + (qty * cost).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            // Check for warnings/errors
            if (qty > available) {
                insufficientStockError.classList.remove('d-none');
                lowStockWarning.classList.add('d-none');
                remainingStock.classList.remove('text-primary', 'text-warning');
                remainingStock.classList.add('text-danger');
                submitBtn.disabled = true;
            } else if (remaining < (available * 0.2)) { // Less than 20% remaining
                lowStockWarning.classList.remove('d-none');
                insufficientStockError.classList.add('d-none');
                remainingStock.classList.remove('text-primary', 'text-danger');
                remainingStock.classList.add('text-warning');
                submitBtn.disabled = false;
            } else {
                lowStockWarning.classList.add('d-none');
                insufficientStockError.classList.add('d-none');
                remainingStock.classList.remove('text-warning', 'text-danger');
                remainingStock.classList.add('text-primary');
                submitBtn.disabled = false;
            }
        } else {
            selectedItemInfo.classList.add('d-none');
            noItemSelected.classList.remove('d-none');
            unitDisplay.textContent = 'units';
            availableHint.textContent = 'Select an item to see available quantity';
            submitBtn.disabled = false;
        }
    }

    stockItemSelect.addEventListener('change', updateSummary);
    quantityInput.addEventListener('input', updateSummary);

    // Initial update
    updateSummary();
});
</script>
@endpush
@endsection
