@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] }} - Utambulisho wa Mkulima</small>
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
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Farmer Identification Form</h5>
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

                    <!-- Farmer Type -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <label class="form-label fw-bold">Farmer Type / Aina ya Mkulima</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="form_data[farmer_type]" id="new_farmer" value="new" {{ old('form_data.farmer_type', $farmerForm->form_data['farmer_type'] ?? '') == 'new' ? 'checked' : '' }}>
                            <label class="form-check-label" for="new_farmer">Mkulima mpya (New Farmer)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="form_data[farmer_type]" id="existing_farmer" value="existing" {{ old('form_data.farmer_type', $farmerForm->form_data['farmer_type'] ?? '') == 'existing' ? 'checked' : '' }}>
                            <label class="form-check-label" for="existing_farmer">Mkulima wa zamani (Existing Farmer)</label>
                        </div>
                    </div>

                    <!-- Farmer Selection or Details -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Farmer / Chagua Mkulima</label>
                            <select name="farmer_id" class="form-select" id="farmerSelect">
                                <option value="">-- Select Farmer --</option>
                                @foreach($farmers as $f)
                                    <option value="{{ $f->id }}" {{ old('farmer_id', $farmerForm->farmer_id ?? ($farmer->id ?? '')) == $f->id ? 'selected' : '' }}>
                                        {{ $f->first_name }} {{ $f->last_name }} ({{ $f->registration_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Farmer Number / Namba ya Mkulima</label>
                            <input type="text" name="form_data[farmer_number]" class="form-control" value="{{ old('form_data.farmer_number', $farmerForm->form_data['farmer_number'] ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Farmer Name / Jina la Mkulima</label>
                            <input type="text" name="form_data[farmer_name]" class="form-control" value="{{ old('form_data.farmer_name', $farmerForm->form_data['farmer_name'] ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender / Jinsia</label>
                            <select name="form_data[gender]" class="form-select">
                                <option value="">Select</option>
                                <option value="male" {{ old('form_data.gender', $farmerForm->form_data['gender'] ?? '') == 'male' ? 'selected' : '' }}>Male / Kiume</option>
                                <option value="female" {{ old('form_data.gender', $farmerForm->form_data['gender'] ?? '') == 'female' ? 'selected' : '' }}>Female / Kike</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Subvillage / Kitongoji</label>
                            <input type="text" name="form_data[subvillage]" class="form-control" value="{{ old('form_data.subvillage', $farmerForm->form_data['subvillage'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supervisor / Msimamizi</label>
                            <input type="text" name="form_data[supervisor]" class="form-control" value="{{ old('form_data.supervisor', $farmerForm->form_data['supervisor'] ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Extension Officer Number / Na. ya Bw/Bi Shamba</label>
                            <input type="text" name="form_data[extension_officer_number]" class="form-control" value="{{ old('form_data.extension_officer_number', $farmerForm->form_data['extension_officer_number'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Extension Officer Name / Bw/Bi Shamba</label>
                            <input type="text" name="form_data[extension_officer_name]" class="form-control" value="{{ old('form_data.extension_officer_name', $farmerForm->form_data['extension_officer_name'] ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Registration Date / Tarehe ya Kusajiliwa</label>
                            <input type="date" name="form_data[registration_date]" class="form-control" value="{{ old('form_data.registration_date', $farmerForm->form_data['registration_date'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Farmer Group / Kundi</label>
                            <input type="text" name="form_data[farmer_group]" class="form-control" value="{{ old('form_data.farmer_group', $farmerForm->form_data['farmer_group'] ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Land Size / Ukubwa wa eneo</label>
                            <input type="text" name="form_data[land_size]" class="form-control" value="{{ old('form_data.land_size', $farmerForm->form_data['land_size'] ?? '') }}" placeholder="acres">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cotton Producers Count / Idadi ya wazalishaji</label>
                            <input type="number" name="form_data[cotton_producers_count]" class="form-control" value="{{ old('form_data.cotton_producers_count', $farmerForm->form_data['cotton_producers_count'] ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lead Farmer / Mkulima Kiongozi</label>
                            <select name="form_data[lead_farmer]" class="form-select">
                                <option value="">Select</option>
                                <option value="yes" {{ old('form_data.lead_farmer', $farmerForm->form_data['lead_farmer'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                <option value="no" {{ old('form_data.lead_farmer', $farmerForm->form_data['lead_farmer'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Demo Farm / Shamba darasa</label>
                            <select name="form_data[demo_farm]" class="form-select">
                                <option value="">Select</option>
                                <option value="yes" {{ old('form_data.demo_farm', $farmerForm->form_data['demo_farm'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                <option value="no" {{ old('form_data.demo_farm', $farmerForm->form_data['demo_farm'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Owns Farming Tools / Anamiliki zana</label>
                            <select name="form_data[owns_farming_tools]" class="form-select">
                                <option value="">Select</option>
                                <option value="yes" {{ old('form_data.owns_farming_tools', $farmerForm->form_data['owns_farming_tools'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                <option value="no" {{ old('form_data.owns_farming_tools', $farmerForm->form_data['owns_farming_tools'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Prohibited Chemical Use</label>
                            <input type="date" name="form_data[last_prohibited_chemical_use]" class="form-control" value="{{ old('form_data.last_prohibited_chemical_use', $farmerForm->form_data['last_prohibited_chemical_use'] ?? '') }}">
                        </div>
                    </div>

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
                        <strong>Form 1:</strong> Utambulisho wa Mkulima
                    </p>
                    <p class="small text-muted mb-0">
                        This form is used to record and identify farmer information including registration details, location, and farming tools ownership.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const farmerSelect = document.getElementById('farmerSelect');
    if (farmerSelect) {
        farmerSelect.addEventListener('change', function() {
            // You can add auto-fill functionality here based on selected farmer
        });
    }
});
</script>
@endpush
