@extends('layouts.base')

@section('title', 'Completed Inspections')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i> Completed Inspections
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
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Completed</h6>
                                            <h3 class="mb-0">{{ $inspections->total() }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Passed</h6>
                                            <h3 class="mb-0">{{ $passed ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Failed</h6>
                                            <h3 class="mb-0">{{ $failed ?? 0 }}</h3>
                                        </div>
                                        <div class="bg-danger text-white rounded-circle p-3">
                                            <i class="fas fa-times fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">Conditional</h6>
                                            <h3 class="mb-0">{{ $conditional ?? 0 }}</h3>
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
                            <a href="{{ route('inspections.scheduled') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-calendar-alt me-1"></i> Scheduled
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('inspections.in-progress') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-spinner me-1"></i> In Progress
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
                                    <th>Completed Date</th>
                                    <th>Inspector</th>
                                    <th>Result</th>
                                    <th>Score</th>
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
                                            @if($inspection->completed_at)
                                                {{ \Carbon\Carbon::parse($inspection->completed_at)->format('M d, Y') }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($inspection->completed_at)->diffForHumans() }}
                                                </small>
                                            @else
                                                <span class="text-muted">Not recorded</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $inspection->inspector->name ?? 'N/A' }}
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
                                            @if($inspection->percentage_score)
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $inspection->percentage_score >= 80 ? 'success' : ($inspection->percentage_score >= 60 ? 'warning' : 'danger') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $inspection->percentage_score }}%;"
                                                         aria-valuenow="{{ $inspection->percentage_score }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ $inspection->percentage_score }}%
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $inspection->total_score ?? 0 }}/{{ $inspection->max_possible_score ?? 100 }}</small>
                                            @else
                                                <span class="text-muted">Not scored</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('inspections.show', $inspection) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!$inspection->signed_by_inspector)
                                                    <form action="{{ route('inspections.sign', $inspection) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-info" title="Sign Off">
                                                            <i class="fas fa-signature"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('findings.create', ['inspection_id' => $inspection->id]) }}"
                                                   class="btn btn-outline-warning"
                                                   title="Report Finding">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                                <h5>No completed inspections</h5>
                                                <p>All inspections are either scheduled, in progress, or cancelled.</p>
                                                <a href="{{ route('inspections.scheduled') }}" class="btn btn-warning">
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
                
                <!-- Export Options -->
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        <div class="btn-group">
                            <a href="{{ route('ics-reports.summary') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-chart-bar me-1"></i> Summary Report
                            </a>
                            <a href="{{ route('ics-reports.compliance') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-file-alt me-1"></i> Compliance Report
                            </a>
                            <a href="{{ route('ics-reports.findings') }}" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-exclamation-triangle me-1"></i> Findings Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection