@extends('layouts.base')

@section('title', 'Stock Distributions Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-truck me-2"></i>
                        Stock Distributions Report
                    </h1>
                    <p class="text-muted mb-0">Track stock distributions to farmers</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.stock.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Overview
                    </a>
                    <a href="{{ route('reports.stock.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
                </div>
            </div>

            <!-- Top Distributed Items -->
            @if($summaryByItem->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Top Distributed Items</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($summaryByItem as $summary)
                            @php
                                $maxQuantity = $summaryByItem->max('total_quantity');
                                $percentage = $maxQuantity > 0 ? round(($summary->total_quantity / $maxQuantity) * 100) : 0;
                            @endphp
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>{{ $summary->item?->name ?? 'Unknown Item' }}</span>
                                    <span class="badge bg-primary">{{ number_format($summary->total_quantity, 2) }} ({{ $summary->count }} distributions)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Monthly Distribution Trend -->
            @if($monthlyDistribution->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Distribution Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="distributionChart" height="100"></canvas>
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.stock.distributions') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('reports.stock.distributions') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Distributions Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Distributions ({{ $distributions->total() }} total)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Distribution #</th>
                                    <th>Farmer</th>
                                    <th>Item</th>
                                    <th class="text-end">Quantity</th>
                                    <th>Request Ref</th>
                                    <th>Notes</th>
                                    <th class="text-end">Distribution Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($distributions as $distribution)
                                    <tr>
                                        <td><code>{{ $distribution->distribution_number ?? 'N/A' }}</code></td>
                                        <td>
                                            <strong>{{ $distribution->farmer?->full_name ?? 'N/A' }}</strong>
                                            @if($distribution->farmer?->farmer_code)
                                                <br><small class="text-muted">{{ $distribution->farmer->farmer_code }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $distribution->item?->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($distribution->quantity, 2) }} {{ $distribution->item?->unit }}</td>
                                        <td>
                                            @if($distribution->request)
                                                <code>{{ $distribution->request->request_number ?? 'N/A' }}</code>
                                            @else
                                                <span class="text-muted">Direct</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($distribution->notes ?? '-', 30) }}</td>
                                        <td class="text-end">{{ $distribution->distribution_date?->format('M d, Y') ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-truck fa-3x mb-3 d-block"></i>
                                            No distributions found matching your criteria
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($distributions->hasPages())
                <div class="card-footer">
                    {{ $distributions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($monthlyDistribution->isNotEmpty())
    const distributionCtx = document.getElementById('distributionChart').getContext('2d');
    const distributionData = @json($monthlyDistribution);

    new Chart(distributionCtx, {
        type: 'line',
        data: {
            labels: Object.keys(distributionData).map(key => {
                const [year, month] = key.split('-');
                const date = new Date(year, month - 1);
                return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
            }),
            datasets: [{
                label: 'Distributed Quantity',
                data: Object.values(distributionData),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    @endif
</script>
@endpush
