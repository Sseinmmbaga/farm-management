@extends('layouts.base')

@section('title', 'Stock Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        Stock Report
                    </h1>
                    <p class="text-muted mb-0">Overview of inventory and stock movements</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.stock.inventory') }}" class="btn btn-outline-primary">
                        <i class="fas fa-box me-1"></i> Inventory
                    </a>
                    <a href="{{ route('reports.stock.movements') }}" class="btn btn-outline-primary">
                        <i class="fas fa-exchange-alt me-1"></i> Movements
                    </a>
                    <a href="{{ route('reports.stock.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
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
                                    <h2 class="mb-0">{{ $totalItems }}</h2>
                                </div>
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-box fa-2x"></i>
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
                                    <h4 class="mb-0">TZS {{ number_format($totalValue, 2) }}</h4>
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
                                    <h2 class="mb-0 text-warning">{{ $lowStockCount }}</h2>
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
                                    <h2 class="mb-0 text-danger">{{ $outOfStockCount }}</h2>
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
                                Stock by Category (Value)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-end">Quantity</th>
                                            <th class="text-end">Value (TZS)</th>
                                            <th class="text-end">% of Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stockByCategory as $category)
                                            @php
                                                $percentage = $totalValue > 0 ? round(($category->total_value / $totalValue) * 100, 1) : 0;
                                            @endphp
                                            <tr>
                                                <td>{{ $category->name }}</td>
                                                <td class="text-end">{{ number_format($category->total_quantity, 2) }}</td>
                                                <td class="text-end">{{ number_format($category->total_value, 2) }}</td>
                                                <td class="text-end">{{ $percentage }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                Recent Stock Transactions
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-end">Quantity</th>
                                            <th>Type</th>
                                            <th class="text-end">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTransactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->item?->display_name }}</td>
                                                <td class="text-end">{{ number_format($transaction->quantity, 2) }} {{ $transaction->item?->unit }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $transaction->transaction_type == 'intake' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($transaction->transaction_type) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">{{ $transaction->transaction_date?->format('M d, Y') }}</td>
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
    </div>
</div>
@endsection