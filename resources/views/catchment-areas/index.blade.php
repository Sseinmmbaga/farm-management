@extends('layouts.base')

@section('title', 'Catchment Areas Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-globe-africa me-2"></i> Catchment Areas Management
                        </h4>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Regions</h6>
                                            <h3 class="mb-0">{{ $stats['regions'] }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-map fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('regions.index') }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                                        Manage Regions
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Districts</h6>
                                            <h3 class="mb-0">{{ $stats['districts'] }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-city fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('districts.index') }}" class="btn btn-sm btn-outline-success mt-2 w-100">
                                        Manage Districts
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-warning h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Villages</h6>
                                            <h3 class="mb-0">{{ $stats['villages'] }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-home fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('villages.index') }}" class="btn btn-sm btn-outline-warning mt-2 w-100">
                                        Manage Villages
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-info h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Subvillages</h6>
                                            <h3 class="mb-0">{{ $stats['subvillages'] }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-map-pin fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('subvillages.index') }}" class="btn btn-sm btn-outline-info mt-2 w-100">
                                        Manage Subvillages
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Regions Overview -->
                <div class="card-body">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-map text-primary me-2"></i>Regions Overview
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Region</th>
                                    <th>Code</th>
                                    <th class="text-center">Districts</th>
                                    <th class="text-center">Villages</th>
                                    <th class="text-center">Subvillages</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($regions as $region)
                                    <tr>
                                        <td>
                                            <strong>{{ $region->name }}</strong>
                                            @if($region->name_sw)
                                                <br><small class="text-muted">{{ $region->name_sw }}</small>
                                            @endif
                                        </td>
                                        <td><code>{{ $region->code }}</code></td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $region->districts_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">{{ $region->villages_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $region->subvillages_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($region->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('regions.show', $region) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-map fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No regions found</p>
                                            <a href="{{ route('regions.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i> Add First Region
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
