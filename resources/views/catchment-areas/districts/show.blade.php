@extends('layouts.base')

@section('title', $district->name . ' - District Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-city text-success me-2"></i>
                        {{ $district->name }}
                        @if($district->name_sw)
                            <small class="text-muted">({{ $district->name_sw }})</small>
                        @endif
                        @if($district->is_active)
                            <span class="badge bg-success ms-2">Active</span>
                        @else
                            <span class="badge bg-secondary ms-2">Inactive</span>
                        @endif
                    </h2>
                    <p class="text-muted mb-0">
                        Code: <code>{{ $district->code }}</code> |
                        Region: <a href="{{ route('regions.show', $district->region) }}">{{ $district->region->name }}</a>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('districts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('districts.edit', $district) }}" class="btn btn-warning">
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
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h3 class="text-warning">{{ $district->villages_count }}</h3>
                            <small class="text-muted">Villages</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h3 class="text-info">{{ $district->farmers_count }}</h3>
                            <small class="text-muted">Farmers</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h3 class="text-primary">{{ $district->farms_count }}</h3>
                            <small class="text-muted">Farms</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Villages List -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-home me-2"></i>Villages in {{ $district->name }}</h5>
                    <a href="{{ route('villages.create') }}?district_id={{ $district->id }}&region_id={{ $district->region_id }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Village
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($villages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Village Name</th>
                                        <th>Code</th>
                                        <th class="text-center">Farmers</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($villages as $village)
                                        <tr>
                                            <td>
                                                <a href="{{ route('villages.show', $village) }}">{{ $village->name }}</a>
                                                @if($village->name_sw)
                                                    <br><small class="text-muted">{{ $village->name_sw }}</small>
                                                @endif
                                            </td>
                                            <td><code>{{ $village->code ?? 'N/A' }}</code></td>
                                            <td class="text-center"><span class="badge bg-info">{{ $village->farmers_count }}</span></td>
                                            <td class="text-center">
                                                @if($village->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('villages.show', $village) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('villages.edit', $village) }}" class="btn btn-sm btn-outline-warning">
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
                            <i class="fas fa-home fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No villages in this district yet</p>
                            <a href="{{ route('villages.create') }}?district_id={{ $district->id }}&region_id={{ $district->region_id }}" class="btn btn-warning">
                                <i class="fas fa-plus me-1"></i> Add First Village
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
