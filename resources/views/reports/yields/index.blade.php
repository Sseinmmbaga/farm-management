@extends('layouts.base')

@section('title', 'Yields Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Yields Report
                    </h1>
                    <p class="text-muted mb-0">Overview of harvest yields and performance</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.yields.by-season') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt me-1"></i> By Season
                    </a>
                    <a href="{{ route('reports.yields.by-crop') }}" class="btn btn-outline-primary">
                        <i class="fas fa-seedling me-1"></i> By Crop
                    </a>
                    <a href="{{ route('reports.yields.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export CSV
                    </a>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Harvested</h6>
                                    <h2 class="mb-0">{{ number_format($totalQuantity, 1) }}</h2>
                                    <small class="text-muted">kg</small>
                                </div>
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-weight-hanging fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Area</h6>
                                    <h2 class="mb-0">{{ number_format($totalArea, 2) }}</h2>
                                    <small class="text-muted">ha</small>
                                </div>
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-map-marked-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Average Yield</h6>
                                    <h2 class="mb-0">{{ number_format($averageYield, 2) }}</h2>
                                    <small class="text-muted">kg/ha</small>
                                </div>
                                <div class="bg-warning text-white rounded-circle p-3">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Recent Harvests</h6>
                                    <h2 class="mb-0">{{ $recentHarvests->count() }}</h2>
                                    <small class="text-muted">last 10</small>
                                </div>
                                <div class="bg-info text-white rounded-circle p-3">
                                    <i class="fas fa-history fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Top Crops -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>
                                Top Crops by Quantity
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Crop</th>
                                            <th class="text-end">Total Quantity (kg)</th>
                                            <th class="text-end">Avg Yield (kg/ha)</th>
                                            <th class="text-end">% of Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topCrops as $crop)
                                            @php
                                                $percentage = $totalQuantity > 0 ? round(($crop->total_quantity / $totalQuantity) * 100, 1) : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ ucfirst($crop->crop_type) }}</strong>
                                                </td>
                                                <td class="text-end">{{ number_format($crop->total_quantity, 1) }}</td>
                                                <td class="text-end">{{ number_format($crop->avg_yield, 2) }}</td>
                                                <td class="text-end">{{ $percentage }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trend -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Monthly Harvest Trend (Last 12 Months)
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(count($monthlyData) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th class="text-end">Quantity (kg)</th>
                                                <th class="text-end">Cumulative</th>
                                                <th class="text-end">Trend</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $cumulative = 0;
                                            @endphp
                                            @foreach($monthlyData as $month => $quantity)
                                                @php
                                                    $cumulative += $quantity;
                                                    $prevMonth = \Carbon\Carbon::parse($month . '-01')->subMonth()->format('Y-m');
                                                    $prev = $monthlyData[$prevMonth] ?? 0;
                                                    $trend = $prev > 0 ? round((($quantity - $prev) / $prev) * 100, 1) : ($quantity > 0 ? 100 : 0);
                                                @endphp
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($month . '-01')->format('M Y') }}</td>
                                                    <td class="text-end">{{ number_format($quantity, 1) }}</td>
                                                    <td class="text-end">{{ number_format($cumulative, 1) }}</td>
                                                    <td class="text-end {{ $trend > 0 ? 'text-success' : ($trend < 0 ? 'text-danger' : 'text-muted') }}">
                                                        <i class="fas fa-arrow-{{ $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'right') }} me-1"></i>
                                                        {{ $trend }}%
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center mb-0">No harvest data available for the last 12 months.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Harvests -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Recent Harvests
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Crop</th>
                                    <th>Farmer</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Yield (kg/ha)</th>
                                    <th class="text-end">Quality</th>
                                    <th class="text-end">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentHarvests as $harvest)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $harvest->crop_type_label }}</span>
                                        </td>
                                        <td>{{ $harvest->activityLog->farmer?->full_name ?? '-' }}</td>
                                        <td class="text-end">{{ $harvest->quantity_display }}</td>
                                        <td class="text-end">{{ $harvest->yield_display }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $harvest->quality_grade_color }}">
                                                {{ $harvest->quality_grade_label }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ $harvest->activityLog->log_date?->format('M d, Y') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        About Yield Data
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-lightbulb me-2"></i> Definitions</h6>
                            <p class="small"><strong>Yield per hectare:</strong> Calculated as total harvested quantity divided by harvested area. This metric helps compare productivity across different crops and seasons.</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i> Data Sources</h6>
                            <p class="small">Data is collected from harvest logs submitted by extension officers and farmers. Only completed harvest activities are included. Missing or pending logs are not reflected.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .btn-group {
        display: none !important;
    }
}
</style>
@endsection