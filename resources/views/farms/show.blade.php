@extends('layouts.base')

@section('title', $farm->display_name . ' - Farm Details')

@push('styles')
<style>
    .detail-card {
        background-color: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .detail-section {
        border-left: 4px solid #27ae60;
        padding-left: 15px;
        margin-bottom: 25px;
    }

    .detail-section h4 {
        color: #27ae60;
        margin-bottom: 15px;
    }

    .detail-item {
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #555;
        min-width: 180px;
        display: inline-block;
    }

    .status-badge {
        font-size: 0.8rem;
        padding: 5px 10px;
        border-radius: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Farm Header -->
    <div class="detail-card">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h2 class="mb-2"><i class="fas fa-tractor text-success"></i> {{ $farm->display_name }}</h2>
                <h5 class="text-muted mb-3">Code: {{ $farm->code }}</h5>

                <div class="d-flex gap-2 mb-3">
                    <span class="badge bg-{{ $farm->status_color }} status-badge">
                        {{ $farm->status_label }}
                    </span>
                    <span class="badge bg-info status-badge">
                        {{ $farm->certification_status_label }}
                    </span>
                    @if($farm->organic_since)
                        <span class="badge bg-success status-badge">
                            Organic for {{ $farm->years_organic }} years
                        </span>
                    @endif
                </div>
            </div>

            <div class="text-end">
                <div class="btn-group">
                    <a href="{{ route('farms.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Farms
                    </a>
                    <a href="{{ route('farms.edit', $farm) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('farms.map', $farm) }}" class="btn btn-info">
                        <i class="fas fa-map"></i> Map
                    </a>
                    <form method="POST" action="{{ route('farms.destroy', $farm) }}"
                          onsubmit="return confirm('Are you sure you want to delete this farm?');"
                          style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Farm Details -->
        <div class="col-md-8">
            <!-- Basic Information -->
            <div class="detail-card">
                <div class="detail-section">
                    <h4><i class="fas fa-info-circle"></i> Basic Information</h4>

                    <div class="detail-item">
                        <span class="detail-label">Farmer:</span>
                        <a href="#">{{ $farm->farmer->full_name ?? 'N/A' }}</a>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Registration Date:</span>
                        {{ $farm->registration_date->format('F d, Y') }}
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Total Area:</span>
                        {{ $farm->total_area }} acres
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Cultivated Area:</span>
                        {{ $farm->cultivated_area }} acres
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Uncultivated Area:</span>
                        {{ number_format($farm->total_area - $farm->cultivated_area, 2) }} acres
                    </div>
                </div>

                <!-- Location Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-map-marker-alt"></i> Location Information</h4>

                    <div class="detail-item">
                        <span class="detail-label">Region:</span>
                        {{ $farm->region->name ?? 'N/A' }}
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">District:</span>
                        {{ $farm->district->name ?? 'N/A' }}
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Village:</span>
                        {{ $farm->village->name ?? 'N/A' }}
                    </div>

                    @if($farm->latitude && $farm->longitude)
                        <div class="detail-item">
                            <span class="detail-label">Coordinates:</span>
                            {{ $farm->latitude }}, {{ $farm->longitude }}
                        </div>
                    @endif

                    @if($farm->has_boundary)
                        <div class="detail-item">
                            <span class="detail-label">Boundary:</span>
                            <span class="text-success">
                                <i class="fas fa-check-circle"></i> Mapped
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Farm Characteristics -->
                <div class="detail-section">
                    <h4><i class="fas fa-leaf"></i> Farm Characteristics</h4>

                    @if($farm->soil_type)
                        <div class="detail-item">
                            <span class="detail-label">Soil Type:</span>
                            {{ $farm->soil_type }}
                        </div>
                    @endif

                    @if($farm->water_source)
                        <div class="detail-item">
                            <span class="detail-label">Water Source:</span>
                            {{ $farm->water_source }}
                        </div>
                    @endif

                    @if($farm->terrain)
                        <div class="detail-item">
                            <span class="detail-label">Terrain:</span>
                            {{ $farm->terrain }}
                        </div>
                    @endif

                    @if($farm->organic_since)
                        <div class="detail-item">
                            <span class="detail-label">Organic Since:</span>
                            {{ $farm->organic_since->format('F d, Y') }}
                        </div>
                    @endif

                    @if($farm->conversion_year)
                        <div class="detail-item">
                            <span class="detail-label">Conversion Year:</span>
                            {{ $farm->conversion_year }}
                        </div>
                    @endif
                </div>

                <!-- Notes -->
                @if($farm->notes)
                    <div class="detail-section">
                        <h4><i class="fas fa-sticky-note"></i> Notes</h4>
                        <p>{{ $farm->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Recent History -->
            @if($farm->histories->count() > 0)
                <div class="detail-card">
                    <div class="detail-section">
                        <h4><i class="fas fa-history"></i> Recent History</h4>

                        <div class="list-group">
                            @foreach($farm->histories as $history)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ ucfirst($history->action) }}</strong>
                                            <p class="mb-1">{{ $history->description }}</p>
                                        </div>
                                        <div class="text-muted">
                                            <small>{{ $history->created_at->diffForHumans() }}</small><br>
                                            <small>By: {{ $history->user->name ?? 'System' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($farm->histories->count() > 5)
                            <div class="mt-3 text-center">
                                <a href="{{ route('farms.history', $farm) }}" class="btn btn-outline-primary btn-sm">
                                    View Full History
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Quick Stats & Actions -->
        <div class="col-md-4">
            <!-- Quick Stats -->
            <div class="detail-card">
                <div class="detail-section">
                    <h4><i class="fas fa-chart-bar"></i> Quick Stats</h4>

                    <div class="text-center mb-4">
                        <div class="display-4 text-success">{{ $farm->total_area }}</div>
                        <div class="text-muted">Total Acres</div>
                    </div>

                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="h4 text-primary">{{ $farm->cultivated_area }}</div>
                            <div class="text-muted">Cultivated</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 text-warning">
                                {{ number_format($farm->total_area - $farm->cultivated_area, 1) }}
                            </div>
                            <div class="text-muted">Uncultivated</div>
                        </div>
                    </div>

                    @if($farm->seasons->count() > 0)
                        <div class="mt-3">
                            <div class="h5 text-center">{{ $farm->seasons->count() }}</div>
                            <div class="text-muted text-center">Growing Seasons</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="detail-card">
                <div class="detail-section">
                    <h4><i class="fas fa-bolt"></i> Quick Actions</h4>

                    <div class="d-grid gap-2">
                        <a href="{{ route('farms.edit', $farm) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit"></i> Edit Farm Details
                        </a>

                        <a href="{{ route('farms.boundaries', $farm) }}" class="btn btn-outline-info">
                            <i class="fas fa-draw-polygon"></i> Edit Boundaries
                        </a>

                        <a href="#" class="btn btn-outline-success">
                            <i class="fas fa-plus"></i> Add Season
                        </a>

                        <a href="{{ route('farms.map', $farm) }}" class="btn btn-outline-primary">
                            <i class="fas fa-map"></i> View on Map
                        </a>

                        <a href="{{ route('farms.history', $farm) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-history"></i> View Full History
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Seasons -->
            @if($farm->seasons->count() > 0)
                <div class="detail-card">
                    <div class="detail-section">
                        <h4><i class="fas fa-calendar-alt"></i> Recent Seasons</h4>

                        <div class="list-group">
                            @foreach($farm->seasons as $farmSeason)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $farmSeason->season->name ?? 'Season' }}</strong>
                                            <p class="mb-1">{{ $farmSeason->crop_type ?? 'No crop specified' }}</p>
                                        </div>
                                        <div class="text-muted">
                                            <small>{{ $farmSeason->year }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3 text-center">
                            <a href="{{ route('farms.seasons-list', $farm) }}" class="btn btn-outline-primary btn-sm">
                                View All Seasons
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
