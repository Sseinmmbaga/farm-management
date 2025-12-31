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
    .card-info { border-left: 4px solid #17a2b8; }
    .activity-item {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    .activity-item:last-child { border-bottom: none; }
    .stock-alert-item {
        padding: 8px 12px;
        border-radius: 5px;
        margin-bottom: 8px;
    }
    .stock-alert-critical { background-color: #fee2e2; border-left: 3px solid #ef4444; }
    .stock-alert-warning { background-color: #fef3c7; border-left: 3px solid #f59e0b; }
    .stock-alert-out { background-color: #fecaca; border-left: 3px solid #dc2626; }
</style>
@endpush

@section('content')
    <div class="header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Stock Manager Dashboard</h1>
            <p class="text-muted mb-0">Welcome! Manage inventory and stock distribution here.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock.index') }}" class="btn btn-primary">
                <i class="fas fa-boxes me-1"></i> View Inventory
            </a>
            <button class="btn btn-outline-secondary" id="refreshBtn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary"><i class="fas fa-boxes"></i></div>
                <div class="stats-number">{{ number_format($totalItems ?? 0) }}</div>
                <div class="stats-label">Total Stock Items</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-danger">
                <div class="stats-icon text-danger"><i class="fas fa-exclamation-circle"></i></div>
                <div class="stats-number">{{ number_format($lowStockItems ?? 0) }}</div>
                <div class="stats-label">Low Stock Alerts</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success"><i class="fas fa-truck"></i></div>
                <div class="stats-number">{{ number_format($pendingDeliveries ?? 0) }}</div>
                <div class="stats-label">Pending Deliveries</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning"><i class="fas fa-inbox"></i></div>
                <div class="stats-number">{{ number_format($pendingRequests ?? 0) }}</div>
                <div class="stats-label">Pending Requests</div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card card-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">Total Stock Value</div>
                        <div class="stats-number h4 mb-0">TZS {{ number_format($totalStockValue ?? 0, 2) }}</div>
                    </div>
                    <i class="fas fa-coins fa-2x text-info"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card card-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">Critical Stock Items</div>
                        <div class="stats-number h4 mb-0">{{ number_format($criticalStockItems ?? 0) }}</div>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card card-danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">Out of Stock Items</div>
                        <div class="stats-number h4 mb-0">{{ number_format($outOfStockItems ?? 0) }}</div>
                    </div>
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Stats & Charts -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Monthly Activity</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-success mb-1">+{{ number_format($monthlyIntakes ?? 0, 2) }}</h3>
                                <p class="text-muted mb-0">Stock Intakes</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-danger mb-1">-{{ number_format($monthlyIssuances ?? 0, 2) }}</h3>
                                <p class="text-muted mb-0">Stock Issuances</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-info mb-1">{{ number_format($monthlyDistributions ?? 0) }}</h3>
                            <p class="text-muted mb-0">Distributions</p>
                        </div>
                    </div>
                    <hr>
                    <canvas id="stockMovementChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Stock by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Low Stock Alerts -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alerts</h5>
                    <a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                    @forelse($lowStockItemsList ?? [] as $item)
                        <div class="stock-alert-item {{ $item->is_out_of_stock ? 'stock-alert-out' : ($item->is_critical_stock ? 'stock-alert-critical' : 'stock-alert-warning') }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item->name }}</strong>
                                    <small class="text-muted d-block">{{ $item->category?->name ?? 'Uncategorized' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $item->stock_status_color }}">{{ $item->quantity_available }} {{ $item->unit }}</span>
                                    <small class="text-muted d-block">Reorder: {{ $item->reorder_level }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-success py-4">
                            <i class="fas fa-check-circle fa-3x mb-2"></i>
                            <p class="mb-0">All stock levels are healthy!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Recent Transactions</h5>
                    <a href="{{ route('stock.transactions.index') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                    @forelse($recentTransactions ?? [] as $transaction)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $transaction->stockItem?->name ?? 'Unknown Item' }}</strong>
                                    <small class="text-muted d-block">
                                        {{ $transaction->type_label }} - {{ $transaction->reference_number }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $transaction->is_incoming ? 'success' : 'danger' }}">
                                        {{ $transaction->quantity_display }}
                                    </span>
                                    <small class="text-muted d-block">{{ $transaction->created_at?->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-2"></i>
                            <p class="mb-0">No recent transactions</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Recent Stock Requests</h5>
                    <a href="{{ route('stock.requests.index') }}" class="btn btn-sm btn-dark">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Requested By</th>
                                    <th>Farmer</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Needed By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentRequests ?? [] as $request)
                                    <tr>
                                        <td><strong>{{ $request->request_number }}</strong></td>
                                        <td>{{ $request->requestedBy?->name ?? 'N/A' }}</td>
                                        <td>{{ $request->farmer?->full_name ?? 'General' }}</td>
                                        <td><span class="badge bg-{{ $request->priority_color }}">{{ $request->priority_display }}</span></td>
                                        <td><span class="badge bg-{{ $request->status_color }}">{{ $request->status_display }}</span></td>
                                        <td>{{ $request->needed_by?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('stock.requests.show', $request) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No recent requests</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-warehouse me-2"></i>Inventory Management</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('stock.index') }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="fas fa-list me-1"></i> View Inventory
                    </a>
                    <a href="{{ route('stock.create') }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-plus me-1"></i> Add Stock Item
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-dolly me-2"></i>Distribution</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('stock.distributions.create') }}" class="btn btn-outline-success w-100 mb-2">
                        <i class="fas fa-seedling me-1"></i> New Distribution
                    </a>
                    <a href="{{ route('stock.distributions.index') }}" class="btn btn-outline-info w-100">
                        <i class="fas fa-hand-holding me-1"></i> View Distributions
                    </a>
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
                        <div class="col-md-3">
                            <a href="{{ route('stock.transactions.intake') }}" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-arrow-down me-1"></i> Record Intake
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('stock.transactions.issuance') }}" class="btn btn-outline-danger w-100 mb-2">
                                <i class="fas fa-arrow-up me-1"></i> Record Issuance
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('stock.requests.index') }}" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-check-circle me-1"></i> Manage Requests
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('stock.reports.summary') }}" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-chart-bar me-1"></i> Stock Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.getElementById('refreshBtn')?.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        setTimeout(() => location.reload(), 500);
    });

    // Stock by Category Chart
    const categoryData = @json($stockByCategory ?? []);
    if (categoryData.length > 0) {
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(c => c.name),
                datasets: [{
                    data: categoryData.map(c => c.total_items || 0),
                    backgroundColor: [
                        '#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6',
                        '#1abc9c', '#34495e', '#e67e22', '#95a5a6', '#d35400'
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 8 }
                    }
                }
            }
        });
    }

    // Monthly Movement Chart (placeholder - you can populate with actual monthly data)
    const movementCtx = document.getElementById('stockMovementChart').getContext('2d');
    new Chart(movementCtx, {
        type: 'bar',
        data: {
            labels: ['Intakes', 'Issuances', 'Distributions'],
            datasets: [{
                label: 'This Month',
                data: [{{ $monthlyIntakes ?? 0 }}, {{ $monthlyIssuances ?? 0 }}, {{ $monthlyDistributions ?? 0 }}],
                backgroundColor: ['#2ecc71', '#e74c3c', '#3498db'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
