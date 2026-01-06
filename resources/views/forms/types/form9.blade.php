@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] ?? 'Orodha ya Ukaguzi wa Ndani' }} - Internal Inspection Report Checklist</small>
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
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Internal Inspection Report Checklist</h5>
                </div>
                <div class="card-body">
                    <!-- Inspection Info -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Season / Msimu</label>
                            <input type="text" name="season" class="form-control" value="{{ old('season', $farmerForm->season ?? '') }}" placeholder="e.g., 2024/2025">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Inspection Date / Tarehe ya Ukaguzi</label>
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

                    <!-- General Information Checklist -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>General Information / Taarifa za Jumla</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Farmer Registration Verified / Usajili Umethibitishwa</label>
                                <select name="form_data[registration_verified]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.registration_verified', $farmerForm->form_data['registration_verified'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.registration_verified', $farmerForm->form_data['registration_verified'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Farm Maps Available / Ramani za Shamba Zinapatikana</label>
                                <select name="form_data[farm_maps_available]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.farm_maps_available', $farmerForm->form_data['farm_maps_available'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.farm_maps_available', $farmerForm->form_data['farm_maps_available'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Farm Size Verified / Ukubwa wa Shamba Umethibitishwa</label>
                                <select name="form_data[farm_size_verified]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.farm_size_verified', $farmerForm->form_data['farm_size_verified'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.farm_size_verified', $farmerForm->form_data['farm_size_verified'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Verified Area (Acres) / Eneo Lililothibitishwa</label>
                                <input type="number" step="0.01" name="form_data[verified_area]" class="form-control" value="{{ old('form_data.verified_area', $farmerForm->form_data['verified_area'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Organic Standards Checklist -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-leaf me-2"></i>Organic Standards / Viwango vya Kilimo Hai</h6>

                        @php
                            $organicChecks = [
                                'no_synthetic_fertilizers' => 'No Synthetic Fertilizers / Hakuna Mbolea ya Viwandani',
                                'no_synthetic_pesticides' => 'No Synthetic Pesticides / Hakuna Dawa za Viwandani',
                                'no_gmo_seeds' => 'No GMO Seeds / Hakuna Mbegu za GMO',
                                'organic_seeds_used' => 'Organic Seeds Used / Mbegu za Kilimo Hai Zinatumika',
                                'proper_composting' => 'Proper Composting / Utengenezaji Sahihi wa Mboji',
                                'biological_pest_control' => 'Biological Pest Control / Udhibiti wa Kiasili wa Wadudu',
                            ];
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Criteria / Kigezo</th>
                                        <th width="120">Status / Hali</th>
                                        <th width="200">Comments / Maoni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($organicChecks as $key => $label)
                                        <tr>
                                            <td>{{ $label }}</td>
                                            <td>
                                                <select name="form_data[organic_{{ $key }}]" class="form-select form-select-sm">
                                                    <option value="">-</option>
                                                    <option value="pass" {{ old("form_data.organic_{$key}", $farmerForm->form_data["organic_{$key}"] ?? '') == 'pass' ? 'selected' : '' }}>Pass</option>
                                                    <option value="fail" {{ old("form_data.organic_{$key}", $farmerForm->form_data["organic_{$key}"] ?? '') == 'fail' ? 'selected' : '' }}>Fail</option>
                                                    <option value="na" {{ old("form_data.organic_{$key}", $farmerForm->form_data["organic_{$key}"] ?? '') == 'na' ? 'selected' : '' }}>N/A</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="form_data[organic_{{ $key }}_comment]" class="form-control form-control-sm" value="{{ old("form_data.organic_{$key}_comment", $farmerForm->form_data["organic_{$key}_comment"] ?? '') }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Buffer Zone Checklist -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-border-all me-2"></i>Buffer Zone / Eneo la Kinga</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Buffer Zone Present / Kuna Eneo la Kinga</label>
                                <select name="form_data[buffer_zone_present]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.buffer_zone_present', $farmerForm->form_data['buffer_zone_present'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.buffer_zone_present', $farmerForm->form_data['buffer_zone_present'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                    <option value="na" {{ old('form_data.buffer_zone_present', $farmerForm->form_data['buffer_zone_present'] ?? '') == 'na' ? 'selected' : '' }}>N/A</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Buffer Width Adequate / Upana Unatosha</label>
                                <select name="form_data[buffer_width_adequate]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.buffer_width_adequate', $farmerForm->form_data['buffer_width_adequate'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.buffer_width_adequate', $farmerForm->form_data['buffer_width_adequate'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Buffer Width (m) / Upana</label>
                                <input type="number" step="0.1" name="form_data[buffer_width_measured]" class="form-control" value="{{ old('form_data.buffer_width_measured', $farmerForm->form_data['buffer_width_measured'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Record Keeping Checklist -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-file-alt me-2"></i>Record Keeping / Utunzaji wa Kumbukumbu</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Input Records Available / Kumbukumbu za Pembejeo</label>
                                <select name="form_data[input_records]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.input_records', $farmerForm->form_data['input_records'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.input_records', $farmerForm->form_data['input_records'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Harvest Records Available / Kumbukumbu za Mavuno</label>
                                <select name="form_data[harvest_records]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.harvest_records', $farmerForm->form_data['harvest_records'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.harvest_records', $farmerForm->form_data['harvest_records'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sales Records Available / Kumbukumbu za Mauzo</label>
                                <select name="form_data[sales_records]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.sales_records', $farmerForm->form_data['sales_records'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.sales_records', $farmerForm->form_data['sales_records'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Non-Conformities -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Non-Conformities / Ukiukaji</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Non-Conformities Found / Umepata Ukiukaji</label>
                                <select name="form_data[non_conformities_found]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.non_conformities_found', $farmerForm->form_data['non_conformities_found'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.non_conformities_found', $farmerForm->form_data['non_conformities_found'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Severity Level / Kiwango cha Ukali</label>
                                <select name="form_data[severity_level]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="minor" {{ old('form_data.severity_level', $farmerForm->form_data['severity_level'] ?? '') == 'minor' ? 'selected' : '' }}>Minor / Ndogo</option>
                                    <option value="major" {{ old('form_data.severity_level', $farmerForm->form_data['severity_level'] ?? '') == 'major' ? 'selected' : '' }}>Major / Kubwa</option>
                                    <option value="critical" {{ old('form_data.severity_level', $farmerForm->form_data['severity_level'] ?? '') == 'critical' ? 'selected' : '' }}>Critical / Hatari</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Non-Conformity Details / Maelezo ya Ukiukaji</label>
                            <textarea name="form_data[non_conformity_details]" class="form-control" rows="3">{{ old('form_data.non_conformity_details', $farmerForm->form_data['non_conformity_details'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Corrective Actions Required / Hatua za Marekebisho</label>
                            <textarea name="form_data[corrective_actions]" class="form-control" rows="3">{{ old('form_data.corrective_actions', $farmerForm->form_data['corrective_actions'] ?? '') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Correction Deadline / Tarehe ya Mwisho</label>
                                <input type="date" name="form_data[correction_deadline]" class="form-control" value="{{ old('form_data.correction_deadline', $farmerForm->form_data['correction_deadline'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Inspection Result -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-clipboard-check me-2"></i>Inspection Result / Matokeo ya Ukaguzi</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Overall Result / Matokeo ya Jumla</label>
                                <select name="form_data[overall_result]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="approved" {{ old('form_data.overall_result', $farmerForm->form_data['overall_result'] ?? '') == 'approved' ? 'selected' : '' }}>Approved / Imekubaliwa</option>
                                    <option value="conditional" {{ old('form_data.overall_result', $farmerForm->form_data['overall_result'] ?? '') == 'conditional' ? 'selected' : '' }}>Conditional Approval / Kukubaliwa kwa Masharti</option>
                                    <option value="suspended" {{ old('form_data.overall_result', $farmerForm->form_data['overall_result'] ?? '') == 'suspended' ? 'selected' : '' }}>Suspended / Imesimamishwa</option>
                                    <option value="rejected" {{ old('form_data.overall_result', $farmerForm->form_data['overall_result'] ?? '') == 'rejected' ? 'selected' : '' }}>Rejected / Imekataliwa</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Next Inspection Date / Tarehe ya Ukaguzi Ujao</label>
                                <input type="date" name="form_data[next_inspection_date]" class="form-control" value="{{ old('form_data.next_inspection_date', $farmerForm->form_data['next_inspection_date'] ?? '') }}">
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
                        <strong>Form 9:</strong> Orodha ya Ukaguzi wa Ndani
                    </p>
                    <p class="small text-muted mb-0">
                        This internal inspection checklist is used to verify organic standards compliance, buffer zones, record keeping, and identify non-conformities.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
