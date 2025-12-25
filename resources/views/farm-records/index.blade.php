@extends('layouts.base')

@section('title', 'Farm Records')

@push('styles')
<style>
    .record-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.3s;
    }

    .record-card:hover {
        transform: translateY(-5px);
    }

    .filter-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .record-type-badge {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .stats-row {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-radius: 8px;
        padding: 10px 15px;
        margin-top: 10px;
    }

    .stat-item {
        text-align: center;
        padding: 5px;
    }

    .stat-value {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2e7d32;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #666;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clipboard-list text-success"></i> Farm Records</h2>
        <div class="btn-group">
            <a href="{{ route('farm-records.new.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> New Farm Record (Form 2)
            </a>
            <a href="{{ route('farm-records.existing.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Existing Farm Record (Form 3)
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('farm-records.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search"
                       value="{{ request('search') }}" placeholder="Search farm or farmer...">
            </div>
            <div class="col-md-2">
                <label for="record_type" class="form-label">Record Type</label>
                <select class="form-select" id="record_type" name="record_type">
                    <option value="">All Types</option>
                    <option value="new" {{ request('record_type') == 'new' ? 'selected' : '' }}>New (Form 2)</option>
                    <option value="existing" {{ request('record_type') == 'existing' ? 'selected' : '' }}>Existing (Form 3)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="season_id" class="form-label">Season</label>
                <select class="form-select" id="season_id" name="season_id">
                    <option value="">All Seasons</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}" {{ request('season_id') == $season->id ? 'selected' : '' }}>
                            {{ $season->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="certification_status" class="form-label">Certification</label>
                <select class="form-select" id="certification_status" name="certification_status">
                    <option value="">All Status</option>
                    <option value="C0" {{ request('certification_status') == 'C0' ? 'selected' : '' }}>C0 - Conventional</option>
                    <option value="C1" {{ request('certification_status') == 'C1' ? 'selected' : '' }}>C1 - Year 1</option>
                    <option value="C2" {{ request('certification_status') == 'C2' ? 'selected' : '' }}>C2 - Year 2</option>
                    <option value="O" {{ request('certification_status') == 'O' ? 'selected' : '' }}>O - Organic</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('farm-records.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Records List -->
    <div class="row">
        @forelse ($records as $record)
            <div class="col-md-4">
                <div class="card record-card">
                    <div class="card-body position-relative">
                        <span class="badge bg-{{ $record->record_type === 'new' ? 'success' : 'primary' }} record-type-badge">
                            {{ $record->record_type_label }}
                        </span>

                        <h5 class="card-title">{{ $record->farm->display_name ?? 'N/A' }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            <i class="fas fa-user"></i> {{ $record->farmer->full_name ?? 'N/A' }}
                        </h6>

                        <p class="card-text mb-2">
                            <strong>Season:</strong> {{ $record->season->name ?? 'N/A' }}<br>
                            <strong>Certification:</strong>
                            <span class="badge bg-{{ $record->certification_status_color }}">
                                {{ $record->certification_status_label }}
                            </span>
                        </p>

                        <!-- Stats Row -->
                        <div class="stats-row">
                            <div class="row">
                                <div class="col-4 stat-item">
                                    <div class="stat-value">{{ $record->cattle_count }}</div>
                                    <div class="stat-label">Cattle</div>
                                </div>
                                <div class="col-4 stat-item">
                                    <div class="stat-value">{{ $record->goats_sheep_count }}</div>
                                    <div class="stat-label">Goats/Sheep</div>
                                </div>
                                <div class="col-4 stat-item">
                                    <div class="stat-value">{{ $record->oxen_count }}</div>
                                    <div class="stat-label">Oxen</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('farm-records.show', $record) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('farm-records.edit', $record) }}" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('farm-records.destroy', $record) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No farm records found.
                    <a href="{{ route('farm-records.new.create') }}" class="alert-link">Create a new farm record</a> or
                    <a href="{{ route('farm-records.existing.create') }}" class="alert-link">record an existing farm</a>.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($records->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $records->links() }}
        </div>
    @endif
</div>
@endsection
