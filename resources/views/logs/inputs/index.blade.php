@extends('layouts.base')

@section('title', 'Input Logs')

@push('styles')
<style>
    .log-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 15px;
        transition: all 0.2s;
        border-left: 4px solid #17a2b8;
    }
    .log-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .log-type-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .log-type-badge i { margin-right: 5px; }
    .log-type-badge.type-input { background: #d1ecf1; color: #0c5460; }
    .filter-section {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .type-filter-btn {
        border-radius: 20px;
        padding: 8px 16px;
        margin: 5px;
        border: 1px solid #dee2e6;
        background: white;
        color: #495057;
        transition: all 0.2s;
    }
    .type-filter-btn:hover, .type-filter-btn.active {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }
    .stats-mini {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }
    .stat-item {
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stat-item i {
        font-size: 1.5rem;
        opacity: 0.8;
    }
    .stat-item .number {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .stat-item .label {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .flagged-badge {
        background: #dc3545;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.75rem;
    }
    .log-meta {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 10px;
    }
    .log-meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.85rem;
        color: #6c757d;
    }
    .log-meta-item i {
        color: #adb5bd;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Input Logs</h1>
            <p class="text-muted mb-0">Track all input applications (fertilizers, pesticides, etc.)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('logs.quick-entry') }}?type=input" class="btn btn-outline-primary">
                <i class="bi bi-lightning"></i> Quick Entry
            </a>
            <a href="{{ route('logs.create') }}?type=input" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Input Log
            </a>
        </div>
    </div>

    <!-- Quick Type Filters (Only input active) -->
    <div class="mb-3">
        <a href="{{ route('logs.index') }}" class="type-filter-btn btn">
            <i class="bi bi-grid"></i> All Logs
        </a>
        @foreach($logTypes as $logType)
            @if($logType->value === 'input')
                <a href="{{ route('logs.index', ['type' => $logType->value]) }}"
                   class="type-filter-btn btn active">
                    <i class="{{ $logType->icon() }}"></i> {{ $logType->label() }}
                </a>
            @else
                <a href="{{ route('logs.index', ['type' => $logType->value]) }}"
                   class="type-filter-btn btn">
                    <i class="{{ $logType->icon() }}"></i> {{ $logType->label() }}
                </a>
            @endif
        @endforeach
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('logs.index') }}" class="row g-3">
            <input type="hidden" name="type" value="input">

            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search input logs..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Season</label>
                <select name="season_id" class="form-select">
                    <option value="">All Seasons</option>
                    @foreach($seasons as $season)
                    <option value="{{ $season->id }}" {{ request('season_id') == $season->id ? 'selected' : '' }}>
                        {{ $season->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>

        <div class="mt-2">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="flagged" id="flaggedFilter"
                       {{ request('flagged') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label text-danger" for="flaggedFilter">
                    <i class="bi bi-flag-fill"></i> Show Flagged Only
                </label>
            </div>
        </div>
    </div>

    <!-- Logs List -->
    @if($logs->count() > 0)
        @foreach($logs as $log)
        <div class="log-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="log-type-badge type-input">
                            <i class="{{ $log->type->icon() }}"></i>
                            {{ $log->type->label() }}
                        </span>
                        <span class="badge bg-{{ $log->status_color }}">{{ $log->status_label }}</span>
                        @if($log->is_flagged)
                        <span class="flagged-badge"><i class="bi bi-flag-fill"></i> Flagged</span>
                        @endif
                    </div>

                    <h5 class="mb-2">
                        <a href="{{ route('logs.show', $log) }}" class="text-decoration-none text-dark">
                            {{ $log->name }}
                        </a>
                    </h5>

                    @if($log->description)
                    <p class="text-muted mb-2">{{ Str::limit($log->description, 150) }}</p>
                    @endif

                    <div class="log-meta">
                        <div class="log-meta-item">
                            <i class="bi bi-calendar"></i>
                            {{ $log->log_date->format('M d, Y') }}
                        </div>
                        @if($log->farmer)
                        <div class="log-meta-item">
                            <i class="bi bi-person"></i>
                            {{ $log->farmer->full_name }}
                        </div>
                        @endif
                        @if($log->farm)
                        <div class="log-meta-item">
                            <i class="bi bi-geo-alt"></i>
                            {{ $log->farm->name }}
                        </div>
                        @endif
                        @if($log->season)
                        <div class="log-meta-item">
                            <i class="bi bi-cloud-sun"></i>
                            {{ $log->season->name }}
                        </div>
                        @endif
                        @if($log->image_count > 0)
                        <div class="log-meta-item">
                            <i class="bi bi-camera"></i>
                            {{ $log->image_count }} photos
                        </div>
                        @endif
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('logs.show', $log) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                        <li><a class="dropdown-item" href="{{ route('logs.edit', $log) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logs.destroy', $log) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this log?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-trash me-2"></i>Delete
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $logs->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-droplet display-1 text-muted"></i>
            <h4 class="mt-3">No Input Logs Found</h4>
            <p class="text-muted">Start by creating your first input log</p>
            <a href="{{ route('logs.create') }}?type=input" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Create Input Log
            </a>
        </div>
    @endif
@endsection