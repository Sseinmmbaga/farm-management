@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] ?? 'Ripoti ya Utendaji wa Kila Mwezi' }} - Monthly Performance Report</small>
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
                <div class="card-header bg-purple text-white" style="background-color: #6f42c1 !important;">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Performance Report</h5>
                </div>
                <div class="card-body">
                    <!-- Report Period -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Season / Msimu</label>
                            <input type="text" name="season" class="form-control" value="{{ old('season', $farmerForm->season ?? '') }}" placeholder="e.g., 2024/2025">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Report Month / Mwezi wa Ripoti</label>
                            <input type="month" name="form_data[report_month]" class="form-control" value="{{ old('form_data.report_month', $farmerForm->form_data['report_month'] ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Report Date / Tarehe ya Ripoti</label>
                            <input type="date" name="form_date" class="form-control" value="{{ old('form_date', isset($farmerForm) ? $farmerForm->form_date?->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>
                    </div>

                    <!-- Supervisor Information -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-user-tie me-2"></i>Supervisor Information / Taarifa za Msimamizi</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Supervisor Name / Jina la Msimamizi</label>
                                <input type="text" name="form_data[supervisor_name]" class="form-control" value="{{ old('form_data.supervisor_name', $farmerForm->form_data['supervisor_name'] ?? auth()->user()->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catchment Area / Eneo la Uvunaji</label>
                                <input type="text" name="form_data[catchment_area]" class="form-control" value="{{ old('form_data.catchment_area', $farmerForm->form_data['catchment_area'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Farmer Statistics -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-users me-2"></i>Farmer Statistics / Takwimu za Wakulima</h6>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Total Farmers / Wakulima Wote</label>
                                <input type="number" name="form_data[total_farmers]" class="form-control" value="{{ old('form_data.total_farmers', $farmerForm->form_data['total_farmers'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Active Farmers / Wakulima Hai</label>
                                <input type="number" name="form_data[active_farmers]" class="form-control" value="{{ old('form_data.active_farmers', $farmerForm->form_data['active_farmers'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">New Registrations / Usajili Mpya</label>
                                <input type="number" name="form_data[new_registrations]" class="form-control" value="{{ old('form_data.new_registrations', $farmerForm->form_data['new_registrations'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Farmers Visited / Waliotembelewa</label>
                                <input type="number" name="form_data[farmers_visited]" class="form-control" value="{{ old('form_data.farmers_visited', $farmerForm->form_data['farmers_visited'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Male Farmers / Wakulima wa Kiume</label>
                                <input type="number" name="form_data[male_farmers]" class="form-control" value="{{ old('form_data.male_farmers', $farmerForm->form_data['male_farmers'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Female Farmers / Wakulima wa Kike</label>
                                <input type="number" name="form_data[female_farmers]" class="form-control" value="{{ old('form_data.female_farmers', $farmerForm->form_data['female_farmers'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Youth Farmers (Below 35) / Vijana</label>
                                <input type="number" name="form_data[youth_farmers]" class="form-control" value="{{ old('form_data.youth_farmers', $farmerForm->form_data['youth_farmers'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Farm Statistics -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-tractor me-2"></i>Farm Statistics / Takwimu za Mashamba</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Total Area (Acres) / Eneo Jumla</label>
                                <input type="number" step="0.01" name="form_data[total_area]" class="form-control" value="{{ old('form_data.total_area', $farmerForm->form_data['total_area'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Area Planted (Acres) / Eneo Liliopandwa</label>
                                <input type="number" step="0.01" name="form_data[area_planted]" class="form-control" value="{{ old('form_data.area_planted', $farmerForm->form_data['area_planted'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Expected Yield (kg) / Mavuno Yanayotarajiwa</label>
                                <input type="number" step="0.1" name="form_data[expected_yield]" class="form-control" value="{{ old('form_data.expected_yield', $farmerForm->form_data['expected_yield'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Activities Performed -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-tasks me-2"></i>Activities Performed / Shughuli Zilizofanyika</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Farm Visits Made / Ziara za Shamba</label>
                                <input type="number" name="form_data[farm_visits]" class="form-control" value="{{ old('form_data.farm_visits', $farmerForm->form_data['farm_visits'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Training Sessions / Mafunzo</label>
                                <input type="number" name="form_data[training_sessions]" class="form-control" value="{{ old('form_data.training_sessions', $farmerForm->form_data['training_sessions'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Group Meetings / Mikutano ya Vikundi</label>
                                <input type="number" name="form_data[group_meetings]" class="form-control" value="{{ old('form_data.group_meetings', $farmerForm->form_data['group_meetings'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Inspections Conducted / Ukaguzi Uliofanywa</label>
                                <input type="number" name="form_data[inspections_conducted]" class="form-control" value="{{ old('form_data.inspections_conducted', $farmerForm->form_data['inspections_conducted'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Seed Distribution Events / Usambazaji wa Mbegu</label>
                                <input type="number" name="form_data[seed_distribution_events]" class="form-control" value="{{ old('form_data.seed_distribution_events', $farmerForm->form_data['seed_distribution_events'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Issues Resolved / Matatizo Yaliyotatuliwa</label>
                                <input type="number" name="form_data[issues_resolved]" class="form-control" value="{{ old('form_data.issues_resolved', $farmerForm->form_data['issues_resolved'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Challenges & Recommendations -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Challenges & Recommendations / Changamoto na Mapendekezo</h6>

                        <div class="mb-3">
                            <label class="form-label">Challenges Faced / Changamoto Zilizokutwa</label>
                            <textarea name="form_data[challenges]" class="form-control" rows="3">{{ old('form_data.challenges', $farmerForm->form_data['challenges'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Recommendations / Mapendekezo</label>
                            <textarea name="form_data[recommendations]" class="form-control" rows="3">{{ old('form_data.recommendations', $farmerForm->form_data['recommendations'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Plans for Next Month / Mipango ya Mwezi Ujao</label>
                            <textarea name="form_data[next_month_plans]" class="form-control" rows="3">{{ old('form_data.next_month_plans', $farmerForm->form_data['next_month_plans'] ?? '') }}</textarea>
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
                        <strong>Form 6:</strong> Ripoti ya Utendaji wa Kila Mwezi
                    </p>
                    <p class="small text-muted mb-0">
                        This monthly report captures performance metrics including farmer statistics, farm data, activities performed, and challenges faced.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
