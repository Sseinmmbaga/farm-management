@extends('layouts.base')

@section('title', 'Farmer Distributions - ' . $farmer->first_name . ' ' . $farmer->last_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('stock.index') }}">Stock Inventory</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stock.distributions.index') }}">Distributions</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Farmer History</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user text-primary me-2"></i>
                        {{ $farmer->first_name }} {{ $farmer->last_name }}
                    </h1>
                    <p class="text-muted mb-0">
                        Registration: {{ $farmer->registration_number ?? 'N/A' }}
                        @if($farmer->phone) | Phone: {{ $farmer->phone }} @endif
                    </p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('stock.distributions.create') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i> New Distribution
                    </a>
                    <a href="{{ route('stock.distributions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> All Distributions
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Distribution History -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>
                                    Distribution History
                                </h5>
                                <span class="badge bg-light text-dark">{{ $distributions->total() }} records</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Stock Item</th>
                                            <th>Quantity</th>
                                            <th>Type</th>
                                            <th>Value</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($distributions as $distribution)
                                            <tr>
                                                <td>
                                                    {{ $distribution->created_at->format('M d, Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $distribution->created_at->format('H:i') }}</small>
                                                </td>
                                                <td>
                                                    @if($distribution->transaction && $distribution->transaction->stockItem)
                                                        <a href="{{ route('stock.show', $distribution->transaction->stockItem) }}">
                                                            {{ $distribution->transaction->stockItem->name }}
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">{{ $distribution->transaction->stockItem->code }}</small>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ number_format($distribution->quantity, 2) }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $distribution->transaction->stockItem->unit ?? 'units' }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $typeColors = [
                                                            'credit' => 'warning',
                                                            'cash' => 'success',
                                                            'free' => 'info',
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $typeColors[$distribution->distribution_type] ?? 'secondary' }}">
                                                        {{ ucfirst($distribution->distribution_type) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong>TZS {{ number_format($distribution->value, 2) }}</strong>
                                                </td>
                                                <td>
                                                    @if($distribution->distribution_type === 'credit')
                                                        @if($distribution->is_repaid)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check me-1"></i> Repaid
                                                            </span>
                                                        @elseif($distribution->due_date && $distribution->due_date->isPast())
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-triangle me-1"></i> Overdue
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-clock me-1"></i> Pending
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('stock.distributions.show', $distribution) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                                        <h5>No distributions found</h5>
                                                        <p>This farmer has no distribution records yet</p>
                                                        <a href="{{ route('stock.distributions.create') }}" class="btn btn-success">
                                                            <i class="fas fa-plus-circle me-1"></i> Create First Distribution
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($distributions->hasPages())
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted">
                                            Showing {{ $distributions->firstItem() }} to {{ $distributions->lastItem() }} of {{ $distributions->total() }} distributions
                                        </div>
                                        <div>
                                            {{ $distributions->links() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary -->
                <div class="col-lg-4">
                    <!-- Farmer Summary -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>
                                Distribution Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $totalDistributions = $distributions->count();
                                $totalValue = $distributions->sum('value');
                                $creditDistributions = $distributions->where('distribution_type', 'credit');
                                $totalCredit = $creditDistributions->sum('value');
                                $totalRepaid = $creditDistributions->sum('amount_repaid');
                                $outstanding = $totalCredit - $totalRepaid;
                            @endphp

                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="display-6 text-primary">{{ $totalDistributions }}</div>
                                    <small class="text-muted">Total Distributions</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="display-6 text-info">{{ $distributions->where('distribution_type', 'credit')->count() }}</div>
                                    <small class="text-muted">On Credit</small>
                                </div>
                            </div>

                            <hr>

                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="fas fa-money-bill-wave text-primary me-2"></i> Total Value</span>
                                    <strong>TZS {{ number_format($totalValue, 2) }}</strong>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="fas fa-credit-card text-warning me-2"></i> Total Credit</span>
                                    <strong>TZS {{ number_format($totalCredit, 2) }}</strong>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="fas fa-check-circle text-success me-2"></i> Total Repaid</span>
                                    <strong class="text-success">TZS {{ number_format($totalRepaid, 2) }}</strong>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="fas fa-exclamation-circle text-danger me-2"></i> Outstanding</span>
                                    <strong class="text-danger">TZS {{ number_format($outstanding, 2) }}</strong>
                                </div>
                            </div>

                            @if($totalCredit > 0)
                                <hr>
                                <div class="progress mb-2" style="height: 25px;">
                                    @php
                                        $repaidPercentage = $totalCredit > 0 ? min(100, ($totalRepaid / $totalCredit) * 100) : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $repaidPercentage }}%;">
                                        {{ number_format($repaidPercentage, 1) }}% Repaid
                                    </div>
                                </div>
                                <small class="text-muted">Credit repayment progress</small>
                            @endif
                        </div>
                    </div>

                    <!-- Distribution by Type -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                By Type
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span><i class="fas fa-credit-card text-warning me-2"></i> Credit</span>
                                    <span>{{ $distributions->where('distribution_type', 'credit')->count() }}</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    @php $creditPct = $totalDistributions > 0 ? ($distributions->where('distribution_type', 'credit')->count() / $totalDistributions) * 100 : 0; @endphp
                                    <div class="progress-bar bg-warning" style="width: {{ $creditPct }}%;"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span><i class="fas fa-money-bill text-success me-2"></i> Cash</span>
                                    <span>{{ $distributions->where('distribution_type', 'cash')->count() }}</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    @php $cashPct = $totalDistributions > 0 ? ($distributions->where('distribution_type', 'cash')->count() / $totalDistributions) * 100 : 0; @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $cashPct }}%;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span><i class="fas fa-gift text-info me-2"></i> Free</span>
                                    <span>{{ $distributions->where('distribution_type', 'free')->count() }}</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    @php $freePct = $totalDistributions > 0 ? ($distributions->where('distribution_type', 'free')->count() / $totalDistributions) * 100 : 0; @endphp
                                    <div class="progress-bar bg-info" style="width: {{ $freePct }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('stock.distributions.create') }}?farmer_id={{ $farmer->id }}" class="btn btn-outline-success">
                                    <i class="fas fa-plus-circle me-2"></i> New Distribution
                                </a>
                                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i> Print History
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, nav, .breadcrumb {
        display: none !important;
    }
}
</style>
@endsection
