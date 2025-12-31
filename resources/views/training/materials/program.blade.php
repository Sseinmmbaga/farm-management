@extends('layouts.base')

@section('title', 'Training Materials - ' . $program->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-book me-2"></i> Training Materials
                        </h4>
                        <div>
                            <a href="{{ route('training.materials.create') }}?program_id={{ $program->id }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-plus-circle me-1"></i> Add Material
                            </a>
                            <a href="{{ route('training-programs.show', $program) }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to Program
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Program Info --}}
                <div class="card-body bg-light border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1">{{ $program->name }}</h5>
                            <p class="text-muted mb-0">
                                <span class="badge bg-info me-2">{{ $program->code }}</span>
                                @if($program->category)
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $program->category)) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="text-muted">{{ $materials->total() }} materials</span>
                        </div>
                    </div>
                </div>

                {{-- Materials List --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Language</th>
                                    <th>Size</th>
                                    <th>Downloads</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($materials as $material)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @switch($material->type)
                                                        @case('document')
                                                            <i class="fas fa-file-alt fa-2x text-primary"></i>
                                                            @break
                                                        @case('video')
                                                            <i class="fas fa-video fa-2x text-danger"></i>
                                                            @break
                                                        @case('presentation')
                                                            <i class="fas fa-file-powerpoint fa-2x text-warning"></i>
                                                            @break
                                                        @case('handout')
                                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                            @break
                                                        @default
                                                            <i class="fas fa-file fa-2x text-secondary"></i>
                                                    @endswitch
                                                </div>
                                                <div>
                                                    <strong>{{ $material->title }}</strong>
                                                    @if($material->title_sw)
                                                        <br><small class="text-muted">{{ $material->title_sw }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $material->type_label }}</span>
                                        </td>
                                        <td>{{ $material->language_label }}</td>
                                        <td>
                                            @if($material->file_path)
                                                {{ $material->file_size_formatted }}
                                            @elseif($material->external_url)
                                                <span class="text-info"><i class="fas fa-link me-1"></i> External</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $material->download_count }}</span>
                                        </td>
                                        <td>
                                            @if($material->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('training.materials.show', $material) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('training.materials.edit', $material) }}"
                                                   class="btn btn-outline-warning"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($material->file_path && $material->is_downloadable)
                                                    <a href="{{ Storage::url($material->file_path) }}"
                                                       class="btn btn-outline-success"
                                                       title="Download"
                                                       download>
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                                            <h5>No materials found</h5>
                                            <p>Add training materials for this program</p>
                                            <a href="{{ route('training.materials.create') }}?program_id={{ $program->id }}" class="btn btn-primary">
                                                <i class="fas fa-plus-circle me-1"></i> Add Material
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($materials->hasPages())
                        <div class="card-footer">
                            {{ $materials->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
