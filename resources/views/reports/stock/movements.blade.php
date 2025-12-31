@extends('layouts.base')

@section('title', 'Stock Movements Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Stock Movements Report
                    </h1>
                    <p class="text-muted mb-0">Track all stock transactions and movements</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.stock.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Overview
                    </a>
                    <a href="{{ route('reports.stock.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
                </div>
            </div>

            <!-- Summary by Type -->
            <div class="row mb-4">
                @foreach($summaryByType as $type => $quantity)
                    @php
                        $typeColors = [
                            'intake' => 'success',
                            'issue' => 'warning',
                            'adjustment' => 'info',
                            'transfer' => 'primary',
                            'return' => 'secondary',
                            'disposal' => 'danger'
                        ];
                        $typeIcons = [
                            'intake' => 'arrow-down',
                            'issue' => 'arrow-up',
                            'adjustment' => 'sliders-h',
                            'transfer' => 'exchange-alt',
                            'return' => 'undo',
                            'disposal' => 'trash'
                        ];
                    @endphp
                    <div class="col-md-2 col-sm-4 mb-3">
                        <div class="card border-{{ $typeColors[$type] ?? 'secondary' }} h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-{{ $typeIcons[$type] ?? 'circle' }} fa-2x text-{{ $typeColors[$type] ?? 'secondary' }} mb-2"></i>
                                <h4 class="mb-1">{{ number_format($quantity, 2) }}</h4>
                                <small class="text-muted">{{ ucfirst($type) }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Monthly Summary Chart -->
            @if($monthlySummary->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Monthly Movement Summary (Last 12 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="movementChart" height="100"></canvas>
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.stock.movements') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="form-label">Transaction Type</label>
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="intake" {{ request('type') === 'intake' ? 'selected' : '' }}>Intake</option>
                                    <option value="issue" {{ request('type') === 'issue' ? 'selected' : '' }}>Issue</option>
                                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>Return</option>
                                    <option value="disposal" {{ request('type') === 'disposal' ? 'selected' : '' }}>Disposal</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('reports.stock.movements') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Movements Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Stock Movements ({{ $movements->total() }} total)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Transaction #</th>
                                    <th>Item</th>
                                    <th>Batch</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-end">Quantity</th>
                                    <th>Reference</th>
                                    <th>User</th>
                                    <th class="text-end">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $movement)
                                    @php
                                        $typeColors = [
                                            'intake' => 'success',
                                            'issue' => 'warning',
                                            'adjustment' => 'info',
                                            'transfer' => 'primary',
                                            'return' => 'secondary',
                                            'disposal' => 'danger'
                                        ];
                                    @endphp
                                    <tr>
                                        <td><code>{{ $movement->transaction_number ?? 'N/A' }}</code></td>
                                        <td>{{ $movement->item?->name ?? 'N/A' }}</td>
                                        <td>{{ $movement->batch?->batch_number ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $typeColors[$movement->transaction_type] ?? 'secondary' }}">
                                                {{ ucfirst($movement->transaction_type ?? 'Unknown') }}
                                            </span>
                                        </td>
                                        <td class="text-end {{ in_array($movement->transaction_type, ['intake', 'return']) ? 'text-success' : 'text-danger' }}">
                                            {{ in_array($movement->transaction_type, ['intake', 'return']) ? '+' : '-' }}{{ number_format($movement->quantity, 2) }} {{ $movement->item?->unit }}
                                        </td>
                                        <td>{{ $movement->reference ?? '-' }}</td>
                                        <td>{{ $movement->user?->name ?? 'System' }}</td>
                                        <td class="text-end">{{ $movement->transaction_date?->format('M d, Y H:i') ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-exchange-alt fa-3x mb-3 d-block"></i>
                                            No stock movements found matching your criteria
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($movements->hasPages())
                <div class="card-footer">
                    {{ $movements->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($monthlySummary->isNotEmpty())
    const movementCtx = document.getElementById('movementChart').getContext('2d');
    const monthlyData = @json($monthlySummary);

    // Prepare data for chart
    const months = Object.keys(monthlyData);
    const types = ['intake', 'issue', 'adjustment', 'transfer', 'return', 'disposal'];
    const typeColors = {
        'intake': '#198754',
        'issue': '#ffc107',
        'adjustment': '#0dcaf0',
        'transfer': '#0d6efd',
        'return': '#6c757d',
        'disposal': '#dc3545'
    };

    const datasets = types.map(type => {
        return {
            label: type.charAt(0).toUpperCase() + type.slice(1),
            data: months.map(month => {
                const monthData = monthlyData[month];
                const typeData = monthData.find(d => d.transaction_type === type);
                return typeData ? typeData.total_quantity : 0;
            }),
            backgroundColor: typeColors[type],
            borderColor: typeColors[type],
            borderWidth: 1
        };
    }).filter(ds => ds.data.some(v => v > 0)); // Only show types with data

    new Chart(movementCtx, {
        type: 'bar',
        data: {
            labels: months.map(key => {
                const [year, month] = key.split('-');
                const date = new Date(year, month - 1);
                return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
            }),
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });
    @endif
</script>
@endpush
