@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] }} - Old Farm Records</small>
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
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Old Farm Records Form</h5>
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

                    <!-- Historical Farm Data -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-archive me-2"></i>Historical Farm Data / Data za Zamani</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Year Started Farming / Mwaka wa Kuanza</label>
                                <input type="number" name="form_data[year_started]" class="form-control" min="1950" max="{{ date('Y') }}" value="{{ old('form_data.year_started', $farmerForm->form_data['year_started'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Years with Remei / Miaka na Remei</label>
                                <input type="number" name="form_data[years_with_remei]" class="form-control" value="{{ old('form_data.years_with_remei', $farmerForm->form_data['years_with_remei'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Organic Certification Year</label>
                                <input type="number" name="form_data[organic_certification_year]" class="form-control" min="1990" max="{{ date('Y') }}" value="{{ old('form_data.organic_certification_year', $farmerForm->form_data['organic_certification_year'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Previous Season Records -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i>Previous Season Records / Rekodi za Msimu Uliopita</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Previous Season / Msimu Uliopita</label>
                                <input type="text" name="form_data[previous_season]" class="form-control" value="{{ old('form_data.previous_season', $farmerForm->form_data['previous_season'] ?? '') }}" placeholder="e.g., 2023/2024">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Area Planted (Acres) / Eneo Liliopandwa</label>
                                <input type="number" step="0.01" name="form_data[prev_area_planted]" class="form-control" value="{{ old('form_data.prev_area_planted', $farmerForm->form_data['prev_area_planted'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Yield (kg) / Mavuno</label>
                                <input type="number" step="0.1" name="form_data[prev_yield]" class="form-control" value="{{ old('form_data.prev_yield', $farmerForm->form_data['prev_yield'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Yield per Acre (kg) / Mavuno kwa Ekari</label>
                                <input type="number" step="0.1" name="form_data[prev_yield_per_acre]" class="form-control" value="{{ old('form_data.prev_yield_per_acre', $farmerForm->form_data['prev_yield_per_acre'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quality Grade / Ubora</label>
                                <select name="form_data[prev_quality_grade]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="A" {{ old('form_data.prev_quality_grade', $farmerForm->form_data['prev_quality_grade'] ?? '') == 'A' ? 'selected' : '' }}>Grade A</option>
                                    <option value="B" {{ old('form_data.prev_quality_grade', $farmerForm->form_data['prev_quality_grade'] ?? '') == 'B' ? 'selected' : '' }}>Grade B</option>
                                    <option value="C" {{ old('form_data.prev_quality_grade', $farmerForm->form_data['prev_quality_grade'] ?? '') == 'C' ? 'selected' : '' }}>Grade C</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Revenue (TZS) / Mapato</label>
                                <input type="number" name="form_data[prev_revenue]" class="form-control" value="{{ old('form_data.prev_revenue', $farmerForm->form_data['prev_revenue'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Challenges & Improvements -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Challenges & Improvements / Changamoto na Maboresho</h6>

                        <div class="mb-3">
                            <label class="form-label">Previous Challenges / Changamoto Zilizopita</label>
                            <div class="row">
                                @foreach(['pests' => 'Pests / Wadudu', 'drought' => 'Drought / Ukame', 'floods' => 'Floods / Mafuriko', 'diseases' => 'Diseases / Magonjwa', 'labor_shortage' => 'Labor Shortage / Upungufu wa Wafanyakazi', 'market_access' => 'Market Access / Upatikanaji wa Soko'] as $value => $label)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="form_data[challenges][]" value="{{ $value }}" id="challenge_{{ $value }}"
                                                {{ in_array($value, old('form_data.challenges', $farmerForm->form_data['challenges'] ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="challenge_{{ $value }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Challenge Details / Maelezo ya Changamoto</label>
                            <textarea name="form_data[challenge_details]" class="form-control" rows="2">{{ old('form_data.challenge_details', $farmerForm->form_data['challenge_details'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Improvements Made / Maboresho Yaliyofanyika</label>
                            <textarea name="form_data[improvements_made]" class="form-control" rows="2">{{ old('form_data.improvements_made', $farmerForm->form_data['improvements_made'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Current Season Plans -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-clipboard-list me-2"></i>Current Season Plans / Mipango ya Msimu Huu</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Planned Area (Acres) / Eneo Lililopangwa</label>
                                <input type="number" step="0.01" name="form_data[planned_area]" class="form-control" value="{{ old('form_data.planned_area', $farmerForm->form_data['planned_area'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Expected Yield (kg) / Mavuno Yanayotarajiwa</label>
                                <input type="number" step="0.1" name="form_data[expected_yield]" class="form-control" value="{{ old('form_data.expected_yield', $farmerForm->form_data['expected_yield'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Seed Requirement (kg) / Mahitaji ya Mbegu</label>
                                <input type="number" step="0.1" name="form_data[seed_requirement]" class="form-control" value="{{ old('form_data.seed_requirement', $farmerForm->form_data['seed_requirement'] ?? '') }}">
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
                        <strong>Form 3:</strong> Kumbukumbu Shamba Zamani
                    </p>
                    <p class="small text-muted mb-0">
                        This form is used to document historical farm records including previous season performance, challenges faced, and current season plans.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
