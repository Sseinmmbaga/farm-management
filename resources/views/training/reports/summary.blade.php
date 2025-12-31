@extends('layouts.base')

@section('title', 'Training Reports Summary')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i> Training Reports Summary
                        </h4>
                        <div>
                            <a href="{{ route('training.reports.attendance') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-clipboard-list me-1"></i> Attendance Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Stats --}}
            <div class="row mb-4">
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="fas fa-graduation-cap fa-lg"></i>
                            </div>
                            <h3 class="mb-0">{{ $totalPrograms }}</h3>
                            <small class="text-muted">Total Programs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="fas fa-calendar-alt fa-lg"></i>
                            </div>
                            <h3 class="mb-0">{{ $totalSessions }}</h3>
                            <small class="text-muted">Total Sessions</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body text-center">
                            <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                            <h3 class="mb-0">{{ $upcomingSessions }}</h3>
                            <small class="text-muted">Upcoming</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                            <h3 class="mb-0">{{ $completedSessions }}</h3>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card border-secondary h-100">
                        <div class="card-body text-center">
                            <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <h3 class="mb-0">{{ $totalParticipants }}</h3>
                            <small class="text-muted">Registered</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="fas fa-user-check fa-lg"></i>
                            </div>
                            <h3 class="mb-0">{{ $totalAttended }}</h3>
                            <small class="text-muted">Attended</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Overall Attendance Rate --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-percentage me-2"></i> Overall Attendance Rate</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $attendanceRate = $totalParticipants > 0 ? ($totalAttended / $totalParticipants) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <div class="display-4 me-3">{{ number_format($attendanceRate, 1) }}%</div>
                                <div>
                                    <p class="mb-0 text-muted">{{ $totalAttended }} out of {{ $totalParticipants }} farmers attended</p>
                                </div>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendanceRate }}%;">
                                    {{ number_format($attendanceRate, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Session Status Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4 class="text-warning">{{ $upcomingSessions }}</h4>
                                    <p class="mb-0 text-muted">Scheduled</p>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-success">{{ $completedSessions }}</h4>
                                    <p class="mb-0 text-muted">Completed</p>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-secondary">{{ $totalSessions - $upcomingSessions - $completedSessions }}</h4>
                                    <p class="mb-0 text-muted">Other</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Programs Performance Table --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i> Programs Performance (Top 10)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Program</th>
                                    <th>Category</th>
                                    <th class="text-center">Sessions</th>
                                    <th class="text-center">Registered</th>
                                    <th class="text-center">Attended</th>
                                    <th class="text-center">Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($programs as $program)
                                    @php
                                        $programAttendanceRate = $program->attendances_count > 0
                                            ? ($program->sessions->sum('attended_count') / $program->attendances_count) * 100
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $program->name }}</strong>
                                            <br><small class="text-muted">{{ $program->code ?? '' }}</small>
                                        </td>
                                        <td>
                                            @if($program->category)
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $program->category)) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $program->sessions_count }}</span>
                                        </td>
                                        <td class="text-center">{{ $program->attendances_count ?? 0 }}</td>
                                        <td class="text-center">{{ $program->sessions->sum('attended_count') ?? 0 }}</td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 20px; min-width: 80px;">
                                                <div class="progress-bar bg-{{ $programAttendanceRate >= 70 ? 'success' : ($programAttendanceRate >= 50 ? 'warning' : 'danger') }}"
                                                     role="progressbar"
                                                     style="width: {{ $programAttendanceRate }}%;">
                                                    {{ number_format($programAttendanceRate, 0) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('training-programs.show', $program) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-graduation-cap fa-2x mb-2"></i>
                                            <p>No training programs found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
