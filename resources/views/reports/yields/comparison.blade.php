@extends('layouts.base')

@section('title', 'Yield Comparison')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-balance-scale me-2"></i>
                        Yield Comparison
                    </h1>
                    <p class="text-muted mb-0">Compare yields across dimensions</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.yields.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Comparison Criteria
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.yields.comparison') }}" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Group By</label>
                            <select name="group_by" class="form-select">
                                <option value="season" {{ $groupBy == 'season' ? 'selected' : '' }}>Season</option>
                                <option value="region" {{ $groupBy == 'region' ? 'selected' : '' }}>Region</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Compare With</label>
                            <select name="compare_with" class="form-select">
                                <option value="crop" {{ $compareWith == 'crop' ? 'selected' : '' }}>Crop</option>
                                <option value="region" {{ $compareWith == 'region' ? 'selected' : '' }}>Region</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sync-alt me-1"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Comparison Table -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        Comparison Results
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ ucfirst($groupBy) }}</th>
                                    <th>{{ ucfirst($compareWith) }}</th>
                                    <th class="text-end">Total Quantity (kg)</th>
                                    <th class="text-end">Avg Yield (kg/ha)</th>
                                    <th class="text-end">% of Group</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comparisonData as $groupName => $items)
                                    @php
                                        $groupTotal = $items->sum('total_quantity');
                                    @endphp
                                    <tr class="table-group-divider">
                                        <td colspan="5" class="bg-light fw-bold">
                                            {{ $groupName }}
                                            <small class="text-muted ms-2">(Total: {{ number_format($groupTotal, 1) }} kg)</small>
                                        </td>
                                    </tr>
                                    @foreach($items as $item)
                                        @php
                                            $percentage = $groupTotal > 0 ? round(($item->total_quantity / $groupTotal) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td></td>
                                            <td>{{ $item->{str_replace('-', '_', $compareWith)} ?? $item->{$compareWith} }}</td>
                                            <td class="text-end">{{ number_format($item->total_quantity, 1) }}</td>
                                            <td class="text-end">{{ number_format($item->avg_yield, 2) }}</td>
                                            <td class="text-end">{{ $percentage }}%</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection