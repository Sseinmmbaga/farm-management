@extends('layouts.base')

@section('title', 'All Training Sessions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i> All Training Sessions
                        </h4>
                        <a href="{{ route('training.sessions.create') }}" class="btn btn-light">
                            <i class="fas fa-plus-circle me-1"></i> Schedule New Session
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card-body bg-light border-bottom">
                    <div class="btn-group" role="group">
                        <a href="{{ route('training.sessions.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-1"></i> All
                        </a>
                        <a href="{{ route('training.upcoming') }}" class="btn btn-outline-info">
                            <i class="fas fa-clock me-1"></i> Upcoming
                        </a>
                        <a href="{{ route('training.completed') }}" class="btn btn-outline-success">
                            <i class="fas fa-check-circle me-1"></i> Completed
                        </a>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('training.sessions.index') }}" class="row g-3">
                        <div class="col-md-4">
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
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if(request('search') || request('status'))
                        <div class="col-md-1">
                            <a href="{{ route('training.sessions.index') }}" class="btn btn-outline-secondary w-100">
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
                                    <th>Participants</th>
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
                                            <span class="badge bg-primary">{{ $session->registered_count ?? 0 }}</span> registered
                                            <br>
                                            <span class="badge bg-success">{{ $session->attended_count ?? 0 }}</span> attended
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'scheduled' => 'info',
                                                    'in_progress' => 'warning',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                    'postponed' => 'secondary',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$session->status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('training.sessions.show', $session) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('training.attendance.index', $session) }}"
                                                   class="btn btn-outline-info"
                                                   title="Attendance">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                                <a href="{{ route('training.sessions.edit', $session) }}"
                                                   class="btn btn-outline-warning"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
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
                                                <p>Schedule your first training session to get started</p>
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
