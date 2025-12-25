@extends('layouts.base')

@section('title', 'Create New Farm')

@push('styles')
<style>
    .form-container {
        background-color: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .form-section {
        border-left: 4px solid #27ae60;
        padding-left: 15px;
        margin-bottom: 30px;
    }

    .form-section h4 {
        color: #27ae60;
        margin-bottom: 15px;
    }

    .unit-conversion-display {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        border: 1px solid #a5d6a7;
    }

    .unit-conversion-display h6 {
        color: #2e7d32;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .conversion-item {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px dashed #a5d6a7;
    }

    .conversion-item:last-child {
        border-bottom: none;
    }

    .conversion-label {
        color: #555;
        font-size: 0.9rem;
    }

    .conversion-value {
        font-weight: 600;
        color: #1b5e20;
    }

    .unit-selector-group {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .unit-btn {
        padding: 6px 12px;
        border: 2px solid #27ae60;
        background: white;
        color: #27ae60;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.85rem;
    }

    .unit-btn:hover {
        background: #e8f5e9;
    }

    .unit-btn.active {
        background: #27ae60;
        color: white;
    }

    .input-with-unit {
        position: relative;
    }

    .input-with-unit .unit-label {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        font-weight: 500;
        background: #f8f9fa;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
    }

    .input-with-unit input {
        padding-right: 70px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-plus-circle text-success"></i> Create New Farm</h2>
        <a href="{{ route('farms.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Farms
        </a>
    </div>

    <div class="form-container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('farms.store') }}" id="farmForm">
            @csrf

            <!-- Farmer Selection -->
            <div class="form-section">
                <h4><i class="fas fa-user"></i> Farmer Information</h4>

                <div class="mb-3">
                    <label for="farmer_id" class="form-label">Farmer *</label>
                    <select class="form-select" id="farmer_id" name="farmer_id" required onchange="updateLocationFromFarmer()">
                        <option value="">-- Select Farmer --</option>
                        @foreach($farmers as $farmer)
                            <option value="{{ $farmer->id }}"
                                    data-region="{{ $farmer->region_id }}"
                                    data-district="{{ $farmer->district_id }}"
                                    data-village="{{ $farmer->village_id }}"
                                    {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                {{ $farmer->full_name }} ({{ $farmer->registration_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Farm Basic Information -->
            <div class="form-section">
                <h4><i class="fas fa-tractor"></i> Farm Basic Information</h4>

                <div class="mb-3">
                    <label for="name" class="form-label">Farm Name *</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="{{ old('name') }}" required placeholder="e.g., Green Valley Farm">
                </div>

                <!-- Area Unit Selection -->
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-ruler-combined"></i> Select Area Unit</label>
                    <div class="unit-selector-group">
                        <button type="button" class="unit-btn active" data-unit="ha" onclick="setAreaUnit('ha')">
                            Hectare (ha)
                        </button>
                        <button type="button" class="unit-btn" data-unit="acre" onclick="setAreaUnit('acre')">
                            Acre
                        </button>
                        <button type="button" class="unit-btn" data-unit="m2" onclick="setAreaUnit('m2')">
                            Square Meter (m²)
                        </button>
                        <button type="button" class="unit-btn" data-unit="km2" onclick="setAreaUnit('km2')">
                            Square Kilometer (km²)
                        </button>
                        <button type="button" class="unit-btn" data-unit="ekari" onclick="setAreaUnit('ekari')">
                            Ekari
                        </button>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Choose your preferred unit. Values will be stored in hectares.
                    </small>
                </div>

                <!-- Hidden field to store the selected unit -->
                <input type="hidden" id="area_unit" name="area_unit" value="{{ old('area_unit', 'ha') }}">

                <!-- Area Fields -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="total_area_input" class="form-label">Total Area *</label>
                        <div class="input-with-unit">
                            <input type="number" step="0.0001" class="form-control" id="total_area_input"
                                   value="{{ old('total_area') }}" required min="0.0001" placeholder="e.g., 15.5"
                                   oninput="convertAndDisplayArea()">
                            <span class="unit-label" id="total_unit_label">ha</span>
                        </div>
                        <!-- Hidden field for hectares (always stored in ha) -->
                        <input type="hidden" id="total_area" name="total_area" value="{{ old('total_area') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cultivated_area_input" class="form-label">Cultivated Area</label>
                        <div class="input-with-unit">
                            <input type="number" step="0.0001" class="form-control" id="cultivated_area_input"
                                   value="{{ old('cultivated_area') }}" min="0" placeholder="Leave blank to use total area"
                                   oninput="convertAndDisplayArea()">
                            <span class="unit-label" id="cultivated_unit_label">ha</span>
                        </div>
                        <!-- Hidden field for hectares (always stored in ha) -->
                        <input type="hidden" id="cultivated_area" name="cultivated_area" value="{{ old('cultivated_area') }}">
                    </div>
                </div>

                <!-- Area Conversion Display -->
                <div class="unit-conversion-display" id="conversion-display" style="display: none;">
                    <h6><i class="fas fa-exchange-alt"></i> Area Equivalents</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="conversion-item">
                                <span class="conversion-label">Hectares (ha):</span>
                                <span class="conversion-value" id="conv-ha">-</span>
                            </div>
                            <div class="conversion-item">
                                <span class="conversion-label">Acres:</span>
                                <span class="conversion-value" id="conv-acre">-</span>
                            </div>
                            <div class="conversion-item">
                                <span class="conversion-label">Square Meters (m²):</span>
                                <span class="conversion-value" id="conv-m2">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="conversion-item">
                                <span class="conversion-label">Square Kilometers (km²):</span>
                                <span class="conversion-value" id="conv-km2">-</span>
                            </div>
                            <div class="conversion-item">
                                <span class="conversion-label">Ekari:</span>
                                <span class="conversion-value" id="conv-ekari">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="form-section">
                <h4><i class="fas fa-map-marker-alt"></i> Location Information</h4>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="region_id" class="form-label">Region *</label>
                        <select class="form-select" id="region_id" name="region_id" required onchange="loadDistricts()">
                            <option value="">-- Select Region --</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="district_id" class="form-label">District *</label>
                        <select class="form-select" id="district_id" name="district_id" required onchange="loadVillages()">
                            <option value="">-- Select District --</option>
                            @if(isset($districts) && $districts->count() > 0)
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="village_id" class="form-label">Village/Ward *</label>
                        <select class="form-select" id="village_id" name="village_id" required>
                            <option value="">-- Select Village --</option>
                            @if(isset($villages) && $villages->count() > 0)
                                @foreach($villages as $village)
                                    <option value="{{ $village->id }}" {{ old('village_id') == $village->id ? 'selected' : '' }}>
                                        {{ $village->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Coordinates -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="latitude" class="form-label">Latitude (optional)</label>
                        <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude"
                               value="{{ old('latitude') }}" placeholder="e.g., -6.123456">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="longitude" class="form-label">Longitude (optional)</label>
                        <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude"
                               value="{{ old('longitude') }}" placeholder="e.g., 35.123456">
                    </div>
                </div>
            </div>

            <!-- Farm Characteristics -->
            <div class="form-section">
                <h4><i class="fas fa-info-circle"></i> Farm Characteristics</h4>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="soil_type" class="form-label">Soil Type</label>
                        <input type="text" class="form-control" id="soil_type" name="soil_type"
                               value="{{ old('soil_type') }}" placeholder="e.g., Loam, Clay, Sandy">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="water_source" class="form-label">Water Source</label>
                        <input type="text" class="form-control" id="water_source" name="water_source"
                               value="{{ old('water_source') }}" placeholder="e.g., River, Well, Rain-fed">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="terrain" class="form-label">Terrain</label>
                        <input type="text" class="form-control" id="terrain" name="terrain"
                               value="{{ old('terrain') }}" placeholder="e.g., Flat, Hilly, Sloping">
                    </div>
                </div>
            </div>

            <!-- Certification & Status -->
            <div class="form-section">
                <h4><i class="fas fa-certificate"></i> Certification & Status</h4>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="certification_status" class="form-label">Certification Status</label>
                        <select class="form-select" id="certification_status" name="certification_status">
                            <option value="">-- Select Status --</option>
                            <option value="organic" {{ old('certification_status') == 'organic' ? 'selected' : '' }}>Organic</option>
                            <option value="in-conversion" {{ old('certification_status') == 'in-conversion' ? 'selected' : '' }}>In Conversion</option>
                            <option value="conventional" {{ old('certification_status') == 'conventional' ? 'selected' : '' }}>Conventional</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="organic_since" class="form-label">Organic Since (if organic)</label>
                        <input type="date" class="form-control" id="organic_since" name="organic_since"
                               value="{{ old('organic_since') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="conversion_year" class="form-label">Conversion Year (if in conversion)</label>
                        <input type="number" class="form-control" id="conversion_year" name="conversion_year"
                               value="{{ old('conversion_year') }}" min="2000" max="{{ date('Y') }}"
                               placeholder="e.g., 2023">
                    </div>
                </div>

                <!-- Status and Date -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Farm Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="abandoned" {{ old('status') == 'abandoned' ? 'selected' : '' }}>Abandoned</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="registration_date" class="form-label">Registration Date *</label>
                        <input type="date" class="form-control" id="registration_date" name="registration_date"
                               value="{{ old('registration_date', date('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="form-section">
                <h4><i class="fas fa-sticky-note"></i> Additional Information</h4>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"
                              placeholder="Any additional information about the farm...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('farms.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Create Farm
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ========================================
    // Area Unit Conversion System
    // ========================================

    // Conversion factors to hectares (base unit)
    const conversionToHa = {
        'ha': 1,                    // 1 hectare = 1 hectare
        'acre': 0.404686,           // 1 acre = 0.404686 hectares
        'm2': 0.0001,               // 1 m² = 0.0001 hectares
        'km2': 100,                 // 1 km² = 100 hectares
        'ekari': 0.404686           // 1 ekari = 1 acre = 0.404686 hectares (East African)
    };

    // Unit display labels
    const unitLabels = {
        'ha': 'ha',
        'acre': 'acres',
        'm2': 'm²',
        'km2': 'km²',
        'ekari': 'ekari'
    };

    // Current selected unit
    let currentUnit = 'ha';

    // Set the area unit
    function setAreaUnit(unit) {
        // Get current values in hectares before changing unit
        const totalHa = parseFloat(document.getElementById('total_area').value) || 0;
        const cultivatedHa = parseFloat(document.getElementById('cultivated_area').value) || 0;

        // Update current unit
        currentUnit = unit;
        document.getElementById('area_unit').value = unit;

        // Update button styles
        document.querySelectorAll('.unit-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-unit') === unit) {
                btn.classList.add('active');
            }
        });

        // Update unit labels
        document.getElementById('total_unit_label').textContent = unitLabels[unit];
        document.getElementById('cultivated_unit_label').textContent = unitLabels[unit];

        // Convert displayed values to new unit
        if (totalHa > 0) {
            const convertedTotal = convertFromHa(totalHa, unit);
            document.getElementById('total_area_input').value = formatNumber(convertedTotal);
        }

        if (cultivatedHa > 0) {
            const convertedCultivated = convertFromHa(cultivatedHa, unit);
            document.getElementById('cultivated_area_input').value = formatNumber(convertedCultivated);
        }

        // Update conversion display
        convertAndDisplayArea();
    }

    // Convert a value to hectares
    function convertToHa(value, fromUnit) {
        return value * conversionToHa[fromUnit];
    }

    // Convert from hectares to another unit
    function convertFromHa(valueInHa, toUnit) {
        return valueInHa / conversionToHa[toUnit];
    }

    // Format number for display
    function formatNumber(num) {
        if (num === 0) return '0';
        if (num < 0.0001) return num.toExponential(2);
        if (num < 1) return num.toFixed(4);
        if (num < 100) return num.toFixed(2);
        if (num < 10000) return num.toFixed(1);
        return num.toLocaleString('en-US', { maximumFractionDigits: 0 });
    }

    // Convert and display area in all units
    function convertAndDisplayArea() {
        const inputValue = parseFloat(document.getElementById('total_area_input').value) || 0;
        const cultivatedInput = parseFloat(document.getElementById('cultivated_area_input').value) || 0;

        // Convert input to hectares
        const totalHa = convertToHa(inputValue, currentUnit);
        const cultivatedHa = convertToHa(cultivatedInput, currentUnit);

        // Store hectare values in hidden fields
        document.getElementById('total_area').value = totalHa > 0 ? totalHa.toFixed(6) : '';
        document.getElementById('cultivated_area').value = cultivatedHa > 0 ? cultivatedHa.toFixed(6) : '';

        // Show/hide conversion display
        const convDisplay = document.getElementById('conversion-display');
        if (inputValue > 0) {
            convDisplay.style.display = 'block';

            // Display conversions
            document.getElementById('conv-ha').textContent = formatNumber(totalHa) + ' ha';
            document.getElementById('conv-acre').textContent = formatNumber(convertFromHa(totalHa, 'acre')) + ' acres';
            document.getElementById('conv-m2').textContent = formatNumber(convertFromHa(totalHa, 'm2')) + ' m²';
            document.getElementById('conv-km2').textContent = formatNumber(convertFromHa(totalHa, 'km2')) + ' km²';
            document.getElementById('conv-ekari').textContent = formatNumber(convertFromHa(totalHa, 'ekari')) + ' ekari';
        } else {
            convDisplay.style.display = 'none';
        }
    }

    // ========================================
    // Location Functions
    // ========================================

    // Function to update location fields based on selected farmer
    function updateLocationFromFarmer() {
        const farmerSelect = document.getElementById('farmer_id');
        const selectedOption = farmerSelect.options[farmerSelect.selectedIndex];

        if (selectedOption.value) {
            const regionId = selectedOption.getAttribute('data-region');
            const districtId = selectedOption.getAttribute('data-district');
            const villageId = selectedOption.getAttribute('data-village');

            // Set region
            if (regionId) {
                document.getElementById('region_id').value = regionId;
                loadDistricts(); // This will load districts for the selected region

                // After a short delay, set district and village
                setTimeout(() => {
                    if (districtId) {
                        document.getElementById('district_id').value = districtId;
                        loadVillages(); // This will load villages for the selected district

                        setTimeout(() => {
                            if (villageId) {
                                document.getElementById('village_id').value = villageId;
                            }
                        }, 300);
                    }
                }, 300);
            }
        }
    }

    // Function to load districts based on selected region
    function loadDistricts() {
        const regionId = document.getElementById('region_id').value;
        const districtSelect = document.getElementById('district_id');

        if (!regionId) {
            districtSelect.innerHTML = '<option value="">-- Select District --</option>';
            document.getElementById('village_id').innerHTML = '<option value="">-- Select Village --</option>';
            return;
        }

        // Show loading
        districtSelect.innerHTML = '<option value="">Loading districts...</option>';
        districtSelect.disabled = true;

        // Fetch districts via AJAX
        fetch(`/api/districts?region_id=${regionId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">-- Select District --</option>';
                data.forEach(district => {
                    options += `<option value="${district.id}">${district.name}</option>`;
                });
                districtSelect.innerHTML = options;
                districtSelect.disabled = false;

                // Clear villages
                document.getElementById('village_id').innerHTML = '<option value="">-- Select Village --</option>';
            })
            .catch(error => {
                console.error('Error loading districts:', error);
                districtSelect.innerHTML = '<option value="">Error loading districts</option>';
            });
    }

    // Function to load villages based on selected district
    function loadVillages() {
        const districtId = document.getElementById('district_id').value;
        const villageSelect = document.getElementById('village_id');

        if (!districtId) {
            villageSelect.innerHTML = '<option value="">-- Select Village --</option>';
            return;
        }

        // Show loading
        villageSelect.innerHTML = '<option value="">Loading villages...</option>';
        villageSelect.disabled = true;

        // Fetch villages via AJAX
        fetch(`/api/villages?district_id=${districtId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">-- Select Village --</option>';
                data.forEach(village => {
                    options += `<option value="${village.id}">${village.name}</option>`;
                });
                villageSelect.innerHTML = options;
                villageSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading villages:', error);
                villageSelect.innerHTML = '<option value="">Error loading villages</option>';
            });
    }

    // ========================================
    // Initialize on Page Load
    // ========================================

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize farmer location
        const farmerId = document.getElementById('farmer_id').value;
        if (farmerId) {
            updateLocationFromFarmer();
        }

        // Auto-fill cultivated area with total area if left empty
        document.getElementById('total_area_input').addEventListener('blur', function() {
            const cultivatedArea = document.getElementById('cultivated_area_input');
            if (!cultivatedArea.value && this.value) {
                cultivatedArea.value = this.value;
                convertAndDisplayArea();
            }
        });

        // Initialize unit from old value if exists
        const savedUnit = document.getElementById('area_unit').value;
        if (savedUnit && savedUnit !== 'ha') {
            setAreaUnit(savedUnit);
        }

        // Initial conversion if values exist
        convertAndDisplayArea();
    });
</script>
@endpush
