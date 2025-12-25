@extends('layouts.base')

@section('title', 'Farmer Assignments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-tag me-2"></i> Farmer Assignments
                        </h4>
                        <a href="{{ route('farmers.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Back to All Farmers
                        </a>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Extension Officers</h6>
                                            <h3 class="mb-0 text-primary">{{ $stats['total_officers'] }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-user-tie fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Unassigned Farmers</h6>
                                            <h3 class="mb-0 text-warning">{{ $stats['unassigned_farmers'] }}</h3>
                                        </div>
                                        <div class="bg-warning text-dark rounded-circle p-3">
                                            <i class="fas fa-user-slash fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Assigned Farmers</h6>
                                            <h3 class="mb-0 text-success">{{ $stats['assigned_farmers'] }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-user-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Extension Officers Overview -->
                <div class="card-body border-bottom">
                    <h5 class="mb-3"><i class="fas fa-users-cog me-2"></i> Extension Officers Workload</h5>
                    <div class="row">
                        @forelse($officers as $officer)
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ substr($officer->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <strong>{{ $officer->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $officer->phone ?? 'No phone' }}</small>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="text-muted">Assigned Farmers:</span>
                                            <span class="badge bg-info fs-6">{{ $officer->assigned_farmers_count }}</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            @php
                                                $maxFarmers = 50; // Assumed max capacity
                                                $percentage = min(($officer->assigned_farmers_count / $maxFarmers) * 100, 100);
                                                $barClass = $percentage < 50 ? 'bg-success' : ($percentage < 80 ? 'bg-warning' : 'bg-danger');
                                            @endphp
                                            <div class="progress-bar {{ $barClass }}" role="progressbar" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No active extension officers found. Please create extension officer accounts first.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Unassigned Farmers Section -->
                <div class="card-body border-bottom">
                    <h5 class="mb-3"><i class="fas fa-user-slash me-2 text-warning"></i> Unassigned Farmers</h5>

                    <!-- Search & Filter -->
                    <form method="GET" action="{{ route('farmers.assignments') }}" class="row g-3 mb-4">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by name, phone, or ID..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select name="region_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Regions</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        @if(request('search') || request('region_id'))
                            <div class="col-md-2">
                                <a href="{{ route('farmers.assignments') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        @endif
                    </form>

                    <!-- Bulk Assignment Form -->
                    @if($unassignedFarmers->count() > 0 && $officers->count() > 0)
                    <form id="bulkAssignForm" method="POST" action="{{ route('farmers.bulk-assign') }}" class="mb-3">
                        @csrf
                        <div class="row align-items-center bg-light p-3 rounded">
                            <div class="col-md-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllUnassigned">
                                    <label class="form-check-label" for="selectAllUnassigned">
                                        <strong>All</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <select name="extension_officer_id" class="form-select" required>
                                    <option value="">-- Select Extension Officer --</option>
                                    @foreach($officers as $officer)
                                        <option value="{{ $officer->id }}">
                                            {{ $officer->name }} ({{ $officer->assigned_farmers_count }} farmers)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info w-100" id="bulkAssignBtn" disabled>
                                    <i class="fas fa-user-tag me-1"></i> Assign Selected (<span id="selectedUnassignedCount">0</span>)
                                </button>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>

                <!-- Unassigned Farmers Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">
                                        <i class="fas fa-check-square"></i>
                                    </th>
                                    <th>Farmer ID</th>
                                    <th>Full Name</th>
                                    <th>Phone Number</th>
                                    <th>Village/Location</th>
                                    <th>Farmer Group</th>
                                    <th>Assign To</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unassignedFarmers as $farmer)
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                   class="form-check-input unassigned-checkbox"
                                                   name="farmer_ids[]"
                                                   value="{{ $farmer->id }}"
                                                   form="bulkAssignForm">
                                        </td>
                                        <td>
                                            <strong class="text-primary">{{ $farmer->registration_number }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($farmer->first_name, 0, 1) }}{{ substr($farmer->last_name, 0, 1) }}
                                                </div>
                                                <strong>{{ $farmer->first_name }} {{ $farmer->last_name }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="tel:{{ $farmer->phone }}" class="text-decoration-none">
                                                <i class="fas fa-phone fa-xs me-1"></i>
                                                {{ $farmer->phone }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($farmer->village)
                                                {{ $farmer->village->name }}
                                                <br>
                                                <small class="text-muted">{{ $farmer->district->name ?? '' }}</small>
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($farmer->group)
                                                <span class="badge bg-primary">{{ $farmer->group->name }}</span>
                                            @else
                                                <span class="text-muted">No group</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($officers->count() > 0)
                                            <form action="{{ route('farmers.assign', $farmer) }}" method="POST" class="d-flex gap-2">
                                                @csrf
                                                <select name="extension_officer_id" class="form-select form-select-sm" required style="min-width: 150px;">
                                                    <option value="">Select Officer</option>
                                                    @foreach($officers as $officer)
                                                        <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-info">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @else
                                                <span class="text-muted">No officers available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                                <h5>All farmers are assigned</h5>
                                                <p>Every active farmer has been assigned to an extension officer</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($unassignedFarmers->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $unassignedFarmers->firstItem() }} to {{ $unassignedFarmers->lastItem() }} of {{ $unassignedFarmers->total() }} unassigned farmers
                                </div>
                                <div>
                                    {{ $unassignedFarmers->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Select All functionality for unassigned farmers
    document.getElementById('selectAllUnassigned')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.unassigned-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateUnassignedCount();
    });

    // Update selected count
    document.querySelectorAll('.unassigned-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateUnassignedCount);
    });

    function updateUnassignedCount() {
        const count = document.querySelectorAll('.unassigned-checkbox:checked').length;
        document.getElementById('selectedUnassignedCount').textContent = count;
        document.getElementById('bulkAssignBtn').disabled = count === 0;
    }
</script>
@endpush
@endsection
