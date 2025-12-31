@extends('layouts.base')

@section('title', 'Material - ' . $material->title)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-file me-2"></i> Training Material
                        </h4>
                        <div>
                            <a href="{{ route('training.materials.edit', $material) }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            @if($material->program)
                                <a href="{{ route('training.materials.program', $material->program) }}" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            @else
                                <a href="{{ route('training-programs.index') }}" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            {{-- Material Info --}}
                            <div class="d-flex align-items-start mb-4">
                                <div class="me-4">
                                    @switch($material->type)
                                        @case('document')
                                            <i class="fas fa-file-alt fa-4x text-primary"></i>
                                            @break
                                        @case('video')
                                            <i class="fas fa-video fa-4x text-danger"></i>
                                            @break
                                        @case('presentation')
                                            <i class="fas fa-file-powerpoint fa-4x text-warning"></i>
                                            @break
                                        @case('handout')
                                            <i class="fas fa-file-pdf fa-4x text-danger"></i>
                                            @break
                                        @default
                                            <i class="fas fa-file fa-4x text-secondary"></i>
                                    @endswitch
                                </div>
                                <div>
                                    <h3 class="mb-1">{{ $material->title }}</h3>
                                    @if($material->title_sw)
                                        <p class="text-muted mb-2">{{ $material->title_sw }}</p>
                                    @endif
                                    <div class="mb-2">
                                        <span class="badge bg-secondary me-2">{{ $material->type_label }}</span>
                                        <span class="badge bg-info me-2">{{ $material->language_label }}</span>
                                        @if($material->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($material->description)
                                <div class="mb-4">
                                    <h6>Description</h6>
                                    <p class="text-muted">{{ $material->description }}</p>
                                </div>
                            @endif

                            {{-- Associated Program/Session --}}
                            <div class="mb-4">
                                <h6>Associated With</h6>
                                <table class="table table-borderless table-sm">
                                    @if($material->program)
                                        <tr>
                                            <th width="30%">Program:</th>
                                            <td>
                                                <a href="{{ route('training-programs.show', $material->program) }}">
                                                    {{ $material->program->name }}
                                                </a>
                                                <span class="badge bg-info ms-2">{{ $material->program->code }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($material->session)
                                        <tr>
                                            <th>Session:</th>
                                            <td>
                                                <a href="{{ route('training.sessions.show', $material->session) }}">
                                                    {{ $material->session->title }}
                                                </a>
                                                <small class="text-muted ms-2">{{ $material->session->scheduled_date->format('M d, Y') }}</small>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            {{-- External Link --}}
                            @if($material->external_url)
                                <div class="alert alert-info">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    <strong>External Link:</strong>
                                    <a href="{{ $material->external_url }}" target="_blank">{{ $material->external_url }}</a>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            {{-- File Details --}}
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">File Details</h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        @if($material->file_path)
                                            <tr>
                                                <th>Size:</th>
                                                <td>{{ $material->file_size_formatted }}</td>
                                            </tr>
                                            <tr>
                                                <th>Type:</th>
                                                <td>{{ $material->file_type ?? 'Unknown' }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Downloads:</th>
                                            <td><span class="badge bg-info">{{ $material->download_count }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Downloadable:</th>
                                            <td>
                                                @if($material->is_downloadable)
                                                    <span class="text-success"><i class="fas fa-check"></i> Yes</span>
                                                @else
                                                    <span class="text-danger"><i class="fas fa-times"></i> No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Uploaded:</th>
                                            <td>{{ $material->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @if($material->uploadedBy)
                                            <tr>
                                                <th>By:</th>
                                                <td>{{ $material->uploadedBy->name }}</td>
                                            </tr>
                                        @endif
                                    </table>

                                    @if($material->file_path && $material->is_downloadable)
                                        <hr>
                                        <a href="{{ Storage::url($material->file_path) }}"
                                           class="btn btn-success w-100"
                                           download>
                                            <i class="fas fa-download me-2"></i> Download File
                                        </a>
                                    @elseif($material->external_url)
                                        <hr>
                                        <a href="{{ $material->external_url }}"
                                           class="btn btn-primary w-100"
                                           target="_blank">
                                            <i class="fas fa-external-link-alt me-2"></i> Open Link
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6 class="card-title">Actions</h6>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('training.materials.edit', $material) }}" class="btn btn-outline-warning">
                                            <i class="fas fa-edit me-2"></i> Edit Material
                                        </a>
                                        <form action="{{ route('training.materials.destroy', $material) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this material?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100">
                                                <i class="fas fa-trash me-2"></i> Delete Material
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
