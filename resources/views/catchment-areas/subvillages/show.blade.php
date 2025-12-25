@extends('layouts.base')

@section('title', $subvillage->name . ' - Subvillage Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-map-pin text-info me-2"></i>
                        {{ $subvillage->name }}
                        @if($subvillage->name_sw)
                            <small class="text-muted">({{ $subvillage->name_sw }})</small>
                        @endif
                        @if($subvillage->is_active)
                            <span class="badge bg-success ms-2">Active</span>
                        @else
                            <span class="badge bg-secondary ms-2">Inactive</span>
                        @endif
                    </h2>
                    <p class="text-muted mb-0">
                        @if($subvillage->code)
                            Code: <code>{{ $subvillage->code }}</code> |
                        @endif
                        Village: <a href="{{ route('villages.show', $subvillage->village) }}">{{ $subvillage->village->name }}</a> |
                        District: <a href="{{ route('districts.show', $subvillage->district) }}">{{ $subvillage->district->name }}</a> |
                        Region: <a href="{{ route('regions.show', $subvillage->region) }}">{{ $subvillage->region->name }}</a>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('subvillages.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('subvillages.edit', $subvillage) }}" class="btn btn-warning">
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
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h3 class="text-primary">{{ $subvillage->farmers_count }}</h3>
                            <small class="text-muted">Farmers</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h3 class="text-success">{{ $subvillage->farms_count }}</h3>
                            <small class="text-muted">Farms</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Subvillage Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Name (English):</th>
                                    <td>{{ $subvillage->name }}</td>
                                </tr>
                                <tr>
                                    <th>Name (Swahili):</th>
                                    <td>{{ $subvillage->name_sw ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Code:</th>
                                    <td><code>{{ $subvillage->code ?? 'N/A' }}</code></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($subvillage->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Village:</th>
                                    <td><a href="{{ route('villages.show', $subvillage->village) }}">{{ $subvillage->village->name }}</a></td>
                                </tr>
                                <tr>
                                    <th>Ward:</th>
                                    <td>{{ $subvillage->ward->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>District:</th>
                                    <td><a href="{{ route('districts.show', $subvillage->district) }}">{{ $subvillage->district->name }}</a></td>
                                </tr>
                                <tr>
                                    <th>Region:</th>
                                    <td><a href="{{ route('regions.show', $subvillage->region) }}">{{ $subvillage->region->name }}</a></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($subvillage->latitude && $subvillage->longitude)
                        <div class="mt-3">
                            <h6>Coordinates:</h6>
                            <p class="text-muted">{{ $subvillage->latitude }}, {{ $subvillage->longitude }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
