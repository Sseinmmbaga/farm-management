@extends('layouts.base')

@section('title', 'Training Attendance Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i> Training Attendance Report
                        </h4>
                        <div>
                            <a href="{{ route('training.reports.summary') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-chart-bar me-1"></i> Summary Report
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="card-body border-bottom bg-light">
                    <form method="GET" action="{{ route('training.reports.attendance') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="program_id" class="form-label">Program</label>
                            <select name="program_id" id="program_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Programs</option>
                                @foreach(\App\Models\Training\TrainingProgram::orderBy('name')->get() as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Session</th>
                                    <th>Program</th>
                                    <th>Date</th>
                                    <th>Venue</th>
                                    <th class="text-center">Registered</th>
                                    <th class="text-center">Attended</th>
                                    <th class="text-center">Rate</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                    @php
                                        $rate = $session->attendances_count > 0
                                            ? ($session->attended_count / $session->attendances_count) * 100
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $session->title }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $session->program->code ?? 'N/A' }}</span>
                                            <br><small>{{ $session->program->name ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            {{ $session->scheduled_date->format('M d, Y') }}
                                            <br><small class="text-muted">{{ $session->scheduled_date->format('H:i') }}</small>
                                        </td>
                                        <td>{{ $session->venue }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $session->attendances_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $session->attended_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 20px; min-width: 80px;">
                                                <div class="progress-bar bg-{{ $rate >= 70 ? 'success' : ($rate >= 50 ? 'warning' : 'danger') }}"
                                                     role="progressbar"
                                                     style="width: {{ $rate }}%;">
                                                    {{ number_format($rate, 0) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $session->status == 'completed' ? 'success' : ($session->status == 'scheduled' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('training.sessions.show', $session) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Session">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('training.attendance.index', $session) }}"
                                                   class="btn btn-outline-info"
                                                   title="View Attendance">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                            <p>No training sessions found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
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
