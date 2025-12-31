@extends('layouts.app')

@section('title', 'Performance Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Performance Reports</h5>
                    @can('create', \App\Models\PerformanceReport::class)
                        <a href="{{ route('performance-reports.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Report
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Status</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">Employee</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">All Employees</option>
                                @foreach($users as $id => $name)
                                    <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select name="department_id" id="department_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $id => $name)
                                    <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="period_year" class="form-label">Year</label>
                            <input type="number" name="period_year" id="period_year" class="form-control"
                                   placeholder="e.g. 2025" value="{{ request('period_year') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="period_month" class="form-label">Month</label>
                            <input type="number" name="period_month" id="period_month" class="form-control"
                                   placeholder="1-12" min="1" max="12" value="{{ request('period_month') }}">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('performance-reports.index') }}" class="btn btn-outline-secondary">
                                Clear
                            </a>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Report #</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Period</th>
                                    <th>Total Score</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td>
                                            <strong>{{ $report->report_number }}</strong>
                                        </td>
                                        <td>{{ $report->user->name ?? 'N/A' }}</td>
                                        <td>{{ $report->department->name ?? 'N/A' }}</td>
                                        <td>{{ $report->period_display }}</td>
                                        <td>
                                            @if($report->total_score)
                                                <span class="badge bg-primary rounded-pill">{{ $report->total_score }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $report->status_color }}">
                                                {{ $report->status_display }}
                                            </span>
                                        </td>
                                        <td>{{ $report->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('performance-reports.show', $report) }}"
                                                   class="btn btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('update', $report)
                                                    <a href="{{ route('performance-reports.edit', $report) }}"
                                                       class="btn btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('submit', $report)
                                                    <form method="POST" action="{{ route('performance-reports.submit', $report) }}"
                                                          class="d-inline" onsubmit="return confirm('Submit this report for review?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-primary" title="Submit">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('review', $report)
                                                    <a href="{{ route('performance-reports.show', $report) }}#review"
                                                       class="btn btn-outline-secondary" title="Review">
                                                        <i class="fas fa-check-circle"></i>
                                                    </a>
                                                @endcan
                                                @can('print', $report)
                                                    <a href="{{ route('performance-reports.print', $report) }}"
                                                       target="_blank" class="btn btn-outline-dark" title="Print">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                                <p>No performance reports found.</p>
                                                @can('create', \App\Models\PerformanceReport::class)
                                                    <a href="{{ route('performance-reports.create') }}" class="btn btn-primary">
                                                        Create your first report
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#status, #user_id, #department_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select...',
            allowClear: true
        });
    });
</script>
@endpush