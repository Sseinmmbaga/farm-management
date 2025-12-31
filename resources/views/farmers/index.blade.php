@extends('layouts.base')

@section('title', 'Farmers Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-users me-2"></i> Farmers Management
                        </h4>
                        @if(!auth()->user()->hasViewOnlyAccess())
                        <div class="btn-group">
                            <a href="{{ route('farmers.pending') }}" class="btn btn-warning">
                                <i class="fas fa-user-clock me-1"></i> Pending Approval
                                @php $pendingCount = \App\Models\Farmers\Farmer::pending()->count(); @endphp
                                @if($pendingCount > 0)
                                    <span class="badge bg-danger">{{ $pendingCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('farmers.assignments') }}" class="btn btn-info">
                                <i class="fas fa-user-tag me-1"></i> Assignments
                            </a>
                            <a href="{{ route('farmers.create') }}" class="btn btn-light">
                                <i class="fas fa-user-plus me-1"></i> Add New Farmer
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Summary Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Farmers</h6>
                                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Active Farmers</h6>
                                            <h3 class="mb-0">{{ $stats['active'] }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-user-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">This Month</h6>
                                            <h3 class="mb-0">{{ $stats['this_month'] }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-calendar-plus fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">With Farms</h6>
                                            <h3 class="mb-0">{{ $stats['with_farms'] }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-tractor fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Search & Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('farmers.index') }}" class="row g-3">
                        <div class="col-md-4">
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

                        <div class="col-md-2">
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
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="group_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Groups</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        <!-- Registration Date Filter Row -->
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <input type="date"
                                       name="date_from"
                                       class="form-control"
                                       placeholder="From Date"
                                       value="{{ request('date_from') }}"
                                       title="Registration Date From">
                            </div>
                            <small class="text-muted">From Date</small>
                        </div>

                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <input type="date"
                                       name="date_to"
                                       class="form-control"
                                       placeholder="To Date"
                                       value="{{ request('date_to') }}"
                                       title="Registration Date To">
                            </div>
                            <small class="text-muted">To Date</small>
                        </div>

                        @if(request('search') || request('region_id') || request('status') || request('group_id') || request('date_from') || request('date_to'))
                            <div class="col-md-2">
                                <a href="{{ route('farmers.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
                
                <!-- Farmers Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Farmer ID</th>
                                    <th>Full Name</th>
                                    <th>Phone Number</th>
                                    <th>Village/Location</th>
                                    <th>Registration Date</th>
                                    <th>Status</th>
                                    <th>Farms</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($farmers as $farmer)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $farmer->registration_number }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($farmer->first_name, 0, 1) }}{{ substr($farmer->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $farmer->first_name }} {{ $farmer->last_name }}</strong>
                                                    @if($farmer->group)
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-users fa-xs me-1"></i>
                                                            {{ $farmer->group->name }}
                                                            <span class="badge bg-{{ $farmer->group->group_type === 'simba' ? 'warning' : 'primary' }} ms-1">
                                                                {{ $farmer->group->group_type_label }}
                                                            </span>
                                                        </small>
                                                    @endif
                                                </div>
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
                                            {{ $farmer->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $farmer->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            @if($farmer->status == 'active')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @elseif($farmer->status == 'inactive')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Inactive
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-tractor me-1"></i>
                                                {{ $farmer->farms_count ?? $farmer->farms->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('farmers.show', $farmer) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!auth()->user()->hasViewOnlyAccess())
                                                <a href="{{ route('farmers.edit', $farmer) }}"
                                                   class="btn btn-outline-warning"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        title="Delete"
                                                        onclick="confirmDelete('{{ route('farmers.destroy', $farmer) }}', '{{ $farmer->first_name }} {{ $farmer->last_name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <h5>No farmers found</h5>
                                                @if(!auth()->user()->hasViewOnlyAccess())
                                                <p>Start by adding your first farmer</p>
                                                <a href="{{ route('farmers.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-user-plus me-1"></i> Add New Farmer
                                                </a>
                                                @else
                                                <p>No farmers available to view</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($farmers->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $farmers->firstItem() }} to {{ $farmers->lastItem() }} of {{ $farmers->total() }} farmers
                                </div>
                                <div>
                                    {{ $farmers->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Bulk Actions & Export -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                Select All
                            </label>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('farmers.export', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-file-csv me-1"></i> Export to CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete farmer <strong id="farmerName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Farmer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, name) {
        document.getElementById('farmerName').textContent = name;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="selected_farmers[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endpush
@endsection