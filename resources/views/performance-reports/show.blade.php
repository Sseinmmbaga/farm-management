@extends('layouts.app')

@section('title', 'Performance Report - ' . $performanceReport->report_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Performance Report: {{ $performanceReport->report_number }}
                        <span class="badge bg-{{ $performanceReport->status_color }} ms-2">
                            {{ $performanceReport->status_display }}
                        </span>
                    </h5>
                    <div class="btn-group">
                        @can('print', $performanceReport)
                            <a href="{{ route('performance-reports.print', $performanceReport) }}" target="_blank"
                               class="btn btn-outline-dark">
                                <i class="fas fa-print"></i> Print
                            </a>
                        @endcan
                        @can('update', $performanceReport)
                            <a href="{{ route('performance-reports.edit', $performanceReport) }}"
                               class="btn btn-outline-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                        <a href="{{ route('performance-reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h6 class="text-muted">Employee</h6>
                            <p class="mb-0">
                                <strong>{{ $performanceReport->user->name ?? 'N/A' }}</strong>
                            </p>
                            <small class="text-muted">ID: {{ $performanceReport->user_id }}</small>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Department</h6>
                            <p class="mb-0">
                                <strong>{{ $performanceReport->department->name ?? 'N/A' }}</strong>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Period</h6>
                            <p class="mb-0">
                                <strong>{{ $performanceReport->period_display }}</strong>
                            </p>
                            <small class="text-muted">
                                {{ $performanceReport->period_type }} – Report Date: {{ $performanceReport->report_date->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>

                    <!-- Metrics Scorecard -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Performance Metrics</h6>
                        </div>
                        <div class="card-body">
                            @if($performanceReport->metrics)
                                <div class="row">
                                    @foreach($performanceReport->getMetricsWithLabels() as $metric)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="d-flex justify-content-between align-items-center border rounded p-3">
                                                <div>
                                                    <strong>{{ $metric['label'] }}</strong>
                                                    @if($metric['comments'])
                                                        <p class="small mb-0 text-muted">{{ $metric['comments'] }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    @if($metric['score'] !== null)
                                                        <h3 class="mb-0">{{ $metric['score'] }}</h3>
                                                        <small class="text-muted">out of 100</small>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">No metrics recorded.</p>
                            @endif
                        </div>
                        @if($performanceReport->total_score)
                            <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
                                <strong>Overall Score</strong>
                                <h3 class="mb-0 text-primary">{{ $performanceReport->total_score }}</h3>
                            </div>
                        @endif
                    </div>

                    <!-- Summary & Notes -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Summary</h6>
                                </div>
                                <div class="card-body">
                                    <p>{{ $performanceReport->summary }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Strengths</h6>
                                </div>
                                <div class="card-body">
                                    <p>{{ $performanceReport->strengths ?: 'Not specified.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Areas for Improvement</h6>
                                </div>
                                <div class="card-body">
                                    <p>{{ $performanceReport->improvements ?: 'Not specified.' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Recommendations</h6>
                                </div>
                                <div class="card-body">
                                    <p>{{ $performanceReport->recommendations ?: 'Not specified.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review & Approval Section -->
                    @if($performanceReport->isSubmitted() || $performanceReport->isReviewed() || $performanceReport->isApproved() || $performanceReport->isRejected())
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Review & Approval</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if($performanceReport->reviewer)
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Reviewed By</h6>
                                            <p class="mb-0">
                                                <strong>{{ $performanceReport->reviewer->name }}</strong>
                                                @if($performanceReport->reviewed_at)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $performanceReport->reviewed_at->format('d/m/Y H:i') }}
                                                    </small>
                                                @endif
                                            </p>
                                            @if($performanceReport->review_notes)
                                                <div class="mt-2">
                                                    <strong>Review Notes:</strong>
                                                    <p class="mb-0">{{ $performanceReport->review_notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    @if($performanceReport->approver)
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Approved By</h6>
                                            <p class="mb-0">
                                                <strong>{{ $performanceReport->approver->name }}</strong>
                                                @if($performanceReport->approved_at)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $performanceReport->approved_at->format('d/m/Y H:i') }}
                                                    </small>
                                                @endif
                                            </p>
                                            @if($performanceReport->approval_notes)
                                                <div class="mt-2">
                                                    <strong>Approval Notes:</strong>
                                                    <p class="mb-0">{{ $performanceReport->approval_notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                @if($performanceReport->isRejected() && $performanceReport->rejection_reason)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="alert alert-danger">
                                                <strong>Rejection Reason:</strong>
                                                <p class="mb-0">{{ $performanceReport->rejection_reason }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="btn-group">
                                @can('submit', $performanceReport)
                                    <form method="POST" action="{{ route('performance-reports.submit', $performanceReport) }}"
                                          class="d-inline" onsubmit="return confirm('Submit this report for review?')">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Submit for Review
                                        </button>
                                    </form>
                                @endcan
                                @can('review', $performanceReport)
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#reviewModal">
                                        <i class="fas fa-check-circle"></i> Review
                                    </button>
                                @endcan
                                @can('approve', $performanceReport)
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#approveModal">
                                        <i class="fas fa-thumbs-up"></i> Approve
                                    </button>
                                @endcan
                                @can('reject', $performanceReport)
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal">
                                        <i class="fas fa-times-circle"></i> Reject
                                    </button>
                                @endcan
                                @can('delete', $performanceReport)
                                    <form method="POST" action="{{ route('performance-reports.destroy', $performanceReport) }}"
                                          class="d-inline" onsubmit="return confirm('Delete this report permanently?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    @if($performanceReport->notes)
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Additional Notes</h6>
                            </div>
                            <div class="card-body">
                                <p>{{ $performanceReport->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="row mt-4 text-muted small">
                        <div class="col-md-4">
                            <strong>Created:</strong>
                            {{ $performanceReport->created_at->format('d/m/Y H:i') }}
                            by {{ $performanceReport->user->name ?? 'System' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Updated:</strong>
                            {{ $performanceReport->updated_at->format('d/m/Y H:i') }}
                        </div>
                        @if($performanceReport->deleted_at)
                            <div class="col-md-4">
                                <strong>Deleted:</strong>
                                {{ $performanceReport->deleted_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
@can('review', $performanceReport)
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('performance-reports.review', $performanceReport) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Review Performance Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="review_notes" class="form-label required">Review Notes</label>
                            <textarea name="review_notes" id="review_notes" rows="4" class="form-control" required
                                      placeholder="Provide feedback, observations..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="total_score" class="form-label">Overall Score (optional)</label>
                            <input type="number" name="total_score" id="total_score" class="form-control"
                                   min="0" max="100" step="0.1"
                                   placeholder="Leave empty to keep auto‑calculated score">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

<!-- Approve Modal -->
@can('approve', $performanceReport)
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('performance-reports.approve', $performanceReport) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Performance Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to approve this performance report?</p>
                        <div class="mb-3">
                            <label for="approval_notes" class="form-label">Approval Notes (optional)</label>
                            <textarea name="approval_notes" id="approval_notes" rows="3" class="form-control"
                                      placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

<!-- Reject Modal -->
@can('reject', $performanceReport)
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('performance-reports.reject', $performanceReport) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Performance Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>This will reject the report and return it to the employee for revision.</p>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label required">Rejection Reason</label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="4" class="form-control" required
                                      placeholder="Explain why this report is being rejected..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan
@endsection