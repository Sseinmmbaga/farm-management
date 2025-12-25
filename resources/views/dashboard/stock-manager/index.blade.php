@extends('layouts.base')

@section('title', 'Stock Manager Dashboard')

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
    .card-danger { border-left: 4px solid #e74c3c; }
</style>
@endpush

@section('content')
    <div class="header">
        <h1 class="h3 mb-0">Stock Manager Dashboard</h1>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-secondary" id="refreshBtn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <p class="lead mb-4">Welcome! Manage inventory and stock distribution here.</p>

    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary"><i class="fas fa-boxes"></i></div>
                <div class="stats-number">{{ $totalItems ?? 0 }}</div>
                <div class="stats-label">Total Stock Items</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-danger">
                <div class="stats-icon text-danger"><i class="fas fa-exclamation-circle"></i></div>
                <div class="stats-number">{{ $lowStockItems ?? 0 }}</div>
                <div class="stats-label">Low Stock Alerts</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success"><i class="fas fa-truck"></i></div>
                <div class="stats-number">{{ $pendingDeliveries ?? 0 }}</div>
                <div class="stats-label">Pending Deliveries</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning"><i class="fas fa-inbox"></i></div>
                <div class="stats-number">{{ $pendingRequests ?? 0 }}</div>
                <div class="stats-label">Pending Requests</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-warehouse me-2"></i>Inventory Management</h5>
                </div>
                <div class="card-body">
                    <a href="#" class="btn btn-outline-primary w-100 mb-2"><i class="fas fa-list"></i> View Inventory</a>
                    <a href="#" class="btn btn-outline-success w-100"><i class="fas fa-plus"></i> Add Stock Item</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-dolly me-2"></i>Distribution</h5>
                </div>
                <div class="card-body">
                    <a href="#" class="btn btn-outline-success w-100 mb-2"><i class="fas fa-seedling"></i> Seed Distribution</a>
                    <a href="#" class="btn btn-outline-info w-100"><i class="fas fa-hand-holding"></i> Input Distribution</a>
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
                        <div class="col-md-3"><a href="#" class="btn btn-outline-warning w-100"><i class="fas fa-dolly"></i> Stock Movements</a></div>
                        <div class="col-md-3"><a href="#" class="btn btn-outline-info w-100"><i class="fas fa-check-circle"></i> Approve Requests</a></div>
                        <div class="col-md-3"><a href="{{ route('farmers.index') }}" class="btn btn-outline-primary w-100"><i class="fas fa-users"></i> View Farmers</a></div>
                        <div class="col-md-3"><a href="#" class="btn btn-outline-success w-100"><i class="fas fa-chart-bar"></i> Stock Reports</a></div>
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
