@extends('layouts.base')

@section('title', 'Farmer Registrations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Farmer Registrations
                    </h1>
                    <p class="text-muted mb-0">Monthly and yearly registration trends</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.farmers.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Overview
                    </a>
                    <a href="{{ route('reports.farmers.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export CSV
                    </a>
                </div>
            </div>

            <!-- Year Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Select Year
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('reports.farmers.registrations') }}" class="row g-3">
                                <div class="col-md-8">
                                    <select name="year" class="form-select" onchange="this.form.submit()">
                                        @foreach($availableYears as $availableYear)
                                            <option value="{{ $availableYear }}" {{ $availableYear == $year ? 'selected' : '' }}>
                                                {{ $availableYear }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Registrations in {{ $year }}</h6>
                            <h2 class="mb-0">{{ array_sum($registrationData) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Chart -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Monthly Registrations for {{ $year }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Registrations</th>
                                    <th class="text-end">Cumulative</th>
                                    <th class="text-end">% of Year</th>
                                    <th class="text-end">Bar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cumulative = 0;
                                    $max = max($registrationData) ?: 1;
                                @endphp
                                @foreach($registrationData as $monthKey => $count)
                                    @php
                                        $cumulative += $count;
                                        $percentage = $count > 0 ? round(($count / $max) * 100, 1) : 0;
                                        $yearPercentage = array_sum($registrationData) > 0 ? round(($count / array_sum($registrationData)) * 100, 1) : 0;
                                        $monthName = \Carbon\Carbon::createFromDate($year, $monthKey, 1)->format('F');
                                    @endphp
                                    <tr>
                                        <td>{{ $monthName }}</td>
                                        <td class="text-end">{{ $count }}</td>
                                        <td class="text-end">{{ $cumulative }}</td>
                                        <td class="text-end">{{ $yearPercentage }}%</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-info" style="width: {{ $percentage }}%;">
                                                    {{ $count }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">{{ array_sum($registrationData) }}</td>
                                    <td class="text-end">{{ $cumulative }}</td>
                                    <td class="text-end">100%</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Year‑Over‑Year Comparison -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Year‑Over‑Year Comparison (Last 5 Years)
                    </h5>
                </div>
                <div class="card-body">
                    @if($yearlyTotals->count())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th class="text-end">Total Registrations</th>
                                        <th class="text-end">Change</th>
                                        <th class="text-end">Growth Rate</th>
                                        <th class="text-end">Trend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $previous = null;
                                        $years = $yearlyTotals->keys()->sort()->values();
                                    @endphp
                                    @foreach($years as $index => $yr)
                                        @php
                                            $current = $yearlyTotals->get($yr, 0);
                                            if ($previous !== null) {
                                                $change = $current - $previous;
                                                $growth = $previous > 0 ? round(($change / $previous) * 100, 1) : ($current > 0 ? 100 : 0);
                                            } else {
                                                $change = null;
                                                $growth = null;
                                            }
                                            $previous = $current;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $yr }}</strong>
                                                @if($yr == $year)
                                                    <span class="badge bg-primary">Current</span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $current }}</td>
                                            <td class="text-end {{ $change > 0 ? 'text-success' : ($change < 0 ? 'text-danger' : 'text-muted') }}">
                                                @if($change !== null)
                                                    {{ $change > 0 ? '+' : '' }}{{ $change }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end {{ $growth > 0 ? 'text-success' : ($growth < 0 ? 'text-danger' : 'text-muted') }}">
                                                @if($growth !== null)
                                                    {{ $growth > 0 ? '+' : '' }}{{ $growth }}%
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($change !== null)
                                                    @if($change > 0)
                                                        <i class="fas fa-arrow-up text-success"></i>
                                                    @elseif($change < 0)
                                                        <i class="fas fa-arrow-down text-danger"></i>
                                                    @else
                                                        <i class="fas fa-minus text-muted"></i>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No yearly data available.</p>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        About Registration Data
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-lightbulb me-2"></i> Interpretation</h6>
                            <p class="small">Registration counts reflect the number of farmers whose application was approved in that month/year. Seasonal patterns (e.g., higher registrations before planting seasons) are common.</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i> Data Quality</h6>
                            <p class="small">Only completed registrations are included. Pending or rejected applications are not counted. Data may be updated retroactively if historical records are corrected.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .btn-group, form {
        display: none !important;
    }
}
</style>
@endsection