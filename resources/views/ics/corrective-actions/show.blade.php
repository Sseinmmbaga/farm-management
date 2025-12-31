@extends('layouts.base')

@section('title', 'Corrective Action: ' . $correctiveAction->action_number)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="display-6 mb-1">
                                <i class="fas fa-tools me-2"></i>
                                {{ $correctiveAction->action_number }}
                            </h1>
                            <p class="lead mb-2">
                                {{ ucfirst(str_replace('_', ' ', $correctiveAction->action_type)) }} • 
                                <a href="{{ route('findings.show', $correctiveAction->finding) }}" class="text-decoration-none">
                                    Finding: {{ $correctiveAction->finding->finding_number }}
                                </a>
                            </p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $correctiveAction->status == 'completed' ? 'success' : ($correctiveAction->status == 'in_progress' ? 'warning' : ($correctiveAction->status == 'verified' ? 'info' : ($correctiveAction->status == 'ineffective' ? 'danger' : 'secondary'))) }} fs-6 px-3 py-2">
                                    {{ ucfirst($correctiveAction->status) }}
                                </span>
                                @if($correctiveAction->planned_date && $correctiveAction->planned_date->isPast() && !in_array($correctiveAction->status, ['completed', 'verified']))
                                    <span class="badge bg-danger ms-2 fs-6 px-3 py-2">Overdue</span>
                                @endif
                                @if($correctiveAction->priority == 'high')
                                    <span class="badge bg-warning ms-2 fs-6 px-3 py-2">High Priority</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('corrective-actions.edit', $correctiveAction) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i> Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if($correctiveAction->status == 'in_progress')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('corrective-actions.complete', $correctiveAction) }}" onclick="return confirm('Mark this action as completed?')">
                                                <i class="fas fa-check text-success me-2"></i> Mark as Completed
                                            </a>
                                        </li>
                                    @endif
                                    @if($correctiveAction->status == 'completed')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('corrective-actions.verify', $correctiveAction) }}" onclick="return confirm('Verify this corrective action?')">
                                                <i class="fas fa-clipboard-check text-primary me-2"></i> Verify Action
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item" href="{{ route('findings.show', $correctiveAction->finding) }}">
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i> View Finding
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash me-2"></i> Delete Action
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
                    <ul class="nav nav-tabs card-header-tabs" id="actionTabs" role="tablist">
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
                            <button class="nav-link" id="evidence-tab" data-bs-toggle="tab" data-bs-target="#evidence" type="button">
                                <i class="fas fa-paperclip me-2"></i> Evidence & Verification
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="actionTabsContent">
                        <!-- Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Finding Details
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Finding</th>
                                            <td>
                                                <a href="{{ route('findings.show', $correctiveAction->finding) }}" class="text-decoration-none">
                                                    <strong>{{ $correctiveAction->finding->finding_number }}</strong>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Inspection</th>
                                            <td>
                                                <a href="{{ route('inspections.show', $correctiveAction->finding->inspection) }}" class="text-decoration-none">
                                                    {{ $correctiveAction->finding->inspection->inspection_number }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Farmer</th>
                                            <td>
                                                {{ $correctiveAction->finding->inspection->farmer->first_name ?? '' }} 
                                                {{ $correctiveAction->finding->inspection->farmer->last_name ?? '' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Severity</th>
                                            <td>
                                                <span class="badge bg-{{ $correctiveAction->finding->severity == 'critical' ? 'danger' : ($correctiveAction->finding->severity == 'major' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($correctiveAction->finding->severity) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-tools me-2"></i>Action Details
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Action Type</th>
                                            <td>{{ ucfirst(str_replace('_', ' ', $correctiveAction->action_type)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created By</th>
                                            <td>{{ $correctiveAction->createdBy->name ?? 'System' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created Date</th>
                                            <td>{{ $correctiveAction->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Priority</th>
                                            <td>
                                                <span class="badge bg-{{ $correctiveAction->priority == 'high' ? 'danger' : ($correctiveAction->priority == 'medium' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($correctiveAction->priority ?? 'medium') }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-align-left me-2"></i>Description
                                    </h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">English</h6>
                                            <p class="card-text">{{ $correctiveAction->description }}</p>
                                        </div>
                                    </div>
                                    @if($correctiveAction->description_sw)
                                    <div class="card bg-light mt-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Swahili</h6>
                                            <p class="card-text">{{ $correctiveAction->description_sw }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Planning & Resources -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-calendar-alt me-2"></i>Planning
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="50%">Planned Date</th>
                                            <td>
                                                @if($correctiveAction->planned_date)
                                                    {{ $correctiveAction->planned_date->format('M d, Y') }}
                                                    @if($correctiveAction->planned_date->isPast() && !in_array($correctiveAction->status, ['completed', 'verified']))
                                                        <br>
                                                        <small class="text-danger">Overdue by {{ $correctiveAction->planned_date->diffInDays(now()) }} days</small>
                                                    @endif
                                                @else
                                                    Not set
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Actual Completion</th>
                                            <td>
                                                @if($correctiveAction->completion_date)
                                                    {{ $correctiveAction->completion_date->format('M d, Y') }}
                                                @else
                                                    Not completed
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Estimated Cost</th>
                                            <td>
                                                @if($correctiveAction->estimated_cost)
                                                    {{ number_format($correctiveAction->estimated_cost) }} TZS
                                                @else
                                                    Not specified
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-users me-2"></i>Resources & Responsibility
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="50%">Responsible Person</th>
                                            <td>
                                                <strong>{{ $correctiveAction->responsiblePerson->name ?? 'Unassigned' }}</strong>
                                                @if($correctiveAction->responsiblePerson)
                                                    <br>
                                                    <small class="text-muted">{{ $correctiveAction->responsiblePerson->email ?? '' }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Resources Required</th>
                                            <td>{{ $correctiveAction->resources_required ?: 'None specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Progress</th>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $correctiveAction->status == 'completed' ? 'success' : ($correctiveAction->status == 'in_progress' ? 'warning' : 'info') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $correctiveAction->progress_percentage ?? 0 }}%;"
                                                         aria-valuenow="{{ $correctiveAction->progress_percentage ?? 0 }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ $correctiveAction->progress_percentage ?? 0 }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Tab -->
                        <div class="tab-pane fade" id="timeline" role="tabpanel">
                            <ul class="timeline">
                                <li class="timeline-item">
                                    <span class="timeline-marker bg-primary"></span>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">Action Created</h6>
                                        <small class="text-muted">{{ $correctiveAction->created_at->format('M d, Y H:i') }}</small>
                                        <p class="mb-0">Created by {{ $correctiveAction->createdBy->name ?? 'System' }}</p>
                                    </div>
                                </li>
                                @if($correctiveAction->planned_date)
                                <li class="timeline-item">
                                    <span class="timeline-marker bg-info"></span>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">Planned Date Set</h6>
                                        <small class="text-muted">{{ $correctiveAction->planned_date->format('M d, Y') }}</small>
                                        <p class="mb-0">Target completion date</p>
                                    </div>
                                </li>
                                @endif
                                @if($correctiveAction->status_updates && count($correctiveAction->status_updates) > 0)
                                    @foreach($correctiveAction->status_updates as $update)
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
                                @if($correctiveAction->completion_date)
                                <li class="timeline-item">
                                    <span class="timeline-marker bg-success"></span>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">Action Completed</h6>
                                        <small class="text-muted">{{ $correctiveAction->completion_date->format('M d, Y H:i') }}</small>
                                        <p class="mb-0">{{ $correctiveAction->completion_notes }}</p>
                                    </div>
                                </li>
                                @endif
                                @if($correctiveAction->verified_at)
                                <li class="timeline-item">
                                    <span class="timeline-marker bg-dark"></span>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">Action Verified</h6>
                                        <small class="text-muted">{{ $correctiveAction->verified_at->format('M d, Y H:i') }}</small>
                                        <p class="mb-0">Verified by {{ $correctiveAction->verifiedBy->name ?? 'Unknown' }}</p>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Evidence & Verification Tab -->
                        <div class="tab-pane fade" id="evidence" role="tabpanel">
                            @if($correctiveAction->status == 'completed' || $correctiveAction->status == 'verified')
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    This corrective action has been completed and verified.
                                </div>
                            @endif

                            <!-- Completion Evidence -->
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-clipboard-check me-2"></i>Completion Evidence
                            </h5>
                            @if($correctiveAction->evidence_of_completion)
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Evidence Description</h6>
                                        <p class="card-text">{{ $correctiveAction->evidence_of_completion }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No evidence of completion has been provided.
                                </div>
                            @endif

                            <!-- Verification -->
                            @if($correctiveAction->verified)
                                <div class="mt-4">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-user-check me-2"></i>Verification Details
                                    </h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Verified By:</strong> {{ $correctiveAction->verifiedBy->name ?? 'Unknown' }}<br>
                                                    <strong>Verification Date:</strong> {{ $correctiveAction->verified_at->format('M d, Y') }}<br>
                                                    <strong>Is Effective:</strong> 
                                                    @if($correctiveAction->is_effective)
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Verification Notes:</strong><br>
                                                    {{ $correctiveAction->verification_notes ?: 'No notes provided.' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($correctiveAction->status == 'completed')
                                <div class="mt-4">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        This action is completed but not yet verified.
                                    </div>
                                    <a href="{{ route('corrective-actions.verify', $correctiveAction) }}" class="btn btn-primary">
                                        <i class="fas fa-clipboard-check me-1"></i> Verify Action
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
                                    <h5 class="mb-0">{{ $correctiveAction->days_since_creation ?? 0 }}</h5>
                                    <small class="text-muted">Days Since Creation</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-warning">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">{{ $correctiveAction->days_until_due ?? 0 }}</h5>
                                    <small class="text-muted">Days Until Due</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <strong>Effectiveness:</strong>
                        @if($correctiveAction->is_effective)
                            <span class="badge bg-success">Effective</span>
                        @elseif($correctiveAction->status == 'ineffective')
                            <span class="badge bg-danger">Ineffective</span>
                        @else
                            <span class="badge bg-secondary">Not Evaluated</span>
                        @endif
                    </div>
                    @if($correctiveAction->requires_verification && !$correctiveAction->verified)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Requires verification by supervisor.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Actions -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-link me-2"></i> Related Actions
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($correctiveAction->finding->correctiveActions->where('id', '!=', $correctiveAction->id)->take(3) as $related)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('corrective-actions.show', $related) }}" class="text-decoration-none">
                                        <strong>{{ $related->action_number }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($related->description, 30) }}</small>
                                </div>
                                <span class="badge bg-{{ $related->status == 'completed' ? 'success' : 'warning' }}">{{ ucfirst($related->status) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">
                                No other actions for this finding
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
                        @if($correctiveAction->status == 'in_progress')
                            <a href="{{ route('corrective-actions.complete', $correctiveAction) }}" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Mark as Completed
                            </a>
                        @endif
                        @if($correctiveAction->status == 'completed' && !$correctiveAction->verified)
                            <a href="{{ route('corrective-actions.verify', $correctiveAction) }}" class="btn btn-primary">
                                <i class="fas fa-clipboard-check me-1"></i> Verify Action
                            </a>
                        @endif
                        <a href="{{ route('corrective-actions.edit', $correctiveAction) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-1"></i> Edit Action
                        </a>
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#commentModal">
                            <i class="fas fa-comment me-1"></i> Add Comment
                        </button>
                        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i> Delete Action
                        </button>
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
                <p>Are you sure you want to delete corrective action <strong>{{ $correctiveAction->action_number }}</strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    This action cannot be undone. The associated finding will not be affected.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('corrective-actions.destroy', $correctiveAction) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Action</button>
                </form>
            </div>
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
            <form method="POST" action="{{ route('corrective-actions.comment', $correctiveAction) }}">
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