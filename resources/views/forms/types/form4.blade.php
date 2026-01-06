@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] }} - Improved Farm Questionnaire</small>
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
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Improved Farm Questionnaire</h5>
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

                    <!-- Farming Practices -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-seedling me-2"></i>Farming Practices / Mazoezi ya Kilimo</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Crop Rotation Practiced / Mzunguko wa Mazao</label>
                                <select name="form_data[crop_rotation]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.crop_rotation', $farmerForm->form_data['crop_rotation'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.crop_rotation', $farmerForm->form_data['crop_rotation'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Intercropping / Kilimo Mseto</label>
                                <select name="form_data[intercropping]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.intercropping', $farmerForm->form_data['intercropping'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.intercropping', $farmerForm->form_data['intercropping'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Crops Rotated With / Mazao Yanayozungushwa</label>
                                <input type="text" name="form_data[rotation_crops]" class="form-control" value="{{ old('form_data.rotation_crops', $farmerForm->form_data['rotation_crops'] ?? '') }}" placeholder="e.g., Maize, Groundnuts">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Intercrops / Mazao ya Mseto</label>
                                <input type="text" name="form_data[intercrops]" class="form-control" value="{{ old('form_data.intercrops', $farmerForm->form_data['intercrops'] ?? '') }}" placeholder="e.g., Beans, Sorghum">
                            </div>
                        </div>
                    </div>

                    <!-- Soil Management -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-mountain me-2"></i>Soil Management / Usimamizi wa Udongo</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Uses Compost / Anatumia Mboji</label>
                                <select name="form_data[uses_compost]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.uses_compost', $farmerForm->form_data['uses_compost'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.uses_compost', $farmerForm->form_data['uses_compost'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Uses Green Manure / Mbolea ya Kijani</label>
                                <select name="form_data[uses_green_manure]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.uses_green_manure', $farmerForm->form_data['uses_green_manure'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.uses_green_manure', $farmerForm->form_data['uses_green_manure'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Uses Animal Manure / Samadi</label>
                                <select name="form_data[uses_animal_manure]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.uses_animal_manure', $farmerForm->form_data['uses_animal_manure'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.uses_animal_manure', $farmerForm->form_data['uses_animal_manure'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mulching / Kutandaza Majani</label>
                                <select name="form_data[mulching]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.mulching', $farmerForm->form_data['mulching'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.mulching', $farmerForm->form_data['mulching'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Soil Conservation Methods / Njia za Kuhifadhi Udongo</label>
                                <input type="text" name="form_data[soil_conservation_methods]" class="form-control" value="{{ old('form_data.soil_conservation_methods', $farmerForm->form_data['soil_conservation_methods'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Pest Management -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-bug me-2"></i>Pest Management / Udhibiti wa Wadudu</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Biological Pest Control / Udhibiti wa Kiasili</label>
                                <select name="form_data[biological_pest_control]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.biological_pest_control', $farmerForm->form_data['biological_pest_control'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.biological_pest_control', $farmerForm->form_data['biological_pest_control'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Uses Neem / Anatumia Mwarobaini</label>
                                <select name="form_data[uses_neem]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.uses_neem', $farmerForm->form_data['uses_neem'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.uses_neem', $farmerForm->form_data['uses_neem'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Other Pest Control Methods / Njia Nyingine za Kudhibiti Wadudu</label>
                            <textarea name="form_data[other_pest_control]" class="form-control" rows="2">{{ old('form_data.other_pest_control', $farmerForm->form_data['other_pest_control'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Water Management -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-water me-2"></i>Water Management / Usimamizi wa Maji</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Water Source / Chanzo cha Maji</label>
                                <select name="form_data[water_source]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="rain" {{ old('form_data.water_source', $farmerForm->form_data['water_source'] ?? '') == 'rain' ? 'selected' : '' }}>Rain Only / Mvua Tu</option>
                                    <option value="well" {{ old('form_data.water_source', $farmerForm->form_data['water_source'] ?? '') == 'well' ? 'selected' : '' }}>Well / Kisima</option>
                                    <option value="river" {{ old('form_data.water_source', $farmerForm->form_data['water_source'] ?? '') == 'river' ? 'selected' : '' }}>River / Mto</option>
                                    <option value="irrigation" {{ old('form_data.water_source', $farmerForm->form_data['water_source'] ?? '') == 'irrigation' ? 'selected' : '' }}>Irrigation System / Umwagiliaji</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Water Harvesting / Uvunaji wa Maji</label>
                                <select name="form_data[water_harvesting]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.water_harvesting', $farmerForm->form_data['water_harvesting'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.water_harvesting', $farmerForm->form_data['water_harvesting'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Farm Improvements -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i>Farm Improvements / Maboresho ya Shamba</h6>

                        <div class="mb-3">
                            <label class="form-label">Improvements Implemented / Maboresho Yaliyotekelezwa</label>
                            <div class="row">
                                @foreach(['soil_testing' => 'Soil Testing / Kupima Udongo', 'contour_farming' => 'Contour Farming / Kilimo cha Mstari', 'agroforestry' => 'Agroforestry / Kilimo Msitu', 'terracing' => 'Terracing / Matuta', 'cover_crops' => 'Cover Crops / Mazao ya Kufunika', 'composting' => 'Composting / Kutengeneza Mboji'] as $value => $label)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="form_data[improvements][]" value="{{ $value }}" id="improvement_{{ $value }}"
                                                {{ in_array($value, old('form_data.improvements', $farmerForm->form_data['improvements'] ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="improvement_{{ $value }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Improvement Score (1-10) / Alama ya Uboreshaji</label>
                                <input type="number" name="form_data[improvement_score]" class="form-control" min="1" max="10" value="{{ old('form_data.improvement_score', $farmerForm->form_data['improvement_score'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Recommendations / Mapendekezo</label>
                                <input type="text" name="form_data[recommendations]" class="form-control" value="{{ old('form_data.recommendations', $farmerForm->form_data['recommendations'] ?? '') }}">
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
                        <strong>Form 4:</strong> Dodoso Shamba Lililoboreshwa
                    </p>
                    <p class="small text-muted mb-0">
                        This questionnaire captures information about improved farming practices, soil management, pest control, and farm improvements implemented.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
