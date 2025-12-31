@extends('layouts.base')

@section('title', 'Corrective Actions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-tools me-2"></i> Corrective Actions
                        </h4>
                        <div>
                            <a href="{{ route('corrective-actions.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i> Create Action
                            </a>
                            <a href="{{ route('findings.index') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-exclamation-triangle me-1"></i> View Findings
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total</h6>
                                            <h3 class="mb-0">{{ $actions->total() }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-tools fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">In Progress</h6>
                                            <h3 class="mb-0">{{ $actions->where('status', 'in_progress')->count() }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-spinner fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Completed</h6>
                                            <h3 class="mb-0">{{ $actions->where('status', 'completed')->count() }}</h3>
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
                                            <h6 class="text-muted mb-1">Verified</h6>
                                            <h3 class="mb-0">{{ $actions->where('status', 'verified')->count() }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-clipboard-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('corrective-actions.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="ineffective" {{ request('status') == 'ineffective' ? 'selected' : '' }}>Ineffective</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="action_type" class="form-label">Action Type</label>
                            <select class="form-select" id="action_type" name="action_type">
                                <option value="">All Types</option>
                                <option value="correction" {{ request('action_type') == 'correction' ? 'selected' : '' }}>Correction</option>
                                <option value="corrective_action" {{ request('action_type') == 'corrective_action' ? 'selected' : '' }}>Corrective Action</option>
                                <option value="preventive_action" {{ request('action_type') == 'preventive_action' ? 'selected' : '' }}>Preventive Action</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Search by action number, description..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('corrective-actions.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Actions Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Action #</th>
                                    <th>Finding</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Responsible</th>
                                    <th>Planned Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($actions as $action)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $action->action_number }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('findings.show', $action->finding) }}" class="text-decoration-none">
                                                <strong>{{ $action->finding->finding_number }}</strong>
                                            </a>
                                            <br>
                                            <small class="text-muted">
                                                {{ $action->finding->inspection->farmer->first_name ?? '' }} 
                                                {{ $action->finding->inspection->farmer->last_name ?? '' }}
                                            </small>
                                        </td>
                                        <td>
                                            {{ Str::limit($action->description, 50) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $action->action_type == 'corrective_action' ? 'primary' : ($action->action_type == 'preventive_action' ? 'info' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $action->action_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $action->responsiblePerson->name ?? 'Unassigned' }}
                                        </td>
                                        <td>
                                            @if($action->planned_date)
                                                {{ $action->planned_date->format('M d, Y') }}
                                                @if($action->planned_date->isPast() && !in_array($action->status, ['completed', 'verified']))
                                                    <br>
                                                    <small class="text-danger">Overdue</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $action->status == 'completed' ? 'success' : ($action->status == 'in_progress' ? 'warning' : ($action->status == 'verified' ? 'info' : ($action->status == 'ineffective' ? 'danger' : 'secondary'))) }}">
                                                {{ ucfirst($action->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('corrective-actions.show', $action) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('corrective-actions.edit', $action) }}"
                                                   class="btn btn-outline-warning"
                                                   title="Edit Action">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($action->status == 'in_progress')
                                                    <form action="{{ route('corrective-actions.complete', $action) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success" title="Mark Completed">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        title="Delete"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $action->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $action->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete corrective action <strong>{{ $action->action_number }}</strong>?</p>
                                                    <p class="text-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        This action cannot be undone.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('corrective-actions.destroy', $action) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete Action</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-tools fa-3x mb-3"></i>
                                                <h5>No corrective actions found</h5>
                                                <p>All findings have been addressed or no corrective actions created yet.</p>
                                                <a href="{{ route('corrective-actions.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-1"></i> Create First Action
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($actions->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $actions->firstItem() }} to {{ $actions->lastItem() }} of {{ $actions->total() }} actions
                                </div>
                                <div>
                                    {{ $actions->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection