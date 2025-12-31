@extends('layouts.base')

@section('title', $program->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i> {{ $program->name }}
                            @if($program->name_sw)
                                <small class="fs-6 fw-normal">({{ $program->name_sw }})</small>
                            @endif
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('training-programs.edit', $program) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="confirmDelete('{{ route('training-programs.destroy', $program) }}', '{{ $program->name }}')">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Program Details</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Program Code:</th>
                                    <td><strong class="text-primary">{{ $program->code }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        @if($program->category)
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $program->category)) }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Duration:</th>
                                    <td>
                                        @if($program->duration_hours)
                                            {{ $program->duration_hours }} hours
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Mandatory:</th>
                                    <td>
                                        @if($program->is_mandatory)
                                            <span class="badge bg-warning">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($program->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $program->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Statistics</h5>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <h2 class="mb-0">{{ $program->sessions->count() }}</h2>
                                            <small class="text-muted">Training Sessions</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <h2 class="mb-0">{{ $program->total_participants ?? 0 }}</h2>
                                            <small class="text-muted">Total Participants</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="card border-warning">
                                        <div class="card-body text-center">
                                            <h2 class="mb-0">{{ $program->total_attendees ?? 0 }}</h2>
                                            <small class="text-muted">Total Attendees</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            <h2 class="mb-0">{{ $program->total_sessions ?? 0 }}</h2>
                                            <small class="text-muted">Total Sessions</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($program->description)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Description</h5>
                            <p>{{ $program->description }}</p>
                        </div>
                    </div>
                    @endif

                    @if($program->objectives)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Objectives</h5>
                            <p>{{ $program->objectives }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Training Sessions Section -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i> Training Sessions
                        </h5>
                        <a href="{{ route('training.sessions.upcoming') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus me-1"></i> Schedule New Session
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Scheduled Date</th>
                                    <th>Venue</th>
                                    <th>Status</th>
                                    <th>Participants</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($program->sessions as $session)
                                    <tr>
                                        <td>
                                            <strong>{{ $session->title }}</strong>
                                        </td>
                                        <td>
                                            {{ $session->scheduled_date->format('M d, Y H:i') }}
                                        </td>
                                        <td>
                                            {{ $session->venue }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $session->status == 'scheduled' ? 'info' : ($session->status == 'completed' ? 'success' : 'danger') }}">
                                                {{ $session->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $session->registered_count }} registered
                                            </span>
                                            <span class="badge bg-success">
                                                {{ $session->attended_count }} attended
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('training.sessions.show', $session) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('training.attendance.index', $session) }}" class="btn btn-outline-info">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                                <p>No training sessions scheduled for this program.</p>
                                                <a href="{{ route('training.sessions.upcoming') }}" class="btn btn-sm btn-primary">
                                                    Schedule a Session
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('training.sessions.upcoming') }}" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-2"></i> Schedule New Session
                        </a>
                        <a href="{{ route('training-programs.edit', $program) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Edit Program
                        </a>
                        <a href="{{ route('training.materials.program', $program) }}" class="btn btn-info">
                            <i class="fas fa-file-alt me-2"></i> Manage Materials
                        </a>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-download me-2"></i> Export Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Program Metadata Card -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Additional Information
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Created By</span>
                            <span class="text-muted">{{ $program->created_by ? \App\Models\User::find($program->created_by)?->name : 'System' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Last Updated</span>
                            <span class="text-muted">{{ $program->updated_at->diffForHumans() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Total Sessions</span>
                            <span class="badge bg-primary rounded-pill">{{ $program->sessions->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Upcoming Sessions</span>
                            <span class="badge bg-info rounded-pill">{{ $program->sessions->where('status', 'scheduled')->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Completed Sessions</span>
                            <span class="badge bg-success rounded-pill">{{ $program->sessions->where('status', 'completed')->count() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete program <strong id="programName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone. If the program has sessions, deletion will be prevented.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Program</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Program Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Select format to export program details and sessions.</p>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-file-csv me-2"></i> CSV Format
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="fas fa-file-excel me-2"></i> Excel Format
                    </a>
                    <a href="#" class="btn btn-outline-danger">
                        <i class="fas fa-file-pdf me-2"></i> PDF Format
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, name) {
        document.getElementById('programName').textContent = name;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endpush
@endsection