@extends('layouts.base')

@section('title', 'Training Programs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chalkboard-teacher me-2"></i> Training Programs
                        </h4>
                        <a href="{{ route('training-programs.create') }}" class="btn btn-light">
                            <i class="fas fa-plus-circle me-1"></i> Add Program
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
                                            <h6 class="text-muted mb-1">Total Programs</h6>
                                            <h3 class="mb-0">{{ $programs->total() }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-graduation-cap fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Active Programs</h6>
                                            <h3 class="mb-0">{{ $programs->where('is_active', true)->count() }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Total Sessions</h6>
                                            <h3 class="mb-0">{{ $programs->sum('sessions_count') }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-calendar-alt fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Mandatory</h6>
                                            <h3 class="mb-0">{{ $programs->where('is_mandatory', true)->count() }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-exclamation-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('training-programs.index') }}" class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by name, code, category..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if(request('search') || request('status'))
                        <div class="col-md-2">
                            <a href="{{ route('training-programs.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times me-1"></i> Clear
                            </a>
                        </div>
                        @endif
                    </form>
                </div>

                <!-- Programs Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Program Name</th>
                                    <th>Category</th>
                                    <th>Duration</th>
                                    <th>Sessions</th>
                                    <th>Status</th>
                                    <th>Mandatory</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($programs as $program)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $program->code }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($program->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $program->name }}</strong>
                                                    @if($program->name_sw)
                                                        <br>
                                                        <small class="text-muted">{{ $program->name_sw }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($program->category)
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $program->category)) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($program->duration_hours)
                                                {{ $program->duration_hours }} hrs
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $program->sessions_count }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($program->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($program->is_mandatory)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-exclamation-circle me-1"></i> Yes
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('training-programs.show', $program) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('training-programs.edit', $program) }}"
                                                   class="btn btn-outline-warning"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        title="Delete"
                                                        onclick="confirmDelete('{{ route('training-programs.destroy', $program) }}', '{{ $program->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                                                <h5>No training programs found</h5>
                                                <p>Start by adding your first training program</p>
                                                <a href="{{ route('training-programs.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> Add Program
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($programs->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $programs->firstItem() }} to {{ $programs->lastItem() }} of {{ $programs->total() }} programs
                                </div>
                                <div>
                                    {{ $programs->links() }}
                                </div>
                            </div>
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
                <p>Are you sure you want to delete program <strong id="programName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone. If the program has sessions, deletion will be prevented.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Program</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, name) {
        document.getElementById('programName').textContent = name;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endpush
@endsection