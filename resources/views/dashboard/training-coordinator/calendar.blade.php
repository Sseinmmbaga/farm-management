@extends('layouts.base')

@section('title', 'Training Calendar')

@push('styles')
<style>
    .calendar-header {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .calendar-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }
    .calendar-day-header {
        text-align: center;
        font-weight: 600;
        padding: 10px;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    .calendar-day {
        min-height: 100px;
        padding: 5px;
        border: 1px solid #eee;
        background: white;
    }
    .calendar-day.other-month {
        background-color: #f8f9fa;
        color: #adb5bd;
    }
    .calendar-day.today {
        background-color: #e3f2fd;
    }
    .calendar-date {
        font-weight: 600;
        margin-bottom: 5px;
    }
    .calendar-event {
        font-size: 0.75rem;
        padding: 3px 6px;
        border-radius: 4px;
        margin-bottom: 3px;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .calendar-event.scheduled { background-color: #fff3cd; color: #856404; }
    .calendar-event.in-progress { background-color: #cce5ff; color: #004085; }
    .calendar-event.completed { background-color: #d4edda; color: #155724; }
    .legend-item {
        display: inline-flex;
        align-items: center;
        margin-right: 15px;
        font-size: 0.85rem;
    }
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        margin-right: 5px;
    }
    .upcoming-trainings {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .training-item {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    .training-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Training Calendar</h1>
            <p class="text-muted mb-0">View and manage training schedule</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.training') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('dashboard.training.trainings') }}" class="btn btn-outline-info">
                <i class="fas fa-list"></i> List View
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Training
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <!-- Calendar Navigation -->
            <div class="calendar-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <a href="?month={{ ($currentMonth ?? now())->subMonth()->format('Y-m') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="?month={{ ($currentMonth ?? now())->addMonth()->format('Y-m') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="?month={{ now()->format('Y-m') }}" class="btn btn-outline-primary btn-sm">Today</a>
                    </div>
                    <h4 class="mb-0">{{ ($currentMonth ?? now())->format('F Y') }}</h4>
                    <div class="legend">
                        <span class="legend-item">
                            <span class="legend-color" style="background-color: #fff3cd;"></span> Scheduled
                        </span>
                        <span class="legend-item">
                            <span class="legend-color" style="background-color: #cce5ff;"></span> In Progress
                        </span>
                        <span class="legend-item">
                            <span class="legend-color" style="background-color: #d4edda;"></span> Completed
                        </span>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-container">
                <div class="calendar-grid">
                    <!-- Day Headers -->
                    <div class="calendar-day-header">Sun</div>
                    <div class="calendar-day-header">Mon</div>
                    <div class="calendar-day-header">Tue</div>
                    <div class="calendar-day-header">Wed</div>
                    <div class="calendar-day-header">Thu</div>
                    <div class="calendar-day-header">Fri</div>
                    <div class="calendar-day-header">Sat</div>

                    <!-- Calendar Days -->
                    @php
                        $startOfMonth = ($currentMonth ?? now())->startOfMonth();
                        $endOfMonth = ($currentMonth ?? now())->endOfMonth();
                        $startDayOfWeek = $startOfMonth->dayOfWeek;
                        $daysInMonth = $endOfMonth->day;
                        $today = ($currentMonth ?? now())->isCurrentMonth() ? now()->day : null;
                    @endphp

                    {{-- Previous month days --}}
                    @for($i = 0; $i < $startDayOfWeek; $i++)
                        <div class="calendar-day other-month">
                            <div class="calendar-date">{{ $startOfMonth->copy()->subDays($startDayOfWeek - $i)->day }}</div>
                        </div>
                    @endfor

                    {{-- Current month days --}}
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        <div class="calendar-day {{ $day === $today ? 'today' : '' }}">
                            <div class="calendar-date">{{ $day }}</div>
                            @foreach($events as $event)
                                @if(($event->scheduled_date ?? now())->day === $day)
                                <div class="calendar-event {{ $event->status ?? 'scheduled' }}" title="{{ $event->title ?? 'Training' }}">
                                    {{ $event->title ?? 'Training' }}
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @endfor

                    {{-- Next month days to fill grid --}}
                    @php
                        $remainingDays = 7 - (($startDayOfWeek + $daysInMonth) % 7);
                        if($remainingDays < 7) {
                            for($i = 1; $i <= $remainingDays; $i++) {
                                echo '<div class="calendar-day other-month"><div class="calendar-date">' . $i . '</div></div>';
                            }
                        }
                    @endphp
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <!-- Upcoming Trainings Sidebar -->
            <div class="upcoming-trainings">
                <h5 class="mb-3"><i class="fas fa-calendar-check text-info me-2"></i>Upcoming Trainings</h5>

                @forelse($events->where('status', 'scheduled')->take(5) as $event)
                <div class="training-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ $event->title ?? 'Training' }}</strong>
                            <p class="mb-0 small text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $event->scheduled_date ? $event->scheduled_date->format('M d, Y') : 'TBD' }}
                            </p>
                            @if($event->location)
                            <p class="mb-0 small text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $event->location }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                    <p class="text-muted small mb-0">No upcoming trainings</p>
                </div>
                @endforelse

                <hr>

                <a href="{{ route('dashboard.training.trainings') }}" class="btn btn-outline-info btn-sm w-100">
                    <i class="fas fa-list"></i> View All Trainings
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Add click handlers for calendar events
    document.querySelectorAll('.calendar-event').forEach(event => {
        event.addEventListener('click', function() {
            // Show event details modal or navigate to event page
            alert('Training: ' + this.getAttribute('title'));
        });
    });
</script>
@endpush
