@extends('layouts.base')

@section('title', 'Training Attendance Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-check me-2"></i> Training Attendance Management
                        </h4>
                        <a href="{{ route('training.sessions.create') }}" class="btn btn-light">
                            <i class="fas fa-plus-circle me-1"></i> Schedule New Session
                        </a>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('training.attendance.list') }}" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by title, venue..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date"
                                   name="from_date"
                                   class="form-control"
                                   placeholder="From Date"
                                   value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date"
                                   name="to_date"
                                   class="form-control"
                                   placeholder="To Date"
                                   value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if(request('search') || request('status') || request('from_date') || request('to_date'))
                        <div class="col-md-1">
                            <a href="{{ route('training.attendance.list') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                        @endif
                    </form>
                </div>

                <!-- Sessions Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Program</th>
                                    <th>Session Title</th>
                                    <th>Scheduled Date</th>
                                    <th>Venue</th>
                                    <th>Attendance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                    {{ substr($session->program->name ?? '?', 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $session->program->name ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $session->program->code ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $session->title }}</strong>
                                            @if($session->description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($session->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $session->scheduled_date->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $session->scheduled_date->format('H:i') }}</small>
                                            <br>
                                            <small class="text-info">{{ $session->scheduled_date->diffForHumans() }}</small>
                                        </td>
                                        <td>{{ $session->venue }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="badge bg-success">{{ $session->attended_count ?? 0 }}</span>
                                                    <small class="text-muted">attended</small>
                                                </div>
                                                <div>
                                                    <span class="badge bg-secondary">{{ $session->attendances_count ?? 0 }}</span>
                                                    <small class="text-muted">registered</small>
                                                </div>
                                            </div>
                                            @if(($session->attendances_count ?? 0) > 0)
                                                @php
                                                    $attendanceRate = round((($session->attended_count ?? 0) / $session->attendances_count) * 100);
                                                @endphp
                                                <div class="progress mt-1" style="height: 6px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendanceRate }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $attendanceRate }}% attendance</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'scheduled' => 'info',
                                                    'in_progress' => 'warning',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$session->status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('training.attendance.index', $session) }}"
                                                   class="btn btn-primary"
                                                   title="Manage Attendance">
                                                    <i class="fas fa-user-check"></i> Manage
                                                </a>
                                                <a href="{{ route('training.sessions.show', $session) }}"
                                                   class="btn btn-outline-secondary"
                                                   title="View Session">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                                <h5>No training sessions found</h5>
                                                <p>Schedule a training session to start managing attendance</p>
                                                <a href="{{ route('training.sessions.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> Schedule Session
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($sessions->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $sessions->firstItem() }} to {{ $sessions->lastItem() }} of {{ $sessions->total() }} sessions
                                </div>
                                <div>
                                    {{ $sessions->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
