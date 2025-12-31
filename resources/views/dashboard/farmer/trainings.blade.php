@extends('layouts.base')

@section('title', 'My Trainings')

@push('styles')
<style>
    .stats-row {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-item {
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        flex: 1;
        text-align: center;
        border-left: 4px solid;
    }
    .stat-item.attended { border-left-color: #27ae60; }
    .stat-item.upcoming { border-left-color: #3498db; }
    .stat-item.certificates { border-left-color: #f39c12; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .training-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #eee;
    }
    .training-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    .upcoming-session {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
        border-left: 4px solid #4caf50;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">My Trainings</h1>
            <p class="text-muted mb-0">View your training history and upcoming sessions</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item attended">
            <div class="number text-success">{{ $stats['attended'] }}</div>
            <div class="label">Trainings Attended</div>
        </div>
        <div class="stat-item upcoming">
            <div class="number text-primary">{{ $stats['upcoming'] }}</div>
            <div class="label">Upcoming Sessions</div>
        </div>
        <div class="stat-item certificates">
            <div class="number text-warning">{{ $stats['certificates'] }}</div>
            <div class="label">Certificates Earned</div>
        </div>
    </div>

    <div class="row">
        <!-- Training History -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Training History</h5>
                </div>
                <div class="card-body">
                    @forelse($attendances as $attendance)
                    <div class="training-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $attendance->session?->program?->name ?? 'Training Session' }}</h6>
                                <p class="text-muted mb-2 small">
                                    {{ $attendance->session?->title ?? 'Session' }}
                                </p>
                            </div>
                            <div class="text-end">
                                @if($attendance->attended)
                                <span class="badge bg-success">Attended</span>
                                @else
                                <span class="badge bg-secondary">Registered</span>
                                @endif
                                @if($attendance->certificate_issued)
                                <span class="badge bg-warning text-dark ms-1">
                                    <i class="fas fa-certificate"></i> Certified
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-auto">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $attendance->session?->scheduled_date?->format('M d, Y') ?? 'N/A' }}
                                </small>
                            </div>
                            @if($attendance->check_in_time)
                            <div class="col-auto">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Check-in: {{ $attendance->check_in_time->format('h:i A') }}
                                </small>
                            </div>
                            @endif
                            @if($attendance->score)
                            <div class="col-auto">
                                <small class="text-muted">
                                    <i class="fas fa-star me-1"></i>
                                    Score: {{ $attendance->score }}%
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Training Records</h6>
                        <p class="text-muted small">You haven't attended any trainings yet.</p>
                    </div>
                    @endforelse

                    @if($attendances instanceof \Illuminate\Pagination\LengthAwarePaginator && $attendances->hasPages())
                    <div class="mt-4">
                        {{ $attendances->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Sessions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Sessions</h5>
                </div>
                <div class="card-body">
                    @forelse($upcomingSessions as $session)
                    <div class="upcoming-session">
                        <h6 class="mb-1">{{ $session->program?->name ?? 'Training' }}</h6>
                        <p class="mb-2 small text-muted">{{ $session->title }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small>
                                <i class="fas fa-calendar text-success me-1"></i>
                                {{ $session->scheduled_date?->format('M d, Y') }}
                            </small>
                            <small>
                                <i class="fas fa-clock text-primary me-1"></i>
                                {{ $session->start_time ?? 'TBD' }}
                            </small>
                        </div>
                        @if($session->location)
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $session->location }}
                        </small>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                        <p class="text-muted small mb-0">No upcoming sessions</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
