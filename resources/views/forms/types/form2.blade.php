@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] }} - New Farm Records</small>
    </div>
    <a href="{{ route('farmer-forms.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ isset($farmerForm) ? route('farmer-forms.update', $farmerForm) : route('farmer-forms.store', $formType) }}" method="POST">
    @csrf
    @if(isset($farmerForm))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>New Farm Records Form</h5>
                </div>
                <div class="card-body">
                    <!-- Season Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Season / Msimu</label>
                            <input type="text" name="season" class="form-control" value="{{ old('season', $farmerForm->season ?? '') }}" placeholder="e.g., 2024/2025">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Form Date / Tarehe</label>
                            <input type="date" name="form_date" class="form-control" value="{{ old('form_date', isset($farmerForm) ? $farmerForm->form_date?->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>
                    </div>

                    <!-- Farmer Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Select Farmer / Chagua Mkulima <span class="text-danger">*</span></label>
                            <select name="farmer_id" class="form-select" required>
                                <option value="">-- Select Farmer --</option>
                                @foreach($farmers as $f)
                                    <option value="{{ $f->id }}" {{ old('farmer_id', $farmerForm->farmer_id ?? ($farmer->id ?? '')) == $f->id ? 'selected' : '' }}>
                                        {{ $f->first_name }} {{ $f->last_name }} ({{ $f->registration_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Select Farm / Chagua Shamba</label>
                            <select name="farm_id" class="form-select">
                                <option value="">-- Select Farm --</option>
                                @foreach($farms as $farm)
                                    <option value="{{ $farm->id }}" {{ old('farm_id', $farmerForm->farm_id ?? '') == $farm->id ? 'selected' : '' }}>
                                        {{ $farm->name }} ({{ $farm->farmer->first_name ?? 'N/A' }} {{ $farm->farmer->last_name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Farm Information -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-tractor me-2"></i>Farm Information / Taarifa za Shamba</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Farm Name / Jina la Shamba</label>
                                <input type="text" name="form_data[farm_name]" class="form-control" value="{{ old('form_data.farm_name', $farmerForm->form_data['farm_name'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Farm Registration Number / Namba ya Usajili</label>
                                <input type="text" name="form_data[farm_registration_number]" class="form-control" value="{{ old('form_data.farm_registration_number', $farmerForm->form_data['farm_registration_number'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Total Farm Size (Acres) / Ukubwa wa Shamba</label>
                                <input type="number" step="0.01" name="form_data[total_farm_size]" class="form-control" value="{{ old('form_data.total_farm_size', $farmerForm->form_data['total_farm_size'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cotton Area (Acres) / Eneo la Pamba</label>
                                <input type="number" step="0.01" name="form_data[cotton_area]" class="form-control" value="{{ old('form_data.cotton_area', $farmerForm->form_data['cotton_area'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Other Crops Area / Eneo la Mazao Mengine</label>
                                <input type="number" step="0.01" name="form_data[other_crops_area]" class="form-control" value="{{ old('form_data.other_crops_area', $farmerForm->form_data['other_crops_area'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Location Details / Mahali</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Village / Kijiji</label>
                                <input type="text" name="form_data[village]" class="form-control" value="{{ old('form_data.village', $farmerForm->form_data['village'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Subvillage / Kitongoji</label>
                                <input type="text" name="form_data[subvillage]" class="form-control" value="{{ old('form_data.subvillage', $farmerForm->form_data['subvillage'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">District / Wilaya</label>
                                <input type="text" name="form_data[district]" class="form-control" value="{{ old('form_data.district', $farmerForm->form_data['district'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">GPS Latitude / Latitudo</label>
                                <input type="text" name="form_data[gps_latitude]" class="form-control" value="{{ old('form_data.gps_latitude', $farmerForm->form_data['gps_latitude'] ?? '') }}" placeholder="e.g., -5.1234">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">GPS Longitude / Longitudo</label>
                                <input type="text" name="form_data[gps_longitude]" class="form-control" value="{{ old('form_data.gps_longitude', $farmerForm->form_data['gps_longitude'] ?? '') }}" placeholder="e.g., 34.5678">
                            </div>
                        </div>
                    </div>

                    <!-- Land Preparation -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-seedling me-2"></i>Land Preparation / Uandaaji wa Shamba</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Land Clearing Date / Tarehe ya Kusafisha</label>
                                <input type="date" name="form_data[land_clearing_date]" class="form-control" value="{{ old('form_data.land_clearing_date', $farmerForm->form_data['land_clearing_date'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Plowing Method / Njia ya Kulima</label>
                                <select name="form_data[plowing_method]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="tractor" {{ old('form_data.plowing_method', $farmerForm->form_data['plowing_method'] ?? '') == 'tractor' ? 'selected' : '' }}>Tractor / Trekta</option>
                                    <option value="ox_plow" {{ old('form_data.plowing_method', $farmerForm->form_data['plowing_method'] ?? '') == 'ox_plow' ? 'selected' : '' }}>Ox Plow / Jembe la Ngombe</option>
                                    <option value="manual" {{ old('form_data.plowing_method', $farmerForm->form_data['plowing_method'] ?? '') == 'manual' ? 'selected' : '' }}>Manual / Kwa Mikono</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Planting Date / Tarehe ya Kupanda</label>
                                <input type="date" name="form_data[planting_date]" class="form-control" value="{{ old('form_data.planting_date', $farmerForm->form_data['planting_date'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Seed Variety / Aina ya Mbegu</label>
                                <input type="text" name="form_data[seed_variety]" class="form-control" value="{{ old('form_data.seed_variety', $farmerForm->form_data['seed_variety'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Seed Quantity (kg) / Kiasi cha Mbegu</label>
                                <input type="number" step="0.1" name="form_data[seed_quantity]" class="form-control" value="{{ old('form_data.seed_quantity', $farmerForm->form_data['seed_quantity'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Seed Source / Chanzo cha Mbegu</label>
                                <select name="form_data[seed_source]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="remei" {{ old('form_data.seed_source', $farmerForm->form_data['seed_source'] ?? '') == 'remei' ? 'selected' : '' }}>Remei</option>
                                    <option value="own_saved" {{ old('form_data.seed_source', $farmerForm->form_data['seed_source'] ?? '') == 'own_saved' ? 'selected' : '' }}>Own Saved / Mwenyewe</option>
                                    <option value="other" {{ old('form_data.seed_source', $farmerForm->form_data['seed_source'] ?? '') == 'other' ? 'selected' : '' }}>Other / Nyingine</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Soil Information -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-mountain me-2"></i>Soil Information / Taarifa za Udongo</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Soil Type / Aina ya Udongo</label>
                                <select name="form_data[soil_type]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="clay" {{ old('form_data.soil_type', $farmerForm->form_data['soil_type'] ?? '') == 'clay' ? 'selected' : '' }}>Clay / Udongo wa Mfinyanzi</option>
                                    <option value="sandy" {{ old('form_data.soil_type', $farmerForm->form_data['soil_type'] ?? '') == 'sandy' ? 'selected' : '' }}>Sandy / Mchanga</option>
                                    <option value="loamy" {{ old('form_data.soil_type', $farmerForm->form_data['soil_type'] ?? '') == 'loamy' ? 'selected' : '' }}>Loamy / Tifutifu</option>
                                    <option value="mixed" {{ old('form_data.soil_type', $farmerForm->form_data['soil_type'] ?? '') == 'mixed' ? 'selected' : '' }}>Mixed / Mchanganyiko</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Terrain / Mwenendo wa Ardhi</label>
                                <select name="form_data[terrain]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="flat" {{ old('form_data.terrain', $farmerForm->form_data['terrain'] ?? '') == 'flat' ? 'selected' : '' }}>Flat / Tambarare</option>
                                    <option value="sloping" {{ old('form_data.terrain', $farmerForm->form_data['terrain'] ?? '') == 'sloping' ? 'selected' : '' }}>Sloping / Mteremko</option>
                                    <option value="hilly" {{ old('form_data.terrain', $farmerForm->form_data['terrain'] ?? '') == 'hilly' ? 'selected' : '' }}>Hilly / Milima</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="mb-3">
                        <label class="form-label">Additional Notes / Maelezo Mengine</label>
                        <textarea name="form_data[notes]" class="form-control" rows="3">{{ old('form_data.notes', $farmerForm->form_data['notes'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Form Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" name="status" value="submitted" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Submit Form
                        </button>
                        <button type="submit" name="status" value="draft" class="btn btn-outline-secondary">
                            <i class="fas fa-save me-2"></i>Save as Draft
                        </button>
                        <a href="{{ route('farmer-forms.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Form Info</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <strong>Form 2:</strong> Kumbukumbu Shamba Mpya
                    </p>
                    <p class="small text-muted mb-0">
                        This form is used to document new farm records including farm information, location, land preparation details, and soil information.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
