@extends('layouts.base')

@section('title', 'My Activity Log')

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 25px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -30px;
        top: 5px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: white;
        border: 3px solid;
        z-index: 1;
    }
    .timeline-item.success::before { border-color: #27ae60; }
    .timeline-item.info::before { border-color: #3498db; }
    .timeline-item.warning::before { border-color: #f39c12; }
    .timeline-item.danger::before { border-color: #e74c3c; }
    .timeline-content {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #eee;
    }
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 15px;
    }
    .icon-success { background-color: rgba(39, 174, 96, 0.1); color: #27ae60; }
    .icon-info { background-color: rgba(52, 152, 219, 0.1); color: #3498db; }
    .icon-warning { background-color: rgba(243, 156, 18, 0.1); color: #f39c12; }
    .icon-danger { background-color: rgba(231, 76, 60, 0.1); color: #e74c3c; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">My Activity Log</h1>
            <p class="text-muted mb-0">Track your recent activities and events</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="#" class="btn btn-outline-primary">
                <i class="fas fa-download"></i> Export Log
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-success mb-1">{{ $stats['today'] ?? 0 }}</h2>
                    <p class="text-muted mb-0">Today's Activities</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-primary mb-1">{{ $stats['this_week'] ?? 0 }}</h2>
                    <p class="text-muted mb-0">This Week</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-warning mb-1">{{ $stats['this_month'] ?? 0 }}</h2>
                    <p class="text-muted mb-0">This Month</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-info mb-1">{{ number_format($stats['total'] ?? 0) }}</h2>
                    <p class="text-muted mb-0">Total Activities</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    @if(isset($activities) && $activities->count() > 0)
    <div class="timeline">
        @foreach($activities as $activity)
        @php
            $typeConfig = match($activity->type) {
                'seeding' => ['color' => 'success', 'icon' => 'fas fa-seedling'],
                'harvest' => ['color' => 'warning', 'icon' => 'fas fa-leaf'],
                'input' => ['color' => 'info', 'icon' => 'fas fa-spray-can'],
                'observation' => ['color' => 'danger', 'icon' => 'fas fa-eye'],
                'training' => ['color' => 'success', 'icon' => 'fas fa-chalkboard-teacher'],
                'inspection' => ['color' => 'info', 'icon' => 'fas fa-clipboard-check'],
                default => ['color' => 'secondary', 'icon' => 'fas fa-clipboard-list'],
            };
        @endphp
        <div class="timeline-item {{ $typeConfig['color'] }}">
            <div class="timeline-content">
                <div class="d-flex align-items-start">
                    <div class="activity-icon icon-{{ $typeConfig['color'] }}">
                        <i class="{{ $typeConfig['icon'] }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $activity->name ?? ucfirst($activity->type) . ' Activity' }}</h6>
                        <p class="text-muted mb-2">{{ $activity->description ?? 'Activity on ' . ($activity->farm?->name ?? 'farm') }}</p>
                        @if($activity->type === 'seeding' || $activity->type === 'harvest')
                        <p class="small mb-2">
                            <span class="badge bg-{{ $activity->detail?->crop_type === 'cotton' ? 'success' : 'warning' }}">
                                {{ ucfirst($activity->detail?->crop_type ?? 'Cotton') }}
                            </span>
                            @if($activity->detail?->variety)
                            <span class="text-muted">- {{ $activity->detail->variety }}</span>
                            @endif
                        </p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $activity->log_date?->diffForHumans() ?? $activity->created_at->diffForHumans() }}
                            </small>
                            <div>
                                <span class="badge bg-{{ $activity->status === 'done' ? 'success' : ($activity->status === 'pending' ? 'warning' : 'secondary') }} me-2">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($activities instanceof \Illuminate\Pagination\LengthAwarePaginator && $activities->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $activities->links() }}
    </div>
    @endif
    @else
    <!-- Empty State -->
    <div class="text-center py-5">
        <i class="fas fa-history fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">No Activities Yet</h5>
        <p class="text-muted">Your activity log will appear here once you start recording farm activities.</p>
        <p class="text-muted small">Track your cotton and sesame farming activities including seeding, inputs, and harvests.</p>
    </div>
    @endif
@endsection