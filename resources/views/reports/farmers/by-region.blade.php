@extends('layouts.base')

@section('title', 'Farmers by Region')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Farmers by Region
                    </h1>
                    <p class="text-muted mb-0">Distribution of farmers across regions</p>
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
                            <h6 class="text-muted mb-1">Total Regions</h6>
                            <h2 class="mb-0">{{ $regions->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Farmers</h6>
                            <h2 class="mb-0">{{ $totalFarmers }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Average per Region</h6>
                            <h2 class="mb-0">{{ $regions->count() > 0 ? round($totalFarmers / $regions->count(), 1) : 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regions Table -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        Regional Farmer Statistics
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Region</th>
                                    <th class="text-end">Total Farmers</th>
                                    <th class="text-end">Active Farmers</th>
                                    <th class="text-end">Organic Certified</th>
                                    <th class="text-end">Total Land Size (ha)</th>
                                    <th class="text-end">Avg Land Size (ha)</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regions as $region)
                                    @php
                                        $percentage = $totalFarmers > 0 ? round(($region->farmers_count / $totalFarmers) * 100, 2) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $region->name }}</strong>
                                        </td>
                                        <td class="text-end">{{ $region->farmers_count }}</td>
                                        <td class="text-end">{{ $region->active_farmers_count ?? 0 }}</td>
                                        <td class="text-end">{{ $region->organic_farmers_count ?? 0 }}</td>
                                        <td class="text-end">{{ number_format($region->total_land_size ?? 0, 2) }}</td>
                                        <td class="text-end">
                                            @if($region->farmers_count > 0)
                                                {{ number_format(($region->total_land_size ?? 0) / $region->farmers_count, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $percentage }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">{{ $totalFarmers }}</td>
                                    <td class="text-end">{{ $regions->sum('active_farmers_count') }}</td>
                                    <td class="text-end">{{ $regions->sum('organic_farmers_count') }}</td>
                                    <td class="text-end">{{ number_format($regions->sum('total_land_size'), 2) }}</td>
                                    <td class="text-end">
                                        @if($totalFarmers > 0)
                                            {{ number_format($regions->sum('total_land_size') / $totalFarmers, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">100%</td>
                                </tr>
                            </tfoot>
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
}
</style>
@endsection