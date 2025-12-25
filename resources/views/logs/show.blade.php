@extends('layouts.base')

@section('title', $log->name . ' - Log Details')

@push('styles')
<style>
    .log-header-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 25px;
        border-left: 6px solid;
    }
    .log-header-card.type-seeding { border-left-color: #28a745; }
    .log-header-card.type-input { border-left-color: #17a2b8; }
    .log-header-card.type-observation { border-left-color: #ffc107; }
    .log-header-card.type-harvest { border-left-color: #fd7e14; }
    .log-header-card.type-activity { border-left-color: #6c757d; }
    .log-header-card.type-training { border-left-color: #6f42c1; }
    .log-header-card.type-inspection { border-left-color: #e83e8c; }

    .detail-section {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .detail-label {
        color: #6c757d;
        font-weight: 500;
    }
    .detail-value {
        font-weight: 500;
    }
    .image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .image-thumb {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .image-thumb:hover {
        transform: scale(1.03);
    }
    .image-thumb img {
        width: 100%;
        height: 140px;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('logs.index') }}">Activity Logs</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $log->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Log Header -->
            <div class="log-header-card type-{{ $log->type->value }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-pill fs-6" style="background: {{ $log->type->color() }}; color: white;">
                                <i class="{{ $log->type->icon() }} me-1"></i> {{ $log->type->label() }}
                            </span>
                            <span class="badge bg-{{ $log->status_color }} fs-6">{{ $log->status_label }}</span>
                            @if($log->is_flagged)
                                <span class="badge bg-danger fs-6"><i class="bi bi-flag-fill me-1"></i> Flagged</span>
                            @endif
                        </div>
                        <h1 class="h2 mb-2">{{ $log->name }}</h1>
                        <p class="text-muted mb-4">
                            <i class="bi bi-calendar me-1"></i> {{ $log->log_date->format('l, F j, Y') }}
                            &bull; Created {{ $log->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('logs.edit', $log) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                        <a href="{{ route('logs.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                @if($log->description)
                    <div class="mt-4">
                        <h5 class="mb-2">Description</h5>
                        <div class="alert alert-light border">
                            {{ $log->description }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Details Grid -->
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-section">
                        <h5 class="mb-3 border-bottom pb-2">
                            <i class="bi bi-info-circle me-2"></i> Log Details
                        </h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="detail-label">Log ID</td>
                                <td class="detail-value">#{{ $log->id }}</td>
                            </tr>
                            <tr>
                                <td class="detail-label">Type</td>
                                <td class="detail-value">
                                    <span class="badge" style="background: {{ $log->type->color() }}; color: white;">
                                        {{ $log->type->label() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-label">Status</td>
                                <td class="detail-value">
                                    <span class="badge bg-{{ $log->status_color }}">{{ $log->status_label }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="detail-label">Log Date</td>
                                <td class="detail-value">{{ $log->log_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="detail-label">Created</td>
                                <td class="detail-value">{{ $log->created_at->format('M d, Y h:i A') }} ({{ $log->created_at->diffForHumans() }})</td>
                            </tr>
                            <tr>
                                <td class="detail-label">Last Updated</td>
                                <td class="detail-value">{{ $log->updated_at->format('M d, Y h:i A') }} ({{ $log->updated_at->diffForHumans() }})</td>
                            </tr>
                            @if($log->notes)
                            <tr>
                                <td class="detail-label">Notes</td>
                                <td class="detail-value">{{ $log->notes }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-section">
                        <h5 class="mb-3 border-bottom pb-2">
                            <i class="bi bi-link-45deg me-2"></i> Associated Entities
                        </h5>
                        <table class="table table-borderless">
                            @if($log->farmer)
                            <tr>
                                <td class="detail-label">Farmer</td>
                                <td class="detail-value">
                                    <a href="{{ route('farmers.show', $log->farmer) }}" class="text-decoration-none">
                                        <i class="bi bi-person me-1"></i> {{ $log->farmer->full_name }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if($log->farm)
                            <tr>
                                <td class="detail-label">Farm</td>
                                <td class="detail-value">
                                    <a href="{{ route('farms.show', $log->farm) }}" class="text-decoration-none">
                                        <i class="bi bi-geo-alt me-1"></i> {{ $log->farm->name }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if($log->season)
                            <tr>
                                <td class="detail-label">Season</td>
                                <td class="detail-value">
                                    <i class="bi bi-cloud-sun me-1"></i> {{ $log->season->name }}
                                </td>
                            </tr>
                            @endif
                            @if($log->field)
                            <tr>
                                <td class="detail-label">Field</td>
                                <td class="detail-value">{{ $log->field->name }}</td>
                            </tr>
                            @endif
                            @if($log->image_count > 0)
                            <tr>
                                <td class="detail-label">Images</td>
                                <td class="detail-value">
                                    <i class="bi bi-camera me-1"></i> {{ $log->image_count }} photo(s)
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Type-Specific Details -->
            @if($log->typeSpecificDetails)
            <div class="detail-section">
                <h5 class="mb-3 border-bottom pb-2">
                    <i class="bi bi-gear me-2"></i> {{ $log->type->label() }} Details
                </h5>
                <div class="row">
                    @foreach($log->typeSpecificDetails as $key => $value)
                    <div class="col-md-6 mb-3">
                        <div class="detail-label">{{ Str::title(str_replace('_', ' ', $key)) }}</div>
                        <div class="detail-value">{{ $value ?? 'Not specified' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Images Gallery -->
            @if($log->images && $log->images->count() > 0)
            <div class="detail-section">
                <h5 class="mb-3 border-bottom pb-2">
                    <i class="bi bi-images me-2"></i> Images ({{ $log->images->count() }})
                </h5>
                <div class="image-gallery">
                    @foreach($log->images as $image)
                    <div class="image-thumb">
                        <a href="{{ asset('storage/' . $image->path) }}" data-lightbox="log-images">
                            <img src="{{ asset('storage/' . $image->path) }}" alt="Log image">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="detail-section">
                <h5 class="mb-3 border-bottom pb-2">
                    <i class="bi bi-lightning me-2"></i> Quick Actions
                </h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('logs.edit', $log) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil me-1"></i> Edit Log
                    </a>
                    <a href="{{ route('logs.create') }}?duplicate={{ $log->id }}" class="btn btn-outline-primary">
                        <i class="bi bi-copy me-1"></i> Duplicate Log
                    </a>
                    @if($log->is_flagged)
                    <form action="{{ route('logs.unflag', $log) }}" method="POST" class="d-grid">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline-success">
                            <i class="bi bi-flag me-1"></i> Unflag Log
                        </button>
                    </form>
                    @else
                    <form action="{{ route('logs.flag', $log) }}" method="POST" class="d-grid">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-flag-fill me-1"></i> Flag Log
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('logs.destroy', $log) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this log?')" class="d-grid">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Delete Log
                        </button>
                    </form>
                </div>
            </div>

            <!-- Related Logs -->
            <div class="detail-section">
                <h5 class="mb-3 border-bottom pb-2">
                    <i class="bi bi-link me-2"></i> Related Logs
                </h5>
                @if($relatedLogs && $relatedLogs->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($relatedLogs->take(5) as $related)
                        <a href="{{ route('logs.show', $related) }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge me-1" style="background: {{ $related->type->color() }}; color: white;">
                                    {{ $related->type->label() }}
                                </span>
                                {{ Str::limit($related->name, 30) }}
                            </div>
                            <small class="text-muted">{{ $related->log_date->format('M d') }}</small>
                        </a>
                        @endforeach
                    </div>
                    @if($relatedLogs->count() > 5)
                        <div class="text-center mt-2">
                            <a href="{{ route('logs.index', ['farmer_id' => $log->farmer_id ?? null, 'farm_id' => $log->farm_id ?? null]) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                View All Related
                            </a>
                        </div>
                    @endif
                @else
                    <p class="text-muted mb-0">No related logs found.</p>
                @endif
            </div>

            <!-- Activity Timeline -->
            <div class="detail-section">
                <h5 class="mb-3 border-bottom pb-2">
                    <i class="bi bi-clock-history me-2"></i> Recent Activity
                </h5>
                @if($log->activities && $log->activities->count() > 0)
                    <div class="timeline">
                        @foreach($log->activities->take(5) as $activity)
                        <div class="timeline-item mb-2">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <small class="text-muted">{{ $activity->description }}</small>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i> {{ $activity->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No activity recorded.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Lightbox for images -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true
    });
</script>
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #6c757d;
    }
    .timeline-content {
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    .timeline-item:last-child .timeline-content {
        border-bottom: none;
    }
</style>
@endpush