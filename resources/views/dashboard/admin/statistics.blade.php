@extends('layouts.base')

@section('title', 'Statistics Overview')

@push('styles')
<style>
    .stats-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .chart-container {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .region-item {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .region-item:last-child {
        border-bottom: none;
    }

    .region-bar {
        height: 8px;
        border-radius: 4px;
        background: linear-gradient(90deg, #3498db, #2ecc71);
    }

    .certification-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-organic { background-color: #d4edda; color: #155724; }
    .badge-in-conversion { background-color: #fff3cd; color: #856404; }
    .badge-conventional { background-color: #e2e3e5; color: #383d41; }

    .month-item {
        padding: 10px 15px;
        border-radius: 8px;
        background: #f8f9fa;
        margin-bottom: 8px;
    }

    .month-item:hover {
        background: #e9ecef;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #2c3e50;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <h1 class="h3 mb-0">Statistics Overview</h1>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Farmers by Region -->
        <div class="col-md-6">
            <div class="chart-container">
                <h4 class="mb-4">
                    <i class="fas fa-map-marked-alt text-primary me-2"></i>
                    Farmers by Region
                </h4>
                @if($farmersByRegion->count() > 0)
                    @php
                        $maxCount = $farmersByRegion->max('count') ?: 1;
                        $totalFarmers = $farmersByRegion->sum('count');
                    @endphp
                    @foreach($farmersByRegion as $item)
                    <div class="region-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium">
                                {{ $item->region->name ?? 'Unknown Region' }}
                            </span>
                            <span class="text-muted">
                                {{ number_format($item->count) }} farmers
                                ({{ $totalFarmers > 0 ? round(($item->count / $totalFarmers) * 100, 1) : 0 }}%)
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="region-bar" style="width: {{ ($item->count / $maxCount) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong>{{ number_format($totalFarmers) }} farmers</strong>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-map fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No regional data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Farmers by Certification Status -->
        <div class="col-md-6">
            <div class="chart-container">
                <h4 class="mb-4">
                    <i class="fas fa-certificate text-success me-2"></i>
                    Certification Status
                </h4>
                @if($farmersByCertification->count() > 0)
                    @php
                        $totalCert = $farmersByCertification->sum('count');
                    @endphp
                    <div class="row text-center mb-4">
                        @foreach($farmersByCertification as $item)
                        <div class="col-md-4 mb-3">
                            <div class="stat-number">{{ number_format($item->count) }}</div>
                            <span class="certification-badge badge-{{ $item->certification_status ?? 'conventional' }}">
                                {{ ucfirst(str_replace('_', ' ', $item->certification_status ?? 'Unknown')) }}
                            </span>
                            <div class="text-muted small mt-1">
                                {{ $totalCert > 0 ? round(($item->count / $totalCert) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Progress bars -->
                    @foreach($farmersByCertification as $item)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ ucfirst(str_replace('_', ' ', $item->certification_status ?? 'Unknown')) }}</span>
                            <span>{{ number_format($item->count) }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            @php
                                $color = match($item->certification_status) {
                                    'organic' => 'success',
                                    'in_conversion', 'in-conversion' => 'warning',
                                    default => 'secondary'
                                };
                            @endphp
                            <div class="progress-bar bg-{{ $color }}"
                                 style="width: {{ $totalCert > 0 ? ($item->count / $totalCert) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No certification data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Monthly Registrations -->
    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <h4 class="mb-4">
                    <i class="fas fa-chart-line text-info me-2"></i>
                    Monthly Farmer Registrations (Last 12 Months)
                </h4>
                @if($monthlyRegistrations->count() > 0)
                    @php
                        $maxMonthly = $monthlyRegistrations->max('count') ?: 1;
                    @endphp
                    <div class="row">
                        @foreach($monthlyRegistrations->reverse() as $item)
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="month-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-primary">
                                            {{ \Carbon\Carbon::parse($item->month . '-01')->format('M Y') }}
                                        </div>
                                        <div class="text-muted small">{{ number_format($item->count) }} farmers</div>
                                    </div>
                                    <div class="stat-number" style="font-size: 1.5rem;">
                                        {{ $item->count }}
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: {{ ($item->count / $maxMonthly) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-3 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="stat-number" style="font-size: 1.5rem; color: #3498db;">
                                    {{ number_format($monthlyRegistrations->sum('count')) }}
                                </div>
                                <div class="text-muted">Total Registrations</div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-number" style="font-size: 1.5rem; color: #2ecc71;">
                                    {{ number_format($monthlyRegistrations->avg('count'), 1) }}
                                </div>
                                <div class="text-muted">Average per Month</div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-number" style="font-size: 1.5rem; color: #9b59b6;">
                                    {{ $monthlyRegistrations->first()->month ?? 'N/A' }}
                                </div>
                                <div class="text-muted">Most Recent Month</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No registration data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
