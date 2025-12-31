@extends('layouts.base')

@section('title', 'Edit Training Program: ' . $program->name)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Training Program: {{ $program->name }}
                        </h4>
                        <a href="{{ route('training-programs.show', $program) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Program
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('training-programs.update', $program) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-info-circle me-2"></i> Basic Information
                                </h5>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Program Name (English) *</label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $program->name) }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_sw" class="form-label">Jina la Programu (Swahili)</label>
                                    <input type="text"
                                           class="form-control @error('name_sw') is-invalid @enderror"
                                           id="name_sw"
                                           name="name_sw"
                                           value="{{ old('name_sw', $program->name_sw) }}">
                                    @error('name_sw')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="code" class="form-label">Program Code *</label>
                                    <input type="text"
                                           class="form-control @error('code') is-invalid @enderror"
                                           id="code"
                                           name="code"
                                           value="{{ old('code', $program->code) }}"
                                           required>
                                    <small class="text-muted">Unique identifier for the program</small>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select @error('category') is-invalid @enderror"
                                            id="category"
                                            name="category">
                                        <option value="">Select Category</option>
                                        <option value="organic_farming" {{ (old('category', $program->category) == 'organic_farming') ? 'selected' : '' }}>Organic Farming</option>
                                        <option value="pest_management" {{ (old('category', $program->category) == 'pest_management') ? 'selected' : '' }}>Pest Management</option>
                                        <option value="harvest" {{ (old('category', $program->category) == 'harvest') ? 'selected' : '' }}>Harvest Techniques</option>
                                        <option value="soil_management" {{ (old('category', $program->category) == 'soil_management') ? 'selected' : '' }}>Soil Management</option>
                                        <option value="water_management" {{ (old('category', $program->category) == 'water_management') ? 'selected' : '' }}>Water Management</option>
                                        <option value="business_skills" {{ (old('category', $program->category) == 'business_skills') ? 'selected' : '' }}>Business Skills</option>
                                        <option value="certification" {{ (old('category', $program->category) == 'certification') ? 'selected' : '' }}>Certification</option>
                                        <option value="other" {{ (old('category', $program->category) == 'other') ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="duration_hours" class="form-label">Duration (hours)</label>
                                    <input type="number"
                                           class="form-control @error('duration_hours') is-invalid @enderror"
                                           id="duration_hours"
                                           name="duration_hours"
                                           value="{{ old('duration_hours', $program->duration_hours) }}"
                                           min="1"
                                           step="1">
                                    @error('duration_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-cog me-2"></i> Settings & Details
                                </h5>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="3">{{ old('description', $program->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="objectives" class="form-label">Objectives</label>
                                    <textarea class="form-control @error('objectives') is-invalid @enderror"
                                              id="objectives"
                                              name="objectives"
                                              rows="3">{{ old('objectives', $program->objectives) }}</textarea>
                                    <small class="text-muted">List the main objectives of this training program</small>
                                    @error('objectives')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input @error('is_mandatory') is-invalid @enderror"
                                               type="checkbox"
                                               role="switch"
                                               id="is_mandatory"
                                               name="is_mandatory"
                                               value="1"
                                               {{ old('is_mandatory', $program->is_mandatory) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_mandatory">
                                            Mandatory Training
                                        </label>
                                        @error('is_mandatory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">If checked, this training is required for farmers</small>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input @error('is_active') is-invalid @enderror"
                                               type="checkbox"
                                               role="switch"
                                               id="is_active"
                                               name="is_active"
                                               value="1"
                                               {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active Program
                                        </label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Inactive programs will not appear for new sessions</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('training-programs.show', $program) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update Program
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