<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tasks - Remei Farm OS</title>

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

        .task-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            transition: transform 0.3s;
            border-left: 4px solid;
        }

        .task-card:hover {
            transform: translateY(-3px);
        }

        .task-card.priority-urgent {
            border-left-color: #dc3545;
        }

        .task-card.priority-high {
            border-left-color: #ffc107;
        }

        .task-card.priority-medium {
            border-left-color: #17a2b8;
        }

        .task-card.priority-low {
            border-left-color: #6c757d;
        }

        .filter-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .status-badge {
            font-size: 0.75rem;
        }

        .task-meta {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .progress-bar-custom {
            height: 6px;
            border-radius: 3px;
        }

        .overdue-indicator {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .stats-card .stat-item {
            text-align: center;
            padding: 10px;
        }

        .stats-card .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .stats-card .stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
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
                @auth
                    <a class="nav-link" href="/dashboard">Dashboard</a>
                    <a class="nav-link active" href="{{ route('tasks.index') }}">Tasks</a>
                    <a class="nav-link" href="{{ route('tasks.calendar') }}">Calendar</a>
                    <a class="nav-link" href="{{ route('labor.index') }}">Labor</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-list-task"></i> Task Management</h1>
            <div>
                <a href="{{ route('tasks.calendar') }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-calendar3"></i> Calendar View
                </a>
                @can('create', App\Models\Tasks\Task::class)
                    <a href="{{ route('tasks.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Create Task
                    </a>
                @endcan
            </div>
        </div>

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

        <!-- Stats Overview -->
        <div class="stats-card">
            <div class="row">
                <div class="col-md-3 stat-item">
                    <div class="stat-number">{{ $tasks->total() }}</div>
                    <div class="stat-label">Total Tasks</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number">{{ $tasks->where('status', 'in_progress')->count() }}</div>
                    <div class="stat-label">In Progress</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number">{{ $tasks->where('is_overdue', true)->count() }}</div>
                    <div class="stat-label">Overdue</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number">{{ $tasks->where('status', 'completed')->count() }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="{{ route('tasks.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Search tasks...">
                </div>

                <div class="col-md-2">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        @foreach($taskTypes as $type)
                            <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        @foreach($taskStatuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priorities</option>
                        @foreach($taskPriorities as $priority)
                            <option value="{{ $priority->value }}" {{ request('priority') == $priority->value ? 'selected' : '' }}>
                                {{ $priority->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="farm_id" class="form-label">Farm</label>
                    <select class="form-select" id="farm_id" name="farm_id">
                        <option value="">All Farms</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ request('farm_id') == $farm->id ? 'selected' : '' }}>
                                {{ $farm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>

            <!-- Additional Filters (Collapsed) -->
            <div class="collapse mt-3" id="moreFilters">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="season_id" class="form-label">Season</label>
                        <select class="form-select" id="season_id" name="season_id" form="filterForm">
                            <option value="">All Seasons</option>
                            @foreach($seasons as $season)
                                <option value="{{ $season->id }}" {{ request('season_id') == $season->id ? 'selected' : '' }}>
                                    {{ $season->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                               value="{{ request('date_from') }}" form="filterForm">
                    </div>

                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                               value="{{ request('date_to') }}" form="filterForm">
                    </div>

                    <div class="col-md-3">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select class="form-select" id="sort_by" name="sort_by" form="filterForm">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                            <option value="planned_start_date" {{ request('sort_by') == 'planned_start_date' ? 'selected' : '' }}>Start Date</option>
                            <option value="planned_end_date" {{ request('sort_by') == 'planned_end_date' ? 'selected' : '' }}>Due Date</option>
                            <option value="priority" {{ request('sort_by') == 'priority' ? 'selected' : '' }}>Priority</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="text-center mt-2">
                <a class="btn btn-sm btn-link" data-bs-toggle="collapse" href="#moreFilters">
                    <i class="bi bi-chevron-down"></i> More Filters
                </a>
                @if(request()->hasAny(['search', 'type', 'status', 'priority', 'farm_id', 'season_id', 'date_from', 'date_to']))
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-link text-danger">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </a>
                @endif
            </div>
        </div>

        <!-- Tasks List -->
        <div class="row">
            @forelse ($tasks as $task)
                <div class="col-md-6 col-lg-4">
                    <div class="card task-card priority-{{ $task->priority->value }}">
                        <div class="card-body">
                            <!-- Status & Priority Badges -->
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-{{ $task->status_color }} status-badge">
                                    <i class="{{ $task->status_icon }}"></i> {{ $task->status_label }}
                                </span>
                                <span class="badge bg-{{ $task->priority_color }} status-badge">
                                    <i class="{{ $task->priority->icon() }}"></i> {{ $task->priority_label }}
                                </span>
                            </div>

                            <!-- Task Title -->
                            <h5 class="card-title mb-1">
                                <i class="{{ $task->type_icon }} text-muted"></i>
                                {{ Str::limit($task->title, 40) }}
                            </h5>
                            <p class="text-muted small mb-2">{{ $task->code }}</p>

                            <!-- Task Type -->
                            <span class="badge bg-light text-dark mb-2">{{ $task->type_label }}</span>

                            <!-- Progress Bar -->
                            <div class="progress progress-bar-custom mb-2">
                                <div class="progress-bar bg-{{ $task->status_color }}"
                                     style="width: {{ $task->progress_percentage }}%"></div>
                            </div>

                            <!-- Task Meta -->
                            <div class="task-meta">
                                @if($task->farm)
                                    <p class="mb-1">
                                        <i class="bi bi-geo-alt"></i> {{ $task->farm->name }}
                                        @if($task->field)
                                            - {{ $task->field->name }}
                                        @endif
                                    </p>
                                @endif

                                @if($task->farmer)
                                    <p class="mb-1">
                                        <i class="bi bi-person"></i> {{ $task->farmer->full_name }}
                                    </p>
                                @endif

                                @if($task->planned_start_date || $task->planned_end_date)
                                    <p class="mb-1">
                                        <i class="bi bi-calendar"></i>
                                        @if($task->planned_start_date)
                                            {{ $task->planned_start_date->format('M d') }}
                                        @endif
                                        @if($task->planned_start_date && $task->planned_end_date)
                                            -
                                        @endif
                                        @if($task->planned_end_date)
                                            {{ $task->planned_end_date->format('M d, Y') }}
                                        @endif
                                    </p>
                                @endif

                                @if($task->assignee_names)
                                    <p class="mb-1">
                                        <i class="bi bi-people"></i> {{ Str::limit($task->assignee_names, 30) }}
                                    </p>
                                @endif
                            </div>

                            <!-- Overdue Indicator -->
                            @if($task->is_overdue)
                                <div class="alert alert-danger py-1 px-2 mb-2 overdue-indicator">
                                    <small><i class="bi bi-exclamation-triangle"></i> Overdue by {{ abs($task->days_until_due) }} days</small>
                                </div>
                            @elseif($task->days_until_due !== null && $task->days_until_due <= 3 && $task->days_until_due >= 0)
                                <div class="alert alert-warning py-1 px-2 mb-2">
                                    <small><i class="bi bi-clock"></i> Due in {{ $task->days_until_due }} days</small>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between mt-3">
                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @can('update', $task)
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @endcan
                                @if($task->canBeStarted())
                                    <form action="{{ route('tasks.start', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-play"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No tasks found.
                        @can('create', App\Models\Tasks\Task::class)
                            <a href="{{ route('tasks.create') }}" class="alert-link">Create your first task</a>.
                        @endcan
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($tasks->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
