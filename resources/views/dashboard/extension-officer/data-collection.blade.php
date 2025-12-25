@extends('layouts.base')

@section('title', 'Data Collection')

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
    .stat-item.total { border-left-color: #3498db; }
    .stat-item.pending { border-left-color: #f39c12; }
    .stat-item.completed { border-left-color: #2ecc71; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .collection-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s;
    }
    .collection-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    .farmer-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        font-size: 1.2rem;
    }
    .farm-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    .farm-tag {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
    }
    .action-buttons {
        display: flex;
        gap: 8px;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Data Collection</h1>
            <p class="text-muted mb-0">Collect and manage field data from farmers</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.extension') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('farm-records.new.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> New Record
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-primary">{{ $stats['total_farmers'] }}</div>
            <div class="label">Active Farmers</div>
        </div>
        <div class="stat-item pending">
            <div class="number text-warning">{{ $stats['pending_records'] }}</div>
            <div class="label">Pending Records</div>
        </div>
        <div class="stat-item completed">
            <div class="number text-success">{{ $stats['completed_today'] }}</div>
            <div class="label">Completed Today</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="{{ route('farm-records.new.create') }}" class="btn btn-outline-success w-100 py-3">
                <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                New Farmer Record
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('farm-records.existing.create') }}" class="btn btn-outline-primary w-100 py-3">
                <i class="fas fa-edit fa-2x mb-2 d-block"></i>
                Existing Farmer Record
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('farms.create') }}" class="btn btn-outline-info w-100 py-3">
                <i class="fas fa-tractor fa-2x mb-2 d-block"></i>
                Register Farm
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('farm-records.index') }}" class="btn btn-outline-warning w-100 py-3">
                <i class="fas fa-clipboard-list fa-2x mb-2 d-block"></i>
                View Records
            </a>
        </div>
    </div>

    <!-- Farmers for Data Collection -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Farmers for Data Collection</h5>
            <div class="input-group" style="max-width: 250px;">
                <input type="text" class="form-control form-control-sm" placeholder="Search farmers..." id="farmerSearch">
                <button class="btn btn-sm btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            @forelse($farmers as $farmer)
            <div class="collection-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="d-flex">
                        <div class="farmer-avatar bg-success me-3">
                            {{ strtoupper(substr($farmer->first_name ?? 'F', 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $farmer->full_name ?? 'N/A' }}</h6>
                            <p class="mb-1 text-muted small">
                                <i class="fas fa-id-card me-1"></i>{{ $farmer->registration_number ?? 'N/A' }}
                                @if($farmer->phone)
                                <span class="ms-2"><i class="fas fa-phone me-1"></i>{{ $farmer->phone }}</span>
                                @endif
                            </p>
                            @if($farmer->village)
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $farmer->village->name }}
                            </p>
                            @endif

                            @if($farmer->farms && $farmer->farms->count() > 0)
                            <div class="farm-list">
                                @foreach($farmer->farms as $farm)
                                <span class="farm-tag">
                                    <i class="fas fa-tractor me-1"></i>{{ $farm->name ?? 'Farm #' . $farm->id }}
                                    ({{ number_format($farm->area ?? 0, 1) }} ac)
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="action-buttons">
                        <a href="{{ route('farm-records.existing.create') }}?farmer_id={{ $farmer->id }}" class="btn btn-sm btn-success" title="Collect Data">
                            <i class="fas fa-clipboard-list"></i> Collect Data
                        </a>
                        <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-sm btn-outline-info" title="View Profile">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No Active Farmers</h5>
                <p class="text-muted">You don't have any active farmers assigned to you.</p>
                <a href="{{ route('farmers.create') }}" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Add Farmer
                </a>
            </div>
            @endforelse
        </div>

        @if($farmers instanceof \Illuminate\Pagination\LengthAwarePaginator && $farmers->hasPages())
        <div class="card-footer">
            {{ $farmers->links() }}
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    // Simple search filter
    document.getElementById('farmerSearch')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.collection-card').forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(searchTerm) ? 'block' : 'none';
        });
    });
</script>
@endpush
