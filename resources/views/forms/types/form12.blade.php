@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] ?? 'Fomu ya Mafunzo' }} - Training Form</small>
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
                    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Training Form</h5>
                </div>
                <div class="card-body">
                    <!-- Training Info -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Season / Msimu</label>
                            <input type="text" name="season" class="form-control" value="{{ old('season', $farmerForm->season ?? '') }}" placeholder="e.g., 2024/2025">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Training Date / Tarehe ya Mafunzo</label>
                            <input type="date" name="form_date" class="form-control" value="{{ old('form_date', isset($farmerForm) ? $farmerForm->form_date?->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Training Duration (Hours) / Muda</label>
                            <input type="number" step="0.5" name="form_data[duration_hours]" class="form-control" value="{{ old('form_data.duration_hours', $farmerForm->form_data['duration_hours'] ?? '') }}">
                        </div>
                    </div>

                    <!-- Training Details -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-chalkboard-teacher me-2"></i>Training Details / Maelezo ya Mafunzo</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Training Title / Kichwa cha Mafunzo <span class="text-danger">*</span></label>
                                <input type="text" name="form_data[training_title]" class="form-control" value="{{ old('form_data.training_title', $farmerForm->form_data['training_title'] ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Training Type / Aina ya Mafunzo</label>
                                <select name="form_data[training_type]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="organic_farming" {{ old('form_data.training_type', $farmerForm->form_data['training_type'] ?? '') == 'organic_farming' ? 'selected' : '' }}>Organic Farming / Kilimo Hai</option>
                                    <option value="pest_management" {{ old('form_data.training_type', $farmerForm->form_data['training_type'] ?? '') == 'pest_management' ? 'selected' : '' }}>Pest Management / Udhibiti wa Wadudu</option>
                                    <option value="soil_management" {{ old('form_data.training_type', $farmerForm->form_data['training_type'] ?? '') == 'soil_management' ? 'selected' : '' }}>Soil Management / Usimamizi wa Udongo</option>
                                    <option value="composting" {{ old('form_data.training_type', $farmerForm->form_data['training_type'] ?? '') == 'composting' ? 'selected' : '' }}>Composting / Utengenezaji wa Mboji</option>
                                    <option value="harvest_processing" {{ old('form_data.training_type', $farmerForm->form_data['training_type'] ?? '') == 'harvest_processing' ? 'selected' : '' }}>Harvest & Processing / Uvunaji na Usindikaji</option>
                                    <option value="record_keeping" {{ old('form_data.training_type', $farmerForm->form_data['training_type'] ?? '') == 'record_keeping' ? 'selected' : '' }}>Record Keeping / Utunzaji wa Kumbukumbu</option>
                                    <option value="certification" {{ old('form_data.training_type', $farmerForm->form_data['training_type'] ?? '') == 'certification' ? 'selected' : '' }}>Certification Requirements / Mahitaji ya Uthibitisho</option>
                                    <option value="other" {{ old('form_data.training_type', $farmerForm->form_data['training_type'] ?? '') == 'other' ? 'selected' : '' }}>Other / Nyingine</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Trainer Name / Jina la Mkufunzi</label>
                                <input type="text" name="form_data[trainer_name]" class="form-control" value="{{ old('form_data.trainer_name', $farmerForm->form_data['trainer_name'] ?? auth()->user()->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Training Location / Mahali pa Mafunzo</label>
                                <input type="text" name="form_data[training_location]" class="form-control" value="{{ old('form_data.training_location', $farmerForm->form_data['training_location'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Training Description / Maelezo ya Mafunzo</label>
                            <textarea name="form_data[training_description]" class="form-control" rows="3">{{ old('form_data.training_description', $farmerForm->form_data['training_description'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Attendance -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-users me-2"></i>Attendance / Mahudhurio</h6>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Total Invited / Walioalikwa</label>
                                <input type="number" name="form_data[total_invited]" class="form-control" value="{{ old('form_data.total_invited', $farmerForm->form_data['total_invited'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Total Attended / Walihudhuria</label>
                                <input type="number" name="form_data[total_attended]" class="form-control" value="{{ old('form_data.total_attended', $farmerForm->form_data['total_attended'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Male Attendees / Wanaume</label>
                                <input type="number" name="form_data[male_attendees]" class="form-control" value="{{ old('form_data.male_attendees', $farmerForm->form_data['male_attendees'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Female Attendees / Wanawake</label>
                                <input type="number" name="form_data[female_attendees]" class="form-control" value="{{ old('form_data.female_attendees', $farmerForm->form_data['female_attendees'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Youth Attendees (Below 35) / Vijana</label>
                                <input type="number" name="form_data[youth_attendees]" class="form-control" value="{{ old('form_data.youth_attendees', $farmerForm->form_data['youth_attendees'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lead Farmers Present / Wakulima Viongozi</label>
                                <input type="number" name="form_data[lead_farmers_present]" class="form-control" value="{{ old('form_data.lead_farmers_present', $farmerForm->form_data['lead_farmers_present'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">New Farmers Present / Wakulima Wapya</label>
                                <input type="number" name="form_data[new_farmers_present]" class="form-control" value="{{ old('form_data.new_farmers_present', $farmerForm->form_data['new_farmers_present'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Farmer Groups Represented / Vikundi Vilivyowakilishwa</label>
                            <input type="text" name="form_data[groups_represented]" class="form-control" value="{{ old('form_data.groups_represented', $farmerForm->form_data['groups_represented'] ?? '') }}" placeholder="List farmer groups that attended">
                        </div>
                    </div>

                    <!-- Training Content -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-book me-2"></i>Training Content / Maudhui ya Mafunzo</h6>

                        <div class="mb-3">
                            <label class="form-label">Topics Covered / Mada Zilizofundishwa</label>
                            <div class="row">
                                @foreach(['organic_principles' => 'Organic Principles / Kanuni za Kilimo Hai', 'composting_techniques' => 'Composting Techniques / Mbinu za Mboji', 'pest_control' => 'Pest Control Methods / Njia za Kudhibiti Wadudu', 'soil_fertility' => 'Soil Fertility / Rutuba ya Udongo', 'crop_rotation' => 'Crop Rotation / Mzunguko wa Mazao', 'record_keeping' => 'Record Keeping / Utunzaji wa Kumbukumbu', 'certification_requirements' => 'Certification Requirements / Mahitaji ya Cheti', 'quality_standards' => 'Quality Standards / Viwango vya Ubora'] as $value => $label)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="form_data[topics_covered][]" value="{{ $value }}" id="topic_{{ $value }}"
                                                {{ in_array($value, old('form_data.topics_covered', $farmerForm->form_data['topics_covered'] ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="topic_{{ $value }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Training Materials Used / Vifaa vya Mafunzo</label>
                                <input type="text" name="form_data[materials_used]" class="form-control" value="{{ old('form_data.materials_used', $farmerForm->form_data['materials_used'] ?? '') }}" placeholder="e.g., Manuals, Posters, Videos">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Practical Demonstrations / Maonyesho ya Vitendo</label>
                                <select name="form_data[practical_demos]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.practical_demos', $farmerForm->form_data['practical_demos'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.practical_demos', $farmerForm->form_data['practical_demos'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluation -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-star me-2"></i>Evaluation / Tathmini</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Knowledge Assessment Conducted / Tathmini ya Maarifa</label>
                                <select name="form_data[assessment_conducted]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('form_data.assessment_conducted', $farmerForm->form_data['assessment_conducted'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                    <option value="no" {{ old('form_data.assessment_conducted', $farmerForm->form_data['assessment_conducted'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Average Score (%) / Alama ya Wastani</label>
                                <input type="number" name="form_data[average_score]" class="form-control" min="0" max="100" value="{{ old('form_data.average_score', $farmerForm->form_data['average_score'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Participant Feedback Rating (1-5) / Maoni ya Washiriki</label>
                                <input type="number" name="form_data[feedback_rating]" class="form-control" min="1" max="5" value="{{ old('form_data.feedback_rating', $farmerForm->form_data['feedback_rating'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Certificates Issued / Vyeti Vilivyotolewa</label>
                                <input type="number" name="form_data[certificates_issued]" class="form-control" value="{{ old('form_data.certificates_issued', $farmerForm->form_data['certificates_issued'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Key Feedback from Participants / Maoni Muhimu kutoka kwa Washiriki</label>
                            <textarea name="form_data[participant_feedback]" class="form-control" rows="3">{{ old('form_data.participant_feedback', $farmerForm->form_data['participant_feedback'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Follow-up -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-forward me-2"></i>Follow-up / Ufuatiliaji</h6>

                        <div class="mb-3">
                            <label class="form-label">Follow-up Actions Required / Hatua za Ufuatiliaji</label>
                            <textarea name="form_data[followup_actions]" class="form-control" rows="2">{{ old('form_data.followup_actions', $farmerForm->form_data['followup_actions'] ?? '') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Next Training Date / Tarehe ya Mafunzo Ijayo</label>
                                <input type="date" name="form_data[next_training_date]" class="form-control" value="{{ old('form_data.next_training_date', $farmerForm->form_data['next_training_date'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Next Training Topic / Mada ya Mafunzo Ijayo</label>
                                <input type="text" name="form_data[next_training_topic]" class="form-control" value="{{ old('form_data.next_training_topic', $farmerForm->form_data['next_training_topic'] ?? '') }}">
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
                        <strong>Form 12:</strong> Fomu ya Mafunzo
                    </p>
                    <p class="small text-muted mb-0">
                        This form records training sessions including attendance, topics covered, evaluation results, and follow-up actions.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
