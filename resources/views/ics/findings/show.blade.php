@extends('layouts.base')

@section('title', 'Finding: ' . $finding->finding_number)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="display-6 mb-1">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ $finding->finding_number }}
                            </h1>
                            <p class="lead mb-2">
                                {{ $finding->finding_type }} • 
                                <a href="{{ route('inspections.show', $finding->inspection) }}" class="text-decoration-none">
                                    Inspection: {{ $finding->inspection->inspection_number }}
                                </a>
                            </p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $finding->severity == 'critical' ? 'danger' : ($finding->severity == 'major' ? 'warning' : ($finding->severity == 'minor' ? 'info' : 'secondary')) }} fs-6 px-3 py-2">
                                    {{ ucfirst($finding->severity) }} Severity
                                </span>
                                <span class="badge bg-{{ $finding->status == 'open' ? 'danger' : ($finding->status == 'in_progress' ? 'warning' : 'success') }} ms-2 fs-6 px-3 py-2">
                                    {{ ucfirst($finding->status) }}
                                </span>
                                @if($finding->due_date && $finding->due_date->isPast() && $finding->status != 'resolved')
                                    <span class="badge bg-danger ms-2 fs-6 px-3 py-2">Overdue</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('findings.edit', $finding) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i> Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if($finding->status == 'open')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('findings.resolve', $finding) }}" onclick="return confirm('Mark finding as resolved?')">
                                                <i class="fas fa-check text-success me-2"></i> Mark Resolved
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#statusModal">
                                                <i class="fas fa-exchange-alt text-primary me-2"></i> Change Status
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item" href="{{ route('corrective-actions.create', ['finding_id' => $finding->id]) }}">
                                            <i class="fas fa-tools text-warning me-2"></i> Create Corrective Action
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash me-2"></i> Delete Finding
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column: Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="findingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
                                <i class="fas fa-info-circle me-2"></i> Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button">
                                <i class="fas fa-history me-2"></i> Timeline
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="actions-tab" data-bs-toggle="tab" data-bs-target="#actions" type="button">
                                <i class="fas fa-tools me-2"></i> Corrective Actions
                                <span class="badge bg-primary ms-1">{{ $finding->correctiveActions->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="findingTabsContent">
                        <!-- Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-clipboard-check me-2"></i>Inspection Information
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Inspection</th>
                                            <td>
                                                <a href="{{ route('inspections.show', $finding->inspection) }}" class="text-decoration-none">
                                                    <strong>{{ $finding->inspection->inspection_number }}</strong>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Farmer</th>
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
                                        </tr>
                                        <tr>
                                            <th>Checklist</th>
                                            <td>{{ $finding->inspection->checklist->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Inspection Date</th>
                                            <td>
                                                @if($finding->inspection->inspection_date)
                                                    {{ $finding->inspection->inspection_date->format('M d, Y') }}
                                                @else
                                                    Not recorded
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-exclamation-circle me-2"></i>Finding Information
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Finding Type</th>
                                            <td>{{ ucfirst($finding->finding_type) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reported Date</th>
                                            <td>{{ $finding->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Due Date</th>
                                            <td>
                                                @if($finding->due_date)
                                                    {{ $finding->due_date->format('M d, Y') }}
                                                    @if($finding->due_date->isPast() && $finding->status != 'resolved')
                                                        <span class="badge bg-danger ms-2">Overdue</span>
                                                    @endif
                                                @else
                                                    No due date
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Assigned To</th>
                                            <td>{{ $finding->assigned_to ?: 'Inspector' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Description & Evidence -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-align-left me-2"></i>Description
                                    </h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <p class="card-text">{{ $finding->description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($finding->evidence || $finding->recommendation)
                            <div class="row mt-4">
                                @if($finding->evidence)
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-paperclip me-2"></i>Evidence
                                    </h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <p class="card-text">{{ $finding->evidence }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($finding->recommendation)
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-lightbulb me-2"></i>Recommendation
                                    </h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <p class="card-text">{{ $finding->recommendation }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Resolution (if resolved) -->
                            @if($finding->status == 'resolved')
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-check-circle me-2"></i>Resolution
                                    </h5>
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                Resolved on {{ $finding->resolved_at->format('M d, Y') }}
                                                @if($finding->resolved_by)
                                                    by {{ $finding->resolvedBy->name ?? 'Unknown' }}
                                                @endif
                                            </h6>
                                            <p class="card-text">{{ $finding->resolution_notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Timeline Tab -->
                        <div class="tab-pane fade" id="timeline" role="tabpanel">
                            <ul class="timeline">
                                <li class="timeline-item">
                                    <span class="timeline-marker bg-primary"></span>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">Finding Reported</h6>
                                        <small class="text-muted">{{ $finding->created_at->format('M d, Y H:i') }}</small>
                                        <p class="mb-0">Finding created by inspector.</p>
                                    </div>
                                </li>
                                @if($finding->due_date)
                                <li class="timeline-item">
                                    <span class="timeline-marker bg-info"></span>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">Due Date Set</h6>
                                        <small class="text-muted">{{ $finding->due_date->format('M d, Y') }}</small>
                                        <p class="mb-0">Target resolution date.</p>
                                    </div>
                                </li>
                                @endif
                                @if($finding->status_updates && count($finding->status_updates) > 0)
                                    @foreach($finding->status_updates as $update)
                                    <li class="timeline-item">
                                        <span class="timeline-marker bg-warning"></span>
                                        <div class="timeline-content">
                                            <h6 class="mb-0">Status Updated</h6>
                                            <small class="text-muted">{{ $update->created_at->format('M d, Y H:i') }}</small>
                                            <p class="mb-0">{{ $update->notes }}</p>
                                        </div>
                                    </li>
                                    @endforeach
                                @endif
                                @if($finding->resolved_at)
                                <li class="timeline-item">
                                    <span class="timeline-marker bg-success"></span>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">Finding Resolved</h6>
                                        <small class="text-muted">{{ $finding->resolved_at->format('M d, Y H:i') }}</small>
                                        <p class="mb-0">{{ $finding->resolution_notes }}</p>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Corrective Actions Tab -->
                        <div class="tab-pane fade" id="actions" role="tabpanel">
                            @if($finding->correctiveActions->count() > 0)
                                <div class="mb-3">
                                    <a href="{{ route('corrective-actions.create', ['finding_id' => $finding->id]) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i> Add Corrective Action
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Action #</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th>Due Date</th>
                                                <th>Completed</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($finding->correctiveActions as $action)
                                                <tr>
                                                    <td>
                                                        <strong class="text-primary">{{ $action->action_number }}</strong>
                                                    </td>
                                                    <td>{{ Str::limit($action->description, 50) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $action->status == 'completed' ? 'success' : ($action->status == 'in_progress' ? 'warning' : 'secondary') }}">
                                                            {{ ucfirst($action->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($action->due_date)
                                                            {{ $action->due_date->format('M d, Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($action->completed_at)
                                                            {{ $action->completed_at->format('M d, Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('corrective-actions.show', $action) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                    <h5>No corrective actions</h5>
                                    <p class="text-muted">No corrective actions have been created for this finding yet.</p>
                                    <a href="{{ route('corrective-actions.create', ['finding_id' => $finding->id]) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Create Corrective Action
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Stats & Actions -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i> Status Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="card border-info">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">{{ $finding->days_open ?? 0 }}</h5>
                                    <small class="text-muted">Days Open</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-warning">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">{{ $finding->days_until_due ?? 0 }}</h5>
                                    <small class="text-muted">Days Until Due</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <strong>Priority:</strong>
                        <span class="badge bg-{{ $finding->severity == 'critical' ? 'danger' : ($finding->severity == 'major' ? 'warning' : 'info') }}">
                            {{ ucfirst($finding->severity) }} Priority
                        </span>
                    </div>
                    @if($finding->requires_verification)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Requires verification after corrective actions are completed.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Findings -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-link me-2"></i> Related Findings
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($finding->inspection->findings->where('id', '!=', $finding->id)->take(3) as $related)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('findings.show', $related) }}" class="text-decoration-none">
                                        <strong>{{ $related->finding_number }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($related->description, 30) }}</small>
                                </div>
                                <span class="badge bg-{{ $related->severity == 'critical' ? 'danger' : 'warning' }}">{{ ucfirst($related->severity) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">
                                No other findings in this inspection
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($finding->status == 'open')
                            <a href="{{ route('findings.resolve', $finding) }}" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Mark as Resolved
                            </a>
                        @endif
                        <a href="{{ route('corrective-actions.create', ['finding_id' => $finding->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Add Corrective Action
                        </a>
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#commentModal">
                            <i class="fas fa-comment me-1"></i> Add Comment
                        </button>
                        <a href="{{ route('findings.edit', $finding) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-1"></i> Edit Finding
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
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
                    This action cannot be undone. All associated corrective actions will also be deleted.
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

<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Finding Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('findings.update', $finding) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="open" {{ $finding->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $finding->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $finding->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $finding->status == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="status_notes" name="status_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('findings.comment', $finding) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Comment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        list-style: none;
        padding-left: 0;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
        padding-left: 2rem;
    }
    .timeline-marker {
        position: absolute;
        left: 0;
        top: 0.25rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
    }
    .timeline-content {
        margin-left: 0.5rem;
    }
</style>
@endpush
@endsection