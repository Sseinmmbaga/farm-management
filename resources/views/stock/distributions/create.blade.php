@extends('layouts.base')

@section('title', 'New Stock Distribution')

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
                            <li class="breadcrumb-item"><a href="{{ route('stock.distributions.index') }}">Distributions</a></li>
                            <li class="breadcrumb-item active" aria-current="page">New Distribution</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users text-primary me-2"></i>
                        Distribute Stock to Farmer
                    </h1>
                    <p class="text-muted mb-0">Issue stock to a farmer with payment tracking</p>
                </div>
                <a href="{{ route('stock.distributions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Distributions
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt me-2"></i>
                                Distribution Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('stock.distributions.store') }}" id="distributionForm">
                                @csrf

                                <!-- Farmer Selection -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="farmer_id" class="form-label">Farmer <span class="text-danger">*</span></label>
                                        <select name="farmer_id" id="farmer_id" class="form-select @error('farmer_id') is-invalid @enderror" required>
                                            <option value="">Select Farmer</option>
                                            @foreach($farmers as $farmer)
                                                <option value="{{ $farmer->id }}"
                                                        data-name="{{ $farmer->first_name }} {{ $farmer->last_name }}"
                                                        {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                                    {{ $farmer->first_name }} {{ $farmer->last_name }}
                                                    ({{ $farmer->registration_number ?? 'No Reg.' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('farmer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="farm_id" class="form-label">Farm (Optional)</label>
                                        <select name="farm_id" id="farm_id" class="form-select @error('farm_id') is-invalid @enderror">
                                            <option value="">Select Farm</option>
                                            @foreach($farms as $farm)
                                                <option value="{{ $farm->id }}"
                                                        data-farmer="{{ $farm->farmer_id }}"
                                                        {{ old('farm_id') == $farm->id ? 'selected' : '' }}>
                                                    {{ $farm->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('farm_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Stock Item & Quantity -->
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

                                <!-- Distribution Type -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="distribution_type" class="form-label">Distribution Type <span class="text-danger">*</span></label>
                                        <select name="distribution_type" id="distribution_type" class="form-select @error('distribution_type') is-invalid @enderror" required>
                                            <option value="">Select Type</option>
                                            <option value="credit" {{ old('distribution_type') == 'credit' ? 'selected' : '' }}>
                                                Credit (Farmer will repay later)
                                            </option>
                                            <option value="cash" {{ old('distribution_type') == 'cash' ? 'selected' : '' }}>
                                                Cash (Paid immediately)
                                            </option>
                                            <option value="free" {{ old('distribution_type') == 'free' ? 'selected' : '' }}>
                                                Free (No payment required)
                                            </option>
                                        </select>
                                        @error('distribution_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="season_id" class="form-label">Season (Optional)</label>
                                        <select name="season_id" id="season_id" class="form-select @error('season_id') is-invalid @enderror">
                                            <option value="">Select Season</option>
                                            @foreach($seasons as $season)
                                                <option value="{{ $season->id }}" {{ old('season_id') == $season->id ? 'selected' : '' }}>
                                                    {{ $season->name }} {{ $season->year }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('season_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Value & Due Date (for credit) -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="value" class="form-label">Value (TZS)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">TZS</span>
                                            <input type="number"
                                                   name="value"
                                                   id="value"
                                                   class="form-control @error('value') is-invalid @enderror"
                                                   step="0.01"
                                                   min="0"
                                                   value="{{ old('value') }}"
                                                   placeholder="Auto-calculated if blank">
                                        </div>
                                        <small class="text-muted">Leave blank to auto-calculate from unit cost</small>
                                        @error('value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3" id="due-date-group">
                                        <label for="due_date" class="form-label">Due Date (for Credit)</label>
                                        <input type="date"
                                               name="due_date"
                                               id="due_date"
                                               class="form-control @error('due_date') is-invalid @enderror"
                                               value="{{ old('due_date') }}"
                                               min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                        <small class="text-muted">When should the farmer repay?</small>
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Purpose & Notes -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="purpose" class="form-label">Purpose</label>
                                        <select name="purpose" id="purpose" class="form-select @error('purpose') is-invalid @enderror">
                                            <option value="">Select Purpose</option>
                                            <option value="planting" {{ old('purpose') == 'planting' ? 'selected' : '' }}>Planting</option>
                                            <option value="fertilizer" {{ old('purpose') == 'fertilizer' ? 'selected' : '' }}>Fertilizer Application</option>
                                            <option value="pest_control" {{ old('purpose') == 'pest_control' ? 'selected' : '' }}>Pest Control</option>
                                            <option value="harvest" {{ old('purpose') == 'harvest' ? 'selected' : '' }}>Harvest</option>
                                            <option value="equipment" {{ old('purpose') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                            <option value="other" {{ old('purpose') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('purpose')
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
                                              placeholder="Additional notes about this distribution...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('stock.distributions.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-check me-1"></i> Create Distribution
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
                                Distribution Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="summaryContent">
                                <div class="mb-3">
                                    <label class="text-muted">Farmer</label>
                                    <h5 id="summaryFarmer">-</h5>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted">Stock Item</label>
                                    <h5 id="summaryItem">-</h5>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted">Quantity</label>
                                    <h5 id="summaryQuantity">0 <span id="summaryUnit">units</span></h5>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label class="text-muted">Distribution Type</label>
                                    <h5 id="summaryType">-</h5>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted">Total Value</label>
                                    <h4 id="summaryValue" class="text-primary">TZS 0.00</h4>
                                </div>

                                <!-- Error for insufficient stock -->
                                <div id="insufficientStockError" class="alert alert-danger d-none">
                                    <i class="fas fa-times-circle me-2"></i>
                                    <strong>Error:</strong> Insufficient stock available!
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Distribution Types
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6><i class="fas fa-credit-card text-warning me-2"></i> Credit</h6>
                                <small class="text-muted">Farmer receives stock now and repays later (usually at harvest)</small>
                            </div>
                            <div class="mb-3">
                                <h6><i class="fas fa-money-bill text-success me-2"></i> Cash</h6>
                                <small class="text-muted">Farmer pays immediately upon receiving stock</small>
                            </div>
                            <div>
                                <h6><i class="fas fa-gift text-info me-2"></i> Free</h6>
                                <small class="text-muted">Stock given free of charge (grants, aid, etc.)</small>
                            </div>
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
    const farmerSelect = document.getElementById('farmer_id');
    const farmSelect = document.getElementById('farm_id');
    const stockItemSelect = document.getElementById('stock_item_id');
    const quantityInput = document.getElementById('quantity');
    const valueInput = document.getElementById('value');
    const distributionTypeSelect = document.getElementById('distribution_type');
    const dueDateGroup = document.getElementById('due-date-group');
    const unitDisplay = document.getElementById('unit-display');
    const availableHint = document.getElementById('available-hint');
    const submitBtn = document.getElementById('submitBtn');

    // Summary elements
    const summaryFarmer = document.getElementById('summaryFarmer');
    const summaryItem = document.getElementById('summaryItem');
    const summaryQuantity = document.getElementById('summaryQuantity');
    const summaryUnit = document.getElementById('summaryUnit');
    const summaryType = document.getElementById('summaryType');
    const summaryValue = document.getElementById('summaryValue');
    const insufficientStockError = document.getElementById('insufficientStockError');

    // Filter farms based on selected farmer
    farmerSelect.addEventListener('change', function() {
        const farmerId = this.value;
        const farms = farmSelect.querySelectorAll('option');

        farms.forEach(function(farm) {
            if (farm.value === '' || farm.dataset.farmer === farmerId) {
                farm.style.display = '';
            } else {
                farm.style.display = 'none';
            }
        });

        farmSelect.value = '';
        updateSummary();
    });

    // Show/hide due date based on distribution type
    distributionTypeSelect.addEventListener('change', function() {
        if (this.value === 'credit') {
            dueDateGroup.style.display = '';
        } else {
            dueDateGroup.style.display = 'none';
        }
        updateSummary();
    });

    function updateSummary() {
        // Farmer
        const selectedFarmer = farmerSelect.options[farmerSelect.selectedIndex];
        summaryFarmer.textContent = farmerSelect.value ? selectedFarmer.dataset.name : '-';

        // Stock Item
        const selectedItem = stockItemSelect.options[stockItemSelect.selectedIndex];
        if (stockItemSelect.value) {
            const unit = selectedItem.dataset.unit || 'units';
            const cost = parseFloat(selectedItem.dataset.cost) || 0;
            const available = parseFloat(selectedItem.dataset.available) || 0;
            const qty = parseFloat(quantityInput.value) || 0;

            summaryItem.textContent = selectedItem.text.split(' (')[0];
            unitDisplay.textContent = unit;
            summaryUnit.textContent = unit;
            availableHint.innerHTML = 'Max available: <strong>' + available.toFixed(2) + '</strong> ' + unit;
            quantityInput.max = available;

            summaryQuantity.innerHTML = qty.toFixed(2) + ' <span id="summaryUnit">' + unit + '</span>';

            // Calculate value
            const customValue = parseFloat(valueInput.value);
            const calculatedValue = customValue || (qty * cost);
            summaryValue.textContent = 'TZS ' + calculatedValue.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            // Check stock
            if (qty > available) {
                insufficientStockError.classList.remove('d-none');
                submitBtn.disabled = true;
            } else {
                insufficientStockError.classList.add('d-none');
                submitBtn.disabled = false;
            }
        } else {
            summaryItem.textContent = '-';
            summaryQuantity.innerHTML = '0 <span id="summaryUnit">units</span>';
            summaryValue.textContent = 'TZS 0.00';
            availableHint.textContent = 'Select an item to see available quantity';
            insufficientStockError.classList.add('d-none');
            submitBtn.disabled = false;
        }

        // Distribution Type
        const typeLabels = {
            'credit': '<span class="badge bg-warning">Credit</span>',
            'cash': '<span class="badge bg-success">Cash</span>',
            'free': '<span class="badge bg-info">Free</span>',
        };
        summaryType.innerHTML = typeLabels[distributionTypeSelect.value] || '-';
    }

    stockItemSelect.addEventListener('change', updateSummary);
    quantityInput.addEventListener('input', updateSummary);
    valueInput.addEventListener('input', updateSummary);

    // Initial setup
    distributionTypeSelect.dispatchEvent(new Event('change'));
    updateSummary();
});
</script>
@endpush
@endsection
