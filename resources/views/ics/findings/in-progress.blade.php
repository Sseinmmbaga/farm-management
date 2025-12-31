@extends('layouts.base')

@section('title', 'Findings In Progress')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-spinner me-2"></i> Findings In Progress
                        </h4>
                        <div>
                            <a href="{{ route('findings.open') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-exclamation-circle me-1"></i> Open
                            </a>
                            <a href="{{ route('findings.index') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-list me-1"></i> All Findings
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">In Progress</h6>
                                            <h3 class="mb-0">{{ $findings->total() }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-spinner fa-2x"></i>
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
                                            <h6 class="text-muted mb-1">With Corrective Actions</h6>
                                            <h3 class="mb-0">{{ $findings->where('has_corrective_actions', true)->count() }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-tools fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Due This Week</h6>
                                            <h3 class="mb-0">{{ $findings->whereBetween('due_date', [now(), now()->addWeek()])->count() }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-calendar-week fa-2x"></i>
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
                            <a href="{{ route('findings.open') }}" class="btn btn-outline-danger w-100">
                                <i class="fas fa-exclamation-circle me-1"></i> Open
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('findings.resolved') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-check-circle me-1"></i> Resolved
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('findings.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-list me-1"></i> All Findings
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Findings Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Finding #</th>
                                    <th>Inspection</th>
                                    <th>Farmer</th>
                                    <th>Description</th>
                                    <th>Severity</th>
                                    <th>Due Date</th>
                                    <th>Progress</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($findings as $finding)
                                    <tr>
                                        <td>
                                            <strong class="text-warning">{{ $finding->finding_number }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('inspections.show', $finding->inspection) }}" class="text-decoration-none">
                                                <strong>{{ $finding->inspection->inspection_number ?? 'N/A' }}</strong>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($finding->inspection->farmer->first_name ?? '', 0, 1) }}{{ substr($finding->inspection->farmer->last_name ?? '', 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $finding->inspection->farmer->first_name ?? '' }} {{ $finding->inspection->farmer->last_name ?? '' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $finding->inspection->farmer->registration_number ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ Str::limit($finding->description, 50) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $finding->severity == 'critical' ? 'danger' : ($finding->severity == 'major' ? 'warning' : ($finding->severity == 'minor' ? 'info' : 'secondary')) }}">
                                                {{ ucfirst($finding->severity) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($finding->due_date)
                                                {{ $finding->due_date->format('M d, Y') }}
                                                @if($finding->due_date->isPast())
                                                    <br>
                                                    <small class="text-danger">Overdue</small>
                                                @else
                                                    <br>
                                                    <small class="text-success">{{ $finding->due_date->diffForHumans() }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No due date</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $totalActions = $finding->correctiveActions->count();
                                                $completedActions = $finding->correctiveActions->where('status', 'completed')->count();
                                                $percentage = $totalActions > 0 ? round(($completedActions / $totalActions) * 100) : 0;
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-warning" 
                                                     role="progressbar" 
                                                     style="width: {{ $percentage }}%;"
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ $percentage }}%
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $completedActions }}/{{ $totalActions }} actions</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('findings.show', $finding) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('corrective-actions.create', ['finding_id' => $finding->id]) }}"
                                                   class="btn btn-outline-info"
                                                   title="Add Corrective Action">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                <form action="{{ route('findings.resolve', $finding) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Mark Resolved">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-spinner fa-3x mb-3"></i>
                                                <h5>No findings in progress</h5>
                                                <p>All findings are either open, resolved, or closed.</p>
                                                <a href="{{ route('findings.open') }}" class="btn btn-warning">
                                                    <i class="fas fa-exclamation-circle me-1"></i> View Open Findings
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($findings->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $findings->firstItem() }} to {{ $findings->lastItem() }} of {{ $findings->total() }} findings
                                </div>
                                <div>
                                    {{ $findings->links() }}
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