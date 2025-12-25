@extends('layouts.base')

@section('title', $region->name . ' - Region Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-map text-primary me-2"></i>
                        {{ $region->name }}
                        @if($region->name_sw)
                            <small class="text-muted">({{ $region->name_sw }})</small>
                        @endif
                        @if($region->is_active)
                            <span class="badge bg-success ms-2">Active</span>
                        @else
                            <span class="badge bg-secondary ms-2">Inactive</span>
                        @endif
                    </h2>
                    <p class="text-muted mb-0">Code: <code>{{ $region->code }}</code></p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('regions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('regions.edit', $region) }}" class="btn btn-warning">
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
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h3 class="text-success">{{ $region->districts_count }}</h3>
                            <small class="text-muted">Districts</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h3 class="text-warning">{{ $region->villages_count }}</h3>
                            <small class="text-muted">Villages</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h3 class="text-info">{{ $region->farmers_count }}</h3>
                            <small class="text-muted">Farmers</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h3 class="text-primary">{{ $region->farms_count }}</h3>
                            <small class="text-muted">Farms</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Districts List -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-city me-2"></i>Districts in {{ $region->name }}</h5>
                    <a href="{{ route('districts.create') }}?region_id={{ $region->id }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus me-1"></i> Add District
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($districts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>District Name</th>
                                        <th>Code</th>
                                        <th class="text-center">Villages</th>
                                        <th class="text-center">Farmers</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($districts as $district)
                                        <tr>
                                            <td>
                                                <a href="{{ route('districts.show', $district) }}">{{ $district->name }}</a>
                                                @if($district->name_sw)
                                                    <br><small class="text-muted">{{ $district->name_sw }}</small>
                                                @endif
                                            </td>
                                            <td><code>{{ $district->code }}</code></td>
                                            <td class="text-center"><span class="badge bg-warning">{{ $district->villages_count }}</span></td>
                                            <td class="text-center"><span class="badge bg-info">{{ $district->farmers_count }}</span></td>
                                            <td class="text-center">
                                                @if($district->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('districts.show', $district) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('districts.edit', $district) }}" class="btn btn-sm btn-outline-warning">
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
                            <i class="fas fa-city fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No districts in this region yet</p>
                            <a href="{{ route('districts.create') }}?region_id={{ $region->id }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Add First District
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
