@extends('layouts.base')

@section('title', 'Upcoming Training Sessions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i> Upcoming Training Sessions
                        </h4>
                        <a href="{{ route('training.sessions.create') }}" class="btn btn-light">
                            <i class="fas fa-plus-circle me-1"></i> Schedule New Session
                        </a>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Upcoming</h6>
                                            <h3 class="mb-0">{{ $sessions->total() }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-calendar-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">This Week</h6>
                                            <h3 class="mb-0">{{ $thisWeekCount ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-calendar-week fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Registered Farmers</h6>
                                            <h3 class="mb-0">{{ $sessions->sum('registered_count') }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Available Slots</h6>
                                            <h3 class="mb-0">{{ $sessions->sum('available_slots') ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-chair fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('training.upcoming') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by title, venue, program..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="program_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Programs</option>
                                @foreach($programs ?? [] as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date"
                                   name="date_from"
                                   class="form-control"
                                   placeholder="From Date"
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if(request('search') || request('program_id') || request('date_from'))
                        <div class="col-md-1">
                            <a href="{{ route('training.upcoming') }}" class="btn btn-outline-secondary w-100">
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
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2">
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
                                        <td>
                                            {{ $session->venue }}
                                            @if($session->village)
                                                <br>
                                                <small class="text-muted">{{ $session->village->name }}, {{ $session->district->name ?? '' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                @php
                                                    $total = $session->max_participants ?? 1;
                                                    $registered = $session->registered_count;
                                                    $percentage = $total > 0 ? min(100, ($registered / $total) * 100) : 0;
                                                    $color = $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success');
                                                @endphp
                                                <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ $registered }}/{{ $total ?? '∞' }}
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $session->available_slots ?? 'Unlimited' }} slots left</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $session->status == 'scheduled' ? 'info' : ($session->status == 'in_progress' ? 'warning' : 'secondary') }}">
                                                {{ $session->status_label }}
                                            </span>
                                            @if($session->is_upcoming)
                                                <br>
                                                <small class="text-success">Upcoming</small>
                                            @endif
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
                                                <button type="button"
                                                        class="btn btn-outline-warning"
                                                        title="Cancel"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cancelModal{{ $session->id }}">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-outline-success"
                                                        title="Mark Complete"
                                                        onclick="markComplete('{{ route('training.complete', $session) }}')">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </div>

                                            <!-- Cancel Modal -->
                                            <div class="modal fade" id="cancelModal{{ $session->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cancel Session</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="POST" action="{{ route('training.cancel', $session) }}">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to cancel session <strong>{{ $session->title }}</strong>?</p>
                                                                <div class="mb-3">
                                                                    <label for="cancellation_reason" class="form-label">Cancellation Reason</label>
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                                <h5>No upcoming training sessions</h5>
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

@push('scripts')
<script>
    function markComplete(url) {
        if (confirm('Are you sure you want to mark this session as completed?')) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error completing session');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>
@endpush
@endsection