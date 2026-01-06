@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] ?? 'Viwango vya Kijamii na Mazingira' }} - Social & Environmental Standards</small>
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
                    <h5 class="mb-0"><i class="fas fa-leaf me-2"></i>Social & Environmental Standards Assessment</h5>
                </div>
                <div class="card-body">
                    <!-- Assessment Info -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Season / Msimu</label>
                            <input type="text" name="season" class="form-control" value="{{ old('season', $farmerForm->season ?? '') }}" placeholder="e.g., 2024/2025">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Assessment Date / Tarehe ya Tathmini</label>
                            <input type="date" name="form_date" class="form-control" value="{{ old('form_date', isset($farmerForm) ? $farmerForm->form_date?->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Inspector Name / Jina la Mkaguzi</label>
                            <input type="text" name="form_data[inspector_name]" class="form-control" value="{{ old('form_data.inspector_name', $farmerForm->form_data['inspector_name'] ?? auth()->user()->name) }}">
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

                    <!-- Social Standards -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-users me-2"></i>Social Standards / Viwango vya Kijamii</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">No Child Labor / Hakuna Watoto Wanaofanya Kazi</label>
                                <select name="form_data[no_child_labor]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.no_child_labor', $farmerForm->form_data['no_child_labor'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.no_child_labor', $farmerForm->form_data['no_child_labor'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                    <option value="not_applicable" {{ old('form_data.no_child_labor', $farmerForm->form_data['no_child_labor'] ?? '') == 'not_applicable' ? 'selected' : '' }}>Not Applicable / Haihusiki</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No Forced Labor / Hakuna Kazi ya Kulazimishwa</label>
                                <select name="form_data[no_forced_labor]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.no_forced_labor', $farmerForm->form_data['no_forced_labor'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.no_forced_labor', $farmerForm->form_data['no_forced_labor'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fair Wages / Mishahara ya Haki</label>
                                <select name="form_data[fair_wages]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.fair_wages', $farmerForm->form_data['fair_wages'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.fair_wages', $farmerForm->form_data['fair_wages'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                    <option value="not_applicable" {{ old('form_data.fair_wages', $farmerForm->form_data['fair_wages'] ?? '') == 'not_applicable' ? 'selected' : '' }}>Not Applicable / Haihusiki</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Safe Working Conditions / Mazingira Salama ya Kazi</label>
                                <select name="form_data[safe_working_conditions]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.safe_working_conditions', $farmerForm->form_data['safe_working_conditions'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.safe_working_conditions', $farmerForm->form_data['safe_working_conditions'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Non-Discrimination / Hakuna Ubaguzi</label>
                                <select name="form_data[non_discrimination]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.non_discrimination', $farmerForm->form_data['non_discrimination'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.non_discrimination', $farmerForm->form_data['non_discrimination'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Freedom of Association / Uhuru wa Kujiunga</label>
                                <select name="form_data[freedom_association]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.freedom_association', $farmerForm->form_data['freedom_association'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.freedom_association', $farmerForm->form_data['freedom_association'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Social Issues Observed / Matatizo ya Kijamii Yaliyozingatiwa</label>
                            <textarea name="form_data[social_issues]" class="form-control" rows="2">{{ old('form_data.social_issues', $farmerForm->form_data['social_issues'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Environmental Standards -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-tree me-2"></i>Environmental Standards / Viwango vya Mazingira</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">No Deforestation / Hakuna Ukataji Miti</label>
                                <select name="form_data[no_deforestation]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.no_deforestation', $farmerForm->form_data['no_deforestation'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.no_deforestation', $farmerForm->form_data['no_deforestation'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Water Protection / Ulinzi wa Maji</label>
                                <select name="form_data[water_protection]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.water_protection', $farmerForm->form_data['water_protection'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.water_protection', $farmerForm->form_data['water_protection'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Biodiversity Conservation / Uhifadhi wa Viumbe</label>
                                <select name="form_data[biodiversity_conservation]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.biodiversity_conservation', $farmerForm->form_data['biodiversity_conservation'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.biodiversity_conservation', $farmerForm->form_data['biodiversity_conservation'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Soil Conservation / Uhifadhi wa Udongo</label>
                                <select name="form_data[soil_conservation]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.soil_conservation', $farmerForm->form_data['soil_conservation'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.soil_conservation', $farmerForm->form_data['soil_conservation'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Waste Management / Usimamizi wa Taka</label>
                                <select name="form_data[waste_management]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.waste_management', $farmerForm->form_data['waste_management'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.waste_management', $farmerForm->form_data['waste_management'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No GMO Usage / Hakuna Matumizi ya GMO</label>
                                <select name="form_data[no_gmo]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.no_gmo', $farmerForm->form_data['no_gmo'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="non_compliant" {{ old('form_data.no_gmo', $farmerForm->form_data['no_gmo'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Environmental Issues Observed / Matatizo ya Mazingira Yaliyozingatiwa</label>
                            <textarea name="form_data[environmental_issues]" class="form-control" rows="2">{{ old('form_data.environmental_issues', $farmerForm->form_data['environmental_issues'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Overall Assessment -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-clipboard-check me-2"></i>Overall Assessment / Tathmini ya Jumla</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Social Score (1-10) / Alama ya Kijamii</label>
                                <input type="number" name="form_data[social_score]" class="form-control" min="1" max="10" value="{{ old('form_data.social_score', $farmerForm->form_data['social_score'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Environmental Score (1-10) / Alama ya Mazingira</label>
                                <input type="number" name="form_data[environmental_score]" class="form-control" min="1" max="10" value="{{ old('form_data.environmental_score', $farmerForm->form_data['environmental_score'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Overall Compliance Status / Hali ya Jumla</label>
                                <select name="form_data[overall_compliance]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="fully_compliant" {{ old('form_data.overall_compliance', $farmerForm->form_data['overall_compliance'] ?? '') == 'fully_compliant' ? 'selected' : '' }}>Fully Compliant / Anazingatia Kikamilifu</option>
                                    <option value="mostly_compliant" {{ old('form_data.overall_compliance', $farmerForm->form_data['overall_compliance'] ?? '') == 'mostly_compliant' ? 'selected' : '' }}>Mostly Compliant / Anazingatia Zaidi</option>
                                    <option value="needs_improvement" {{ old('form_data.overall_compliance', $farmerForm->form_data['overall_compliance'] ?? '') == 'needs_improvement' ? 'selected' : '' }}>Needs Improvement / Anahitaji Kuboreshwa</option>
                                    <option value="non_compliant" {{ old('form_data.overall_compliance', $farmerForm->form_data['overall_compliance'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Recommendations / Mapendekezo</label>
                            <textarea name="form_data[recommendations]" class="form-control" rows="3">{{ old('form_data.recommendations', $farmerForm->form_data['recommendations'] ?? '') }}</textarea>
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
                        <strong>Form 8:</strong> Viwango vya Kijamii na Mazingira
                    </p>
                    <p class="small text-muted mb-0">
                        This form assesses compliance with social and environmental standards including labor practices, working conditions, and environmental conservation.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
