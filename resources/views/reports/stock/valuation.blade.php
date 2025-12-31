@extends('layouts.base')

@section('title', 'Stock Valuation Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-coins me-2"></i>
                        Stock Valuation Report
                    </h1>
                    <p class="text-muted mb-0">Inventory valuation and analysis</p>
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

            <!-- Total Valuation -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center py-4">
                            <h6 class="text-white-50 mb-2">Total Inventory Valuation</h6>
                            <h1 class="display-4 mb-0">TZS {{ number_format($totalValuation, 2) }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Valuation by Organic Status -->
            <div class="row mb-4">
                @foreach($valuationByOrganic as $item)
                    <div class="col-md-6 mb-3">
                        <div class="card border-{{ $item->is_organic_approved ? 'success' : 'secondary' }} h-100">
                            <div class="card-header bg-{{ $item->is_organic_approved ? 'success' : 'secondary' }} text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-{{ $item->is_organic_approved ? 'leaf' : 'flask' }} me-2"></i>
                                    {{ $item->is_organic_approved ? 'Organic Approved' : 'Conventional' }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h4 class="mb-1">{{ number_format($item->item_count) }}</h4>
                                        <small class="text-muted">Items</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="mb-1">{{ number_format($item->total_quantity, 2) }}</h4>
                                        <small class="text-muted">Total Qty</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="mb-1">TZS {{ number_format($item->total_value, 0) }}</h4>
                                        <small class="text-muted">Value</small>
                                    </div>
                                </div>
                                @php
                                    $percentage = $totalValuation > 0 ? round(($item->total_value / $totalValuation) * 100, 1) : 0;
                                @endphp
                                <div class="progress mt-3" style="height: 10px;">
                                    <div class="progress-bar bg-{{ $item->is_organic_approved ? 'success' : 'secondary' }}" style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ $percentage }}% of total value</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <!-- Valuation by Category -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Valuation by Category</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-end">Quantity</th>
                                            <th class="text-end">Value (TZS)</th>
                                            <th class="text-end">% of Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($valuationByCategory as $category)
                                            @php
                                                $percentage = $totalValuation > 0 ? round(($category->total_value / $totalValuation) * 100, 1) : 0;
                                            @endphp
                                            <tr>
                                                <td>{{ $category->name }}</td>
                                                <td class="text-end">{{ number_format($category->total_quantity, 2) }}</td>
                                                <td class="text-end">{{ number_format($category->total_value, 2) }}</td>
                                                <td class="text-end">
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <span class="me-2">{{ $percentage }}%</span>
                                                        <div class="progress" style="width: 60px; height: 6px;">
                                                            <div class="progress-bar bg-info" style="width: {{ $percentage }}%"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">No categories found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-end">{{ number_format($valuationByCategory->sum('total_quantity'), 2) }}</th>
                                            <th class="text-end">{{ number_format($totalValuation, 2) }}</th>
                                            <th class="text-end">100%</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Value Items -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 Highest Value Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th>Category</th>
                                            <th class="text-end">Value (TZS)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topValueItems as $index => $item)
                                            <tr>
                                                <td>
                                                    @if($index < 3)
                                                        <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'danger') }}">
                                                            {{ $index + 1 }}
                                                        </span>
                                                    @else
                                                        {{ $index + 1 }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $item->name }}</strong>
                                                    <br><small class="text-muted">{{ number_format($item->quantity_on_hand, 2) }} {{ $item->unit }}</small>
                                                </td>
                                                <td>{{ $item->category?->name ?? 'N/A' }}</td>
                                                <td class="text-end">{{ number_format($item->item_value, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">No items found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aging Inventory -->
            @if($agingItems->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Aging Inventory (No Movement in 6+ Months)</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        These items have had no stock transactions in the last 6 months and may require attention.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th class="text-end">Quantity on Hand</th>
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end">Stock Value (TZS)</th>
                                    <th class="text-center">Organic</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agingItems as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->name }}</strong>
                                            @if($item->code)
                                                <br><small class="text-muted">{{ $item->code }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->category?->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($item->quantity_on_hand, 2) }} {{ $item->unit }}</td>
                                        <td class="text-end">{{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->quantity_on_hand * $item->unit_cost, 2) }}</td>
                                        <td class="text-center">
                                            @if($item->is_organic_approved)
                                                <span class="badge bg-success"><i class="fas fa-leaf"></i></span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Valuation Chart -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Valuation Distribution by Category</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="valuationChart" height="300"></canvas>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="w-100">
                                @foreach($valuationByCategory->take(6) as $index => $category)
                                    @php
                                        $colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d'];
                                        $percentage = $totalValuation > 0 ? round(($category->total_value / $totalValuation) * 100, 1) : 0;
                                    @endphp
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle me-3" style="width: 16px; height: 16px; background-color: {{ $colors[$index % count($colors)] }};"></div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <span>{{ $category->name }}</span>
                                                <strong>{{ $percentage }}%</strong>
                                            </div>
                                            <small class="text-muted">TZS {{ number_format($category->total_value, 0) }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
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
    const valuationCtx = document.getElementById('valuationChart').getContext('2d');
    const categoryData = @json($valuationByCategory->take(6));
    const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d'];

    new Chart(valuationCtx, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(c => c.name),
            datasets: [{
                data: categoryData.map(c => c.total_value),
                backgroundColor: colors.slice(0, categoryData.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '60%'
        }
    });
</script>
@endpush
