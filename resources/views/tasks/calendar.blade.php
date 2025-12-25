<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task Calendar - Remei Farm OS</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
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

        .calendar-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        #calendar {
            max-width: 100%;
        }

        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            padding: 2px 5px;
            font-size: 0.85rem;
        }

        .fc-toolbar-title {
            color: #27ae60;
        }

        .fc-button-primary {
            background-color: #27ae60 !important;
            border-color: #27ae60 !important;
        }

        .fc-button-primary:hover {
            background-color: #1e8449 !important;
            border-color: #1e8449 !important;
        }

        .fc-button-primary:not(:disabled).fc-button-active {
            background-color: #1e8449 !important;
            border-color: #1e8449 !important;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            margin-right: 15px;
            font-size: 0.85rem;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
            margin-right: 6px;
        }

        .task-popup {
            max-width: 300px;
        }

        .fc-daygrid-event {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .today-tasks {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .today-tasks h5 {
            color: #27ae60;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .task-list-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 8px;
            background-color: #f8f9fa;
            transition: background-color 0.2s;
        }

        .task-list-item:hover {
            background-color: #e9ecef;
        }

        .task-priority-indicator {
            width: 4px;
            height: 100%;
            min-height: 40px;
            border-radius: 2px;
            margin-right: 12px;
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
                    <a class="nav-link" href="{{ route('tasks.index') }}">Tasks List</a>
                    <a class="nav-link active" href="{{ route('tasks.calendar') }}">Calendar</a>
                    <a class="nav-link" href="{{ route('labor.index') }}">Labor</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-calendar3"></i> Task Calendar</h1>
            <div>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-list-task"></i> List View
                </a>
                @can('create', App\Models\Tasks\Task::class)
                    <a href="{{ route('tasks.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Create Task
                    </a>
                @endcan
            </div>
        </div>

        <!-- Legend -->
        <div class="mb-3">
            <div class="legend-item">
                <div class="legend-color" style="background-color: #ffc107;"></div>
                <span>Pending</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #17a2b8;"></div>
                <span>Assigned</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #007bff;"></div>
                <span>In Progress</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #6c757d;"></div>
                <span>On Hold</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #28a745;"></div>
                <span>Completed</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #dc3545;"></div>
                <span>Overdue</span>
            </div>
        </div>

        <!-- Calendar -->
        <div class="calendar-container">
            <div id="calendar"></div>
        </div>

        <!-- Today's Tasks -->
        <div class="today-tasks" id="todayTasks">
            <h5><i class="bi bi-calendar-check"></i> Today's Tasks</h5>
            <div id="todayTasksList">
                <!-- Populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Task Detail Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalTitle">Task Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="taskModalBody">
                    <!-- Populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="taskModalViewBtn" class="btn btn-primary">
                        <i class="bi bi-eye"></i> View Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const events = @json($events);

            // Identify today's tasks
            const today = new Date().toISOString().split('T')[0];
            const todayTasks = events.filter(event => {
                const start = event.start;
                const end = event.end || event.start;
                return today >= start && today <= end;
            });

            // Render today's tasks
            renderTodayTasks(todayTasks);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: events,
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    showTaskModal(info.event);
                },
                eventDidMount: function(info) {
                    // Add tooltip
                    info.el.setAttribute('title', info.event.title);
                },
                dateClick: function(info) {
                    // Show tasks for clicked date
                    const clickedDate = info.dateStr;
                    const tasksForDate = events.filter(event => {
                        const start = event.start;
                        const end = event.end || event.start;
                        return clickedDate >= start && clickedDate <= end;
                    });

                    if (tasksForDate.length > 0) {
                        showDateTasks(clickedDate, tasksForDate);
                    }
                },
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                firstDay: 1, // Start week on Monday
                height: 'auto',
                aspectRatio: 1.8,
                displayEventTime: false,
                eventDisplay: 'block',
                nowIndicator: true,
            });

            calendar.render();

            function showTaskModal(event) {
                const modal = new bootstrap.Modal(document.getElementById('taskModal'));
                document.getElementById('taskModalTitle').textContent = event.title;
                document.getElementById('taskModalViewBtn').href = event.url;

                const body = document.getElementById('taskModalBody');
                body.innerHTML = `
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="legend-color me-2" style="background-color: ${event.backgroundColor};"></div>
                            <span>Status</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Start Date:</strong> ${event.start ? formatDate(event.start) : 'Not set'}
                    </div>
                    <div class="mb-3">
                        <strong>End Date:</strong> ${event.end ? formatDate(event.end) : 'Same as start'}
                    </div>
                `;

                modal.show();
            }

            function showDateTasks(date, tasks) {
                let html = `<h6 class="mb-3">Tasks for ${formatDate(new Date(date))}</h6>`;

                tasks.forEach(task => {
                    html += `
                        <div class="task-list-item">
                            <div class="task-priority-indicator" style="background-color: ${task.color};"></div>
                            <div class="flex-grow-1">
                                <strong>${task.title}</strong>
                            </div>
                            <a href="${task.url}" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    `;
                });

                document.getElementById('todayTasksList').innerHTML = html;
            }

            function renderTodayTasks(tasks) {
                const container = document.getElementById('todayTasksList');

                if (tasks.length === 0) {
                    container.innerHTML = '<p class="text-muted mb-0">No tasks scheduled for today.</p>';
                    return;
                }

                let html = '';
                tasks.forEach(task => {
                    html += `
                        <div class="task-list-item">
                            <div class="task-priority-indicator" style="background-color: ${task.color};"></div>
                            <div class="flex-grow-1">
                                <strong>${task.title}</strong>
                            </div>
                            <a href="${task.url}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    `;
                });

                container.innerHTML = html;
            }

            function formatDate(date) {
                if (typeof date === 'string') {
                    date = new Date(date);
                }
                return date.toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        });
    </script>
</body>
</html>
