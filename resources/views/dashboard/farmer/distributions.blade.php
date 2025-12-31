@extends('layouts.base')

@section('title', 'My Distributions')

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
    .stat-item.total { border-left-color: #27ae60; }
    .stat-item.this-year { border-left-color: #3498db; }
    .stat-item.items { border-left-color: #f39c12; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .distribution-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #eee;
        transition: all 0.3s;
    }
    .distribution-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    .item-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">My Distributions</h1>
            <p class="text-muted mb-0">View items distributed to you</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-success">{{ $stats['total_distributions'] }}</div>
            <div class="label">Total Distributions</div>
        </div>
        <div class="stat-item this-year">
            <div class="number text-primary">{{ $stats['this_year'] }}</div>
            <div class="label">This Year</div>
        </div>
        <div class="stat-item items">
            <div class="number text-warning">{{ number_format($stats['items_received']) }}</div>
            <div class="label">Total Items Received</div>
        </div>
    </div>

    <!-- Distributions List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-box me-2"></i>Distribution History</h5>
        </div>
        <div class="card-body">
            @forelse($distributions as $distribution)
            <div class="distribution-card">
                <div class="d-flex align-items-center">
                    <div class="item-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $distribution->stockItem?->name ?? 'Item' }}</h6>
                        <p class="mb-0 text-muted small">
                            {{ $distribution->stockItem?->category?->name ?? 'Stock Item' }}
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success fs-6">{{ $distribution->quantity }} {{ $distribution->stockItem?->unit ?? 'units' }}</span>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-auto">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $distribution->distribution_date?->format('M d, Y') ?? 'N/A' }}
                        </small>
                    </div>
                    @if($distribution->distributedBy)
                    <div class="col-auto">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>
                            By: {{ $distribution->distributedBy->name }}
                        </small>
                    </div>
                    @endif
                    @if($distribution->notes)
                    <div class="col-12 mt-2">
                        <small class="text-muted">
                            <i class="fas fa-sticky-note me-1"></i>
                            {{ $distribution->notes }}
                        </small>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No Distributions Found</h6>
                <p class="text-muted small">You haven't received any distributions yet.</p>
            </div>
            @endforelse

            @if($distributions instanceof \Illuminate\Pagination\LengthAwarePaginator && $distributions->hasPages())
            <div class="mt-4">
                {{ $distributions->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection
