@extends('layouts.base')

@section('title', 'Payments')

@push('styles')
<style>
    .filter-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .stats-row {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-item {
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        flex: 1;
        text-align: center;
        border-left: 4px solid;
    }
    .stat-item.pending { border-left-color: #f39c12; }
    .stat-item.processing { border-left-color: #3498db; }
    .stat-item.completed { border-left-color: #2ecc71; }
    .stat-item.failed { border-left-color: #e74c3c; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .data-table {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Payments</h1>
            <p class="text-muted mb-0">Manage farmer payments and disbursements</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.accountant') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Payment
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item pending">
            <div class="number text-warning">{{ $stats['pending'] }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-item processing">
            <div class="number text-primary">{{ $stats['processing'] }}</div>
            <div class="label">Processing</div>
        </div>
        <div class="stat-item completed">
            <div class="number text-success">{{ $stats['completed'] }}</div>
            <div class="label">Completed</div>
        </div>
        <div class="stat-item failed">
            <div class="number text-danger">{{ $stats['failed'] }}</div>
            <div class="label">Failed</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.accountant.payments') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Payment Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="harvest" {{ request('type') === 'harvest' ? 'selected' : '' }}>Harvest Payment</option>
                    <option value="bonus" {{ request('type') === 'bonus' ? 'selected' : '' }}>Bonus</option>
                    <option value="advance" {{ request('type') === 'advance' ? 'selected' : '' }}>Advance</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="data-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Farmer</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td><strong>#{{ $payment->id ?? 'N/A' }}</strong></td>
                        <td>{{ $payment->farmer->full_name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($payment->type ?? 'N/A') }}</td>
                        <td>{{ number_format($payment->amount ?? 0, 2) }}</td>
                        <td>{{ $payment->created_at ? $payment->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            @switch($payment->status ?? 'pending')
                                @case('pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @break
                                @case('processing')
                                    <span class="badge bg-info">Processing</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Completed</span>
                                    @break
                                @case('failed')
                                    <span class="badge bg-danger">Failed</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $payment->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(($payment->status ?? 'pending') === 'pending')
                            <a href="#" class="btn btn-sm btn-outline-success" title="Approve">
                                <i class="fas fa-check"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No payments found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
        <div class="p-3 border-top">
            {{ $payments->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
