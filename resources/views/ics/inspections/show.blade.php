@extends('layouts.base')

@section('title', 'Inspection Details: ' . $inspection->inspection_number)

@section('content')
<div class="container-fluid">
    <!-- Header with Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="display-6 mb-1">
                                <i class="fas fa-clipboard-check me-2"></i>
                                {{ $inspection->inspection_number }}
                            </h1>
                            <p class="lead mb-2">
                                {{ $inspection->checklist->name ?? 'N/A' }}
                                @if($inspection->checklist->code)
                                    <span class="text-muted">({{ $inspection->checklist->code }})</span>
                                @endif
                            </p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $inspection->status == 'scheduled' ? 'info' : ($inspection->status == 'in_progress' ? 'warning' : ($inspection->status == 'completed' ? 'success' : 'secondary')) }} fs-6 px-3 py-2">
                                    {{ $inspection->status_label }}
                                </span>
                                @if($inspection->isOverdue)
                                    <span class="badge bg-danger ms-2 fs-6 px-3 py-2">Overdue</span>
                                @endif
                                @if($inspection->result)
                                    <span class="badge bg-{{ $inspection->result == 'passed' ? 'success' : ($inspection->result == 'failed' ? 'danger' : ($inspection->result == 'conditional' ? 'warning' : 'secondary')) }} ms-2 fs-6 px-3 py-2">
                                        {{ $inspection->result_label }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('inspections.edit', $inspection) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i> Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if($inspection->status == 'scheduled')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('inspections.start', $inspection) }}" onclick="return confirm('Start inspection now?')">
                                                <i class="fas fa-play text-success me-2"></i> Start Inspection
                                            </a>
                                        </li>
                                    @endif
                                    @if($inspection->status == 'in_progress')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('inspections.complete', $inspection) }}" onclick="return confirm('Mark inspection as completed?')">
                                                <i class="fas fa-check text-primary me-2"></i> Complete Inspection
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item" href="{{ route('findings.create', ['inspection_id' => $inspection->id]) }}">
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i> Report Finding
                                        </a>
                                    </li>
                                    @if($inspection->status == 'completed')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('inspections.sign', $inspection) }}" onclick="return confirm('Sign off on this inspection?')">
                                                <i class="fas fa-signature text-info me-2"></i> Sign Off
                                            </a>
                                        </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                            <i class="fas fa-times me-2"></i> Cancel Inspection
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
            <!-- Tabs -->
            <div class="card mb-4">
                <div class="card-header bg-white border-bottom-0">
                    <ul class="nav nav-tabs card-header-tabs" id="inspectionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
                                <i class="fas fa-info-circle me-2"></i> Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="responses-tab" data-bs-toggle="tab" data-bs-target="#responses" type="button">
                                <i class="fas fa-list-check me-2"></i> Checklist Responses
                                <span class="badge bg-primary ms-1">{{ $inspection->responses->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="findings-tab" data-bs-toggle="tab" data-bs-target="#findings" type="button">
                                <i class="fas fa-exclamation-triangle me-2"></i> Findings
                                <span class="badge bg-danger ms-1">{{ $inspection->findings->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attachments-tab" data-bs-toggle="tab" data-bs-target="#attachments" type="button">
                                <i class="fas fa-paperclip me-2"></i> Attachments
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="inspectionTabsContent">
                        <!-- Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-user-tie me-2"></i>Farmer & Farm Information
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Farmer</th>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ substr($inspection->farmer->first_name ?? '', 0, 1) }}{{ substr($inspection->farmer->last_name ?? '', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $inspection->farmer->first_name ?? '' }} {{ $inspection->farmer->last_name ?? '' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $inspection->farmer->registration_number ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Farm</th>
                                            <td>{{ $inspection->farm->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Location</th>
                                            <td>
                                                @if($inspection->farm && $inspection->farm->village)
                                                    {{ $inspection->farm->village->name ?? '' }}, {{ $inspection->farm->district->name ?? '' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Contact</th>
                                            <td>{{ $inspection->farmer->phone ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-calendar-alt me-2"></i>Inspection Schedule
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Scheduled Date</th>
                                            <td>
                                                @if($inspection->scheduled_date)
                                                    {{ $inspection->scheduled_date->format('l, M d, Y') }}
                                                    @if($inspection->isOverdue)
                                                        <span class="badge bg-danger ms-2">Overdue</span>
                                                    @endif
                                                @else
                                                    Not scheduled
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Inspection Date</th>
                                            <td>
                                                @if($inspection->inspection_date)
                                                    {{ $inspection->inspection_date->format('M d, Y') }}
                                                @else
                                                    Not recorded
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Start Time</th>
                                            <td>
                                                @if($inspection->start_time)
                                                    {{ $inspection->start_time->format('h:i A') }}
                                                @else
                                                    Not started
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Duration</th>
                                            <td>
                                                @if($inspection->duration_minutes)
                                                    {{ $inspection->duration_minutes }} minutes
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Inspection Results -->
                            @if($inspection->status == 'completed')
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-chart-bar me-2"></i>Inspection Results
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card border-success">
                                                <div class="card-body text-center">
                                                    <h1 class="display-4 text-success">
                                                        {{ $inspection->percentage_score ?? 0 }}%
                                                    </h1>
                                                    <p class="text-muted mb-0">Overall Score</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h3 class="text-success">{{ $inspection->items_compliant ?? 0 }}</h3>
                                                            <p class="text-muted mb-0">Compliant Items</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h3 class="text-danger">{{ $inspection->items_non_compliant ?? 0 }}</h3>
                                                            <p class="text-muted mb-0">Non-Compliant</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h3 class="text-warning">{{ $inspection->items_na ?? 0 }}</h3>
                                                            <p class="text-muted mb-0">Not Applicable</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Notes and Observations -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-sticky-note me-2"></i>Notes & Observations
                                    </h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Summary</h6>
                                            <p class="card-text">{{ $inspection->summary ?? 'No summary provided.' }}</p>
                                        </div>
                                    </div>
                                    @if($inspection->observations)
                                    <div class="card bg-light mt-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Observations</h6>
                                            <p class="card-text">{{ $inspection->observations }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($inspection->recommendations)
                                    <div class="card bg-light mt-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Recommendations</h6>
                                            <p class="card-text">{{ $inspection->recommendations }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Responses Tab -->
                        <div class="tab-pane fade" id="responses" role="tabpanel">
                            @if($inspection->responses->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Requirement</th>
                                                <th>Response</th>
                                                <th>Notes</th>
                                                <th>Evidence</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inspection->responses as $response)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $response->checklistItem->code ?? 'N/A' }}</strong>
                                                    </td>
                                                    <td>{{ $response->checklistItem->description ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($response->response == 'compliant')
                                                            <span class="badge bg-success">Compliant</span>
                                                        @elseif($response->response == 'non_compliant')
                                                            <span class="badge bg-danger">Non-Compliant</span>
                                                        @else
                                                            <span class="badge bg-secondary">Not Applicable</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $response->notes ?? '-' }}</td>
                                                    <td>
                                                        @if($response->evidence)
                                                            <a href="{{ asset('storage/' . $response->evidence) }}" target="_blank">View</a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-list-check fa-3x text-muted mb-3"></i>
                                    <h5>No responses recorded</h5>
                                    <p class="text-muted">Responses will appear once the inspection is started.</p>
                                    @if($inspection->status == 'in_progress')
                                        <a href="{{ route('inspections.edit', $inspection) }}" class="btn btn-primary">
                                            <i class="fas fa-edit me-1"></i> Record Responses
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Findings Tab -->
                        <div class="tab-pane fade" id="findings" role="tabpanel">
                            @if($inspection->findings->count() > 0)
                                <div class="mb-3">
                                    <a href="{{ route('findings.create', ['inspection_id' => $inspection->id]) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i> Add Finding
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Finding #</th>
                                                <th>Description</th>
                                                <th>Severity</th>
                                                <th>Status</th>
                                                <th>Due Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inspection->findings as $finding)
                                                <tr>
                                                    <td>
                                                        <strong class="text-danger">{{ $finding->finding_number }}</strong>
                                                    </td>
                                                    <td>{{ Str::limit($finding->description, 50) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $finding->severity == 'critical' ? 'danger' : ($finding->severity == 'major' ? 'warning' : 'secondary') }}">
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
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('findings.show', $finding) }}" class="btn btn-outline-primary btn-sm">
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
                                    <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                                    <h5>No findings reported</h5>
                                    <p class="text-muted">All checklist items are compliant or no non-conformities identified.</p>
                                    <a href="{{ route('findings.create', ['inspection_id' => $inspection->id]) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Report Finding
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Attachments Tab -->
                        <div class="tab-pane fade" id="attachments" role="tabpanel">
                            <div class="mb-3">
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fas fa-upload me-1"></i> Upload Attachment
                                </button>
                            </div>
                            @if($inspection->images && count($inspection->images) > 0)
                                <div class="row">
                                    @foreach($inspection->images as $image)
                                        <div class="col-md-4 mb-3">
                                            <div class="card">
                                                <img src="{{ asset('storage/' . $image) }}" class="card-img-top" alt="Inspection Image">
                                                <div class="card-body">
                                                    <p class="card-text">
                                                        <small class="text-muted">Uploaded on {{ $inspection->created_at->format('M d, Y') }}</small>
                                                    </p>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ asset('storage/' . $image) }}" target="_blank" class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button class="btn btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
                                    <h5>No attachments</h5>
                                    <p class="text-muted">Upload images, documents, or other files related to this inspection.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Stats & Actions -->
        <div class="col-lg-4">
            <!-- Inspector Card -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-check me-2"></i> Inspector
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                            {{ substr($inspection->inspector->name ?? 'I', 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $inspection->inspector->name ?? 'N/A' }}</h6>
                            <small class="text-muted">{{ $inspection->inspector->email ?? '' }}</small>
                        </div>
                    </div>
                    <div class="mb-2">
                        <strong>Organization:</strong> {{ $inspection->inspector_organization ?? 'Internal ICS' }}
                    </div>
                    <div class="mb-2">
                        <strong>Assigned:</strong> {{ $inspection->created_at->format('M d, Y') }}
                    </div>
                    @if($inspection->signed_by_inspector)
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-signature me-2"></i>
                            Signed off on {{ $inspection->signed_at->format('M d, Y') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i> Quick Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="card border-info">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">{{ $inspection->checklist->items_count ?? 0 }}</h5>
                                    <small class="text-muted">Total Items</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-success">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">{{ $inspection->responses->count() }}</h5>
                                    <small class="text-muted">Responses</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-danger">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">{{ $inspection->findings->count() }}</h5>
                                    <small class="text-muted">Findings</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-warning">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">{{ $inspection->followUps->count() }}</h5>
                                    <small class="text-muted">Follow-ups</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Timeline -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i> Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <span class="timeline-marker bg-primary"></span>
                            <div class="timeline-content">
                                <h6 class="mb-0">Created</h6>
                                <small class="text-muted">{{ $inspection->created_at->format('M d, Y H:i') }}</small>
                            </div>
                        </li>
                        @if($inspection->scheduled_date)
                        <li class="timeline-item">
                            <span class="timeline-marker bg-info"></span>
                            <div class="timeline-content">
                                <h6 class="mb-0">Scheduled</h6>
                                <small class="text-muted">{{ $inspection->scheduled_date->format('M d, Y') }}</small>
                            </div>
                        </li>
                        @endif
                        @if($inspection->started_at)
                        <li class="timeline-item">
                            <span class="timeline-marker bg-warning"></span>
                            <div class="timeline-content">
                                <h6 class="mb-0">Started</h6>
                                <small class="text-muted">{{ $inspection->started_at->format('M d, Y H:i') }}</small>
                            </div>
                        </li>
                        @endif
                        @if($inspection->completed_at)
                        <li class="timeline-item">
                            <span class="timeline-marker bg-success"></span>
                            <div class="timeline-content">
                                <h6 class="mb-0">Completed</h6>
                                <small class="text-muted">{{ $inspection->completed_at->format('M d, Y H:i') }}</small>
                            </div>
                        </li>
                        @endif
                        @if($inspection->signed_at)
                        <li class="timeline-item">
                            <span class="timeline-marker bg-dark"></span>
                            <div class="timeline-content">
                                <h6 class="mb-0">Signed Off</h6>
                                <small class="text-muted">{{ $inspection->signed_at->format('M d, Y H:i') }}</small>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Inspection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel inspection <strong>{{ $inspection->inspection_number }}</strong>?</p>
                <div class="mb-3">
                    <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                    <textarea class="form-control" id="cancelReason" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="{{ route('inspections.cancel', $inspection) }}" method="POST">
                    @csrf
                    <input type="hidden" name="reason" id="cancelReasonInput">
                    <button type="submit" class="btn btn-danger">Cancel Inspection</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Attachment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('inspections.upload', $inspection) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="attachment" class="form-label">Select File</label>
                        <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx">
                    </div>
                    <div class="mb-3">
                        <label for="attachmentDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="attachmentDescription" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
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
        padding-bottom: 1rem;
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cancel modal reason sync
        const cancelTextarea = document.getElementById('cancelReason');
        const cancelInput = document.getElementById('cancelReasonInput');
        cancelTextarea.addEventListener('input', function() {
            cancelInput.value = this.value;
        });
    });
</script>
@endpush
@endsection