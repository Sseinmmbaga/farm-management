<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $task->title }} - Remei Farm OS</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
        }

        .navbar {
            background-color: #27ae60;
            margin-bottom: 20px;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }

        .task-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .detail-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            height: 100%;
        }

        .detail-card h5 {
            color: #27ae60;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6c757d;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 500;
        }

        .progress-container {
            background-color: #e9ecef;
            border-radius: 10px;
            height: 10px;
            overflow: hidden;
        }

        .progress-bar-custom {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .action-btn {
            min-width: 100px;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #27ae60;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 15px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #27ae60;
            border: 2px solid white;
        }

        .assignee-badge {
            display: inline-flex;
            align-items: center;
            background-color: #f8f9fa;
            border-radius: 20px;
            padding: 5px 12px;
            margin: 3px;
        }

        .assignee-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #27ae60;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            margin-right: 8px;
        }

        .labor-record {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .subtask-item {
            display: flex;
            align-items: center;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .overdue-banner {
            background-color: #dc3545;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-seedling"></i> Remei Farm OS
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('tasks.index') }}">
                    <i class="bi bi-arrow-left"></i> Back to Tasks
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Overdue Banner -->
        @if($task->is_overdue)
            <div class="overdue-banner">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>This task is overdue!</strong> It was due on {{ $task->planned_end_date->format('M d, Y') }}
                ({{ abs($task->days_until_due) }} days ago)
            </div>
        @endif

        <!-- Task Header -->
        <div class="task-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-light text-dark me-2">{{ $task->code }}</span>
                        <span class="badge bg-{{ $task->status_color }} me-2">
                            <i class="{{ $task->status_icon }}"></i> {{ $task->status_label }}
                        </span>
                        <span class="badge bg-{{ $task->priority_color }}">
                            <i class="{{ $task->priority->icon() }}"></i> {{ $task->priority_label }}
                        </span>
                    </div>
                    <h1 class="mb-2">{{ $task->title }}</h1>
                    <p class="mb-0 opacity-75">
                        <i class="{{ $task->type_icon }}"></i> {{ $task->type_label }}
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @can('update', $task)
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-light action-btn me-2">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    @endcan

                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if($task->canBeStarted())
                                <li>
                                    <form action="{{ route('tasks.start', $task) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-play text-success"></i> Start Task
                                        </button>
                                    </form>
                                </li>
                            @endif

                            @if($task->status->value === 'in_progress')
                                <li>
                                    <form action="{{ route('tasks.hold', $task) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-pause text-warning"></i> Put On Hold
                                        </button>
                                    </form>
                                </li>
                            @endif

                            @if($task->status->value === 'on_hold')
                                <li>
                                    <form action="{{ route('tasks.resume', $task) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-play text-primary"></i> Resume Task
                                        </button>
                                    </form>
                                </li>
                            @endif

                            @if($task->canBeCompleted())
                                <li>
                                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#completeModal">
                                        <i class="bi bi-check-circle text-success"></i> Complete Task
                                    </button>
                                </li>
                            @endif

                            @if($task->canBeCancelled())
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                        <i class="bi bi-x-circle"></i> Cancel Task
                                    </button>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="{{ route('labor.create', ['task_id' => $task->id]) }}" class="dropdown-item">
                                    <i class="bi bi-clock-history text-info"></i> Log Labor
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="d-flex justify-content-between mb-1">
                    <small>Progress</small>
                    <small>{{ $task->progress_percentage }}%</small>
                </div>
                <div class="progress-container">
                    <div class="progress-bar-custom bg-light" style="width: {{ $task->progress_percentage }}%"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Description -->
                @if($task->description)
                    <div class="detail-card">
                        <h5><i class="bi bi-text-paragraph"></i> Description</h5>
                        <p class="mb-0">{!! nl2br(e($task->description)) !!}</p>
                    </div>
                @endif

                <!-- Schedule & Estimates -->
                <div class="detail-card">
                    <h5><i class="bi bi-calendar"></i> Schedule & Estimates</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Planned Start:</span>
                                <span class="info-value">{{ $task->planned_start_date?->format('M d, Y') ?? 'Not set' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Planned End:</span>
                                <span class="info-value">{{ $task->planned_end_date?->format('M d, Y') ?? 'Not set' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Duration:</span>
                                <span class="info-value">{{ $task->duration_days ? $task->duration_days . ' days' : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">Estimated Hours:</span>
                                <span class="info-value">{{ $task->estimated_hours ?? 'Not set' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Actual Hours:</span>
                                <span class="info-value">{{ $task->actual_hours ?? $task->total_labor_hours }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Estimated Cost:</span>
                                <span class="info-value">{{ $task->estimated_cost ? number_format($task->estimated_cost) . ' TZS' : 'Not set' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assignments -->
                <div class="detail-card">
                    <h5><i class="bi bi-people"></i> Assignments</h5>
                    @if($task->assignments->count() > 0)
                        <div class="d-flex flex-wrap">
                            @foreach($task->assignments as $assignment)
                                <div class="assignee-badge">
                                    <div class="assignee-avatar">
                                        {{ strtoupper(substr($assignment->assignee->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span>{{ $assignment->assignee->name ?? 'Unknown' }}</span>
                                    @can('assign', $task)
                                        <form action="{{ route('tasks.unassign', [$task, $assignment]) }}" method="POST" class="ms-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No assignees yet.</p>
                    @endif

                    @can('assign', $task)
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i class="bi bi-person-plus"></i> Add Assignee
                        </button>
                    @endcan
                </div>

                <!-- Labor Records -->
                <div class="detail-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Labor Records</h5>
                        <a href="{{ route('labor.create', ['task_id' => $task->id]) }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-plus"></i> Log Labor
                        </a>
                    </div>

                    @if($task->laborRecords->count() > 0)
                        @foreach($task->laborRecords as $labor)
                            <div class="labor-record">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $labor->worker_display_name }}</strong>
                                        <span class="badge bg-{{ $labor->labor_type_color }} ms-2">{{ $labor->labor_type_label }}</span>
                                    </div>
                                    <span class="text-muted">{{ $labor->work_date->format('M d, Y') }}</span>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-4">
                                        <small class="text-muted">Hours: {{ $labor->work_duration }}</small>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Cost: {{ number_format($labor->total_cost) }} TZS</small>
                                    </div>
                                    <div class="col-4 text-end">
                                        <span class="badge bg-{{ $labor->payment_status_color }}">{{ $labor->payment_status_label }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="text-center mt-3">
                            <a href="{{ route('labor.index', ['task_id' => $task->id]) }}" class="btn btn-sm btn-link">
                                View All Labor Records <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <p class="text-muted mb-0">No labor records yet.</p>
                    @endif
                </div>

                <!-- Notes -->
                @if($task->notes)
                    <div class="detail-card">
                        <h5><i class="bi bi-sticky"></i> Notes</h5>
                        <p class="mb-0">{!! nl2br(e($task->notes)) !!}</p>
                    </div>
                @endif

                <!-- Completion Notes -->
                @if($task->isCompleted() && $task->completion_notes)
                    <div class="detail-card">
                        <h5><i class="bi bi-check-circle"></i> Completion Notes</h5>
                        <p class="mb-0">{!! nl2br(e($task->completion_notes)) !!}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Location -->
                <div class="detail-card">
                    <h5><i class="bi bi-geo-alt"></i> Location</h5>
                    <div class="info-row">
                        <span class="info-label">Farm:</span>
                        <span class="info-value">
                            @if($task->farm)
                                <a href="{{ route('farms.show', $task->farm) }}">{{ $task->farm->name }}</a>
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Field:</span>
                        <span class="info-value">{{ $task->field->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Farmer:</span>
                        <span class="info-value">{{ $task->farmer->full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Season:</span>
                        <span class="info-value">{{ $task->season->name ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="detail-card">
                    <h5><i class="bi bi-list-check"></i> Requirements</h5>

                    @if($task->equipment_required && count($task->equipment_required) > 0)
                        <p class="mb-2"><strong>Equipment:</strong></p>
                        <ul class="mb-3">
                            @foreach($task->equipment_required as $equipment)
                                <li>{{ $equipment }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if($task->materials_required && count($task->materials_required) > 0)
                        <p class="mb-2"><strong>Materials:</strong></p>
                        <ul class="mb-0">
                            @foreach($task->materials_required as $material)
                                <li>{{ $material }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if((!$task->equipment_required || count($task->equipment_required) == 0) && (!$task->materials_required || count($task->materials_required) == 0))
                        <p class="text-muted mb-0">No requirements specified.</p>
                    @endif
                </div>

                <!-- Summary Statistics -->
                <div class="detail-card">
                    <h5><i class="bi bi-bar-chart"></i> Summary</h5>
                    <div class="info-row">
                        <span class="info-label">Total Labor Hours:</span>
                        <span class="info-value">{{ number_format($task->total_labor_hours, 1) }} hrs</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Labor Cost:</span>
                        <span class="info-value">{{ number_format($task->total_labor_cost) }} TZS</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Assignees:</span>
                        <span class="info-value">{{ $task->assignments->count() }}</span>
                    </div>
                </div>

                <!-- Audit Info -->
                <div class="detail-card">
                    <h5><i class="bi bi-clock"></i> History</h5>
                    <div class="timeline">
                        <div class="timeline-item">
                            <small class="text-muted">Created</small><br>
                            <strong>{{ $task->created_at->format('M d, Y H:i') }}</strong>
                        </div>

                        @if($task->actual_start_date)
                            <div class="timeline-item">
                                <small class="text-muted">Started</small><br>
                                <strong>{{ $task->actual_start_date->format('M d, Y H:i') }}</strong>
                            </div>
                        @endif

                        @if($task->completed_at)
                            <div class="timeline-item">
                                <small class="text-muted">Completed</small><br>
                                <strong>{{ $task->completed_at->format('M d, Y H:i') }}</strong>
                                @if($task->completedByUser)
                                    <br><small>by {{ $task->completedByUser->name }}</small>
                                @endif
                            </div>
                        @endif

                        @if($task->cancelled_at)
                            <div class="timeline-item">
                                <small class="text-muted">Cancelled</small><br>
                                <strong>{{ $task->cancelled_at->format('M d, Y H:i') }}</strong>
                                @if($task->cancellation_reason)
                                    <br><small class="text-danger">{{ $task->cancellation_reason }}</small>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Task Modal -->
    <div class="modal fade" id="completeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tasks.complete', $task) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="completion_notes" class="form-label">Completion Notes</label>
                            <textarea class="form-control" id="completion_notes" name="completion_notes" rows="3"
                                      placeholder="Add any completion notes..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="actual_hours" class="form-label">Actual Hours</label>
                                <input type="number" step="0.5" min="0" class="form-control" id="actual_hours"
                                       name="actual_hours" value="{{ $task->total_labor_hours ?: $task->estimated_hours }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="actual_cost" class="form-label">Actual Cost (TZS)</label>
                                <input type="number" step="100" min="0" class="form-control" id="actual_cost"
                                       name="actual_cost" value="{{ $task->total_labor_cost ?: $task->estimated_cost }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Complete Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Task Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tasks.cancel', $task) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> This action cannot be undone.
                        </div>
                        <div class="mb-3">
                            <label for="cancellation_reason" class="form-label">Cancellation Reason *</label>
                            <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3"
                                      required placeholder="Please provide a reason for cancellation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Task</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Cancel Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tasks.assign', $task) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Users</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="assignees" class="form-label">Select Users</label>
                            <select class="form-select" id="modal_assignees" name="assignees[]" multiple size="8">
                                {{-- Users list would be populated from controller --}}
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple users</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
