@extends('layouts.base')

@section('title', 'Yields by Season')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Yields by Season
                    </h1>
                    <p class="text-muted mb-0">Harvest performance across seasons</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.yields.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        Season‑wise Yield Summary
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Season</th>
                                    <th class="text-end">Harvest Count</th>
                                    <th class="text-end">Total Quantity (kg)</th>
                                    <th class="text-end">Avg Yield (kg/ha)</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seasonYields as $season)
                                    @php
                                        $percentage = $totalQuantity > 0 ? round(($season->total_quantity / $totalQuantity) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $season->name }}</strong></td>
                                        <td class="text-end">{{ $season->harvest_count }}</td>
                                        <td class="text-end">{{ number_format($season->total_quantity, 1) }}</td>
                                        <td class="text-end">{{ number_format($season->avg_yield, 2) }}</td>
                                        <td class="text-end">{{ $percentage }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">{{ $seasonYields->sum('harvest_count') }}</td>
                                    <td class="text-end">{{ number_format($totalQuantity, 1) }}</td>
                                    <td class="text-end">
                                        @if($seasonYields->count() > 0)
                                            {{ number_format($seasonYields->avg('avg_yield'), 2) }}
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
@endsection