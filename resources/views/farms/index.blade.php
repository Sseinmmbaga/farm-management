@extends('layouts.base')

@section('title', 'Farms Management')

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

    .filter-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tractor text-success"></i> Farms Management</h2>
        <a href="{{ route('farms.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Farm
        </a>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('farms.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search"
                       value="{{ request('search') }}" placeholder="Search by name or code...">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandoned</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="certification_status" class="form-label">Certification</label>
                <select class="form-select" id="certification_status" name="certification_status">
                    <option value="">All Certification</option>
                    <option value="organic" {{ request('certification_status') == 'organic' ? 'selected' : '' }}>Organic</option>
                    <option value="in-conversion" {{ request('certification_status') == 'in-conversion' ? 'selected' : '' }}>In Conversion</option>
                    <option value="conventional" {{ request('certification_status') == 'conventional' ? 'selected' : '' }}>Conventional</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Farms List -->
    <div class="row">
        @forelse ($farms as $farm)
            <div class="col-md-4">
                <div class="card farm-card">
                    <div class="card-body">
                        <span class="badge bg-{{ $farm->status_color }} status-badge">
                            {{ $farm->status_label }}
                        </span>

                        <h5 class="card-title">{{ $farm->display_name }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Code: {{ $farm->code }}</h6>

                        <p class="card-text">
                            <strong>Farmer:</strong> {{ $farm->farmer->full_name ?? 'N/A' }}
                            @if($farm->farmer && $farm->farmer->group)
                                <span class="badge bg-{{ $farm->farmer->group->group_type === 'simba' ? 'warning' : 'primary' }} ms-1">
                                    {{ $farm->farmer->group->group_type_label }}
                                </span>
                            @endif
                            <br>
                            <strong>Location:</strong> {{ $farm->village->name ?? 'N/A' }}, {{ $farm->district->name ?? 'N/A' }}<br>
                            <strong>Area:</strong> {{ $farm->total_area }} acres ({{ $farm->cultivated_area }} cultivated)<br>
                            <strong>Certification:</strong> {{ $farm->certification_status_label }}<br>
                            <strong>Registered:</strong> {{ $farm->registration_date->format('M d, Y') }}
                        </p>

                        <div class="d-flex justify-content-between">
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
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No farms found.
                    @if(auth()->user()->can('create', App\Models\Farms\Farm::class))
                        <a href="{{ route('farms.create') }}" class="alert-link">Create your first farm</a>.
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($farms->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $farms->links() }}
        </div>
    @endif
</div>
@endsection
