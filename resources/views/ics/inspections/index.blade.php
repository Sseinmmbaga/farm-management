@extends('layouts.base')

@section('title', 'Inspections Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i> Inspections
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('inspections.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Schedule Inspection
                            </a>
                        </div>
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
                                            <h6 class="text-muted mb-1">Total Inspections</h6>
                                            <h3 class="mb-0">{{ $stats['total'] ?? $inspections->total() }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-clipboard-list fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Scheduled</h6>
                                            <h3 class="mb-0">{{ $stats['scheduled'] ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
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
                                            <h6 class="text-muted mb-1">In Progress</h6>
                                            <h3 class="mb-0">{{ $stats['in_progress'] ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-spinner fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Completed</h6>
                                            <h3 class="mb-0">{{ $stats['completed'] ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card-body border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('inspections.scheduled') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-calendar-alt me-1"></i> Scheduled
                                <span class="badge bg-success ms-1">{{ $stats['scheduled'] ?? 0 }}</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('inspections.in-progress') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-spinner me-1"></i> In Progress
                                <span class="badge bg-info ms-1">{{ $stats['in_progress'] ?? 0 }}</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('inspections.completed') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-check-circle me-1"></i> Completed
                                <span class="badge bg-warning ms-1">{{ $stats['completed'] ?? 0 }}</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('inspections.index', ['status' => 'cancelled']) }}" class="btn btn-outline-danger w-100">
                                <i class="fas fa-times-circle me-1"></i> Cancelled
                                <span class="badge bg-danger ms-1">{{ $stats['cancelled'] ?? 0 }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('inspections.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by farmer, inspector..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="result" class="form-select" onchange="this.form.submit()">
                                <option value="">All Results</option>
                                <option value="pending" {{ request('result') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="passed" {{ request('result') == 'passed' ? 'selected' : '' }}>Passed</option>
                                <option value="failed" {{ request('result') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="conditional" {{ request('result') == 'conditional' ? 'selected' : '' }}>Conditional</option>
                            </select>
                        </div>

                        <div class="col-md-3">
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

                        @if(request('search') || request('status') || request('result') || request('date_from') || request('date_to'))
                            <div class="col-md-2">
                                <a href="{{ route('inspections.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Inspections Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Inspection #</th>
                                    <th>Farmer</th>
                                    <th>Checklist</th>
                                    <th>Scheduled Date</th>
                                    <th>Inspector</th>
                                    <th>Status</th>
                                    <th>Result</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inspections as $inspection)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $inspection->inspection_number }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($inspection->farmer->first_name ?? '', 0, 1) }}{{ substr($inspection->farmer->last_name ?? '', 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $inspection->farmer->first_name ?? '' }} {{ $inspection->farmer->last_name ?? '' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $inspection->farmer->registration_number ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $inspection->checklist->name ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">{{ $inspection->checklist->code ?? '' }}</small>
                                        </td>
                                        <td>
                                            @if($inspection->scheduled_date)
                                                {{ \Carbon\Carbon::parse($inspection->scheduled_date)->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">Not scheduled</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $inspection->inspector->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @if($inspection->status == 'scheduled')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-calendar-check me-1"></i> Scheduled
                                                </span>
                                            @elseif($inspection->status == 'in_progress')
                                                <span class="badge bg-info">
                                                    <i class="fas fa-spinner me-1"></i> In Progress
                                                </span>
                                            @elseif($inspection->status == 'completed')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-check-circle me-1"></i> Completed
                                                </span>
                                            @elseif($inspection->status == 'cancelled')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Cancelled
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inspection->result == 'passed')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i> Passed
                                                </span>
                                            @elseif($inspection->result == 'failed')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i> Failed
                                                </span>
                                            @elseif($inspection->result == 'conditional')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i> Conditional
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-clock me-1"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('inspections.show', $inspection) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($inspection->status == 'scheduled')
                                                    <a href="{{ route('inspections.edit', $inspection) }}"
                                                       class="btn btn-outline-warning"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('inspections.start', $inspection) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-info" title="Start Inspection">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($inspection->status == 'in_progress')
                                                    <form action="{{ route('inspections.complete', $inspection) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success" title="Complete Inspection">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(in_array($inspection->status, ['scheduled', 'in_progress']))
                                                    <button type="button"
                                                            class="btn btn-outline-danger"
                                                            title="Cancel"
                                                            onclick="confirmCancel('{{ route('inspections.cancel', $inspection) }}', '{{ $inspection->inspection_number }}')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                                                <h5>No inspections found</h5>
                                                <p>Start by scheduling your first inspection</p>
                                                <a href="{{ route('inspections.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> Schedule Inspection
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($inspections->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $inspections->firstItem() }} to {{ $inspections->lastItem() }} of {{ $inspections->total() }} inspections
                                </div>
                                <div>
                                    {{ $inspections->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Bulk Actions & Export -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                Select All
                            </label>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('export.inspections', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-file-csv me-1"></i> Export to CSV
                            </a>
                            <a href="{{ route('ics-reports.summary') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-chart-bar me-1"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Cancellation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel inspection <strong id="inspectionNumber"></strong>?</p>
                <div class="mb-3">
                    <label for="cancellation_reason" class="form-label">Reason for Cancellation</label>
                    <textarea class="form-control" id="cancellation_reason" rows="3"></textarea>
                </div>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="cancelForm" method="POST">
                    @csrf
                    <input type="hidden" name="reason" id="reasonInput">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Inspection</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmCancel(url, inspectionNumber) {
        document.getElementById('inspectionNumber').textContent = inspectionNumber;
        document.getElementById('cancelForm').action = url;
        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }
    
    document.getElementById('cancelModal').addEventListener('show.bs.modal', function () {
        const reasonInput = document.getElementById('reasonInput');
        const textarea = document.getElementById('cancellation_reason');
        textarea.addEventListener('input', function() {
            reasonInput.value = this.value;
        });
    });
    
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="selected_inspections[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endpush
@endsection