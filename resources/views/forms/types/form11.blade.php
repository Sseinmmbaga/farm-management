@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] ?? 'Historia ya Shamba' }} - Farm History</small>
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
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Farm History Form</h5>
                </div>
                <div class="card-body">
                    <!-- Form Info -->
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

                    <!-- Farmer & Farm Selection -->
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

                    <!-- Farm Origin -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-landmark me-2"></i>Farm Origin / Asili ya Shamba</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Year Farm Established / Mwaka wa Kuanzisha</label>
                                <input type="number" name="form_data[year_established]" class="form-control" min="1900" max="{{ date('Y') }}" value="{{ old('form_data.year_established', $farmerForm->form_data['year_established'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Original Farm Size (Acres) / Ukubwa wa Awali</label>
                                <input type="number" step="0.01" name="form_data[original_size]" class="form-control" value="{{ old('form_data.original_size', $farmerForm->form_data['original_size'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Current Farm Size (Acres) / Ukubwa wa Sasa</label>
                                <input type="number" step="0.01" name="form_data[current_size]" class="form-control" value="{{ old('form_data.current_size', $farmerForm->form_data['current_size'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Land Acquisition Method / Njia ya Kupata Ardhi</label>
                                <select name="form_data[acquisition_method]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="inherited" {{ old('form_data.acquisition_method', $farmerForm->form_data['acquisition_method'] ?? '') == 'inherited' ? 'selected' : '' }}>Inherited / Urithi</option>
                                    <option value="purchased" {{ old('form_data.acquisition_method', $farmerForm->form_data['acquisition_method'] ?? '') == 'purchased' ? 'selected' : '' }}>Purchased / Imenunuliwa</option>
                                    <option value="allocated" {{ old('form_data.acquisition_method', $farmerForm->form_data['acquisition_method'] ?? '') == 'allocated' ? 'selected' : '' }}>Government Allocated / Imegawiwa na Serikali</option>
                                    <option value="leased" {{ old('form_data.acquisition_method', $farmerForm->form_data['acquisition_method'] ?? '') == 'leased' ? 'selected' : '' }}>Leased / Imekodishwa</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Land Title Status / Hali ya Hati ya Ardhi</label>
                                <select name="form_data[land_title_status]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="titled" {{ old('form_data.land_title_status', $farmerForm->form_data['land_title_status'] ?? '') == 'titled' ? 'selected' : '' }}>Titled / Ina Hati</option>
                                    <option value="customary" {{ old('form_data.land_title_status', $farmerForm->form_data['land_title_status'] ?? '') == 'customary' ? 'selected' : '' }}>Customary Rights / Haki za Kimila</option>
                                    <option value="pending" {{ old('form_data.land_title_status', $farmerForm->form_data['land_title_status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending / Inasubiri</option>
                                    <option value="none" {{ old('form_data.land_title_status', $farmerForm->form_data['land_title_status'] ?? '') == 'none' ? 'selected' : '' }}>None / Hakuna</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Previous Land Use -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-retweet me-2"></i>Previous Land Use / Matumizi ya Zamani ya Ardhi</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Previous Use Before Cotton / Matumizi Kabla ya Pamba</label>
                                <select name="form_data[previous_use]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="virgin_land" {{ old('form_data.previous_use', $farmerForm->form_data['previous_use'] ?? '') == 'virgin_land' ? 'selected' : '' }}>Virgin Land / Ardhi Bikira</option>
                                    <option value="food_crops" {{ old('form_data.previous_use', $farmerForm->form_data['previous_use'] ?? '') == 'food_crops' ? 'selected' : '' }}>Food Crops / Mazao ya Chakula</option>
                                    <option value="conventional_cotton" {{ old('form_data.previous_use', $farmerForm->form_data['previous_use'] ?? '') == 'conventional_cotton' ? 'selected' : '' }}>Conventional Cotton / Pamba ya Kawaida</option>
                                    <option value="pasture" {{ old('form_data.previous_use', $farmerForm->form_data['previous_use'] ?? '') == 'pasture' ? 'selected' : '' }}>Pasture / Malisho</option>
                                    <option value="forest" {{ old('form_data.previous_use', $farmerForm->form_data['previous_use'] ?? '') == 'forest' ? 'selected' : '' }}>Forest / Msitu</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year Started Cotton Farming / Mwaka wa Kuanza Pamba</label>
                                <input type="number" name="form_data[year_started_cotton]" class="form-control" min="1900" max="{{ date('Y') }}" value="{{ old('form_data.year_started_cotton', $farmerForm->form_data['year_started_cotton'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Chemicals Previously Used / Kemikali Zilizotumika Zamani</label>
                            <textarea name="form_data[previous_chemicals]" class="form-control" rows="2" placeholder="List any synthetic fertilizers, pesticides used before organic conversion">{{ old('form_data.previous_chemicals', $farmerForm->form_data['previous_chemicals'] ?? '') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Last Chemical Application Date / Tarehe ya Mwisho ya Kemikali</label>
                                <input type="date" name="form_data[last_chemical_date]" class="form-control" value="{{ old('form_data.last_chemical_date', $farmerForm->form_data['last_chemical_date'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Conversion Period Status / Hali ya Kipindi cha Kubadilika</label>
                                <select name="form_data[conversion_status]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="completed" {{ old('form_data.conversion_status', $farmerForm->form_data['conversion_status'] ?? '') == 'completed' ? 'selected' : '' }}>Completed / Imekamilika</option>
                                    <option value="in_progress" {{ old('form_data.conversion_status', $farmerForm->form_data['conversion_status'] ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress / Inaendelea</option>
                                    <option value="not_started" {{ old('form_data.conversion_status', $farmerForm->form_data['conversion_status'] ?? '') == 'not_started' ? 'selected' : '' }}>Not Started / Haijaanza</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Historical Yields -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Historical Yields / Historia ya Mavuno</h6>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Year / Mwaka</th>
                                        <th>Area (Acres)</th>
                                        <th>Yield (kg)</th>
                                        <th>Quality Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 1; $i <= 5; $i++)
                                        <tr>
                                            <td>
                                                <input type="number" name="form_data[history_year_{{ $i }}]" class="form-control form-control-sm" min="2000" max="{{ date('Y') }}" value="{{ old("form_data.history_year_{$i}", $farmerForm->form_data["history_year_{$i}"] ?? '') }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="form_data[history_area_{{ $i }}]" class="form-control form-control-sm" value="{{ old("form_data.history_area_{$i}", $farmerForm->form_data["history_area_{$i}"] ?? '') }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.1" name="form_data[history_yield_{{ $i }}]" class="form-control form-control-sm" value="{{ old("form_data.history_yield_{$i}", $farmerForm->form_data["history_yield_{$i}"] ?? '') }}">
                                            </td>
                                            <td>
                                                <select name="form_data[history_grade_{{ $i }}]" class="form-select form-select-sm">
                                                    <option value="">-</option>
                                                    <option value="A" {{ old("form_data.history_grade_{$i}", $farmerForm->form_data["history_grade_{$i}"] ?? '') == 'A' ? 'selected' : '' }}>A</option>
                                                    <option value="B" {{ old("form_data.history_grade_{$i}", $farmerForm->form_data["history_grade_{$i}"] ?? '') == 'B' ? 'selected' : '' }}>B</option>
                                                    <option value="C" {{ old("form_data.history_grade_{$i}", $farmerForm->form_data["history_grade_{$i}"] ?? '') == 'C' ? 'selected' : '' }}>C</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Changes & Improvements -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-wrench me-2"></i>Changes & Improvements / Mabadiliko na Maboresho</h6>

                        <div class="mb-3">
                            <label class="form-label">Major Changes to Farm / Mabadiliko Makubwa ya Shamba</label>
                            <textarea name="form_data[major_changes]" class="form-control" rows="3">{{ old('form_data.major_changes', $farmerForm->form_data['major_changes'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Improvements Implemented / Maboresho Yaliyotekelezwa</label>
                            <textarea name="form_data[improvements_implemented]" class="form-control" rows="3">{{ old('form_data.improvements_implemented', $farmerForm->form_data['improvements_implemented'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Historical Challenges / Changamoto za Kihistoria</label>
                            <textarea name="form_data[historical_challenges]" class="form-control" rows="3">{{ old('form_data.historical_challenges', $farmerForm->form_data['historical_challenges'] ?? '') }}</textarea>
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
                        <strong>Form 11:</strong> Historia ya Shamba
                    </p>
                    <p class="small text-muted mb-0">
                        This form documents the complete farm history including origin, previous land use, chemical history, historical yields, and improvements made over time.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
