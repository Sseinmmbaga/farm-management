@extends('layouts.base')

@section('title', 'Production Manager Dashboard')

@push('styles')
<style>
    .stats-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.3s;
    }
    .stats-card:hover { transform: translateY(-5px); }
    .stats-icon { font-size: 2.5rem; margin-bottom: 15px; }
    .stats-number { font-size: 2rem; font-weight: bold; margin-bottom: 5px; }
    .stats-label { color: #6c757d; font-size: 0.9rem; }
    .card-primary { border-left: 4px solid #3498db; }
    .card-success { border-left: 4px solid #2ecc71; }
    .card-warning { border-left: 4px solid #f39c12; }
    .card-dark { border-left: 4px solid #34495e; }
</style>
@endpush

@section('content')
    <div class="header">
        <h1 class="h3 mb-0">Production Manager Dashboard</h1>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-dark" id="refreshBtn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <p class="lead mb-4">Welcome! Oversee production and yield tracking here.</p>

    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary"><i class="fas fa-tasks"></i></div>
                <div class="stats-number">{{ $productionPlans ?? 0 }}</div>
                <div class="stats-label">Production Plans</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success"><i class="fas fa-leaf"></i></div>
                <div class="stats-number">{{ $totalYield ?? 0 }}</div>
                <div class="stats-label">Total Yield (tons)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning"><i class="fas fa-tractor"></i></div>
                <div class="stats-number">{{ $activeFarms ?? 0 }}</div>
                <div class="stats-label">Active Farms</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-dark">
                <div class="stats-icon text-dark"><i class="fas fa-clipboard-list"></i></div>
                <div class="stats-number">{{ $harvestRecords ?? 0 }}</div>
                <div class="stats-label">Harvest Records</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-industry me-2"></i>Production Management</h5>
                </div>
                <div class="card-body">
                    <a href="#" class="btn btn-outline-dark w-100 mb-2"><i class="fas fa-tasks"></i> Production Plans</a>
                    <a href="#" class="btn btn-outline-primary w-100"><i class="fas fa-clipboard-list"></i> Production Records</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-leaf me-2"></i>Harvest Management</h5>
                </div>
                <div class="card-body">
                    <a href="#" class="btn btn-outline-success w-100 mb-2"><i class="fas fa-leaf"></i> Harvest Records</a>
                    <a href="#" class="btn btn-outline-warning w-100"><i class="fas fa-weight"></i> Yield Tracking</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3"><a href="{{ route('farmers.index') }}" class="btn btn-outline-primary w-100"><i class="fas fa-users"></i> View Farmers</a></div>
                        <div class="col-md-3"><a href="{{ route('farms.index') }}" class="btn btn-outline-success w-100"><i class="fas fa-tractor"></i> View Farms</a></div>
                        <div class="col-md-3"><a href="{{ route('farms.map.all') }}" class="btn btn-outline-info w-100"><i class="fas fa-map-marked-alt"></i> Map View</a></div>
                        <div class="col-md-3"><a href="#" class="btn btn-outline-warning w-100"><i class="fas fa-chart-pie"></i> Yield Reports</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('refreshBtn')?.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        setTimeout(() => location.reload(), 1000);
    });
</script>
@endpush
