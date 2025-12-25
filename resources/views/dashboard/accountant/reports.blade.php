@extends('layouts.base')

@section('title', 'Financial Reports')

@push('styles')
<style>
    .filter-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .stats-row {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-item {
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        flex: 1;
        text-align: center;
        border-left: 4px solid;
    }
    .stat-item.total { border-left-color: #3498db; }
    .stat-item.monthly { border-left-color: #2ecc71; }
    .stat-item.pending { border-left-color: #f39c12; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .report-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s;
    }
    .report-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .report-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Financial Reports</h1>
            <p class="text-muted mb-0">Generate and manage financial reports</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.accountant') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i> Generate Report
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-primary">{{ $stats['total_reports'] }}</div>
            <div class="label">Total Reports</div>
        </div>
        <div class="stat-item monthly">
            <div class="number text-success">{{ $stats['generated_this_month'] }}</div>
            <div class="label">Generated This Month</div>
        </div>
        <div class="stat-item pending">
            <div class="number text-warning">{{ $stats['pending_review'] }}</div>
            <div class="label">Pending Review</div>
        </div>
    </div>

    <!-- Quick Report Generation -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="report-card text-center">
                <div class="report-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h6>Daily Summary</h6>
                <p class="text-muted small mb-3">Generate today's financial summary</p>
                <a href="#" class="btn btn-sm btn-outline-primary">Generate</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="report-card text-center">
                <div class="report-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <h6>Weekly Report</h6>
                <p class="text-muted small mb-3">Generate weekly financial report</p>
                <a href="#" class="btn btn-sm btn-outline-success">Generate</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="report-card text-center">
                <div class="report-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h6>Monthly Report</h6>
                <p class="text-muted small mb-3">Generate monthly financial report</p>
                <a href="#" class="btn btn-sm btn-outline-warning">Generate</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="report-card text-center">
                <div class="report-icon bg-info bg-opacity-10 text-info mx-auto mb-3">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h6>Custom Report</h6>
                <p class="text-muted small mb-3">Generate custom date range report</p>
                <a href="#" class="btn btn-sm btn-outline-info">Generate</a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.accountant.reports') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Report Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="daily" {{ request('type') === 'daily' ? 'selected' : '' }}>Daily Summary</option>
                    <option value="weekly" {{ request('type') === 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                    <option value="monthly" {{ request('type') === 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                    <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Custom Report</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Reports List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Generated Reports</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Type</th>
                        <th>Period</th>
                        <th>Generated By</th>
                        <th>Generated At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td><strong>#{{ $report->id ?? 'N/A' }}</strong></td>
                        <td>{{ ucfirst($report->type ?? 'N/A') }}</td>
                        <td>{{ $report->period ?? 'N/A' }}</td>
                        <td>{{ $report->user->name ?? 'N/A' }}</td>
                        <td>{{ $report->created_at ? $report->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ ($report->status ?? 'completed') === 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($report->status ?? 'completed') }}
                            </span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-success" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No reports generated yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports instanceof \Illuminate\Pagination\LengthAwarePaginator && $reports->hasPages())
        <div class="card-footer">
            {{ $reports->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
