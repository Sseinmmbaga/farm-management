@extends('layouts.base')

@section('title', 'Team Management')

@push('styles')
<style>
    .team-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s;
        border: 1px solid #eee;
    }
    .team-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .avatar-lg {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    .stat-badge {
        display: inline-block;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-right: 10px;
        margin-bottom: 5px;
    }
    .summary-card {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 25px;
    }
    .summary-stat {
        text-align: center;
        padding: 10px;
    }
    .summary-stat .number {
        font-size: 2rem;
        font-weight: bold;
    }
    .summary-stat .label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Team Management</h1>
            <p class="text-muted mb-0">Manage and monitor your extension officers</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.supervisor') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="summary-card">
        <div class="row">
            <div class="col-md-4">
                <div class="summary-stat">
                    <div class="number">{{ $extensionOfficers->count() }}</div>
                    <div class="label">Extension Officers</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-stat">
                    <div class="number">{{ number_format($totalFarmers) }}</div>
                    <div class="label">Total Farmers</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-stat">
                    <div class="number">{{ $avgFarmersPerOfficer }}</div>
                    <div class="label">Avg. Farmers per Officer</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members -->
    <div class="row">
        @forelse($extensionOfficers as $officer)
        <div class="col-md-6 col-lg-4">
            <div class="team-card">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3 avatar-lg">
                        {{ strtoupper(substr($officer->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $officer->name }}</h5>
                        <p class="text-muted mb-0 small">{{ $officer->email }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    @if($officer->phone)
                    <p class="mb-1 small">
                        <i class="fas fa-phone text-muted me-2"></i>{{ $officer->phone }}
                    </p>
                    @endif
                    <p class="mb-0 small">
                        <i class="fas fa-calendar text-muted me-2"></i>Joined {{ $officer->created_at->format('M d, Y') }}
                    </p>
                </div>

                <div class="mb-3">
                    <span class="stat-badge bg-success bg-opacity-10 text-success">
                        <i class="fas fa-users me-1"></i>{{ $officer->farmers_count }} Farmers
                    </span>
                    <span class="stat-badge bg-info bg-opacity-10 text-info">
                        <i class="fas fa-tractor me-1"></i>{{ $officer->farms_count }} Farms
                    </span>
                </div>

                <div class="d-flex gap-2">
                    <span class="stat-badge bg-primary bg-opacity-10 text-primary small">
                        <i class="fas fa-user-check me-1"></i>{{ $officer->active_farmers }} Active
                    </span>
                    @if($officer->pending_farmers > 0)
                    <span class="stat-badge bg-warning bg-opacity-10 text-warning small">
                        <i class="fas fa-clock me-1"></i>{{ $officer->pending_farmers }} Pending
                    </span>
                    @endif
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('farmers.index') }}?extension_officer_id={{ $officer->id }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> View Farmers
                    </a>
                    <span class="badge bg-{{ $officer->is_active ? 'success' : 'secondary' }}">
                        {{ $officer->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Extension Officers Found</h4>
                <p class="text-muted">There are no extension officers assigned to your supervision.</p>
            </div>
        </div>
        @endforelse
    </div>
@endsection
