@extends('layouts.base')

@section('title', 'Edit ' . $farm->display_name)

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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-edit text-warning"></i> Edit Farm: {{ $farm->display_name }}</h2>
            <p class="text-muted mb-0">Code: {{ $farm->code }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('farms.show', $farm) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Farm
            </a>
            <a href="{{ route('farms.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list"></i> All Farms
            </a>
        </div>
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

        <form method="POST" action="{{ route('farms.update', $farm) }}">
            @csrf
            @method('PUT')

            <!-- Farmer Selection -->
            <div class="form-section">
                <h4><i class="fas fa-user"></i> Farmer Information</h4>

                <div class="mb-3">
                    <label for="farmer_id" class="form-label">Select Farmer *</label>
                    <select class="form-select" id="farmer_id" name="farmer_id" required>
                        <option value="">-- Select Farmer --</option>
                        @foreach($farmers as $farmerOption)
                            <option value="{{ $farmerOption->id }}"
                                {{ old('farmer_id', $farm->farmer_id) == $farmerOption->id ? 'selected' : '' }}>
                                {{ $farmerOption->full_name }} ({{ $farmerOption->registration_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Farm Basic Information -->
            <div class="form-section">
                <h4><i class="fas fa-tractor"></i> Farm Basic Information</h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Farm Name *</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="{{ old('name', $farm->name) }}" required placeholder="e.g., Green Valley Farm">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Farm Code</label>
                        <input type="text" class="form-control" id="code" value="{{ $farm->code }}" readonly>
                        <div class="form-text">Farm code is auto-generated and cannot be changed.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="total_area" class="form-label">Total Area (acres) *</label>
                        <input type="number" step="0.01" class="form-control" id="total_area" name="total_area"
                               value="{{ old('total_area', $farm->total_area) }}" required min="0.01" placeholder="e.g., 15.5">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cultivated_area" class="form-label">Cultivated Area (acres)</label>
                        <input type="number" step="0.01" class="form-control" id="cultivated_area" name="cultivated_area"
                               value="{{ old('cultivated_area', $farm->cultivated_area) }}" min="0" placeholder="Leave empty to use total area">
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="form-section">
                <h4><i class="fas fa-map-marker-alt"></i> Location Information</h4>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="region_id" class="form-label">Region *</label>
                        <select class="form-select" id="region_id" name="region_id" required>
                            <option value="">-- Select Region --</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}"
                                    {{ old('region_id', $farm->region_id) == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="district_id" class="form-label">District *</label>
                        <select class="form-select" id="district_id" name="district_id" required>
                            <option value="">-- Select District --</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}"
                                    {{ old('district_id', $farm->district_id) == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="village_id" class="form-label">Village *</label>
                        <select class="form-select" id="village_id" name="village_id" required>
                            <option value="">-- Select Village --</option>
                            @foreach($villages as $village)
                                <option value="{{ $village->id }}"
                                    {{ old('village_id', $farm->village_id) == $village->id ? 'selected' : '' }}>
                                    {{ $village->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" class="form-control" id="latitude" name="latitude"
                               value="{{ old('latitude', $farm->latitude) }}" placeholder="e.g., 6.5244">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control" id="longitude" name="longitude"
                               value="{{ old('longitude', $farm->longitude) }}" placeholder="e.g., 3.3792">
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
                               value="{{ old('soil_type', $farm->soil_type) }}" placeholder="e.g., Loamy, Sandy, Clay">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="water_source" class="form-label">Water Source</label>
                        <input type="text" class="form-control" id="water_source" name="water_source"
                               value="{{ old('water_source', $farm->water_source) }}" placeholder="e.g., River, Well, Rain-fed">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="terrain" class="form-label">Terrain</label>
                        <input type="text" class="form-control" id="terrain" name="terrain"
                               value="{{ old('terrain', $farm->terrain) }}" placeholder="e.g., Flat, Hilly, Valley">
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
                            <option value="organic" {{ old('certification_status', $farm->certification_status) == 'organic' ? 'selected' : '' }}>Organic</option>
                            <option value="in-conversion" {{ old('certification_status', $farm->certification_status) == 'in-conversion' ? 'selected' : '' }}>In Conversion</option>
                            <option value="conventional" {{ old('certification_status', $farm->certification_status) == 'conventional' ? 'selected' : '' }}>Conventional</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="organic_since" class="form-label">Organic Since</label>
                        <input type="date" class="form-control" id="organic_since" name="organic_since"
                               value="{{ old('organic_since', $farm->organic_since ? $farm->organic_since->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="conversion_year" class="form-label">Conversion Year</label>
                        <input type="number" class="form-control" id="conversion_year" name="conversion_year"
                               value="{{ old('conversion_year', $farm->conversion_year) }}" min="2000" max="{{ date('Y') }}" placeholder="e.g., 2020">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Farm Status *</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" {{ old('status', $farm->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $farm->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="abandoned" {{ old('status', $farm->status) == 'abandoned' ? 'selected' : '' }}>Abandoned</option>
                    </select>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="form-section">
                <h4><i class="fas fa-sticky-note"></i> Additional Information</h4>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"
                              placeholder="Any additional information about the farm...">{{ old('notes', $farm->notes) }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('farms.show', $farm) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
