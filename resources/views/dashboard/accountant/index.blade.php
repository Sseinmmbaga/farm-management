@extends('layouts.base')

@section('title', 'Accountant Dashboard')

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
    .card-info { border-left: 4px solid #17a2b8; }
</style>
@endpush

@section('content')
    <div class="header">
        <h1 class="h3 mb-0">Accountant Dashboard</h1>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-warning" id="refreshBtn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <p class="lead mb-4">Welcome! Manage financial data and reports here.</p>

    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stats-number">{{ $totalTransactions ?? 0 }}</div>
                <div class="stats-label">Total Transactions</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary"><i class="fas fa-receipt"></i></div>
                <div class="stats-number">{{ $pendingPayments ?? 0 }}</div>
                <div class="stats-label">Pending Payments</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="stats-number">{{ $distributions ?? 0 }}</div>
                <div class="stats-label">Distributions</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-info">
                <div class="stats-icon text-info"><i class="fas fa-file-invoice"></i></div>
                <div class="stats-number">{{ $reports ?? 0 }}</div>
                <div class="stats-label">Reports Generated</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Financial Reports</h5>
                </div>
                <div class="card-body">
                    <a href="#" class="btn btn-outline-warning w-100 mb-2"><i class="fas fa-file-invoice-dollar"></i> Financial Reports</a>
                    <a href="#" class="btn btn-outline-primary w-100"><i class="fas fa-exchange-alt"></i> Transactions</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Payment Records</h5>
                </div>
                <div class="card-body">
                    <a href="#" class="btn btn-outline-success w-100 mb-2"><i class="fas fa-receipt"></i> Payment Records</a>
                    <a href="#" class="btn btn-outline-info w-100"><i class="fas fa-hand-holding-usd"></i> Disbursements</a>
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
                        <div class="col-md-3"><a href="#" class="btn btn-outline-success w-100"><i class="fas fa-box"></i> Stock Transactions</a></div>
                        <div class="col-md-3"><a href="#" class="btn btn-outline-warning w-100"><i class="fas fa-chart-pie"></i> Financial Analytics</a></div>
                        <div class="col-md-3"><a href="#" class="btn btn-outline-info w-100"><i class="fas fa-download"></i> Export Reports</a></div>
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
