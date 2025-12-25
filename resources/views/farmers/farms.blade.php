@extends('layouts.base')

@section('title', 'Farmer Farms - ' . $farmer->first_name . ' ' . $farmer->last_name)

@push('styles')
<style>
    .farm-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.3s;
    }

    .farm-card:hover {
        transform: translateY(-5px);
    }

    .status-badge {
        position: absolute;
        top: 10px;
        right: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header with Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('farmers.index') }}">Farmers</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('farmers.show', $farmer) }}">{{ $farmer->first_name }} {{ $farmer->last_name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Farms</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-tractor me-2"></i>
                        Farms for {{ $farmer->first_name }} {{ $farmer->last_name }}
                        <span class="badge bg-primary ms-2">{{ $farms->count() }}</span>
                    </h1>
                    <p class="text-muted mb-0">Farmer ID: {{ $farmer->registration_number }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('farms.create', ['farmer_id' => $farmer->id]) }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Add Farm
                    </a>
                    <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <!-- Farms List -->
            <div class="row">
                @forelse ($farms as $farm)
                    <div class="col-md-4">
                        <div class="card farm-card">
                            <div class="card-body position-relative">
                                <span class="badge bg-{{ $farm->status_color }} status-badge">
                                    {{ $farm->status_label }}
                                </span>

                                <h5 class="card-title">{{ $farm->display_name }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">Code: {{ $farm->code }}</h6>

                                <p class="card-text">
                                    <strong>Location:</strong> {{ $farm->village->name ?? 'N/A' }}, {{ $farm->district->name ?? 'N/A' }}<br>
                                    <strong>Area:</strong> {{ $farm->total_area }} acres ({{ $farm->cultivated_area }} cultivated)<br>
                                    <strong>Certification:</strong>
                                    <span class="badge bg-{{ $farm->certification_status == 'organic' ? 'success' : ($farm->certification_status == 'in-conversion' ? 'warning' : 'secondary') }}">
                                        {{ $farm->certification_status_label }}
                                    </span><br>
                                    @if($farm->organic_since)
                                        <strong>Organic Since:</strong> {{ $farm->organic_since->format('M d, Y') }}<br>
                                    @endif
                                    <strong>Registered:</strong> {{ $farm->registration_date ? $farm->registration_date->format('M d, Y') : 'N/A' }}
                                </p>

                                @if($farm->seasons->count() > 0)
                                    <div class="mb-2">
                                        <strong>Seasons:</strong>
                                        @foreach($farm->seasons->take(3) as $season)
                                            <span class="badge bg-info">{{ $season->name ?? $season->year ?? 'Season' }}</span>
                                        @endforeach
                                        @if($farm->seasons->count() > 3)
                                            <span class="badge bg-secondary">+{{ $farm->seasons->count() - 3 }} more</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between mt-3">
                                    <a href="{{ route('farms.show', $farm) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('farms.edit', $farm) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="{{ route('farms.map', $farm) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-map"></i> Map
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-tractor fa-4x text-muted mb-3"></i>
                                <h4>No Farms Registered</h4>
                                <p class="text-muted">This farmer hasn't registered any farms yet.</p>
                                <a href="{{ route('farms.create', ['farmer_id' => $farmer->id]) }}" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i> Add First Farm
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
