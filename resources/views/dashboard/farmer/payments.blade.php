@extends('layouts.base')

@section('title', 'My Payments')

@push('styles')
<style>
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
    .stat-item.received { border-left-color: #2ecc71; }
    .stat-item.total { border-left-color: #3498db; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .payment-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid;
    }
    .payment-card.completed { border-left-color: #2ecc71; }
    .payment-card.pending { border-left-color: #f39c12; }
    .payment-card.processing { border-left-color: #3498db; }
    .payment-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
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
            <h1 class="h3 mb-0">My Payments</h1>
            <p class="text-muted mb-0">View your payment history and pending payments</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item pending">
            <div class="number text-warning">{{ $stats['pending'] }}</div>
            <div class="label">Pending Payments</div>
        </div>
        <div class="stat-item received">
            <div class="number text-success">{{ $stats['received_this_year'] }}</div>
            <div class="label">Received This Year</div>
        </div>
        <div class="stat-item total">
            <div class="number text-primary">{{ number_format($stats['total_amount'], 2) }}</div>
            <div class="label">Total Amount (TZS)</div>
        </div>
    </div>

    <!-- Pending Payments Section -->
    @if($payments->where('status', 'pending')->count() > 0)
    <div class="mb-4">
        <h5 class="mb-3"><i class="fas fa-clock text-warning me-2"></i>Pending Payments</h5>
        @foreach($payments->where('status', 'pending') as $payment)
        <div class="payment-card pending">
            <div class="d-flex align-items-center">
                <div class="payment-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ ucfirst($payment->type ?? 'Payment') }}</h6>
                            <p class="mb-0 text-muted small">
                                @if($payment->description)
                                {{ $payment->description }}
                                @else
                                Payment for {{ $payment->season ?? 'current season' }}
                                @endif
                            </p>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0 text-warning">TZS {{ number_format($payment->amount ?? 0, 2) }}</h5>
                            <small class="text-muted">{{ $payment->created_at ? $payment->created_at->diffForHumans() : '' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Payment History -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Payment History</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td><strong>#{{ $payment->id ?? 'N/A' }}</strong></td>
                        <td>{{ ucfirst($payment->type ?? 'N/A') }}</td>
                        <td>TZS {{ number_format($payment->amount ?? 0, 2) }}</td>
                        <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : ($payment->created_at ? $payment->created_at->format('M d, Y') : 'N/A') }}</td>
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
                        <td>{{ $payment->reference ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No payment records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
        <div class="card-footer">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
@endsection
