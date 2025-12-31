@extends('layouts.base')

@section('title', 'Training Attendance - ' . $session->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Attendance for: {{ $session->title }}
                        </h4>
                        <div>
                            <a href="{{ route('training.sessions.show', $session) }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to Session
                            </a>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkRegistrationModal">
                                <i class="fas fa-user-plus me-1"></i> Bulk Register
                            </button>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#checkInBulkModal">
                                <i class="fas fa-check-circle me-1"></i> Bulk Check-in
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Session Info -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-calendar fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Session Details</h5>
                                    <p class="mb-0">
                                        <strong>Date:</strong> {{ $session->scheduled_date->format('F j, Y') }}<br>
                                        <strong>Time:</strong> {{ $session->scheduled_date->format('H:i') }}<br>
                                        <strong>Venue:</strong> {{ $session->venue }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-chart-pie fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Attendance Statistics</h5>
                                    <p class="mb-0">
                                        <strong>Registered:</strong> {{ $session->registered_count }} farmers<br>
                                        <strong>Attended:</strong> {{ $session->attended_count ?? 0 }} farmers<br>
                                        <strong>Capacity:</strong> {{ $session->max_participants ?? 'Unlimited' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-certificate fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Program</h5>
                                    <p class="mb-0">
                                        <strong>{{ $session->program->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $session->program->code ?? '' }}</small><br>
                                        <a href="{{ route('training.programs.show', $session->program) }}" class="btn btn-sm btn-outline-primary mt-1">View Program</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters & Add Single -->
                <div class="card-body border-bottom">
                    <div class="row">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('training.attendance.index', $session) }}" class="row g-2">
                                <div class="col-md-4">
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="">All Statuses</option>
                                        <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>Registered</option>
                                        <option value="waitlist" {{ request('status') == 'waitlist' ? 'selected' : '' }}>Waitlist</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="attended" class="form-select" onchange="this.form.submit()">
                                        <option value="">Attendance</option>
                                        <option value="1" {{ request('attended') == '1' ? 'selected' : '' }}>Attended</option>
                                        <option value="0" {{ request('attended') == '0' ? 'selected' : '' }}>Not Attended</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text"
                                               name="search"
                                               class="form-control"
                                               placeholder="Search farmer..."
                                               value="{{ request('search') }}">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <!-- Single Farmer Registration -->
                            <form method="POST" action="{{ route('training.attendance.store', $session) }}" class="row g-2">
                                @csrf
                                <div class="col-md-8">
                                    <select name="farmer_id" class="form-select" required>
                                        <option value="">Select Farmer</option>
                                        @foreach($availableFarmers as $farmer)
                                            <option value="{{ $farmer->id }}">
                                                {{ $farmer->first_name }} {{ $farmer->last_name }} ({{ $farmer->registration_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus-circle me-1"></i> Add
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Farmer</th>
                                    <th>Registration Number</th>
                                    <th>Registration Status</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Attended</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($attendance->farmer->first_name ?? '?', 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $attendance->farmer->first_name ?? 'N/A' }} {{ $attendance->farmer->last_name ?? '' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $attendance->farmer->phone_number ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $attendance->farmer->registration_number ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $attendance->registration_status == 'registered' ? 'success' : ($attendance->registration_status == 'waitlist' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($attendance->registration_status) }}
                                            </span>
                                            @if($attendance->notes)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($attendance->notes, 30) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->check_in_time)
                                                <span class="badge bg-success">
                                                    {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->check_in_time)->diffForHumans() }}</small>
                                            @else
                                                <span class="badge bg-secondary">Not checked in</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->check_out_time)
                                                <span class="badge bg-info">
                                                    {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') }}
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->check_out_time)->diffForHumans() }}</small>
                                            @else
                                                <span class="badge bg-secondary">Not checked out</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->attended)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i> Yes
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i> No
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <!-- Check-in Button -->
                                                @if(!$attendance->check_in_time)
                                                    <a href="{{ route('training.attendance.check-in', ['session' => $session, 'farmer' => $attendance->farmer_id]) }}"
                                                       class="btn btn-outline-success"
                                                       title="Check-in">
                                                        <i class="fas fa-sign-in-alt"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-outline-success disabled">
                                                        <i class="fas fa-sign-in-alt"></i>
                                                    </button>
                                                @endif

                                                <!-- Check-out Button -->
                                                @if($attendance->check_in_time && !$attendance->check_out_time)
                                                    <a href="{{ route('training.attendance.check-out', ['session' => $session, 'farmer' => $attendance->farmer_id]) }}"
                                                       class="btn btn-outline-info"
                                                       title="Check-out">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-outline-info disabled">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                @endif

                                                <!-- Edit Modal Trigger -->
                                                <button type="button"
                                                        class="btn btn-outline-warning"
                                                        title="Edit"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $attendance->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Remove Button -->
                                                <form method="POST" action="{{ route('training.attendance.destroy', ['session' => $session, 'attendance' => $attendance]) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-outline-danger"
                                                            title="Remove"
                                                            onclick="return confirm('Are you sure you want to remove this farmer from the session?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal{{ $attendance->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('training.attendance.update', ['session' => $session, 'attendance' => $attendance]) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Attendance</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Registration Status</label>
                                                                    <select name="registration_status" class="form-select">
                                                                        <option value="registered" {{ $attendance->registration_status == 'registered' ? 'selected' : '' }}>Registered</option>
                                                                        <option value="waitlist" {{ $attendance->registration_status == 'waitlist' ? 'selected' : '' }}>Waitlist</option>
                                                                        <option value="cancelled" {{ $attendance->registration_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Attended</label>
                                                                    <select name="attended" class="form-select">
                                                                        <option value="1" {{ $attendance->attended ? 'selected' : '' }}>Yes</option>
                                                                        <option value="0" {{ !$attendance->attended ? 'selected' : '' }}>No</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Score (0-100)</label>
                                                                    <input type="number"
                                                                           name="score"
                                                                           class="form-control"
                                                                           min="0" max="100"
                                                                           value="{{ $attendance->score }}">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Rating (1-5)</label>
                                                                    <select name="rating" class="form-select">
                                                                        <option value="">Select rating</option>
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <option value="{{ $i }}" {{ $attendance->rating == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Feedback</label>
                                                                    <textarea name="feedback" class="form-control" rows="2">{{ $attendance->feedback }}</textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Notes</label>
                                                                    <textarea name="notes" class="form-control" rows="2">{{ $attendance->notes }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save changes</button>
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
                                                <i class="fas fa-users-slash fa-3x mb-3"></i>
                                                <h5>No attendance records yet</h5>
                                                <p>Register farmers to start tracking attendance</p>
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkRegistrationModal">
                                                    <i class="fas fa-user-plus me-1"></i> Register Farmers
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($attendances->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} farmers
                                </div>
                                <div>
                                    {{ $attendances->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Registration Modal -->
<div class="modal fade" id="bulkRegistrationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('training.attendance.bulk.store', $session) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Farmers (Hold Ctrl/Cmd to select multiple)</label>
                        <select name="farmer_ids[]" class="form-select" multiple size="10">
                            @foreach($availableFarmers as $farmer)
                                <option value="{{ $farmer->id }}">
                                    {{ $farmer->first_name }} {{ $farmer->last_name }} ({{ $farmer->registration_number }}) - {{ $farmer->village->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple farmers.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Registration Status</label>
                        <select name="registration_status" class="form-select">
                            <option value="registered">Registered</option>
                            <option value="waitlist">Waitlist</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Selected Farmers</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Check-in Modal -->
<div class="modal fade" id="checkInBulkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('training.attendance.bulk.check-in', $session) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Check-in</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Select farmers who have arrived and need to be checked in.</p>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Farmer</th>
                                    <th>Registration Number</th>
                                    <th>Current Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $attendance)
                                    @if(!$attendance->check_in_time)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="farmer_ids[]" value="{{ $attendance->farmer_id }}">
                                        </td>
                                        <td>{{ $attendance->farmer->first_name }} {{ $attendance->farmer->last_name }}</td>
                                        <td>{{ $attendance->farmer->registration_number }}</td>
                                        <td>
                                            <span class="badge bg-warning">Not Checked In</span>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Check-in Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Select all checkboxes
    document.getElementById('selectAll').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('input[name="farmer_ids[]"]');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });
</script>
@endpush
@endsection