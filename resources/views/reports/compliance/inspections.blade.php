@extends('layouts.base')

@section('title', 'Inspection Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Inspection Reports
                    </h1>
                    <p class="text-muted mb-0">Detailed view of all compliance inspections</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.compliance.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Overview
                    </a>
                    <a href="{{ route('reports.compliance.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
                </div>
            </div>

            <!-- Status Summary -->
            <div class="row mb-4">
                @foreach($statusCounts as $status => $count)
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-{{ $status === 'completed' ? 'success' : ($status === 'pending' ? 'warning' : ($status === 'scheduled' ? 'info' : 'secondary')) }} h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1">{{ ucfirst($status) }}</h6>
                            <h2 class="mb-0">{{ number_format($count) }}</h2>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Result Summary -->
            <div class="row mb-4">
                @foreach($resultCounts as $result => $count)
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card border-{{ $result === 'passed' ? 'success' : ($result === 'failed' ? 'danger' : 'warning') }} h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-1">{{ ucfirst($result) }}</h6>
                            <h2 class="mb-0 text-{{ $result === 'passed' ? 'success' : ($result === 'failed' ? 'danger' : 'warning') }}">{{ number_format($count) }}</h2>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.compliance.inspections') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Result</label>
                                <select name="result" class="form-select">
                                    <option value="">All Results</option>
                                    <option value="passed" {{ request('result') === 'passed' ? 'selected' : '' }}>Passed</option>
                                    <option value="failed" {{ request('result') === 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="pending" {{ request('result') === 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('reports.compliance.inspections') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Inspections Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Inspections ({{ $inspections->total() }} total)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Inspection #</th>
                                    <th>Farmer</th>
                                    <th>Checklist</th>
                                    <th>Inspector</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Result</th>
                                    <th class="text-end">Score</th>
                                    <th class="text-end">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inspections as $inspection)
                                    <tr>
                                        <td><code>{{ $inspection->inspection_number ?? 'N/A' }}</code></td>
                                        <td>{{ $inspection->farmer?->full_name ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($inspection->checklist?->name ?? 'N/A', 30) }}</td>
                                        <td>{{ $inspection->inspector?->name ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            @php
                                                $statusColors = [
                                                    'scheduled' => 'info',
                                                    'in_progress' => 'primary',
                                                    'completed' => 'success',
                                                    'cancelled' => 'secondary'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$inspection->status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $inspection->status ?? 'Unknown')) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($inspection->result === 'passed')
                                                <span class="badge bg-success">Passed</span>
                                            @elseif($inspection->result === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @elseif($inspection->result === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($inspection->result ?? 'N/A') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($inspection->percentage_score !== null)
                                                <span class="badge bg-{{ $inspection->percentage_score >= 80 ? 'success' : ($inspection->percentage_score >= 60 ? 'warning' : 'danger') }}">
                                                    {{ $inspection->percentage_score }}%
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $inspection->inspection_date?->format('M d, Y') ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-clipboard-list fa-3x mb-3 d-block"></i>
                                            No inspections found matching your criteria
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($inspections->hasPages())
                <div class="card-footer">
                    {{ $inspections->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
