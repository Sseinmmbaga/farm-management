@extends('layouts.base')

@section('title', 'Farmer Groups Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-layer-group me-2"></i> Farmer Groups Management
                        </h4>
                        <a href="{{ route('farmer-groups.create') }}" class="btn btn-light">
                            <i class="fas fa-plus me-1"></i> Add New Group
                        </a>
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
                                            <h6 class="text-muted mb-1">Total Groups</h6>
                                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-layer-group fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Total Members</h6>
                                            <h3 class="mb-0">{{ $stats['total_members'] }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
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
                                            <h6 class="text-muted mb-1">Active Groups</h6>
                                            <h3 class="mb-0">{{ $stats['active'] }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('farmer-groups.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by name, code, or leader..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
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

                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </form>
                </div>

                @if(session('success'))
                    <div class="alert alert-success m-3">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger m-3">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <!-- Groups Grid -->
                <div class="card-body">
                    <div class="row">
                        @forelse($groups as $group)
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card h-100 shadow-sm {{ $group->is_active ? '' : 'bg-light' }}">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary">
                                            <i class="fas fa-layer-group me-1"></i>
                                            {{ $group->code }}
                                        </span>
                                        @if($group->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $group->name }}</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">{{ $group->code }}</h6>

                                        <p class="card-text small">
                                            @if($group->region)
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                {{ $group->region->name }}
                                                @if($group->district)
                                                    , {{ $group->district->name }}
                                                @endif
                                            @endif
                                        </p>

                                        @if($group->leader_name)
                                            <p class="card-text small mb-1">
                                                <i class="fas fa-user-tie text-primary me-1"></i>
                                                {{ $group->leader_name }}
                                            </p>
                                        @endif

                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="badge bg-primary">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $group->farmers_count }} Members
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ route('farmer-groups.show', $group) }}"
                                               class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('farmer-groups.members', $group) }}"
                                               class="btn btn-sm btn-outline-info" title="Members">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <a href="{{ route('farmer-groups.edit', $group) }}"
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete"
                                                    onclick="confirmDelete('{{ route('farmer-groups.destroy', $group) }}', '{{ $group->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fas fa-layer-group fa-4x text-muted mb-3"></i>
                                    <h5>No farmer groups found</h5>
                                    <p class="text-muted">Start by creating your first farmer group</p>
                                    <a href="{{ route('farmer-groups.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Add New Group
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($groups->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $groups->links() }}
                        </div>
                    @endif
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
                <p>Are you sure you want to delete group <strong id="groupName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Group</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, name) {
        document.getElementById('groupName').textContent = name;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endpush
@endsection
