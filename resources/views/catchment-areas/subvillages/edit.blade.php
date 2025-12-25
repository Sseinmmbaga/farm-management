@extends('layouts.base')

@section('title', 'Edit Subvillage')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Subvillage: {{ $subvillage->name }}
                        </h4>
                        <a href="{{ route('subvillages.show', $subvillage) }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('subvillages.update', $subvillage) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="region_id" class="form-label">Region <span class="text-danger">*</span></label>
                                <select class="form-select @error('region_id') is-invalid @enderror"
                                        id="region_id" name="region_id" required>
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id', $subvillage->region_id) == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('region_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="district_id" class="form-label">District <span class="text-danger">*</span></label>
                                <select class="form-select @error('district_id') is-invalid @enderror"
                                        id="district_id" name="district_id" required>
                                    <option value="">Select District</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ old('district_id', $subvillage->district_id) == $district->id ? 'selected' : '' }}>
                                            {{ $district->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('district_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ward_id" class="form-label">Ward <span class="text-danger">*</span></label>
                                <select class="form-select @error('ward_id') is-invalid @enderror"
                                        id="ward_id" name="ward_id" required>
                                    <option value="">Select Ward</option>
                                    @foreach($wards as $ward)
                                        <option value="{{ $ward->id }}" {{ old('ward_id', $subvillage->ward_id) == $ward->id ? 'selected' : '' }}>
                                            {{ $ward->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ward_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="village_id" class="form-label">Village <span class="text-danger">*</span></label>
                                <select class="form-select @error('village_id') is-invalid @enderror"
                                        id="village_id" name="village_id" required>
                                    <option value="">Select Village</option>
                                    @foreach($villages as $village)
                                        <option value="{{ $village->id }}" {{ old('village_id', $subvillage->village_id) == $village->id ? 'selected' : '' }}>
                                            {{ $village->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('village_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Subvillage Name (English) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $subvillage->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="name_sw" class="form-label">Subvillage Name (Swahili)</label>
                                <input type="text" class="form-control @error('name_sw') is-invalid @enderror"
                                       id="name_sw" name="name_sw" value="{{ old('name_sw', $subvillage->name_sw) }}">
                                @error('name_sw')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="code" class="form-label">Subvillage Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" value="{{ old('code', $subvillage->code) }}" maxlength="20">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                       id="latitude" name="latitude" value="{{ old('latitude', $subvillage->latitude) }}">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                       id="longitude" name="longitude" value="{{ old('longitude', $subvillage->longitude) }}">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $subvillage->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('subvillages.show', $subvillage) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i> Update Subvillage
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('region_id');
    const districtSelect = document.getElementById('district_id');
    const wardSelect = document.getElementById('ward_id');
    const villageSelect = document.getElementById('village_id');

    regionSelect.addEventListener('change', function() {
        const regionId = this.value;
        districtSelect.innerHTML = '<option value="">Loading...</option>';
        wardSelect.innerHTML = '<option value="">Select Ward</option>';
        villageSelect.innerHTML = '<option value="">Select Village</option>';

        if (regionId) {
            fetch(`/api/regions/${regionId}/districts`)
                .then(response => response.json())
                .then(data => {
                    districtSelect.innerHTML = '<option value="">Select District</option>';
                    data.forEach(district => {
                        districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
                    });
                })
                .catch(() => {
                    districtSelect.innerHTML = '<option value="">Select District</option>';
                });
        } else {
            districtSelect.innerHTML = '<option value="">Select District</option>';
        }
    });

    districtSelect.addEventListener('change', function() {
        const districtId = this.value;
        wardSelect.innerHTML = '<option value="">Loading...</option>';
        villageSelect.innerHTML = '<option value="">Select Village</option>';

        if (districtId) {
            fetch(`/api/districts/${districtId}/wards`)
                .then(response => response.json())
                .then(data => {
                    wardSelect.innerHTML = '<option value="">Select Ward</option>';
                    data.forEach(ward => {
                        wardSelect.innerHTML += `<option value="${ward.id}">${ward.name}</option>`;
                    });
                })
                .catch(() => {
                    wardSelect.innerHTML = '<option value="">Select Ward</option>';
                });
        } else {
            wardSelect.innerHTML = '<option value="">Select Ward</option>';
        }
    });

    wardSelect.addEventListener('change', function() {
        const wardId = this.value;
        villageSelect.innerHTML = '<option value="">Loading...</option>';

        if (wardId) {
            fetch(`/api/wards/${wardId}/villages`)
                .then(response => response.json())
                .then(data => {
                    villageSelect.innerHTML = '<option value="">Select Village</option>';
                    data.forEach(village => {
                        villageSelect.innerHTML += `<option value="${village.id}">${village.name}</option>`;
                    });
                })
                .catch(() => {
                    villageSelect.innerHTML = '<option value="">Select Village</option>';
                });
        } else {
            villageSelect.innerHTML = '<option value="">Select Village</option>';
        }
    });
});
</script>
@endpush
@endsection
