@extends('layouts.base')

@section('title', 'Extension Officer Dashboard')

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
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stats-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .card-primary { border-left: 4px solid #3498db; }
    .card-success { border-left: 4px solid #2ecc71; }
    .card-warning { border-left: 4px solid #f39c12; }
    .card-info { border-left: 4px solid #17a2b8; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <h1 class="h3 mb-0">Extension Officer Dashboard</h1>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-success" id="refreshBtn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <a href="{{ route('farmers.create') }}" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Add Farmer
            </a>
        </div>
    </div>

    <p class="lead mb-4">Welcome! Manage farmers and collect field data here.</p>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">{{ $myFarmers ?? 0 }}</div>
                <div class="stats-label">My Farmers</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success">
                    <i class="fas fa-tractor"></i>
                </div>
                <div class="stats-number">{{ $totalFarms ?? 0 }}</div>
                <div class="stats-label">Total Farms</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stats-number">{{ $pendingRecords ?? 0 }}</div>
                <div class="stats-label">Pending Records</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card card-info">
                <div class="stats-icon text-info">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stats-number">{{ $fieldVisits ?? 0 }}</div>
                <div class="stats-label">Field Visits This Month</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('farmers.create') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-user-plus"></i> Add New Farmer
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('farms.create') }}" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-plus-circle"></i> Register Farm
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('farm-records.new.create') }}" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-file-alt"></i> New Farmer Record
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('farm-records.existing.create') }}" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-edit"></i> Existing Farmer Record
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>My Farmers</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">View and manage your assigned farmers</p>
                    <a href="{{ route('farmers.index') }}" class="btn btn-primary">View All Farmers</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-tractor me-2"></i>Farm Records</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Manage farm records and data collection</p>
                    <a href="{{ route('farm-records.index') }}" class="btn btn-success">View Farm Records</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-map-marked-alt me-2"></i>Map View</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">View farms on interactive map</p>
                    <a href="{{ route('farms.map.all') }}" class="btn btn-info">Open Map</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Farmer History</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">View farmer registration history</p>
                    <a href="{{ route('farm-records.new.history') }}" class="btn btn-warning">View History</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                    location.reload();
                }, 1000);
            });
        }
    });
</script>
@endpush
