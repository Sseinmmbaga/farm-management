<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Farm Visits Calendar - Remei Farm OS</title>

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
            background-color: #3b82f6;
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
            color: #3b82f6;
        }

        .fc-button-primary {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
        }

        .fc-button-primary:hover {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
        }

        .fc-button-primary:not(:disabled).fc-button-active {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
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

        .visit-popup {
            max-width: 300px;
        }

        .fc-daygrid-event {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .today-visits {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .today-visits h5 {
            color: #3b82f6;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .visit-list-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 8px;
            background-color: #f8f9fa;
            transition: background-color 0.2s;
        }

        .visit-list-item:hover {
            background-color: #e9ecef;
        }

        .visit-priority-indicator {
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
                    <a class="nav-link" href="{{ route('visits.index') }}">Visits List</a>
                    <a class="nav-link active" href="{{ route('visits.calendar') }}">Calendar</a>
                    <a class="nav-link" href="{{ route('visits.create') }}">Schedule Visit</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-calendar3"></i> Farm Visits Calendar</h1>
            <div>
                <a href="{{ route('visits.index') }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-list-task"></i> List View
                </a>
                <a href="{{ route('visits.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Schedule Visit
                </a>
            </div>
        </div>

        <!-- Legend -->
        <div class="mb-3">
            <div class="legend-item">
                <div class="legend-color" style="background-color: #3b82f6;"></div>
                <span>Scheduled</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #f59e0b;"></div>
                <span>In Progress</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #10b981;"></div>
                <span>Completed</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #6c757d;"></div>
                <span>Cancelled</span>
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

        <!-- Today's Visits -->
        <div class="today-visits" id="todayVisits">
            <h5><i class="bi bi-calendar-check"></i> Today's Visits</h5>
            <div id="todayVisitsList">
                <!-- Populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Visit Detail Modal -->
    <div class="modal fade" id="visitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="visitModalTitle">Visit Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="visitModalBody">
                    <!-- Populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="visitModalViewBtn" class="btn btn-primary">
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

            // Identify today's visits
            const today = new Date().toISOString().split('T')[0];
            const todayVisits = events.filter(event => {
                const start = event.start;
                const end = event.end || event.start;
                return today >= start && today <= end;
            });

            // Render today's visits
            renderTodayVisits(todayVisits);

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
                    showVisitModal(info.event);
                },
                eventDidMount: function(info) {
                    // Add tooltip
                    info.el.setAttribute('title', info.event.title);
                },
                dateClick: function(info) {
                    // Show visits for clicked date
                    const clickedDate = info.dateStr;
                    const visitsForDate = events.filter(event => {
                        const start = event.start;
                        const end = event.end || event.start;
                        return clickedDate >= start && clickedDate <= end;
                    });

                    if (visitsForDate.length > 0) {
                        showDateVisits(clickedDate, visitsForDate);
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

            function showVisitModal(event) {
                const modal = new bootstrap.Modal(document.getElementById('visitModal'));
                document.getElementById('visitModalTitle').textContent = event.title;
                document.getElementById('visitModalViewBtn').href = event.url;

                const body = document.getElementById('visitModalBody');
                body.innerHTML = `
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="legend-color me-2" style="background-color: ${event.color};"></div>
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

            function showDateVisits(date, visits) {
                let html = `<h6 class="mb-3">Visits for ${formatDate(new Date(date))}</h6>`;

                visits.forEach(visit => {
                    html += `
                        <div class="visit-list-item">
                            <div class="visit-priority-indicator" style="background-color: ${visit.color};"></div>
                            <div class="flex-grow-1">
                                <strong>${visit.title}</strong>
                            </div>
                            <a href="${visit.url}" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    `;
                });

                document.getElementById('todayVisitsList').innerHTML = html;
            }

            function renderTodayVisits(visits) {
                const container = document.getElementById('todayVisitsList');

                if (visits.length === 0) {
                    container.innerHTML = '<p class="text-muted mb-0">No visits scheduled for today.</p>';
                    return;
                }

                let html = '';
                visits.forEach(visit => {
                    html += `
                        <div class="visit-list-item">
                            <div class="visit-priority-indicator" style="background-color: ${visit.color};"></div>
                            <div class="flex-grow-1">
                                <strong>${visit.title}</strong>
                            </div>
                            <a href="${visit.url}" class="btn btn-sm btn-outline-primary">
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