@extends('layouts.base')

@section('title', $farmerGroup->name . ' - Group Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-layer-group text-primary me-2"></i>
                        {{ $farmerGroup->name }}
                        @if($farmerGroup->is_active)
                            <span class="badge bg-success ms-2">Active</span>
                        @else
                            <span class="badge bg-secondary ms-2">Inactive</span>
                        @endif
                    </h2>
                    <p class="text-muted mb-0">Code: {{ $farmerGroup->code }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('farmer-groups.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('farmer-groups.edit', $farmerGroup) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('farmer-groups.members', $farmerGroup) }}" class="btn btn-info">
                        <i class="fas fa-users"></i> Members
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Group Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Group Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Group Name:</th>
                                    <td>{{ $farmerGroup->name }}</td>
                                </tr>
                                <tr>
                                    <th>Code:</th>
                                    <td><code>{{ $farmerGroup->code }}</code></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($farmerGroup->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Location:</th>
                                    <td>
                                        {{ $farmerGroup->region->name ?? 'N/A' }},
                                        {{ $farmerGroup->district->name ?? '' }}
                                        @if($farmerGroup->village)
                                            , {{ $farmerGroup->village->name }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Leader:</th>
                                    <td>{{ $farmerGroup->leader_name ?? 'Not assigned' }}</td>
                                </tr>
                                <tr>
                                    <th>Leader Phone:</th>
                                    <td>
                                        @if($farmerGroup->leader_phone)
                                            <a href="tel:{{ $farmerGroup->leader_phone }}">{{ $farmerGroup->leader_phone }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Established:</th>
                                    <td>{{ $farmerGroup->established_date?->format('M d, Y') ?? 'Not specified' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($farmerGroup->description)
                        <div class="mt-3">
                            <h6>Description:</h6>
                            <p class="text-muted">{{ $farmerGroup->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Members -->
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Recent Members</h5>
                    <a href="{{ route('farmer-groups.members', $farmerGroup) }}" class="btn btn-light btn-sm">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($farmerGroup->farmers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Village</th>
                                        <th>Farms</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($farmerGroup->farmers as $farmer)
                                        <tr>
                                            <td>
                                                <a href="{{ route('farmers.show', $farmer) }}">
                                                    {{ $farmer->first_name }} {{ $farmer->last_name }}
                                                </a>
                                            </td>
                                            <td>{{ $farmer->village->name ?? 'N/A' }}</td>
                                            <td>{{ $farmer->farms->count() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $farmer->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($farmer->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No members in this group yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Stats -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="display-6 text-primary">{{ $stats['total_members'] }}</div>
                            <small class="text-muted">Total Members</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="display-6 text-success">{{ $stats['active_members'] }}</div>
                            <small class="text-muted">Active Members</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="display-6 text-warning">{{ $stats['total_farms'] }}</div>
                            <small class="text-muted">Total Farms</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="display-6 text-info">{{ $stats['organic_farmers'] }}</div>
                            <small class="text-muted">Organic Farmers</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('farmer-groups.edit', $farmerGroup) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-2"></i>Edit Group
                        </a>
                        <a href="{{ route('farmer-groups.members', $farmerGroup) }}" class="btn btn-outline-info">
                            <i class="fas fa-users me-2"></i>View All Members
                        </a>
                        <a href="{{ route('farmers.create') }}?group_id={{ $farmerGroup->id }}" class="btn btn-outline-success">
                            <i class="fas fa-user-plus me-2"></i>Add New Farmer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
