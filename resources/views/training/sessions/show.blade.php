@extends('layouts.base')

@section('title', 'Session Details - ' . $session->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Header Card --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i> {{ $session->title }}
                        </h4>
                        <div>
                            <a href="{{ route('training.attendance.index', $session) }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-users me-1"></i> Manage Attendance
                            </a>
                            <a href="{{ route('training.upcoming') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Left Column --}}
                <div class="col-lg-8">
                    {{-- Session Details --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Session Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Program:</th>
                                            <td>
                                                <a href="{{ route('training-programs.show', $session->program) }}">
                                                    {{ $session->program->name ?? 'N/A' }}
                                                </a>
                                                <br><small class="text-muted">{{ $session->program->code ?? '' }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $session->status == 'scheduled' ? 'info' : ($session->status == 'completed' ? 'success' : ($session->status == 'cancelled' ? 'danger' : 'warning')) }} fs-6">
                                                    {{ ucfirst($session->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Scheduled Date:</th>
                                            <td>
                                                <strong>{{ $session->scheduled_date->format('l, M d, Y') }}</strong>
                                                <br>{{ $session->scheduled_date->format('h:i A') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Duration:</th>
                                            <td>{{ $session->duration_hours ?? 'N/A' }} hours</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Venue:</th>
                                            <td>{{ $session->venue }}</td>
                                        </tr>
                                        @if($session->village || $session->district)
                                        <tr>
                                            <th>Location:</th>
                                            <td>
                                                {{ $session->village->name ?? '' }}
                                                {{ $session->district ? ', ' . $session->district->name : '' }}
                                                {{ $session->region ? ', ' . $session->region->name : '' }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if($session->latitude && $session->longitude)
                                        <tr>
                                            <th>Coordinates:</th>
                                            <td>
                                                <a href="https://www.google.com/maps?q={{ $session->latitude }},{{ $session->longitude }}" target="_blank">
                                                    {{ $session->latitude }}, {{ $session->longitude }}
                                                    <i class="fas fa-external-link-alt ms-1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Season:</th>
                                            <td>{{ $session->season->name ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($session->description)
                            <hr>
                            <h6>Description</h6>
                            <p class="text-muted">{{ $session->description }}</p>
                            @endif

                            @if($session->cancellation_reason)
                            <div class="alert alert-danger mt-3">
                                <strong><i class="fas fa-exclamation-triangle me-2"></i> Cancellation Reason:</strong>
                                <p class="mb-0">{{ $session->cancellation_reason }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Trainer Information --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i> Trainer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Trainer:</th>
                                            <td>
                                                @if($session->trainer)
                                                    {{ $session->trainer->name }}
                                                    <span class="badge bg-secondary ms-1">System User</span>
                                                @else
                                                    {{ $session->trainer_name ?? 'Not Assigned' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Organization:</th>
                                            <td>{{ $session->trainer_organization ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Contact:</th>
                                            <td>{{ $session->trainer_contact ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Attendance --}}
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i> Registered Farmers</h5>
                            <a href="{{ route('training.attendance.index', $session) }}" class="btn btn-sm btn-outline-primary">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Farmer</th>
                                            <th>Registration</th>
                                            <th>Check-in</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($session->attendances->take(10) as $attendance)
                                            <tr>
                                                <td>
                                                    {{ $attendance->farmer->first_name ?? '' }} {{ $attendance->farmer->last_name ?? '' }}
                                                    <br><small class="text-muted">{{ $attendance->farmer->registration_number ?? '' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $attendance->registration_status == 'registered' ? 'success' : ($attendance->registration_status == 'waitlist' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($attendance->registration_status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($attendance->check_in_time)
                                                        {{ $attendance->check_in_time->format('H:i') }}
                                                        @if($attendance->check_out_time)
                                                            - {{ $attendance->check_out_time->format('H:i') }}
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Not checked in</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($attendance->attended)
                                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i> Attended</span>
                                                    @else
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="fas fa-user-slash fa-2x mb-2"></i>
                                                    <p>No farmers registered yet</p>
                                                    <a href="{{ route('training.attendance.index', $session) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-user-plus me-1"></i> Register Farmers
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="col-lg-4">
                    {{-- Quick Stats --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Capacity:</span>
                                <span class="badge bg-info fs-6">
                                    {{ $session->registered_count }}/{{ $session->max_participants ?? '∞' }}
                                </span>
                            </div>

                            @if($session->max_participants)
                            <div class="progress mb-3" style="height: 20px;">
                                @php
                                    $percentage = min(100, ($session->registered_count / $session->max_participants) * 100);
                                    $color = $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success');
                                @endphp
                                <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $percentage }}%;">
                                    {{ number_format($percentage, 0) }}%
                                </div>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Registered:</span>
                                <strong>{{ $session->registered_count }}</strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Attended:</span>
                                <strong>{{ $session->attended_count }}</strong>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Attendance Rate:</span>
                                <strong>{{ number_format($session->attendance_rate, 1) }}%</strong>
                            </div>

                            @if($session->max_participants)
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Available Slots:</span>
                                <span class="badge bg-{{ $session->available_slots > 5 ? 'success' : ($session->available_slots > 0 ? 'warning' : 'danger') }}">
                                    {{ $session->available_slots }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cogs me-2"></i> Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('training.attendance.index', $session) }}" class="btn btn-primary">
                                    <i class="fas fa-users me-2"></i> Manage Attendance
                                </a>

                                @if($session->status == 'scheduled')
                                <a href="{{ route('training.edit', $session) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit me-2"></i> Edit Session
                                </a>

                                <form action="{{ route('training.complete', $session) }}" method="POST" onsubmit="return confirm('Mark this session as completed?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success w-100">
                                        <i class="fas fa-check-circle me-2"></i> Mark Completed
                                    </button>
                                </form>

                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times-circle me-2"></i> Cancel Session
                                </button>
                                @endif

                                @if($session->status == 'completed')
                                <form action="{{ route('training.issue-certificates', $session) }}" method="POST" onsubmit="return confirm('Issue certificates to all attended farmers?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-certificate me-2"></i> Issue Certificates
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Timeline</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <small class="text-muted">Created</small>
                                    <p class="mb-0">{{ $session->created_at->format('M d, Y H:i') }}</p>
                                </li>
                                <li class="mb-3">
                                    <small class="text-muted">Last Updated</small>
                                    <p class="mb-0">{{ $session->updated_at->format('M d, Y H:i') }}</p>
                                </li>
                                @if($session->end_date)
                                <li>
                                    <small class="text-muted">Ended</small>
                                    <p class="mb-0">{{ $session->end_date->format('M d, Y H:i') }}</p>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('training.cancel', $session) }}">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to cancel this session?</p>
                    <p><strong>{{ $session->title }}</strong></p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Cancellation Reason *</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
