@extends('layouts.base')

@section('title', 'Stock Movements Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Stock Movements Report
                    </h1>
                    <p class="text-muted mb-0">Track all stock movements over time</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('stock.reports.summary') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar me-1"></i> Summary
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
                                <a class="dropdown-item" href="{{ route('stock.export.movements', request()->query()) }}">
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

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('stock.reports.movements') }}" class="row g-3">
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="intake" {{ request('type') == 'intake' ? 'selected' : '' }}>Intake</option>
                                <option value="issuance" {{ request('type') == 'issuance' ? 'selected' : '' }}>Issuance</option>
                                <option value="distribution" {{ request('type') == 'distribution' ? 'selected' : '' }}>Distribution</option>
                                <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
                                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="stock_item_id" class="form-label">Stock Item</label>
                            <select name="stock_item_id" id="stock_item_id" class="form-select">
                                <option value="">All Items</option>
                                @foreach($stockItems as $item)
                                    <option value="{{ $item->id }}" {{ request('stock_item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} ({{ $item->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if(request()->hasAny(['date_from', 'date_to', 'type', 'stock_item_id']))
                            <div class="col-md-1 d-flex align-items-end">
                                <a href="{{ route('stock.reports.movements') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="row mb-4">
                @php
                    $intakes = $transactions->where('transaction_type', 'intake');
                    $issuances = $transactions->where('transaction_type', 'issuance');
                    $distributions = $transactions->where('transaction_type', 'distribution');
                @endphp
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-down text-success fa-2x mb-2"></i>
                            <h3 class="mb-0 text-success">{{ $intakes->count() }}</h3>
                            <small class="text-muted">Intakes</small>
                            <hr class="my-2">
                            <small class="text-success">+{{ number_format($intakes->sum('quantity'), 2) }} units</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-up text-warning fa-2x mb-2"></i>
                            <h3 class="mb-0 text-warning">{{ $issuances->count() }}</h3>
                            <small class="text-muted">Issuances</small>
                            <hr class="my-2">
                            <small class="text-warning">-{{ number_format($issuances->sum('quantity'), 2) }} units</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-users text-info fa-2x mb-2"></i>
                            <h3 class="mb-0 text-info">{{ $distributions->count() }}</h3>
                            <small class="text-muted">Distributions</small>
                            <hr class="my-2">
                            <small class="text-info">-{{ number_format($distributions->sum('quantity'), 2) }} units</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-money-bill-wave text-primary fa-2x mb-2"></i>
                            <h3 class="mb-0 text-primary">{{ $transactions->count() }}</h3>
                            <small class="text-muted">Total Transactions</small>
                            <hr class="my-2">
                            <small class="text-primary">TZS {{ number_format($transactions->sum('total_cost'), 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Transaction Details
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Type</th>
                                    <th>Stock Item</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Balance Before</th>
                                    <th class="text-end">Balance After</th>
                                    <th class="text-end">Value</th>
                                    <th>Farmer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    @php
                                        $type = $transaction->transaction_type->value ?? $transaction->transaction_type;
                                        $typeColors = [
                                            'intake' => 'success',
                                            'issuance' => 'warning',
                                            'distribution' => 'info',
                                            'return' => 'secondary',
                                            'adjustment' => 'dark',
                                        ];
                                        $isIncoming = in_array($type, ['intake', 'return']);
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $transaction->transaction_date ? $transaction->transaction_date->format('M d, Y') : $transaction->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                        </td>
                                        <td><code>{{ $transaction->reference_number }}</code></td>
                                        <td>
                                            <span class="badge bg-{{ $typeColors[$type] ?? 'secondary' }}">
                                                {{ ucfirst($type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($transaction->stockItem)
                                                {{ $transaction->stockItem->name }}
                                                <br>
                                                <small class="text-muted">{{ $transaction->stockItem->code }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="{{ $isIncoming ? 'text-success' : 'text-danger' }} fw-bold">
                                                {{ $isIncoming ? '+' : '-' }}{{ number_format($transaction->quantity, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($transaction->balance_before, 2) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($transaction->balance_after, 2) }}</td>
                                        <td class="text-end">TZS {{ number_format($transaction->total_cost, 2) }}</td>
                                        <td>
                                            @if($transaction->farmer)
                                                {{ $transaction->farmer->first_name }} {{ $transaction->farmer->last_name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <h5>No transactions found</h5>
                                                <p class="mb-0">Try adjusting your filters or date range</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($transactions->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
                                </div>
                                <div>
                                    {{ $transactions->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .btn-group, form {
        display: none !important;
    }
    .table-sm td, .table-sm th {
        padding: 0.25rem;
        font-size: 0.8rem;
    }
}
</style>
@endsection
