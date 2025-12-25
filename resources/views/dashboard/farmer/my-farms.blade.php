@extends('layouts.base')

@section('title', 'My Farms')

@push('styles')
<style>
    .stats-row {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-item {
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        flex: 1;
        text-align: center;
        border-left: 4px solid;
    }
    .stat-item.total { border-left-color: #27ae60; }
    .stat-item.active { border-left-color: #2ecc71; }
    .stat-item.area { border-left-color: #3498db; }
    .stat-item.certified { border-left-color: #f39c12; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .farm-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s;
        border: 1px solid #eee;
    }
    .farm-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .farm-icon {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .farm-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }
    .farm-info-item i {
        width: 20px;
        color: #6c757d;
        margin-right: 10px;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">My Farms</h1>
            <p class="text-muted mb-0">View and manage your registered farms</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-success">{{ $stats['total_farms'] }}</div>
            <div class="label">Total Farms</div>
        </div>
        <div class="stat-item active">
            <div class="number text-success">{{ $stats['active_farms'] }}</div>
            <div class="label">Active Farms</div>
        </div>
        <div class="stat-item area">
            <div class="number text-primary">{{ number_format($stats['total_area'], 1) }}</div>
            <div class="label">Total Area (acres)</div>
        </div>
        <div class="stat-item certified">
            <div class="number text-warning">{{ $stats['organic_certified'] }}</div>
            <div class="label">Organic Certified</div>
        </div>
    </div>

    <!-- Farms Grid -->
    <div class="row">
        @forelse($farms as $farm)
        <div class="col-md-6 col-lg-4">
            <div class="farm-card">
                <div class="d-flex align-items-start mb-3">
                    <div class="farm-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="fas fa-tractor"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">{{ $farm->name ?? 'Farm #' . $farm->id }}</h5>
                        <span class="badge bg-{{ ($farm->status ?? 'active') === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($farm->status ?? 'active') }}
                        </span>
                        @if($farm->is_organic_certified)
                        <span class="badge bg-warning text-dark ms-1">
                            <i class="fas fa-certificate"></i> Organic
                        </span>
                        @endif
                    </div>
                </div>

                <div class="farm-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $farm->village->name ?? 'Location not set' }}</span>
                </div>

                <div class="farm-info-item">
                    <i class="fas fa-ruler-combined"></i>
                    <span>{{ number_format($farm->area ?? 0, 2) }} acres</span>
                </div>

                <div class="farm-info-item">
                    <i class="fas fa-seedling"></i>
                    <span>{{ $farm->crop_type ?? 'Cotton' }}</span>
                </div>

                @if($farm->gps_coordinates)
                <div class="farm-info-item">
                    <i class="fas fa-map-pin"></i>
                    <span class="text-muted small">GPS: {{ $farm->gps_coordinates }}</span>
                </div>
                @endif

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Registered {{ $farm->created_at ? $farm->created_at->format('M d, Y') : 'N/A' }}
                    </small>
                    <a href="#" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-eye"></i> View
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-tractor fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No Farms Found</h5>
                <p class="text-muted">You don't have any registered farms yet.</p>
            </div>
        </div>
        @endforelse
    </div>

    @if($farms instanceof \Illuminate\Pagination\LengthAwarePaginator && $farms->hasPages())
    <div class="mt-4">
        {{ $farms->links() }}
    </div>
    @endif
@endsection
