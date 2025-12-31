@extends('layouts.base')

@section('title', 'Farmer Details')

@section('content')
<div class="container-fluid">
    {{-- Show login credentials if farmer was just created --}}
    @if(session('credentials') && session('credentials')['password'])
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fas fa-key me-2"></i>Farmer Login Credentials</h5>
                <p class="mb-2">Please share these credentials with the farmer so they can log in to the system:</p>
                <div class="bg-white p-3 rounded border">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Email:</strong> <code>{{ session('credentials')['email'] }}</code>
                        </div>
                        <div class="col-md-6">
                            <strong>Password:</strong> <code>{{ session('credentials')['password'] }}</code>
                        </div>
                    </div>
                </div>
                <hr>
                <p class="mb-0 small"><i class="fas fa-exclamation-triangle me-1"></i> <strong>Important:</strong> This password will not be shown again. Please note it down or share it with the farmer now.</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <!-- Header with Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('farmers.index') }}">Farmers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $farmer->first_name }} {{ $farmer->last_name }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user me-2"></i>
                        {{ $farmer->first_name }} {{ $farmer->last_name }}
                        <span class="badge bg-{{ $farmer->status == 'active' ? 'success' : ($farmer->status == 'inactive' ? 'danger' : 'warning') }} ms-2">
                            {{ ucfirst($farmer->status) }}
                        </span>
                    </h1>
                    <p class="text-muted mb-0">Farmer ID: {{ $farmer->registration_number }}</p>
                </div>
                <div class="btn-group">
                    @if(!auth()->user()->hasViewOnlyAccess())
                    <a href="{{ route('farmers.edit', $farmer) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    @endif
                    <a href="{{ route('farmers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="row">
                <!-- Left Column: Farmer Info -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Farmer Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Full Name:</th>
                                            <td>{{ $farmer->first_name }} {{ $farmer->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td>
                                                <a href="tel:{{ $farmer->phone }}" class="text-decoration-none">
                                                    <i class="fas fa-phone fa-xs me-1"></i>
                                                    {{ $farmer->phone }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>
                                                @if($farmer->email)
                                                    <a href="mailto:{{ $farmer->email }}" class="text-decoration-none">
                                                        <i class="fas fa-envelope fa-xs me-1"></i>
                                                        {{ $farmer->email }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Gender:</th>
                                            <td>{{ ucfirst($farmer->gender ?? 'Not specified') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date of Birth:</th>
                                            <td>
                                                @if($farmer->date_of_birth)
                                                    {{ \Carbon\Carbon::parse($farmer->date_of_birth)->format('M d, Y') }}
                                                    ({{ \Carbon\Carbon::parse($farmer->date_of_birth)->age }} years)
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Registration Date:</th>
                                            <td>
                                                {{ $farmer->created_at->format('M d, Y') }}
                                                <br>
                                                <small class="text-muted">{{ $farmer->created_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Location:</th>
                                            <td>
                                                @if($farmer->village)
                                                    {{ $farmer->village->name }}, 
                                                    {{ $farmer->district->name ?? '' }}, 
                                                    {{ $farmer->region->name ?? '' }}
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Address:</th>
                                            <td>{{ $farmer->address ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Farmer Group:</th>
                                            <td>
                                                @if($farmer->group)
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-users me-1"></i>
                                                        {{ $farmer->group->name }}
                                                    </span>
                                                    <span class="badge bg-{{ $farmer->group->group_type === 'simba' ? 'warning' : 'primary' }} ms-1">
                                                        <i class="fas fa-{{ $farmer->group->group_type === 'simba' ? 'lion' : 'elephant' }} me-1"></i>
                                                        {{ $farmer->group->group_type_label }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Extension Officer:</th>
                                            <td>
                                                @if($farmer->extensionOfficer)
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-user-tie me-1"></i>
                                                        {{ $farmer->extensionOfficer->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            @if($farmer->notes)
                                <div class="mt-3">
                                    <h6>Notes:</h6>
                                    <div class="alert alert-light">
                                        {{ $farmer->notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Farms Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-tractor me-2"></i>
                                Farms
                                <span class="badge bg-light text-dark ms-2">{{ $farmer->farms->count() }}</span>
                            </h5>
                            <a href="{{ route('farmers.farms', $farmer) }}" class="btn btn-light btn-sm">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            @if($farmer->farms->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Farm ID</th>
                                                <th>Farm Name</th>
                                                <th>Size (Acres)</th>
                                                <th>Status</th>
                                                <th>Last Updated</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($farmer->farms->take(5) as $farm)
                                                <tr>
                                                    <td>#{{ $farm->farm_id }}</td>
                                                    <td>{{ $farm->name }}</td>
                                                    <td>{{ $farm->size }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $farm->status == 'active' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($farm->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $farm->updated_at->format('M d, Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($farmer->farms->count() > 5)
                                    <div class="text-center mt-2">
                                        <a href="{{ route('farmers.farms', $farmer) }}" class="btn btn-outline-success btn-sm">
                                            View {{ $farmer->farms->count() - 5 }} more farms
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-tractor fa-3x text-muted mb-3"></i>
                                    <h5>No farms registered</h5>
                                    <p class="text-muted">This farmer hasn't registered any farms yet.</p>
                                    @if(!auth()->user()->hasViewOnlyAccess())
                                    <a href="#" class="btn btn-success">
                                        <i class="fas fa-plus me-1"></i> Add Farm
                                    </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Stats & Activity -->
                <div class="col-lg-4">
                    <!-- Stats Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                Statistics
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="display-6 text-primary">{{ $farmer->farms->count() }}</div>
                                    <small class="text-muted">Farms</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="display-6 text-success">{{ $farmer->certifications->count() }}</div>
                                    <small class="text-muted">Certifications</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="display-6 text-warning">{{ $farmer->inspections->count() }}</div>
                                    <small class="text-muted">Inspections</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="display-6 text-danger">{{ $farmer->documents->count() }}</div>
                                    <small class="text-muted">Documents</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                Recent Activity
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($farmer->activityLogs->count() > 0)
                                <div class="timeline">
                                    @foreach($farmer->activityLogs->take(5) as $log)
                                        <div class="timeline-item mb-3">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">{{ $log->description }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $log->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-2">
                                    <a href="{{ route('farmers.activity-logs', $farmer) }}" class="btn btn-outline-dark btn-sm">
                                        View All Activity
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-history fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No recent activity</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    @if(!auth()->user()->hasViewOnlyAccess())
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i> Add Farm
                                </a>
                                <a href="{{ route('farmers.documents.create', $farmer) }}" class="btn btn-outline-success">
                                    <i class="fas fa-file-invoice me-2"></i> Add Document
                                </a>
                                <a href="{{ route('farmers.inspections', $farmer) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-clipboard-check me-2"></i> Schedule Inspection
                                </a>
                                <a href="#" class="btn btn-outline-info">
                                    <i class="fas fa-chart-line me-2"></i> View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .timeline-content {
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    .timeline-item:last-child .timeline-content {
        border-bottom: none;
    }
</style>
@endsection