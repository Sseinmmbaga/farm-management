@extends('layouts.base')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-users-cog me-2"></i> User Management
                </h4>
                @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}" class="btn btn-light">
                    <i class="fas fa-user-plus me-1"></i> Add New User
                </a>
                @endcan
            </div>
        </div>

        {{-- Stats Section --}}
        <div class="card-body bg-light border-bottom">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Users</h6>
                                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                </div>
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Active</h6>
                                    <h3 class="mb-0">{{ $stats['active'] }}</h3>
                                </div>
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-user-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Inactive</h6>
                                    <h3 class="mb-0">{{ $stats['inactive'] }}</h3>
                                </div>
                                <div class="bg-danger text-white rounded-circle p-3">
                                    <i class="fas fa-user-slash fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Admins</h6>
                                    <h3 class="mb-0">{{ $stats['admins'] }}</h3>
                                </div>
                                <div class="bg-warning text-white rounded-circle p-3">
                                    <i class="fas fa-user-shield fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Filters --}}
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Search by name, email, phone..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->value }}" {{ request('role') == $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Bulk Actions & Export --}}
        <div class="card-body border-bottom py-2">
            <div class="d-flex justify-content-between align-items-center">
                <form method="POST" action="{{ route('users.bulk-action') }}" id="bulkActionForm" class="d-flex align-items-center gap-2">
                    @csrf
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label" for="selectAll">Select All</label>
                    </div>
                    <select name="action" class="form-select form-select-sm" style="width: auto;" id="bulkActionSelect">
                        <option value="">Bulk Actions</option>
                        <option value="activate">Activate Selected</option>
                        <option value="deactivate">Deactivate Selected</option>
                        @can('create', App\Models\User::class)
                        <option value="delete">Delete Selected</option>
                        @endcan
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary" id="bulkActionBtn" disabled>
                        Apply
                    </button>
                </form>
                <a href="{{ route('users.export', request()->query()) }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-file-csv me-1"></i> Export CSV
                </a>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>User</th>
                                <th>Contact</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input user-checkbox"
                                               name="users[]" value="{{ $user->id }}" form="bulkActionForm"
                                               {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-{{ $user->is_active ? 'primary' : 'secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                @if($user->id === auth()->id())
                                                    <span class="badge bg-info ms-1">You</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->phone)
                                            <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->isAdmin() ? 'danger' : ($user->isSupervisor() ? 'warning' : 'primary') }}">
                                            {{ $user->role->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                            <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'times-circle' }} me-1"></i>
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->last_login_at)
                                            <small>{{ $user->last_login_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('update', $user)
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('resetPassword', $user)
                                            <form method="POST" action="{{ route('users.reset-password', $user) }}" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to reset this user\'s password?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-info" title="Reset Password">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                            </form>
                                            @endcan
                                            @can('toggleStatus', $user)
                                            <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'secondary' : 'success' }}"
                                                        title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-{{ $user->is_active ? 'user-slash' : 'user-check' }}"></i>
                                                </button>
                                            </form>
                                            @endcan
                                            @can('delete', $user)
                                            <button type="button" class="btn btn-outline-danger" title="Delete"
                                                    onclick="confirmDelete('{{ route('users.destroy', $user) }}', '{{ $user->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="text-muted">
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                        </span>
                        {{ $users->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h5>No users found</h5>
                    <p class="text-muted">Try adjusting your search or filter criteria.</p>
                    @can('create', App\Models\User::class)
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i> Add First User
                    </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
                <p class="text-muted mb-0">This action can be undone by restoring from trash.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, name) {
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox:not(:disabled)');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActionBtn();
    });

    // Update bulk action button state
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkActionBtn);
    });

    document.getElementById('bulkActionSelect').addEventListener('change', updateBulkActionBtn);

    function updateBulkActionBtn() {
        const hasChecked = document.querySelectorAll('.user-checkbox:checked').length > 0;
        const hasAction = document.getElementById('bulkActionSelect').value !== '';
        document.getElementById('bulkActionBtn').disabled = !(hasChecked && hasAction);
    }

    // Confirm bulk action
    document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
        const action = document.getElementById('bulkActionSelect').value;
        const count = document.querySelectorAll('.user-checkbox:checked').length;

        if (!confirm(`Are you sure you want to ${action} ${count} user(s)?`)) {
            e.preventDefault();
        }
    });
</script>
@endpush
@endsection
