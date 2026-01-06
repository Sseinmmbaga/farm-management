@extends('layouts.base')

@section('title', $ward->name . ' - Ward Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-map-marker-alt me-2" style="color: #6f42c1;"></i>
                        {{ $ward->name }}
                        @if($ward->name_sw)
                            <small class="text-muted">({{ $ward->name_sw }})</small>
                        @endif
                        @if($ward->is_active)
                            <span class="badge bg-success ms-2">Active</span>
                        @else
                            <span class="badge bg-secondary ms-2">Inactive</span>
                        @endif
                    </h2>
                    <p class="text-muted mb-0">
                        @if($ward->code)
                            Code: <code>{{ $ward->code }}</code> |
                        @endif
                        District: <a href="{{ route('districts.show', $ward->district) }}">{{ $ward->district->name }}</a> |
                        Region: <a href="{{ route('regions.show', $ward->district->region) }}">{{ $ward->district->region->name }}</a>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('wards.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    @if(!auth()->user()->hasViewOnlyAccess())
                    <a href="{{ route('wards.edit', $ward) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endif
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
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h3 class="text-warning">{{ $ward->villages_count }}</h3>
                            <small class="text-muted">Villages</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card" style="border-color: #6f42c1;">
                        <div class="card-body text-center">
                            <h3 style="color: #6f42c1;">{{ $villages->sum('subvillages_count') }}</h3>
                            <small class="text-muted">Subvillages</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Villages List -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-home me-2"></i>Villages in {{ $ward->name }}</h5>
                    @if(!auth()->user()->hasViewOnlyAccess())
                    <a href="{{ route('villages.create') }}?ward_id={{ $ward->id }}&district_id={{ $ward->district_id }}&region_id={{ $ward->district->region_id }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Village
                    </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($villages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Village Name</th>
                                        <th>Code</th>
                                        <th class="text-center">Subvillages</th>
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
                                            <td class="text-center"><span class="badge bg-info">{{ $village->subvillages_count }}</span></td>
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
                                                @if(!auth()->user()->hasViewOnlyAccess())
                                                <a href="{{ route('villages.edit', $village) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-home fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No villages in this ward yet</p>
                            @if(!auth()->user()->hasViewOnlyAccess())
                            <a href="{{ route('villages.create') }}?ward_id={{ $ward->id }}&district_id={{ $ward->district_id }}&region_id={{ $ward->district->region_id }}" class="btn btn-warning">
                                <i class="fas fa-plus me-1"></i> Add First Village
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
