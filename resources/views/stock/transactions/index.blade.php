@extends('layouts.base')

@section('title', 'Stock Transactions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-exchange-alt me-2"></i> Stock Transactions
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock.transactions.intake') }}" class="btn btn-success">
                                <i class="fas fa-arrow-down me-1"></i> Record Intake
                            </a>
                            <a href="{{ route('stock.transactions.issuance') }}" class="btn btn-warning">
                                <i class="fas fa-arrow-up me-1"></i> Record Issuance
                            </a>
                            <a href="{{ route('stock.index') }}" class="btn btn-light">
                                <i class="fas fa-boxes me-1"></i> Stock Inventory
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Intakes</h6>
                                            <h3 class="mb-0 text-success">{{ $transactions->where('transaction_type', 'intake')->count() }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-arrow-down fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Issuances</h6>
                                            <h3 class="mb-0 text-warning">{{ $transactions->where('transaction_type', 'issuance')->count() }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-arrow-up fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Distributions</h6>
                                            <h3 class="mb-0 text-info">{{ $transactions->where('transaction_type', 'distribution')->count() }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Value</h6>
                                            <h3 class="mb-0 text-primary">{{ number_format($transactions->sum('total_cost'), 2) }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-money-bill-wave fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('stock.transactions.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by reference..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="intake" {{ request('type') == 'intake' ? 'selected' : '' }}>Intake</option>
                                <option value="issuance" {{ request('type') == 'issuance' ? 'selected' : '' }}>Issuance</option>
                                <option value="distribution" {{ request('type') == 'distribution' ? 'selected' : '' }}>Distribution</option>
                                <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
                                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        @if(request('search') || request('type') || request('date_from') || request('date_to'))
                            <div class="col-md-1">
                                <a href="{{ route('stock.transactions.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Transactions Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Reference</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Stock Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Total</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $transaction->reference_number }}</strong>
                                        </td>
                                        <td>
                                            {{ $transaction->transaction_date ? $transaction->transaction_date->format('M d, Y') : $transaction->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $typeColors = [
                                                    'intake' => 'success',
                                                    'issuance' => 'warning',
                                                    'distribution' => 'info',
                                                    'return' => 'secondary',
                                                    'adjustment' => 'dark',
                                                    'transfer' => 'primary',
                                                ];
                                                $typeIcons = [
                                                    'intake' => 'arrow-down',
                                                    'issuance' => 'arrow-up',
                                                    'distribution' => 'users',
                                                    'return' => 'undo',
                                                    'adjustment' => 'sliders-h',
                                                    'transfer' => 'exchange-alt',
                                                ];
                                                $type = $transaction->transaction_type->value ?? $transaction->transaction_type;
                                            @endphp
                                            <span class="badge bg-{{ $typeColors[$type] ?? 'secondary' }}">
                                                <i class="fas fa-{{ $typeIcons[$type] ?? 'circle' }} me-1"></i>
                                                {{ ucfirst($type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($transaction->stockItem)
                                                <a href="{{ route('stock.show', $transaction->stockItem) }}">
                                                    {{ $transaction->stockItem->name }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $transaction->stockItem->code }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold {{ in_array($type, ['intake', 'return']) ? 'text-success' : 'text-danger' }}">
                                                {{ in_array($type, ['intake', 'return']) ? '+' : '-' }}{{ number_format($transaction->quantity, 2) }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $transaction->stockItem->unit ?? 'units' }}</small>
                                        </td>
                                        <td>{{ number_format($transaction->unit_cost, 2) }}</td>
                                        <td>
                                            <strong>{{ number_format($transaction->total_cost, 2) }}</strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">Before: {{ number_format($transaction->balance_before, 2) }}</small>
                                            <br>
                                            <strong>After: {{ number_format($transaction->balance_after, 2) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#transactionModal{{ $transaction->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Transaction Detail Modal -->
                                    <div class="modal fade" id="transactionModal{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-{{ $typeColors[$type] ?? 'secondary' }} text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-{{ $typeIcons[$type] ?? 'circle' }} me-2"></i>
                                                        Transaction Details
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <th width="40%">Reference:</th>
                                                            <td>{{ $transaction->reference_number }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Type:</th>
                                                            <td><span class="badge bg-{{ $typeColors[$type] ?? 'secondary' }}">{{ ucfirst($type) }}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Stock Item:</th>
                                                            <td>{{ $transaction->stockItem->name ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Quantity:</th>
                                                            <td>{{ number_format($transaction->quantity, 2) }} {{ $transaction->stockItem->unit ?? 'units' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Unit Cost:</th>
                                                            <td>{{ number_format($transaction->unit_cost, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Total Cost:</th>
                                                            <td><strong>{{ number_format($transaction->total_cost, 2) }}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Balance Before:</th>
                                                            <td>{{ number_format($transaction->balance_before, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Balance After:</th>
                                                            <td>{{ number_format($transaction->balance_after, 2) }}</td>
                                                        </tr>
                                                        @if($transaction->source)
                                                            <tr>
                                                                <th>Source:</th>
                                                                <td>{{ $transaction->source }}</td>
                                                            </tr>
                                                        @endif
                                                        @if($transaction->destination)
                                                            <tr>
                                                                <th>Destination:</th>
                                                                <td>{{ $transaction->destination }}</td>
                                                            </tr>
                                                        @endif
                                                        @if($transaction->farmer)
                                                            <tr>
                                                                <th>Farmer:</th>
                                                                <td>{{ $transaction->farmer->full_name ?? 'N/A' }}</td>
                                                            </tr>
                                                        @endif
                                                        @if($transaction->notes)
                                                            <tr>
                                                                <th>Notes:</th>
                                                                <td>{{ $transaction->notes }}</td>
                                                            </tr>
                                                        @endif
                                                        <tr>
                                                            <th>Date:</th>
                                                            <td>{{ $transaction->transaction_date ? $transaction->transaction_date->format('M d, Y H:i') : $transaction->created_at->format('M d, Y H:i') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Status:</th>
                                                            <td><span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($transaction->status) }}</span></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-exchange-alt fa-3x mb-3"></i>
                                                <h5>No transactions found</h5>
                                                <p>Start by recording a stock intake or issuance</p>
                                                <div class="btn-group">
                                                    <a href="{{ route('stock.transactions.intake') }}" class="btn btn-success">
                                                        <i class="fas fa-arrow-down me-1"></i> Record Intake
                                                    </a>
                                                    <a href="{{ route('stock.transactions.issuance') }}" class="btn btn-warning">
                                                        <i class="fas fa-arrow-up me-1"></i> Record Issuance
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($transactions->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
                                </div>
                                <div>
                                    {{ $transactions->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
