@extends('layouts.base')

@section('title', 'Non-Conformities (Findings)')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> Non-Conformities (Findings)
                        </h4>
                        <div>
                            <a href="{{ route('findings.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i> Report Finding
                            </a>
                            <a href="{{ route('ics-reports.findings') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-chart-bar me-1"></i> Generate Report
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Open</h6>
                                            <h3 class="mb-0">{{ $findings->total() }}</h3>
                                        </div>
                                        <div class="bg-danger text-white rounded-circle p-3">
                                            <i class="fas fa-exclamation-circle fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('findings.open') }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">In Progress</h6>
                                            <h3 class="mb-0">{{ $findings->where('status', 'in_progress')->count() }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-spinner fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('findings.in-progress') }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Resolved</h6>
                                            <h3 class="mb-0">{{ $findings->where('status', 'resolved')->count() }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                    <a href="{{ route('findings.resolved') }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Critical</h6>
                                            <h3 class="mb-0">{{ $findings->where('severity', 'critical')->count() }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-skull-crossbones fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('findings.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="severity" class="form-label">Severity</label>
                            <select class="form-select" id="severity" name="severity">
                                <option value="">All Severity</option>
                                <option value="minor" {{ request('severity') == 'minor' ? 'selected' : '' }}>Minor</option>
                                <option value="major" {{ request('severity') == 'major' ? 'selected' : '' }}>Major</option>
                                <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                                <option value="observation" {{ request('severity') == 'observation' ? 'selected' : '' }}>Observation</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Search by finding number, description..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('findings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Findings Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Finding #</th>
                                    <th>Inspection</th>
                                    <th>Farmer</th>
                                    <th>Description</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($findings as $finding)
                                    <tr>
                                        <td>
                                            <strong class="text-danger">{{ $finding->finding_number }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <a href="{{ route('inspections.show', $finding->inspection_id) }}" class="text-decoration-none">
                                                    <strong>{{ $finding->inspection->inspection_number ?? 'N/A' }}</strong>
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $finding->inspection->checklist->name ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($finding->inspection->farmer->first_name ?? '', 0, 1) }}{{ substr($finding->inspection->farmer->last_name ?? '', 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $finding->inspection->farmer->first_name ?? '' }} {{ $finding->inspection->farmer->last_name ?? '' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $finding->inspection->farmer->registration_number ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ Str::limit($finding->description, 60) }}
                                            @if($finding->evidence)
                                                <br>
                                                <small class="text-info">
                                                    <i class="fas fa-paperclip me-1"></i> Evidence attached
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $finding->severity == 'critical' ? 'danger' : ($finding->severity == 'major' ? 'warning' : ($finding->severity == 'minor' ? 'info' : 'secondary')) }}">
                                                {{ ucfirst($finding->severity) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $finding->status == 'open' ? 'danger' : ($finding->status == 'in_progress' ? 'warning' : 'success') }}">
                                                {{ ucfirst($finding->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($finding->due_date)
                                                {{ $finding->due_date->format('M d, Y') }}
                                                @if($finding->due_date->isPast() && $finding->status != 'resolved')
                                                    <br>
                                                    <small class="text-danger">Overdue</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No due date</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('findings.show', $finding) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('findings.edit', $finding) }}"
                                                   class="btn btn-outline-warning"
                                                   title="Edit Finding">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($finding->status == 'open')
                                                    <form action="{{ route('findings.resolve', $finding) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success" title="Mark Resolved">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        title="Delete"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $finding->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $finding->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete finding <strong>{{ $finding->finding_number }}</strong>?</p>
                                                    <p class="text-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        This action cannot be undone.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('findings.destroy', $finding) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete Finding</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                                <h5>No findings found</h5>
                                                <p>All non-conformities have been resolved or no findings reported yet.</p>
                                                <a href="{{ route('findings.create') }}" class="btn btn-danger">
                                                    <i class="fas fa-plus me-1"></i> Report First Finding
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($findings->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $findings->firstItem() }} to {{ $findings->lastItem() }} of {{ $findings->total() }} findings
                                </div>
                                <div>
                                    {{ $findings->links() }}
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