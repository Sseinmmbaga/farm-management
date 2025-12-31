@extends('layouts.base')

@section('title', 'Completed Training Sessions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i> Completed Training Sessions
                        </h4>
                        <a href="{{ route('training.upcoming') }}" class="btn btn-light">
                            <i class="fas fa-calendar-alt me-1"></i> View Upcoming
                        </a>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Completed</h6>
                                            <h3 class="mb-0">{{ $sessions->total() }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-calendar-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Attendees</h6>
                                            <h3 class="mb-0">{{ $sessions->sum('attended_count') }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-users fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Average Attendance</h6>
                                            <h3 class="mb-0">{{ number_format($sessions->avg('attendance_rate') ?? 0, 1) }}%</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-chart-line fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Certificates Issued</h6>
                                            <h3 class="mb-0">{{ $certificatesCount ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-certificate fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('training.completed') }}" class="row g-3">
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
                            <input type="month"
                                   name="month"
                                   class="form-control"
                                   value="{{ request('month') }}"
                                   placeholder="Select month">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if(request('search') || request('program_id') || request('month'))
                        <div class="col-md-1">
                            <a href="{{ route('training.completed') }}" class="btn btn-outline-secondary w-100">
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
                                    <th>Completed Date</th>
                                    <th>Venue</th>
                                    <th>Attendance</th>
                                    <th>Certificates</th>
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
                                            <strong>{{ $session->end_date?->format('M d, Y') ?? $session->scheduled_date->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $session->end_date?->format('H:i') ?? $session->scheduled_date->format('H:i') }}</small>
                                            <br>
                                            <small class="text-info">{{ $session->end_date ? $session->end_date->diffForHumans() : $session->scheduled_date->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            {{ $session->venue }}
                                            @if($session->village)
                                                <br>
                                                <small class="text-muted">{{ $session->village->name }}, {{ $session->district->name ?? '' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                    @php
                                                        $total = $session->registered_count;
                                                        $attended = $session->attended_count;
                                                        $percentage = $total > 0 ? min(100, ($attended / $total) * 100) : 0;
                                                        $color = $percentage >= 80 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger');
                                                    @endphp
                                                    <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                        {{ $attended }}/{{ $total }}
                                                    </div>
                                                </div>
                                                <span class="badge bg-primary">{{ number_format($percentage, 1) }}%</span>
                                            </div>
                                            <small class="text-muted">{{ $attended }} attended</small>
                                        </td>
                                        <td>
                                            @php
                                                $issued = $session->attendances->where('certificate_issued', true)->count();
                                                $eligible = $session->attendances->where('attended', true)->count();
                                            @endphp
                                            <span class="badge bg-{{ $issued == $eligible ? 'success' : ($issued > 0 ? 'warning' : 'secondary') }}">
                                                {{ $issued }}/{{ $eligible }}
                                            </span>
                                            <br>
                                            <small class="text-muted">Issued</small>
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
                                                @if($issued < $eligible)
                                                    <button type="button"
                                                            class="btn btn-outline-success"
                                                            title="Issue Certificates"
                                                            onclick="issueCertificates('{{ route('training.issue-certificates', $session) }}')">
                                                        <i class="fas fa-certificate"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-outline-success disabled">
                                                        <i class="fas fa-certificate"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('training.reports.summary') }}?session={{ $session->id }}"
                                                   class="btn btn-outline-warning"
                                                   title="Reports">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-check fa-3x mb-3"></i>
                                                <h5>No completed training sessions</h5>
                                                <p>Complete a training session to see it here</p>
                                                <a href="{{ route('training.upcoming') }}" class="btn btn-primary">
                                                    <i class="fas fa-calendar-alt me-1"></i> View Upcoming
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
    function issueCertificates(url) {
        if (confirm('Issue certificates to all eligible attendees? This action cannot be undone.')) {
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
                    alert(data.message || 'Error issuing certificates');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>
@endpush
@endsection