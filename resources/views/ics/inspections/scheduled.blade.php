@extends('layouts.base')

@section('title', 'Scheduled Inspections')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i> Scheduled Inspections
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('inspections.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Schedule New
                            </a>
                            <a href="{{ route('inspections.index') }}" class="btn btn-light">
                                <i class="fas fa-list me-1"></i> All Inspections
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Scheduled</h6>
                                            <h3 class="mb-0">{{ $inspections->total() }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-calendar-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">This Week</h6>
                                            <h3 class="mb-0">{{ $thisWeek ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-calendar-week fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Overdue</h6>
                                            <h3 class="mb-0">{{ $overdue ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                        <div class="col-md-4">
                            <a href="{{ route('inspections.in-progress') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-spinner me-1"></i> In Progress
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('inspections.completed') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('inspections.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-list me-1"></i> View All
                            </a>
                        </div>
                    </div>
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
                                                <br>
                                                <small class="text-{{ \Carbon\Carbon::parse($inspection->scheduled_date)->isPast() ? 'danger' : 'muted' }}">
                                                    {{ \Carbon\Carbon::parse($inspection->scheduled_date)->diffForHumans() }}
                                                </small>
                                            @else
                                                <span class="text-muted">Not scheduled</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $inspection->inspector->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fas fa-calendar-check me-1"></i> Scheduled
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('inspections.show', $inspection) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        title="Cancel"
                                                        onclick="confirmCancel('{{ route('inspections.cancel', $inspection) }}', '{{ $inspection->inspection_number }}')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                                <h5>No scheduled inspections</h5>
                                                <p>All inspections are either in progress, completed, or cancelled.</p>
                                                <a href="{{ route('inspections.create') }}" class="btn btn-success">
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
</script>
@endpush
@endsection