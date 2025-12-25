@extends('layouts.base')

@section('title', 'Data Review')

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
    .stat-item.approved { border-left-color: #2ecc71; }
    .stat-item.rejected { border-left-color: #e74c3c; }
    .stat-item .number {
        font-size: 1.8rem;
        font-weight: bold;
    }
    .stat-item .label {
        font-size: 0.85rem;
        color: #6c757d;
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
    .action-btns .btn {
        padding: 5px 10px;
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Data Review</h1>
            <p class="text-muted mb-0">Review and approve farmer submissions</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.supervisor') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item pending">
            <div class="number text-warning">{{ $stats['pending'] }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-item approved">
            <div class="number text-success">{{ $stats['approved'] }}</div>
            <div class="label">Approved</div>
        </div>
        <div class="stat-item rejected">
            <div class="number text-danger">{{ $stats['rejected'] }}</div>
            <div class="label">Rejected</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.supervisor.data-review') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Extension Officer</label>
                <select name="officer_id" class="form-select">
                    <option value="">All Officers</option>
                    @foreach($extensionOfficers as $officer)
                    <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>
                        {{ $officer->name }}
                    </option>
                    @endforeach
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
                        <th>Registration #</th>
                        <th>Village</th>
                        <th>Farms</th>
                        <th>Extension Officer</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $farmer)
                    <tr>
                        <td>
                            <strong>{{ $farmer->full_name }}</strong>
                            @if($farmer->phone)
                            <br><small class="text-muted">{{ $farmer->phone }}</small>
                            @endif
                        </td>
                        <td>{{ $farmer->registration_number }}</td>
                        <td>{{ $farmer->village->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $farmer->farms->count() }} farms</span>
                        </td>
                        <td>{{ $farmer->extensionOfficer->name ?? 'N/A' }}</td>
                        <td>{{ $farmer->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($farmer->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($farmer->status === 'active')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td class="action-btns">
                            <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($farmer->status === 'pending')
                            <form action="{{ route('farmers.update', $farmer) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm('Approve this farmer?')">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('farmers.update', $farmer) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-sm btn-danger" title="Reject" onclick="return confirm('Reject this farmer?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No submissions found matching your criteria</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
        <div class="p-3 border-top">
            {{ $submissions->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
