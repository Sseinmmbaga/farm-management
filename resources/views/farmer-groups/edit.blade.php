@extends('layouts.base')

@section('title', 'Edit Farmer Group')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Farmer Group: {{ $farmerGroup->name }}
                        </h4>
                        <a href="{{ route('farmer-groups.show', $farmerGroup) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Details
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

                    <form method="POST" action="{{ route('farmer-groups.update', $farmerGroup) }}">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-info-circle text-primary me-2"></i>Basic Information
                        </h5>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Group Code</label>
                                <input type="text" class="form-control" value="{{ $farmerGroup->code }}" disabled>
                            </div>
                            <div class="col-md-8">
                                <label for="name" class="form-label">Group Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $farmerGroup->name) }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3">{{ old('description', $farmerGroup->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Location Information -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>Location
                        </h5>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="region_id" class="form-label">Region <span class="text-danger">*</span></label>
                                <select class="form-select @error('region_id') is-invalid @enderror"
                                        id="region_id"
                                        name="region_id"
                                        required>
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id', $farmerGroup->region_id) == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('region_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="district_id" class="form-label">District <span class="text-danger">*</span></label>
                                <select class="form-select @error('district_id') is-invalid @enderror"
                                        id="district_id"
                                        name="district_id"
                                        required>
                                    <option value="">Select District</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ old('district_id', $farmerGroup->district_id) == $district->id ? 'selected' : '' }}>
                                            {{ $district->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('district_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="village_id" class="form-label">Village</label>
                                <select class="form-select @error('village_id') is-invalid @enderror"
                                        id="village_id"
                                        name="village_id">
                                    <option value="">Select Village</option>
                                    @foreach($villages as $village)
                                        <option value="{{ $village->id }}" {{ old('village_id', $farmerGroup->village_id) == $village->id ? 'selected' : '' }}>
                                            {{ $village->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('village_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Leader Information -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-user-tie text-success me-2"></i>Leader Information
                        </h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="leader_name" class="form-label">Leader Name</label>
                                <input type="text"
                                       class="form-control @error('leader_name') is-invalid @enderror"
                                       id="leader_name"
                                       name="leader_name"
                                       value="{{ old('leader_name', $farmerGroup->leader_name) }}">
                                @error('leader_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="leader_phone" class="form-label">Leader Phone</label>
                                <input type="text"
                                       class="form-control @error('leader_phone') is-invalid @enderror"
                                       id="leader_phone"
                                       name="leader_phone"
                                       value="{{ old('leader_phone', $farmerGroup->leader_phone) }}">
                                @error('leader_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-cog text-secondary me-2"></i>Additional Settings
                        </h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="established_date" class="form-label">Established Date</label>
                                <input type="date"
                                       class="form-control @error('established_date') is-invalid @enderror"
                                       id="established_date"
                                       name="established_date"
                                       value="{{ old('established_date', $farmerGroup->established_date?->format('Y-m-d')) }}">
                                @error('established_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="is_active"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', $farmerGroup->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('farmer-groups.show', $farmerGroup) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i> Update Group
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
        const villageSelect = document.getElementById('village_id');

        regionSelect.addEventListener('change', function() {
            const regionId = this.value;
            districtSelect.innerHTML = '<option value="">Loading...</option>';
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
            villageSelect.innerHTML = '<option value="">Loading...</option>';

            if (districtId) {
                fetch(`/api/districts/${districtId}/villages`)
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
