@extends('layouts.base')

@section('title', 'Stock Alerts')

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
    .stat-item.critical { border-left-color: #e74c3c; }
    .stat-item.warning { border-left-color: #f39c12; }
    .stat-item.info { border-left-color: #3498db; }
    .stat-item.resolved { border-left-color: #2ecc71; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .alert-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid;
    }
    .alert-card.critical { border-left-color: #e74c3c; }
    .alert-card.warning { border-left-color: #f39c12; }
    .alert-card.info { border-left-color: #3498db; }
    .alert-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
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
            <h1 class="h3 mb-0">Stock Alerts</h1>
            <p class="text-muted mb-0">Monitor and manage stock alerts</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.stock') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button class="btn btn-outline-success" id="markAllResolvedBtn">
                <i class="fas fa-check-double"></i> Mark All Resolved
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item critical">
            <div class="number text-danger">{{ $stats['critical_alerts'] }}</div>
            <div class="label">Critical Alerts</div>
        </div>
        <div class="stat-item warning">
            <div class="number text-warning">{{ $stats['warning_alerts'] }}</div>
            <div class="label">Warning Alerts</div>
        </div>
        <div class="stat-item info">
            <div class="number text-info">{{ $stats['info_alerts'] }}</div>
            <div class="label">Info Alerts</div>
        </div>
        <div class="stat-item resolved">
            <div class="number text-success">{{ $stats['resolved_today'] }}</div>
            <div class="label">Resolved Today</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.stock.alerts') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Severity</label>
                <select name="severity" class="form-select">
                    <option value="">All Severities</option>
                    <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="acknowledged" {{ request('status') === 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search alerts..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Alerts List -->
    <div class="alerts-list">
        @forelse($alerts as $alert)
        <div class="alert-card {{ $alert->severity ?? 'info' }}">
            <div class="d-flex align-items-start">
                <div class="alert-icon bg-{{ ($alert->severity ?? 'info') === 'critical' ? 'danger' : (($alert->severity ?? 'info') === 'warning' ? 'warning' : 'info') }} bg-opacity-10 text-{{ ($alert->severity ?? 'info') === 'critical' ? 'danger' : (($alert->severity ?? 'info') === 'warning' ? 'warning' : 'info') }} me-3">
                    <i class="fas fa-{{ ($alert->severity ?? 'info') === 'critical' ? 'exclamation-circle' : (($alert->severity ?? 'info') === 'warning' ? 'exclamation-triangle' : 'info-circle') }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $alert->title ?? 'Stock Alert' }}</h6>
                            <p class="mb-1 text-muted">{{ $alert->message ?? 'No message' }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>{{ $alert->created_at->diffForHumans() ?? 'N/A' }}
                                @if($alert->item)
                                <span class="ms-2"><i class="fas fa-box me-1"></i>{{ $alert->item->name }}</span>
                                @endif
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            @if(($alert->status ?? 'active') === 'active')
                            <button class="btn btn-sm btn-outline-primary" title="Acknowledge">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success" title="Resolve">
                                <i class="fas fa-check"></i>
                            </button>
                            @else
                            <span class="badge bg-{{ ($alert->status ?? 'active') === 'resolved' ? 'success' : 'secondary' }}">
                                {{ ucfirst($alert->status ?? 'active') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No Active Alerts</h5>
            <p class="text-muted">All stock levels are within acceptable ranges</p>
        </div>
        @endforelse
    </div>

    @if($alerts instanceof \Illuminate\Pagination\LengthAwarePaginator && $alerts->hasPages())
    <div class="mt-4">
        {{ $alerts->withQueryString()->links() }}
    </div>
    @endif
@endsection

@push('scripts')
<script>
    document.getElementById('markAllResolvedBtn')?.addEventListener('click', function() {
        if (confirm('Are you sure you want to mark all alerts as resolved?')) {
            // Add your AJAX call here
            alert('This feature will be implemented soon.');
        }
    });
</script>
@endpush
