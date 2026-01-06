@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] ?? 'Makisio Yanayohitajika' }} - Required Estimates</small>
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
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Required Estimates Form</h5>
                </div>
                <div class="card-body">
                    <!-- Season Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Season / Msimu <span class="text-danger">*</span></label>
                            <input type="text" name="season" class="form-control" value="{{ old('season', $farmerForm->season ?? '') }}" placeholder="e.g., 2024/2025" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estimate Date / Tarehe ya Makisio</label>
                            <input type="date" name="form_date" class="form-control" value="{{ old('form_date', isset($farmerForm) ? $farmerForm->form_date?->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>
                    </div>

                    <!-- Catchment Area Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Catchment Area / Eneo la Uvunaji</label>
                            <input type="text" name="form_data[catchment_area]" class="form-control" value="{{ old('form_data.catchment_area', $farmerForm->form_data['catchment_area'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prepared By / Imeandaliwa na</label>
                            <input type="text" name="form_data[prepared_by]" class="form-control" value="{{ old('form_data.prepared_by', $farmerForm->form_data['prepared_by'] ?? auth()->user()->name) }}">
                        </div>
                    </div>

                    <!-- Farmer Estimates -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-users me-2"></i>Farmer Estimates / Makisio ya Wakulima</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Total Registered Farmers / Wakulima Waliosajiliwa</label>
                                <input type="number" name="form_data[total_registered_farmers]" class="form-control" value="{{ old('form_data.total_registered_farmers', $farmerForm->form_data['total_registered_farmers'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Expected Active Farmers / Wakulima Watakaoshiriki</label>
                                <input type="number" name="form_data[expected_active_farmers]" class="form-control" value="{{ old('form_data.expected_active_farmers', $farmerForm->form_data['expected_active_farmers'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">New Farmers Expected / Wakulima Wapya Wanatarajiwa</label>
                                <input type="number" name="form_data[new_farmers_expected]" class="form-control" value="{{ old('form_data.new_farmers_expected', $farmerForm->form_data['new_farmers_expected'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Production Estimates -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-tractor me-2"></i>Production Estimates / Makisio ya Uzalishaji</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Expected Area (Acres) / Eneo Linalotarajiwa</label>
                                <input type="number" step="0.01" name="form_data[expected_area]" class="form-control" value="{{ old('form_data.expected_area', $farmerForm->form_data['expected_area'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Expected Yield (kg) / Mavuno Yanayotarajiwa</label>
                                <input type="number" step="0.1" name="form_data[expected_yield]" class="form-control" value="{{ old('form_data.expected_yield', $farmerForm->form_data['expected_yield'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Yield per Acre (kg) / Mavuno kwa Ekari</label>
                                <input type="number" step="0.1" name="form_data[yield_per_acre]" class="form-control" value="{{ old('form_data.yield_per_acre', $farmerForm->form_data['yield_per_acre'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Expected Organic Cotton (kg) / Pamba ya Kilimo Hai</label>
                                <input type="number" step="0.1" name="form_data[expected_organic_cotton]" class="form-control" value="{{ old('form_data.expected_organic_cotton', $farmerForm->form_data['expected_organic_cotton'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expected Conventional Cotton (kg) / Pamba ya Kawaida</label>
                                <input type="number" step="0.1" name="form_data[expected_conventional_cotton]" class="form-control" value="{{ old('form_data.expected_conventional_cotton', $farmerForm->form_data['expected_conventional_cotton'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Input Requirements -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-seedling me-2"></i>Input Requirements / Mahitaji ya Pembejeo</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Seed Required (kg) / Mbegu Zinazohitajika</label>
                                <input type="number" step="0.1" name="form_data[seed_required]" class="form-control" value="{{ old('form_data.seed_required', $farmerForm->form_data['seed_required'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Seed Variety / Aina ya Mbegu</label>
                                <input type="text" name="form_data[seed_variety]" class="form-control" value="{{ old('form_data.seed_variety', $farmerForm->form_data['seed_variety'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Organic Inputs Required (kg) / Pembejeo za Kilimo Hai</label>
                                <input type="number" step="0.1" name="form_data[organic_inputs_required]" class="form-control" value="{{ old('form_data.organic_inputs_required', $farmerForm->form_data['organic_inputs_required'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Compost Required (kg) / Mboji Inayohitajika</label>
                                <input type="number" step="0.1" name="form_data[compost_required]" class="form-control" value="{{ old('form_data.compost_required', $farmerForm->form_data['compost_required'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Neem Required (liters) / Mwarobaini Unaohitajika</label>
                                <input type="number" step="0.1" name="form_data[neem_required]" class="form-control" value="{{ old('form_data.neem_required', $farmerForm->form_data['neem_required'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Other Inputs / Pembejeo Nyingine</label>
                                <input type="text" name="form_data[other_inputs]" class="form-control" value="{{ old('form_data.other_inputs', $farmerForm->form_data['other_inputs'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Financial Estimates -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-money-bill-wave me-2"></i>Financial Estimates / Makisio ya Fedha</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Seed Cost (TZS) / Gharama ya Mbegu</label>
                                <input type="number" name="form_data[seed_cost]" class="form-control" value="{{ old('form_data.seed_cost', $farmerForm->form_data['seed_cost'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Input Cost (TZS) / Gharama ya Pembejeo</label>
                                <input type="number" name="form_data[input_cost]" class="form-control" value="{{ old('form_data.input_cost', $farmerForm->form_data['input_cost'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Training Cost (TZS) / Gharama ya Mafunzo</label>
                                <input type="number" name="form_data[training_cost]" class="form-control" value="{{ old('form_data.training_cost', $farmerForm->form_data['training_cost'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Transport Cost (TZS) / Gharama ya Usafiri</label>
                                <input type="number" name="form_data[transport_cost]" class="form-control" value="{{ old('form_data.transport_cost', $farmerForm->form_data['transport_cost'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Other Costs (TZS) / Gharama Nyingine</label>
                                <input type="number" name="form_data[other_costs]" class="form-control" value="{{ old('form_data.other_costs', $farmerForm->form_data['other_costs'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Total Budget (TZS) / Bajeti Jumla</label>
                                <input type="number" name="form_data[total_budget]" class="form-control" value="{{ old('form_data.total_budget', $farmerForm->form_data['total_budget'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Expected Revenue (TZS) / Mapato Yanayotarajiwa</label>
                                <input type="number" name="form_data[expected_revenue]" class="form-control" value="{{ old('form_data.expected_revenue', $farmerForm->form_data['expected_revenue'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expected Farmer Income (TZS) / Mapato ya Wakulima</label>
                                <input type="number" name="form_data[expected_farmer_income]" class="form-control" value="{{ old('form_data.expected_farmer_income', $farmerForm->form_data['expected_farmer_income'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Risk Assessment -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Risk Assessment / Tathmini ya Hatari</h6>

                        <div class="mb-3">
                            <label class="form-label">Potential Risks / Hatari Zinazowezekana</label>
                            <div class="row">
                                @foreach(['drought' => 'Drought / Ukame', 'floods' => 'Floods / Mafuriko', 'pests' => 'Pests / Wadudu', 'market_fluctuation' => 'Market Fluctuation / Kubadilika kwa Soko', 'input_shortage' => 'Input Shortage / Upungufu wa Pembejeo', 'labor_shortage' => 'Labor Shortage / Upungufu wa Wafanyakazi'] as $value => $label)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="form_data[risks][]" value="{{ $value }}" id="risk_{{ $value }}"
                                                {{ in_array($value, old('form_data.risks', $farmerForm->form_data['risks'] ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="risk_{{ $value }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Risk Mitigation Strategies / Mikakati ya Kupunguza Hatari</label>
                            <textarea name="form_data[risk_mitigation]" class="form-control" rows="3">{{ old('form_data.risk_mitigation', $farmerForm->form_data['risk_mitigation'] ?? '') }}</textarea>
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
                        <strong>Form 10:</strong> Makisio Yanayohitajika
                    </p>
                    <p class="small text-muted mb-0">
                        This form captures production estimates, input requirements, financial projections, and risk assessments for the upcoming season.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
