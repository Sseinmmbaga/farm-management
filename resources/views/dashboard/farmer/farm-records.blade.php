@extends('layouts.base')

@section('title', 'Farm Records')

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
    .stat-item.active { border-left-color: #2ecc71; }
    .stat-item.harvest { border-left-color: #f39c12; }
    .stat-item.pending { border-left-color: #e74c3c; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .record-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #eee;
    }
    .record-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .status-completed { background-color: #e6f7ee; color: #27ae60; }
    .status-pending { background-color: #fff4e6; color: #e67e22; }
    .status-overdue { background-color: #ffeaea; color: #c0392b; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Farm Records</h1>
            <p class="text-muted mb-0">Track your farm activities and records</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Record
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-success">{{ $stats['total_records'] ?? 0 }}</div>
            <div class="label">Total Records</div>
        </div>
        <div class="stat-item active">
            <div class="number text-success">{{ $stats['active_tasks'] ?? 0 }}</div>
            <div class="label">Active Tasks</div>
        </div>
        <div class="stat-item harvest">
            <div class="number text-warning">{{ $stats['harvests_recorded'] ?? 0 }}</div>
            <div class="label">Harvests Recorded</div>
        </div>
        <div class="stat-item pending">
            <div class="number text-danger">{{ $stats['pending_actions'] ?? 0 }}</div>
            <div class="label">Pending Actions</div>
        </div>
    </div>

    <!-- Records List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Farm Records</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($records) && $records->count() > 0)
                    @foreach($records as $record)
                    @php
                        $typeIcon = match($record->type) {
                            'seeding' => 'fa-seedling',
                            'harvest' => 'fa-leaf',
                            'input' => 'fa-spray-can',
                            'observation' => 'fa-eye',
                            default => 'fa-clipboard-list',
                        };
                        $statusClass = match($record->status) {
                            'done' => 'status-completed',
                            'pending' => 'status-pending',
                            'cancelled' => 'status-overdue',
                            default => 'status-pending',
                        };
                    @endphp
                    <div class="record-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    <i class="fas {{ $typeIcon }} me-2 text-success"></i>
                                    {{ $record->name ?? ucfirst($record->type) . ' Record' }}
                                </h6>
                                <p class="text-muted mb-2 small">{{ $record->description ?? 'Farm activity recorded' }}</p>
                                @if($record->type === 'seeding' || $record->type === 'harvest')
                                <p class="small mb-2">
                                    <span class="badge bg-{{ $record->detail?->crop_type === 'cotton' ? 'success' : 'warning' }}">
                                        {{ ucfirst($record->detail?->crop_type ?? 'Cotton') }}
                                    </span>
                                    @if($record->detail?->variety)
                                    <span class="text-muted">- {{ $record->detail->variety }}</span>
                                    @endif
                                </p>
                                @endif
                                <div class="d-flex gap-3 align-items-center flex-wrap">
                                    <span class="record-status {{ $statusClass }}">{{ ucfirst($record->status) }}</span>
                                    <span class="text-muted small">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $record->log_date?->format('M d, Y') ?? $record->created_at->format('M d, Y') }}
                                    </span>
                                    @if($record->farm)
                                    <span class="text-muted small">
                                        <i class="fas fa-tractor me-1"></i>
                                        Farm: {{ $record->farm->name }}
                                    </span>
                                    @endif
                                    @if($record->season)
                                    <span class="text-muted small">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Season: {{ $record->season->name }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Farm Records</h5>
                        <p class="text-muted">You haven't created any farm records yet.</p>
                        <p class="text-muted small">Start tracking your cotton and sesame farming activities.</p>
                    </div>
                    @endif
                </div>
                @if(isset($records) && $records instanceof \Illuminate\Pagination\LengthAwarePaginator && $records->hasPages())
                <div class="card-footer">
                    {{ $records->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection