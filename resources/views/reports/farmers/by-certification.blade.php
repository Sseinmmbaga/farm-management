@extends('layouts.base')

@section('title', 'Farmers by Certification')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-certificate me-2"></i>
                        Farmers by Certification
                    </h1>
                    <p class="text-muted mb-0">Distribution of farmers by organic certification status</p>
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

            <!-- Summary -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Farmers</h6>
                            <h2 class="mb-0">{{ $total }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Organic Certified</h6>
                            <h2 class="mb-0">{{ $certificationData['organic']['count'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">In Conversion</h6>
                            <h2 class="mb-0">{{ $certificationData['in-conversion']['count'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certification Distribution -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>
                                Certification Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($certificationData as $cert => $data)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>
                                        <i class="fas fa-circle text-{{ $data['color'] }} me-2"></i>
                                        {{ $data['label'] }}
                                    </span>
                                    <span>{{ $data['count'] }} ({{ $data['percentage'] }}%)</span>
                                </div>
                                <div class="progress mb-3" style="height: 12px;">
                                    <div class="progress-bar bg-{{ $data['color'] }}" style="width: {{ $data['percentage'] }}%;"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-table me-2"></i>
                                Certification Details
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Certification</th>
                                            <th class="text-end">Count</th>
                                            <th class="text-end">Percentage</th>
                                            <th class="text-end">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($certificationData as $cert => $data)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-{{ $data['color'] }}">{{ $data['label'] }}</span>
                                                </td>
                                                <td class="text-end">{{ $data['count'] }}</td>
                                                <td class="text-end">{{ $data['percentage'] }}%</td>
                                                <td class="text-end small text-muted">
                                                    @switch($cert)
                                                        @case('organic')
                                                            Fully certified organic farmers
                                                            @break
                                                        @case('in-conversion')
                                                            Farmers transitioning to organic
                                                            @break
                                                        @default
                                                            Conventional farmers
                                                    @endswitch
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certification Trends -->
            @if($certificationTrends->count())
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Certification Trends (Last 12 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Organic</th>
                                    <th class="text-end">In Conversion</th>
                                    <th class="text-end">Conventional</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grouped = $certificationTrends->groupBy('month');
                                @endphp
                                @foreach($grouped as $month => $items)
                                    @php
                                        $organic = $items->where('certification_status', 'organic')->sum('count');
                                        $inConversion = $items->where('certification_status', 'in-conversion')->sum('count');
                                        $conventional = $items->where('certification_status', 'conventional')->sum('count');
                                        $totalMonth = $organic + $inConversion + $conventional;
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($month . '-01')->format('M Y') }}</td>
                                        <td class="text-end">{{ $organic }}</td>
                                        <td class="text-end">{{ $inConversion }}</td>
                                        <td class="text-end">{{ $conventional }}</td>
                                        <td class="text-end fw-bold">{{ $totalMonth }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Certification Definitions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-success me-2">Organic Certified</span>
                                <span class="small">Farmers who have completed organic certification and comply with organic standards.</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-warning me-2">In Conversion</span>
                                <span class="small">Farmers undergoing the 2‑3 year transition period to become fully organic certified.</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-secondary me-2">Conventional</span>
                                <span class="small">Farmers using conventional farming methods (not yet in organic conversion).</span>
                            </div>
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