@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] }} - Compliance Information</small>
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
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-check-double me-2"></i>Compliance Information Form</h5>
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

                    <!-- Organic Certification Compliance -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-certificate me-2"></i>Organic Certification Compliance / Uthibitisho wa Kilimo Hai</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Organic Certified / Amethibitishwa</label>
                                <select name="form_data[organic_certified]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.organic_certified', $farmerForm->form_data['organic_certified'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.organic_certified', $farmerForm->form_data['organic_certified'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                    <option value="in_conversion" {{ old('form_data.organic_certified', $farmerForm->form_data['organic_certified'] ?? '') == 'in_conversion' ? 'selected' : '' }}>In Conversion / Katika Mchakato</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Certification Year / Mwaka wa Uthibitisho</label>
                                <input type="number" name="form_data[certification_year]" class="form-control" min="1990" max="{{ date('Y') }}" value="{{ old('form_data.certification_year', $farmerForm->form_data['certification_year'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Certificate Number / Namba ya Cheti</label>
                                <input type="text" name="form_data[certificate_number]" class="form-control" value="{{ old('form_data.certificate_number', $farmerForm->form_data['certificate_number'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Prohibited Substances -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-ban me-2"></i>Prohibited Substances / Vitu Vilivyokatazwa</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Any Prohibited Chemical Used? / Je, Ametumia Kemikali Zilizozokatazwa?</label>
                                <select name="form_data[prohibited_chemical_used]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.prohibited_chemical_used', $farmerForm->form_data['prohibited_chemical_used'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.prohibited_chemical_used', $farmerForm->form_data['prohibited_chemical_used'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Use Date / Tarehe ya Mwisho Kutumia</label>
                                <input type="date" name="form_data[last_chemical_use_date]" class="form-control" value="{{ old('form_data.last_chemical_use_date', $farmerForm->form_data['last_chemical_use_date'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">If Yes, Specify Chemicals Used / Taja Kemikali Zilizotumika</label>
                            <textarea name="form_data[chemicals_used_details]" class="form-control" rows="2">{{ old('form_data.chemicals_used_details', $farmerForm->form_data['chemicals_used_details'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Buffer Zones -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-border-all me-2"></i>Buffer Zones / Maeneo ya Kinga</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Buffer Zone Maintained / Eneo la Kinga Linadumishwa</label>
                                <select name="form_data[buffer_zone_maintained]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.buffer_zone_maintained', $farmerForm->form_data['buffer_zone_maintained'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.buffer_zone_maintained', $farmerForm->form_data['buffer_zone_maintained'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Buffer Width (m) / Upana wa Kinga</label>
                                <input type="number" step="0.1" name="form_data[buffer_width]" class="form-control" value="{{ old('form_data.buffer_width', $farmerForm->form_data['buffer_width'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Adjacent Land Use / Matumizi ya Ardhi Jirani</label>
                                <select name="form_data[adjacent_land_use]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="organic" {{ old('form_data.adjacent_land_use', $farmerForm->form_data['adjacent_land_use'] ?? '') == 'organic' ? 'selected' : '' }}>Organic / Kilimo Hai</option>
                                    <option value="conventional" {{ old('form_data.adjacent_land_use', $farmerForm->form_data['adjacent_land_use'] ?? '') == 'conventional' ? 'selected' : '' }}>Conventional / Kawaida</option>
                                    <option value="forest" {{ old('form_data.adjacent_land_use', $farmerForm->form_data['adjacent_land_use'] ?? '') == 'forest' ? 'selected' : '' }}>Forest / Msitu</option>
                                    <option value="residential" {{ old('form_data.adjacent_land_use', $farmerForm->form_data['adjacent_land_use'] ?? '') == 'residential' ? 'selected' : '' }}>Residential / Makazi</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Record Keeping -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-file-alt me-2"></i>Record Keeping / Utunzaji wa Kumbukumbu</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Maintains Farm Records / Anatunza Kumbukumbu</label>
                                <select name="form_data[maintains_records]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.maintains_records', $farmerForm->form_data['maintains_records'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.maintains_records', $farmerForm->form_data['maintains_records'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Records Updated / Kumbukumbu Zimesasishwa</label>
                                <select name="form_data[records_updated]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.records_updated', $farmerForm->form_data['records_updated'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.records_updated', $farmerForm->form_data['records_updated'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Record Type / Aina ya Kumbukumbu</label>
                                <select name="form_data[record_type]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="written" {{ old('form_data.record_type', $farmerForm->form_data['record_type'] ?? '') == 'written' ? 'selected' : '' }}>Written / Maandishi</option>
                                    <option value="digital" {{ old('form_data.record_type', $farmerForm->form_data['record_type'] ?? '') == 'digital' ? 'selected' : '' }}>Digital / Kidijitali</option>
                                    <option value="both" {{ old('form_data.record_type', $farmerForm->form_data['record_type'] ?? '') == 'both' ? 'selected' : '' }}>Both / Zote</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Compliance Status -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-clipboard-check me-2"></i>Compliance Status / Hali ya Uzingatiaji</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Overall Compliance Status / Hali ya Jumla</label>
                                <select name="form_data[compliance_status]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="compliant" {{ old('form_data.compliance_status', $farmerForm->form_data['compliance_status'] ?? '') == 'compliant' ? 'selected' : '' }}>Compliant / Anazingatia</option>
                                    <option value="minor_issues" {{ old('form_data.compliance_status', $farmerForm->form_data['minor_issues'] ?? '') == 'minor_issues' ? 'selected' : '' }}>Minor Issues / Matatizo Madogo</option>
                                    <option value="major_issues" {{ old('form_data.compliance_status', $farmerForm->form_data['compliance_status'] ?? '') == 'major_issues' ? 'selected' : '' }}>Major Issues / Matatizo Makubwa</option>
                                    <option value="non_compliant" {{ old('form_data.compliance_status', $farmerForm->form_data['compliance_status'] ?? '') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant / Hazingatii</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Inspection Date / Tarehe ya Ukaguzi wa Mwisho</label>
                                <input type="date" name="form_data[last_inspection_date]" class="form-control" value="{{ old('form_data.last_inspection_date', $farmerForm->form_data['last_inspection_date'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Non-Compliance Issues (if any) / Matatizo ya Kutokuzingatia</label>
                            <textarea name="form_data[non_compliance_issues]" class="form-control" rows="2">{{ old('form_data.non_compliance_issues', $farmerForm->form_data['non_compliance_issues'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Corrective Actions Required / Hatua za Marekebisho Zinazohitajika</label>
                            <textarea name="form_data[corrective_actions]" class="form-control" rows="2">{{ old('form_data.corrective_actions', $farmerForm->form_data['corrective_actions'] ?? '') }}</textarea>
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
                        <strong>Form 5:</strong> Taarifa Ufiatiaji
                    </p>
                    <p class="small text-muted mb-0">
                        This form tracks compliance information including organic certification status, prohibited substance usage, buffer zones, and record keeping practices.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
