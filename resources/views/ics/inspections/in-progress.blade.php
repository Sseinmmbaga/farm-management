@extends('layouts.base')

@section('title', 'Inspections In Progress')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-spinner me-2"></i> Inspections In Progress
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('inspections.scheduled') }}" class="btn btn-light">
                                <i class="fas fa-calendar-alt me-1"></i> Scheduled
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
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">In Progress</h6>
                                            <h3 class="mb-0">{{ $inspections->total() }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-spinner fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Started Today</h6>
                                            <h3 class="mb-0">{{ $today ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-calendar-day fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Overdue</h6>
                                            <h3 class="mb-0">{{ $overdue ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-danger text-white rounded-circle p-3">
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
                            <a href="{{ route('inspections.scheduled') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-calendar-alt me-1"></i> Scheduled
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
                                    <th>Started At</th>
                                    <th>Inspector</th>
                                    <th>Progress</th>
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
                                            @if($inspection->started_at)
                                                {{ \Carbon\Carbon::parse($inspection->started_at)->format('M d, Y H:i') }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($inspection->started_at)->diffForHumans() }}
                                                </small>
                                            @else
                                                <span class="text-muted">Not started</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $inspection->inspector->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @php
                                                $totalItems = $inspection->checklist->items_count ?? 0;
                                                $completedItems = $inspection->responses->count() ?? 0;
                                                $percentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-info" 
                                                     role="progressbar" 
                                                     style="width: {{ $percentage }}%;"
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ $percentage }}%
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $completedItems }}/{{ $totalItems }} items</small>
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
                                                   title="Edit Responses">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('inspections.complete', $inspection) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Complete Inspection">
                                                        <i class="fas fa-check"></i>
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
                                                <i class="fas fa-spinner fa-3x mb-3"></i>
                                                <h5>No inspections in progress</h5>
                                                <p>All inspections are either scheduled, completed, or cancelled.</p>
                                                <a href="{{ route('inspections.scheduled') }}" class="btn btn-info">
                                                    <i class="fas fa-calendar-alt me-1"></i> View Scheduled
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