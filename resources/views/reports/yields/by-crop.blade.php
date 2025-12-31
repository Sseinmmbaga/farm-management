@extends('layouts.base')

@section('title', 'Yields by Crop')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-seedling me-2"></i>
                        Yields by Crop
                    </h1>
                    <p class="text-muted mb-0">Harvest performance per crop type</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.yields.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        Crop‑wise Yield Summary
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Crop</th>
                                    <th class="text-end">Harvest Count</th>
                                    <th class="text-end">Total Quantity (kg)</th>
                                    <th class="text-end">Avg Yield (kg/ha)</th>
                                    <th class="text-end">Min Yield</th>
                                    <th class="text-end">Max Yield</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cropYields as $crop)
                                    @php
                                        $percentage = $totalQuantity > 0 ? round(($crop->total_quantity / $totalQuantity) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ ucfirst($crop->crop_type) }}</strong></td>
                                        <td class="text-end">{{ $crop->harvest_count }}</td>
                                        <td class="text-end">{{ number_format($crop->total_quantity, 1) }}</td>
                                        <td class="text-end">{{ number_format($crop->avg_yield, 2) }}</td>
                                        <td class="text-end">{{ number_format($crop->min_yield, 2) }}</td>
                                        <td class="text-end">{{ number_format($crop->max_yield, 2) }}</td>
                                        <td class="text-end">{{ $percentage }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">{{ $cropYields->sum('harvest_count') }}</td>
                                    <td class="text-end">{{ number_format($totalQuantity, 1) }}</td>
                                    <td class="text-end">
                                        @if($cropYields->count() > 0)
                                            {{ number_format($cropYields->avg('avg_yield'), 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($qualityDistribution->count())
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>
                        Quality Grade Distribution per Crop
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($qualityDistribution as $cropType => $findings)
                        <h6 class="mt-3">{{ ucfirst($cropType) }}</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Quality Grade</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">% of Crop</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cropTotal = $findings->sum('count');
                                    @endphp
                                    @foreach($findings as $finding)
                                        <tr>
                                            <td>{{ $finding->quality_grade }}</td>
                                            <td class="text-end">{{ $finding->count }}</td>
                                            <td class="text-end">{{ $cropTotal > 0 ? round(($finding->count / $cropTotal) * 100, 1) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection