@extends('layouts.base')

@section('title', 'Stock Valuation Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Stock Valuation Report
                    </h1>
                    <p class="text-muted mb-0">Generated: {{ now()->format('M d, Y H:i') }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('stock.reports.summary') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar me-1"></i> Summary
                    </a>
                    <a href="{{ route('stock.reports.movements') }}" class="btn btn-outline-primary">
                        <i class="fas fa-exchange-alt me-1"></i> Movements
                    </a>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('stock.export.valuation') }}">
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

            <!-- Total Valuation -->
            <div class="card mb-4 bg-success text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-0">Total Stock Valuation</h4>
                            <p class="mb-0 opacity-75">Based on current quantities and unit costs</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <h1 class="mb-0">TZS {{ number_format($totalValuation, 2) }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Valuation by Category -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        Valuation by Category
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Total Quantity</th>
                                    <th class="text-end">Total Value</th>
                                    <th class="text-end">% of Total</th>
                                    <th style="width: 200px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    @php
                                        $categoryItems = $stockItems->where('category_id', $category->id);
                                        $categoryValue = $categoryItems->sum('stock_value');
                                        $categoryQty = $categoryItems->sum('quantity_on_hand');
                                        $percentage = $totalValuation > 0 ? ($categoryValue / $totalValuation) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary me-2">{{ $category->code }}</span>
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td class="text-center">{{ $categoryItems->count() }}</td>
                                        <td class="text-end">{{ number_format($categoryQty, 2) }}</td>
                                        <td class="text-end"><strong>TZS {{ number_format($categoryValue, 2) }}</strong></td>
                                        <td class="text-end">{{ number_format($percentage, 1) }}%</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" style="width: {{ $percentage }}%;">
                                                    {{ number_format($percentage, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @php
                                    $uncategorized = $stockItems->whereNull('category_id');
                                    $uncatValue = $uncategorized->sum('stock_value');
                                    $uncatPercentage = $totalValuation > 0 ? ($uncatValue / $totalValuation) * 100 : 0;
                                @endphp
                                @if($uncategorized->count() > 0)
                                    <tr>
                                        <td><em class="text-muted">Uncategorized</em></td>
                                        <td class="text-center">{{ $uncategorized->count() }}</td>
                                        <td class="text-end">{{ number_format($uncategorized->sum('quantity_on_hand'), 2) }}</td>
                                        <td class="text-end"><strong>TZS {{ number_format($uncatValue, 2) }}</strong></td>
                                        <td class="text-end">{{ number_format($uncatPercentage, 1) }}%</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-secondary" style="width: {{ $uncatPercentage }}%;">
                                                    {{ number_format($uncatPercentage, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-success">
                                <tr class="fw-bold">
                                    <td>TOTAL</td>
                                    <td class="text-center">{{ $stockItems->count() }}</td>
                                    <td class="text-end">{{ number_format($stockItems->sum('quantity_on_hand'), 2) }}</td>
                                    <td class="text-end">TZS {{ number_format($totalValuation, 2) }}</td>
                                    <td class="text-end">100%</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detailed Valuation -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt me-2"></i>
                        Detailed Stock Valuation
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th class="text-end">On Hand</th>
                                    <th class="text-end">Reserved</th>
                                    <th class="text-end">Available</th>
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end">Stock Value</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentCategory = null; @endphp
                                @foreach($stockItems->sortBy('category_id') as $item)
                                    @if($item->category_id !== $currentCategory)
                                        @php $currentCategory = $item->category_id; @endphp
                                        <tr class="table-secondary">
                                            <td colspan="9" class="fw-bold">
                                                <i class="fas fa-folder me-2"></i>
                                                {{ $item->category ? $item->category->name : 'Uncategorized' }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><strong class="text-primary">{{ $item->code }}</strong></td>
                                        <td>
                                            {{ $item->name }}
                                            @if($item->sku)
                                                <br><small class="text-muted">SKU: {{ $item->sku }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->category)
                                                <span class="badge bg-secondary">{{ $item->category->code }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($item->quantity_on_hand, 2) }} {{ $item->unit }}</td>
                                        <td class="text-end">{{ number_format($item->quantity_reserved, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->quantity_available, 2) }}</td>
                                        <td class="text-end">TZS {{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="text-end"><strong>TZS {{ number_format($item->stock_value, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $item->stock_status_color }}">
                                                {{ $item->stock_status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-success">
                                <tr class="fw-bold">
                                    <td colspan="7" class="text-end">TOTAL VALUATION:</td>
                                    <td class="text-end">TZS {{ number_format($totalValuation, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Report Footer -->
            <div class="card mt-4 d-print-block">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Report Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Generated On:</th>
                                    <td>{{ now()->format('F d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Generated By:</th>
                                    <td>{{ auth()->user()->name ?? 'System' }}</td>
                                </tr>
                                <tr>
                                    <th>Report Type:</th>
                                    <td>Stock Valuation Report</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6>Summary</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th>Total Items:</th>
                                    <td>{{ $stockItems->count() }}</td>
                                </tr>
                                <tr>
                                    <th>Total Categories:</th>
                                    <td>{{ $categories->count() }}</td>
                                </tr>
                                <tr>
                                    <th>Total Valuation:</th>
                                    <td><strong class="text-success">TZS {{ number_format($totalValuation, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
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
        border: 1px solid #ddd !important;
    }
    .table-sm td, .table-sm th {
        padding: 0.25rem;
        font-size: 0.75rem;
    }
    .bg-success {
        background-color: #198754 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
@endsection
