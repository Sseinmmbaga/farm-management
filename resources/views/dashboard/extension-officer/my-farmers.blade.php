@extends('layouts.base')

@section('title', 'My Farmers')

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
    .stat-item.total { border-left-color: #3498db; }
    .stat-item.active { border-left-color: #2ecc71; }
    .stat-item.pending { border-left-color: #f39c12; }
    .stat-item.inactive { border-left-color: #e74c3c; }
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
    .farmer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">My Farmers</h1>
            <p class="text-muted mb-0">Manage farmers assigned to you</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.extension') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('farmers.create') }}" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Add Farmer
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-primary">{{ $stats['total'] }}</div>
            <div class="label">Total Farmers</div>
        </div>
        <div class="stat-item active">
            <div class="number text-success">{{ $stats['active'] }}</div>
            <div class="label">Active</div>
        </div>
        <div class="stat-item pending">
            <div class="number text-warning">{{ $stats['pending'] }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-item inactive">
            <div class="number text-danger">{{ $stats['inactive'] }}</div>
            <div class="label">Inactive</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.extension.my-farmers') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Group</label>
                <select name="group" class="form-select">
                    <option value="">All Groups</option>
                    {{-- Add group options dynamically --}}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name or registration..." value="{{ request('search') }}">
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
                        <th>Farmer</th>
                        <th>Reg. Number</th>
                        <th>Phone</th>
                        <th>Village</th>
                        <th>Farms</th>
                        <th>Group</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($farmers as $farmer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="farmer-avatar bg-success me-2">
                                    {{ strtoupper(substr($farmer->first_name ?? 'F', 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $farmer->full_name ?? 'N/A' }}</strong>
                                    @if($farmer->gender)
                                    <br><small class="text-muted">{{ ucfirst($farmer->gender) }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $farmer->registration_number ?? 'N/A' }}</td>
                        <td>{{ $farmer->phone ?? '-' }}</td>
                        <td>{{ $farmer->village->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $farmer->farms_count ?? $farmer->farms->count() ?? 0 }} farms</span>
                        </td>
                        <td>{{ $farmer->group->name ?? '-' }}</td>
                        <td>
                            @switch($farmer->status ?? 'pending')
                                @case('active')
                                    <span class="badge bg-success">Active</span>
                                    @break
                                @case('pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @break
                                @case('inactive')
                                    <span class="badge bg-danger">Inactive</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $farmer->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('farmers.edit', $farmer) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No farmers found</p>
                            <a href="{{ route('farmers.create') }}" class="btn btn-success mt-3">
                                <i class="fas fa-user-plus"></i> Add First Farmer
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($farmers instanceof \Illuminate\Pagination\LengthAwarePaginator && $farmers->hasPages())
        <div class="p-3 border-top">
            {{ $farmers->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
