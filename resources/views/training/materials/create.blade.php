@extends('layouts.base')

@section('title', 'Upload Training Material')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-upload me-2"></i> Upload Training Material
                        </h4>
                        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('training.materials.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-info-circle me-2"></i> Material Information
                                </h5>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Title (English) *</label>
                                    <input type="text"
                                           class="form-control @error('title') is-invalid @enderror"
                                           id="title"
                                           name="title"
                                           value="{{ old('title') }}"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="title_sw" class="form-label">Title (Swahili)</label>
                                    <input type="text"
                                           class="form-control @error('title_sw') is-invalid @enderror"
                                           id="title_sw"
                                           name="title_sw"
                                           value="{{ old('title_sw') }}">
                                    @error('title_sw')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Type *</label>
                                            <select class="form-select @error('type') is-invalid @enderror"
                                                    id="type"
                                                    name="type"
                                                    required>
                                                <option value="">Select Type</option>
                                                <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>Document</option>
                                                <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                                                <option value="presentation" {{ old('type') == 'presentation' ? 'selected' : '' }}>Presentation</option>
                                                <option value="handout" {{ old('type') == 'handout' ? 'selected' : '' }}>Handout</option>
                                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="language" class="form-label">Language</label>
                                            <select class="form-select @error('language') is-invalid @enderror"
                                                    id="language"
                                                    name="language">
                                                <option value="sw" {{ old('language', 'sw') == 'sw' ? 'selected' : '' }}>Swahili</option>
                                                <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                                            </select>
                                            @error('language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-link me-2"></i> Association & File
                                </h5>

                                <div class="mb-3">
                                    <label for="training_program_id" class="form-label">Training Program</label>
                                    <select class="form-select @error('training_program_id') is-invalid @enderror"
                                            id="training_program_id"
                                            name="training_program_id">
                                        <option value="">Select Program (Optional)</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ old('training_program_id', request('program_id')) == $program->id ? 'selected' : '' }}>
                                                {{ $program->code }} - {{ $program->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('training_program_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="training_session_id" class="form-label">Training Session</label>
                                    <select class="form-select @error('training_session_id') is-invalid @enderror"
                                            id="training_session_id"
                                            name="training_session_id">
                                        <option value="">Select Session (Optional)</option>
                                        @foreach($sessions as $session)
                                            <option value="{{ $session->id }}" {{ old('training_session_id') == $session->id ? 'selected' : '' }}>
                                                {{ $session->title }} ({{ $session->scheduled_date->format('M d, Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('training_session_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-3">

                                <div class="mb-3">
                                    <label for="file" class="form-label">Upload File</label>
                                    <input type="file"
                                           class="form-control @error('file') is-invalid @enderror"
                                           id="file"
                                           name="file">
                                    <small class="text-muted">Max file size: 50MB</small>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="external_url" class="form-label">Or External URL</label>
                                    <input type="url"
                                           class="form-control @error('external_url') is-invalid @enderror"
                                           id="external_url"
                                           name="external_url"
                                           value="{{ old('external_url') }}"
                                           placeholder="https://...">
                                    <small class="text-muted">Link to YouTube, Google Drive, etc.</small>
                                    @error('external_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               role="switch"
                                               id="is_downloadable"
                                               name="is_downloadable"
                                               value="1"
                                               {{ old('is_downloadable', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_downloadable">
                                            Allow Download
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-1"></i> Upload Material
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
