@extends('layouts.base')

@section('title', 'Edit Training Session')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Training Session
                        </h4>
                        <a href="{{ route('training.show', $session) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Session
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('training.update', $session) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Left Column --}}
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-info-circle me-2"></i> Session Information
                                </h5>

                                <div class="mb-3">
                                    <label for="training_program_id" class="form-label">Training Program *</label>
                                    <select class="form-select @error('training_program_id') is-invalid @enderror"
                                            id="training_program_id"
                                            name="training_program_id"
                                            required>
                                        <option value="">Select Program</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ old('training_program_id', $session->training_program_id) == $program->id ? 'selected' : '' }}>
                                                {{ $program->code }} - {{ $program->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('training_program_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Session Title *</label>
                                    <input type="text"
                                           class="form-control @error('title') is-invalid @enderror"
                                           id="title"
                                           name="title"
                                           value="{{ old('title', $session->title) }}"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="3">{{ old('description', $session->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="scheduled_date" class="form-label">Scheduled Date & Time *</label>
                                            <input type="datetime-local"
                                                   class="form-control @error('scheduled_date') is-invalid @enderror"
                                                   id="scheduled_date"
                                                   name="scheduled_date"
                                                   value="{{ old('scheduled_date', $session->scheduled_date->format('Y-m-d\TH:i')) }}"
                                                   required>
                                            @error('scheduled_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="duration_hours" class="form-label">Duration (hours)</label>
                                            <input type="number"
                                                   class="form-control @error('duration_hours') is-invalid @enderror"
                                                   id="duration_hours"
                                                   name="duration_hours"
                                                   value="{{ old('duration_hours', $session->duration_hours) }}"
                                                   min="1"
                                                   step="0.5">
                                            @error('duration_hours')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="max_participants" class="form-label">Maximum Participants</label>
                                    <input type="number"
                                           class="form-control @error('max_participants') is-invalid @enderror"
                                           id="max_participants"
                                           name="max_participants"
                                           value="{{ old('max_participants', $session->max_participants) }}"
                                           min="1">
                                    <small class="text-muted">Leave empty for unlimited capacity. Currently registered: {{ $session->registered_count }}</small>
                                    @error('max_participants')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status"
                                            name="status">
                                        <option value="scheduled" {{ old('status', $session->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="in_progress" {{ old('status', $session->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status', $session->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="postponed" {{ old('status', $session->status) == 'postponed' ? 'selected' : '' }}>Postponed</option>
                                        <option value="cancelled" {{ old('status', $session->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(isset($seasons) && count($seasons) > 0)
                                <div class="mb-3">
                                    <label for="season_id" class="form-label">Season</label>
                                    <select class="form-select @error('season_id') is-invalid @enderror"
                                            id="season_id"
                                            name="season_id">
                                        <option value="">Select Season (Optional)</option>
                                        @foreach($seasons as $season)
                                            <option value="{{ $season->id }}" {{ old('season_id', $session->season_id) == $season->id ? 'selected' : '' }}>
                                                {{ $season->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('season_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif
                            </div>

                            {{-- Right Column --}}
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i> Location & Trainer
                                </h5>

                                <div class="mb-3">
                                    <label for="venue" class="form-label">Venue *</label>
                                    <input type="text"
                                           class="form-control @error('venue') is-invalid @enderror"
                                           id="venue"
                                           name="venue"
                                           value="{{ old('venue', $session->venue) }}"
                                           required>
                                    @error('venue')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(isset($regions) && count($regions) > 0)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="region_id" class="form-label">Region</label>
                                            <select class="form-select @error('region_id') is-invalid @enderror"
                                                    id="region_id"
                                                    name="region_id">
                                                <option value="">Select Region</option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}" {{ old('region_id', $session->region_id) == $region->id ? 'selected' : '' }}>
                                                        {{ $region->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('region_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="district_id" class="form-label">District</label>
                                            <select class="form-select @error('district_id') is-invalid @enderror"
                                                    id="district_id"
                                                    name="district_id">
                                                <option value="">Select District</option>
                                                @if(isset($districts))
                                                    @foreach($districts as $district)
                                                        <option value="{{ $district->id }}" {{ old('district_id', $session->district_id) == $district->id ? 'selected' : '' }}>
                                                            {{ $district->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('district_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="latitude" class="form-label">Latitude</label>
                                            <input type="text"
                                                   class="form-control @error('latitude') is-invalid @enderror"
                                                   id="latitude"
                                                   name="latitude"
                                                   value="{{ old('latitude', $session->latitude) }}"
                                                   placeholder="e.g., -6.1629">
                                            @error('latitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="longitude" class="form-label">Longitude</label>
                                            <input type="text"
                                                   class="form-control @error('longitude') is-invalid @enderror"
                                                   id="longitude"
                                                   name="longitude"
                                                   value="{{ old('longitude', $session->longitude) }}"
                                                   placeholder="e.g., 35.7516">
                                            @error('longitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <h6 class="mb-3"><i class="fas fa-user-tie me-2"></i> Trainer Information</h6>

                                @if(isset($trainers) && count($trainers) > 0)
                                <div class="mb-3">
                                    <label for="trainer_id" class="form-label">Select Trainer (System User)</label>
                                    <select class="form-select @error('trainer_id') is-invalid @enderror"
                                            id="trainer_id"
                                            name="trainer_id">
                                        <option value="">Select Trainer</option>
                                        @foreach($trainers as $trainer)
                                            <option value="{{ $trainer->id }}" {{ old('trainer_id', $session->trainer_id) == $trainer->id ? 'selected' : '' }}>
                                                {{ $trainer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('trainer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label for="trainer_name" class="form-label">Trainer Name (External)</label>
                                    <input type="text"
                                           class="form-control @error('trainer_name') is-invalid @enderror"
                                           id="trainer_name"
                                           name="trainer_name"
                                           value="{{ old('trainer_name', $session->trainer_name) }}">
                                    <small class="text-muted">Use if trainer is not a system user</small>
                                    @error('trainer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="trainer_organization" class="form-label">Organization</label>
                                            <input type="text"
                                                   class="form-control @error('trainer_organization') is-invalid @enderror"
                                                   id="trainer_organization"
                                                   name="trainer_organization"
                                                   value="{{ old('trainer_organization', $session->trainer_organization) }}">
                                            @error('trainer_organization')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="trainer_contact" class="form-label">Contact</label>
                                            <input type="text"
                                                   class="form-control @error('trainer_contact') is-invalid @enderror"
                                                   id="trainer_contact"
                                                   name="trainer_contact"
                                                   value="{{ old('trainer_contact', $session->trainer_contact) }}">
                                            @error('trainer_contact')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('training.show', $session) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i> Update Session
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
