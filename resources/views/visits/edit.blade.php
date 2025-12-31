@extends('layouts.base')

@section('title', 'Edit Farm Visit')

@push('styles')
<style>
    .form-section {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .required:after {
        content: ' *';
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-edit me-2"></i> Edit Farm Visit: {{ $visit->visit_number }}
                        </h4>
                        <a href="{{ route('visits.show', $visit) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Details
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('visits.update', $visit) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="form-section">
                            <h5 class="mb-3 border-bottom pb-2">
                                <i class="fas fa-info-circle me-2"></i> Basic Information
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="visit_number" class="form-label required">Visit Number</label>
                                    <input type="text" 
                                           class="form-control @error('visit_number') is-invalid @enderror" 
                                           id="visit_number" 
                                           name="visit_number" 
                                           value="{{ old('visit_number', $visit->visit_number) }}" 
                                           placeholder="e.g., V-2025-001" 
                                           required>
                                    @error('visit_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="purpose" class="form-label required">Purpose</label>
                                    <select class="form-select @error('purpose') is-invalid @enderror" 
                                            id="purpose" 
                                            name="purpose" 
                                            required>
                                        <option value="">Select Purpose</option>
                                        <option value="inspection" {{ old('purpose', $visit->purpose) == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                        <option value="training" {{ old('purpose', $visit->purpose) == 'training' ? 'selected' : '' }}>Training</option>
                                        <option value="support" {{ old('purpose', $visit->purpose) == 'support' ? 'selected' : '' }}>Support</option>
                                        <option value="monitoring" {{ old('purpose', $visit->purpose) == 'monitoring' ? 'selected' : '' }}>Monitoring</option>
                                        <option value="other" {{ old('purpose', $visit->purpose) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('purpose')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="scheduled_date" class="form-label required">Scheduled Date & Time</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('scheduled_date') is-invalid @enderror" 
                                           id="scheduled_date" 
                                           name="scheduled_date" 
                                           value="{{ old('scheduled_date', $visit->scheduled_date->format('Y-m-d\TH:i')) }}" 
                                           required>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label required">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="scheduled" {{ old('status', $visit->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="in_progress" {{ old('status', $visit->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status', $visit->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status', $visit->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="actual_date" class="form-label">Actual Date & Time</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('actual_date') is-invalid @enderror" 
                                           id="actual_date" 
                                           name="actual_date" 
                                           value="{{ old('actual_date', $visit->actual_date ? $visit->actual_date->format('Y-m-d\TH:i') : '') }}">
                                    <small class="text-muted">Leave empty if not yet visited</small>
                                    @error('actual_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                                    <input type="number" 
                                           class="form-control @error('duration_minutes') is-invalid @enderror" 
                                           id="duration_minutes" 
                                           name="duration_minutes" 
                                           value="{{ old('duration_minutes', $visit->duration_minutes) }}" 
                                           min="0">
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3">{{ old('notes', $visit->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Location & Associated Entities -->
                        <div class="form-section">
                            <h5 class="mb-3 border-bottom pb-2">
                                <i class="fas fa-map-marker-alt me-2"></i> Location & Associated Entities
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="farm_id" class="form-label">Farm</label>
                                    <select class="form-select @error('farm_id') is-invalid @enderror" 
                                            id="farm_id" 
                                            name="farm_id">
                                        <option value="">Select Farm (Optional)</option>
                                        @foreach($farms as $farm)
                                            <option value="{{ $farm->id }}" {{ old('farm_id', $visit->farm_id) == $farm->id ? 'selected' : '' }}>
                                                {{ $farm->code }} - {{ $farm->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('farm_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="field_id" class="form-label">Field</label>
                                    <select class="form-select @error('field_id') is-invalid @enderror" 
                                            id="field_id" 
                                            name="field_id">
                                        <option value="">Select Field (Optional)</option>
                                        @foreach($fields as $field)
                                            <option value="{{ $field->id }}" {{ old('field_id', $visit->field_id) == $field->id ? 'selected' : '' }}>
                                                {{ $field->name }} ({{ $field->farm->code ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('field_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="farmer_id" class="form-label">Farmer</label>
                                    <select class="form-select @error('farmer_id') is-invalid @enderror" 
                                            id="farmer_id" 
                                            name="farmer_id">
                                        <option value="">Select Farmer (Optional)</option>
                                        @foreach($farmers as $farmer)
                                            <option value="{{ $farmer->id }}" {{ old('farmer_id', $visit->farmer_id) == $farmer->id ? 'selected' : '' }}>
                                                {{ $farmer->registration_number }} - {{ $farmer->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('farmer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="supervisor_id" class="form-label">Supervisor</label>
                                    <select class="form-select @error('supervisor_id') is-invalid @enderror" 
                                            id="supervisor_id" 
                                            name="supervisor_id">
                                        <option value="">Select Supervisor (Optional)</option>
                                        @foreach($supervisors as $supervisor)
                                            <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $visit->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                                {{ $supervisor->name }} ({{ $supervisor->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supervisor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="region_id" class="form-label">Region</label>
                                    <select class="form-select @error('region_id') is-invalid @enderror" 
                                            id="region_id" 
                                            name="region_id">
                                        <option value="">Select Region (Optional)</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}" {{ old('region_id', $visit->region_id) == $region->id ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="district_id" class="form-label">District</label>
                                    <select class="form-select @error('district_id') is-invalid @enderror" 
                                            id="district_id" 
                                            name="district_id">
                                        <option value="">Select District (Optional)</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}" {{ old('district_id', $visit->district_id) == $district->id ? 'selected' : '' }}>
                                                {{ $district->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('district_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="village_id" class="form-label">Village</label>
                                    <select class="form-select @error('village_id') is-invalid @enderror" 
                                            id="village_id" 
                                            name="village_id">
                                        <option value="">Select Village (Optional)</option>
                                        @foreach($villages as $village)
                                            <option value="{{ $village->id }}" {{ old('village_id', $visit->village_id) == $village->id ? 'selected' : '' }}>
                                                {{ $village->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('village_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" 
                                           step="any" 
                                           class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" 
                                           name="latitude" 
                                           value="{{ old('latitude', $visit->latitude) }}" 
                                           placeholder="e.g., -6.123456">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" 
                                           step="any" 
                                           class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" 
                                           name="longitude" 
                                           value="{{ old('longitude', $visit->longitude) }}" 
                                           placeholder="e.g., 35.123456">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Report & Photos -->
                        <div class="form-section">
                            <h5 class="mb-3 border-bottom pb-2">
                                <i class="fas fa-sticky-note me-2"></i> Report & Photos
                            </h5>

                            <div class="mb-3">
                                <label for="report" class="form-label">Report</label>
                                <textarea class="form-control @error('report') is-invalid @enderror" 
                                          id="report" 
                                          name="report" 
                                          rows="4">{{ old('report', $visit->report) }}</textarea>
                                @error('report')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="photos" class="form-label">Upload Additional Photos</label>
                                <input type="file" 
                                       class="form-control @error('photos') is-invalid @enderror" 
                                       id="photos" 
                                       name="photos[]" 
                                       multiple 
                                       accept="image/*">
                                <small class="text-muted">You can select multiple images (max 10). Existing photos will be preserved.</small>
                                @error('photos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-check-label">
                                    <input type="checkbox" 
                                           class="form-check-input @error('has_photos') is-invalid @enderror" 
                                           id="has_photos" 
                                           name="has_photos" 
                                           value="1" 
                                           {{ old('has_photos', $visit->has_photos) ? 'checked' : '' }}>
                                    Mark as having photos
                                </label>
                                @error('has_photos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Display existing photos -->
                            @if($visit->has_photos && count($visit->photos) > 0)
                                <div class="mt-3">
                                    <label class="form-label">Existing Photos</label>
                                    <div class="row g-2">
                                        @foreach($visit->photos as $photo)
                                            <div class="col-3">
                                                <div class="position-relative">
                                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $photo) }}" class="img-thumbnail" alt="Photo">
                                                    </a>
                                                    <div class="position-absolute top-0 end-0 p-1">
                                                        <input type="checkbox" name="delete_photos[]" value="{{ $photo }}" id="delete_photo_{{ $loop->index }}">
                                                        <label class="form-check-label text-danger" for="delete_photo_{{ $loop->index }}">
                                                            <i class="fas fa-trash"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                                <small class="d-block text-center">Delete?</small>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('visits.show', $visit) }}" class="btn btn-secondary">
                                <i class="fas fa-times-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i> Update Visit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection