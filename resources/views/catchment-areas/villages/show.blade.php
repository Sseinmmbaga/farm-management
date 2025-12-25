@extends('layouts.base')

@section('title', $village->name . ' - Village Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-home text-warning me-2"></i>
                        {{ $village->name }}
                        @if($village->name_sw)
                            <small class="text-muted">({{ $village->name_sw }})</small>
                        @endif
                        @if($village->is_active)
                            <span class="badge bg-success ms-2">Active</span>
                        @else
                            <span class="badge bg-secondary ms-2">Inactive</span>
                        @endif
                    </h2>
                    <p class="text-muted mb-0">
                        @if($village->code)
                            Code: <code>{{ $village->code }}</code> |
                        @endif
                        District: <a href="{{ route('districts.show', $village->district) }}">{{ $village->district->name }}</a> |
                        Region: <a href="{{ route('regions.show', $village->region) }}">{{ $village->region->name }}</a>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('villages.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('villages.edit', $village) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- Stats Cards -->
        <div class="col-12 mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h3 class="text-info">{{ $village->subvillages_count }}</h3>
                            <small class="text-muted">Subvillages</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h3 class="text-primary">{{ $village->farmers_count }}</h3>
                            <small class="text-muted">Farmers</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h3 class="text-success">{{ $village->farms_count }}</h3>
                            <small class="text-muted">Farms</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subvillages List -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-map-pin me-2"></i>Subvillages in {{ $village->name }}</h5>
                    <a href="{{ route('subvillages.create') }}?village_id={{ $village->id }}&ward_id={{ $village->ward_id }}&district_id={{ $village->district_id }}&region_id={{ $village->region_id }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Subvillage
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($subvillages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subvillage Name</th>
                                        <th>Code</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subvillages as $subvillage)
                                        <tr>
                                            <td>
                                                <a href="{{ route('subvillages.show', $subvillage) }}">{{ $subvillage->name }}</a>
                                                @if($subvillage->name_sw)
                                                    <br><small class="text-muted">{{ $subvillage->name_sw }}</small>
                                                @endif
                                            </td>
                                            <td><code>{{ $subvillage->code ?? 'N/A' }}</code></td>
                                            <td class="text-center">
                                                @if($subvillage->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('subvillages.show', $subvillage) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('subvillages.edit', $subvillage) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-map-pin fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No subvillages in this village yet</p>
                            <a href="{{ route('subvillages.create') }}?village_id={{ $village->id }}&ward_id={{ $village->ward_id }}&district_id={{ $village->district_id }}&region_id={{ $village->region_id }}" class="btn btn-info">
                                <i class="fas fa-plus me-1"></i> Add First Subvillage
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
