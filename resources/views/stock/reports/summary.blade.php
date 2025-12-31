@extends('layouts.base')

@section('title', 'Stock Summary Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Stock Summary Report
                    </h1>
                    <p class="text-muted mb-0">Generated: {{ now()->format('M d, Y H:i') }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('stock.reports.movements') }}" class="btn btn-outline-primary">
                        <i class="fas fa-exchange-alt me-1"></i> Movements
                    </a>
                    <a href="{{ route('stock.reports.valuation') }}" class="btn btn-outline-primary">
                        <i class="fas fa-money-bill-wave me-1"></i> Valuation
                    </a>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('stock.export.summary') }}">
                                    <i class="fas fa-file-csv me-2"></i> Export as CSV
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i> Print Report
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Items</h6>
                                    <h2 class="mb-0">{{ $stats['total_items'] }}</h2>
                                </div>
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-boxes fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Value</h6>
                                    <h4 class="mb-0">TZS {{ number_format($stats['total_value'], 2) }}</h4>
                                </div>
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Low Stock</h6>
                                    <h2 class="mb-0 text-warning">{{ $stats['low_stock'] }}</h2>
                                </div>
                                <div class="bg-warning text-white rounded-circle p-3">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Out of Stock</h6>
                                    <h2 class="mb-0 text-danger">{{ $stats['out_of_stock'] }}</h2>
                                </div>
                                <div class="bg-danger text-white rounded-circle p-3">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Stock by Category -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-layer-group me-2"></i>
                                Stock by Category
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-center">Items</th>
                                            <th class="text-end">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categories as $category)
                                            @php
                                                $categoryItems = $stockItems->where('category_id', $category->id);
                                                $categoryValue = $categoryItems->sum('stock_value');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary me-2">{{ $category->code }}</span>
                                                    {{ $category->name }}
                                                </td>
                                                <td class="text-center">{{ $categoryItems->count() }}</td>
                                                <td class="text-end">TZS {{ number_format($categoryValue, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        @php
                                            $uncategorized = $stockItems->whereNull('category_id');
                                        @endphp
                                        @if($uncategorized->count() > 0)
                                            <tr>
                                                <td><em class="text-muted">Uncategorized</em></td>
                                                <td class="text-center">{{ $uncategorized->count() }}</td>
                                                <td class="text-end">TZS {{ number_format($uncategorized->sum('stock_value'), 2) }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            <td class="text-center">{{ $stockItems->count() }}</td>
                                            <td class="text-end">TZS {{ number_format($stats['total_value'], 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Status Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>
                                Stock Status Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $inStock = $stockItems->filter(fn($i) => !$i->is_low_stock && !$i->is_out_of_stock)->count();
                                $lowStock = $stockItems->filter(fn($i) => $i->is_low_stock && !$i->is_critical_stock)->count();
                                $criticalStock = $stockItems->filter(fn($i) => $i->is_critical_stock && !$i->is_out_of_stock)->count();
                                $outOfStock = $stockItems->filter(fn($i) => $i->is_out_of_stock)->count();
                                $total = $stockItems->count();
                            @endphp

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><i class="fas fa-circle text-success me-2"></i> In Stock</span>
                                    <span>{{ $inStock }} ({{ $total > 0 ? number_format(($inStock / $total) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar bg-success" style="width: {{ $total > 0 ? ($inStock / $total) * 100 : 0 }}%;">
                                        {{ $inStock }}
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span><i class="fas fa-circle text-warning me-2"></i> Low Stock</span>
                                    <span>{{ $lowStock }} ({{ $total > 0 ? number_format(($lowStock / $total) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $total > 0 ? ($lowStock / $total) * 100 : 0 }}%;">
                                        {{ $lowStock }}
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span><i class="fas fa-circle text-danger me-2"></i> Critical</span>
                                    <span>{{ $criticalStock }} ({{ $total > 0 ? number_format(($criticalStock / $total) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $total > 0 ? ($criticalStock / $total) * 100 : 0 }}%;">
                                        {{ $criticalStock }}
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span><i class="fas fa-circle text-dark me-2"></i> Out of Stock</span>
                                    <span>{{ $outOfStock }} ({{ $total > 0 ? number_format(($outOfStock / $total) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-dark" style="width: {{ $total > 0 ? ($outOfStock / $total) * 100 : 0 }}%;">
                                        {{ $outOfStock }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Items by Value -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>
                        Top 10 Items by Stock Value
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end">Stock Value</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockItems->sortByDesc('stock_value')->take(10) as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong class="text-primary">{{ $item->code }}</strong></td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            @if($item->category)
                                                <span class="badge bg-secondary">{{ $item->category->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($item->quantity_on_hand, 2) }} {{ $item->unit }}</td>
                                        <td class="text-end">TZS {{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="text-end"><strong>TZS {{ number_format($item->stock_value, 2) }}</strong></td>
                                        <td class="text-end">
                                            {{ $stats['total_value'] > 0 ? number_format(($item->stock_value / $stats['total_value']) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .btn-group {
        display: none !important;
    }
    .card {
        break-inside: avoid;
    }
}
</style>
@endsection
