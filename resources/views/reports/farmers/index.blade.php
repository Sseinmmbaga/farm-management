@extends('layouts.base')

@section('title', 'Farmers Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users me-2"></i>
                        Farmers Report
                    </h1>
                    <p class="text-muted mb-0">Overview of farmer statistics and trends</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.farmers.by-region') }}" class="btn btn-outline-primary">
                        <i class="fas fa-map-marked-alt me-1"></i> By Region
                    </a>
                    <a href="{{ route('reports.farmers.by-status') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-check me-1"></i> By Status
                    </a>
                    <a href="{{ route('reports.farmers.export') }}" class="btn btn-success">
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
                                    <h6 class="text-muted mb-1">Total Farmers</h6>
                                    <h2 class="mb-0">{{ $totalFarmers }}</h2>
                                </div>
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-user-friends fa-2x"></i>
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
                                    <h6 class="text-muted mb-1">Active Farmers</h6>
                                    <h2 class="mb-0">{{ $activeFarmers }}</h2>
                                </div>
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-user-check fa-2x"></i>
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
                                    <h6 class="text-muted mb-1">Pending Approval</h6>
                                    <h2 class="mb-0">{{ $pendingFarmers }}</h2>
                                </div>
                                <div class="bg-warning text-white rounded-circle p-3">
                                    <i class="fas fa-user-clock fa-2x"></i>
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
                                    <h6 class="text-muted mb-1">Organic Certified</h6>
                                    <h2 class="mb-0">{{ $organicFarmers }}</h2>
                                </div>
                                <div class="bg-info text-white rounded-circle p-3">
                                    <i class="fas fa-leaf fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Gender Distribution -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-venus-mars me-2"></i>
                                Gender Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($genderDistribution->count())
                                <div class="mb-3">
                                    @foreach($genderDistribution as $gender => $count)
                                        @php
                                            $percentage = $totalFarmers > 0 ? round(($count / $totalFarmers) * 100, 1) : 0;
                                            $color = match($gender) {
                                                'male' => 'primary',
                                                'female' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2">
                                            <span><i class="fas fa-circle text-{{ $color }} me-2"></i> {{ ucfirst($gender) }}</span>
                                            <span>{{ $count }} ({{ $percentage }}%)</span>
                                        </div>
                                        <div class="progress mb-3" style="height: 12px;">
                                            <div class="progress-bar bg-{{ $color }}" style="width: {{ $percentage }}%;"></div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center mb-0">No gender data available</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Monthly Registrations -->
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Monthly Registrations (Last 12 Months)
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($monthlyRegistrations->count())
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th class="text-end">Registrations</th>
                                                <th class="text-end">Cumulative</th>
                                                <th class="text-end">Trend</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $cumulative = 0;
                                            @endphp
                                            @foreach($monthlyRegistrations as $month => $count)
                                                @php
                                                    $cumulative += $count;
                                                    $prevMonth = \Carbon\Carbon::parse($month . '-01')->subMonth()->format('Y-m');
                                                    $prevCount = $monthlyRegistrations->get($prevMonth, 0);
                                                    $trend = $prevCount > 0 ? round((($count - $prevCount) / $prevCount) * 100, 1) : ($count > 0 ? 100 : 0);
                                                @endphp
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($month . '-01')->format('M Y') }}</td>
                                                    <td class="text-end">{{ $count }}</td>
                                                    <td class="text-end">{{ $cumulative }}</td>
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
                                <p class="text-muted text-center mb-0">No registration data available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Regions -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Top 5 Regions by Farmer Count
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Region</th>
                                    <th class="text-end">Farmers</th>
                                    <th class="text-end">% of Total</th>
                                    <th class="text-end">Avg Land Size (ha)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topRegions as $region)
                                    @php
                                        $percentage = $totalFarmers > 0 ? round(($region->farmers_count / $totalFarmers) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $region->name }}</strong>
                                        </td>
                                        <td class="text-end">{{ $region->farmers_count }}</td>
                                        <td class="text-end">{{ $percentage }}%</td>
                                        <td class="text-end">
                                            @if($region->farmers_sum_total_land_size)
                                                {{ number_format($region->farmers_sum_total_land_size / $region->farmers_count, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Farmers -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Recently Registered Farmers
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Registration No</th>
                                    <th>Name</th>
                                    <th>Region</th>
                                    <th>Extension Officer</th>
                                    <th class="text-end">Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentFarmers as $farmer)
                                    <tr>
                                        <td><strong class="text-primary">{{ $farmer->registration_number }}</strong></td>
                                        <td>{{ $farmer->full_name }}</td>
                                        <td>{{ $farmer->region?->name ?? '-' }}</td>
                                        <td>{{ $farmer->extensionOfficer?->name ?? '-' }}</td>
                                        <td class="text-end">{{ $farmer->registration_date?->format('M d, Y') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    .card {
        break-inside: avoid;
    }
}
</style>
@endsection