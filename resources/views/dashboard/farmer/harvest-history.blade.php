@extends('layouts.base')

@section('title', 'Harvest History')

@push('styles')
<style>
    .stats-row {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-item {
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        flex: 1;
        text-align: center;
        border-left: 4px solid;
    }
    .stat-item.total { border-left-color: #27ae60; }
    .stat-item.volume { border-left-color: #2ecc71; }
    .stat-item.avg { border-left-color: #3498db; }
    .stat-item.recent { border-left-color: #f39c12; }
    .stat-item .number { font-size: 1.8rem; font-weight: bold; }
    .stat-item .label { font-size: 0.85rem; color: #6c757d; }
    .harvest-table th {
        border-top: none;
        font-weight: 600;
        color: #555;
        background: #f8f9fa;
    }
    .harvest-table td {
        vertical-align: middle;
    }
    .harvest-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .badge-completed, .badge-done { background-color: #e6f7ee; color: #27ae60; }
    .badge-pending { background-color: #fff4e6; color: #e67e22; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Harvest History</h1>
            <p class="text-muted mb-0">Track your cotton and sesame harvests over time</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-item total">
            <div class="number text-success">{{ $stats['total_harvests'] }}</div>
            <div class="label">Total Harvests</div>
        </div>
        <div class="stat-item volume">
            <div class="number text-success">{{ number_format($stats['total_volume']) }}</div>
            <div class="label">Total Volume (kg)</div>
        </div>
        <div class="stat-item avg">
            <div class="number text-primary">{{ number_format($stats['avg_per_harvest']) }}</div>
            <div class="label">Avg per Harvest (kg)</div>
        </div>
        <div class="stat-item recent">
            <div class="number text-warning">{{ $stats['pending_weighing'] }}</div>
            <div class="label">Pending Weighing</div>
        </div>
    </div>

    <!-- Harvest Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Harvest Records</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover harvest-table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Crop</th>
                            <th>Variety</th>
                            <th>Farm</th>
                            <th>Quantity</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($harvests as $harvest)
                        <tr>
                            <td>{{ $harvest->activityLog?->log_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $harvest->crop_type === 'cotton' ? 'seedling' : 'leaf' }} text-{{ $harvest->crop_type === 'cotton' ? 'success' : 'warning' }} me-2"></i>
                                    {{ $harvest->crop_type_label ?? ucfirst($harvest->crop_type ?? 'Cotton') }}
                                </div>
                            </td>
                            <td>{{ $harvest->variety ?? 'N/A' }}</td>
                            <td>{{ $harvest->activityLog?->farm?->name ?? 'N/A' }}</td>
                            <td><strong>{{ number_format($harvest->quantity_harvested) }}</strong> {{ $harvest->quantity_unit ?? 'kg' }}</td>
                            <td>
                                <span class="badge bg-{{ $harvest->quality_grade === 'A' ? 'success' : ($harvest->quality_grade === 'B' ? 'warning' : 'secondary') }}">
                                    Grade {{ $harvest->quality_grade ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="harvest-badge badge-{{ $harvest->activityLog?->status ?? 'pending' }}">
                                    {{ ucfirst($harvest->activityLog?->status ?? 'Pending') }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-leaf fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No Harvest Records</h6>
                                <p class="text-muted small mb-0">You haven't recorded any harvests yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($harvests instanceof \Illuminate\Pagination\LengthAwarePaginator && $harvests->hasPages())
        <div class="card-footer">
            {{ $harvests->links() }}
        </div>
        @endif
    </div>

    <!-- Harvest Summary by Crop -->
    @if($harvests->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Harvest by Crop Type</h5>
                </div>
                <div class="card-body">
                    @php
                        $cottonTotal = $harvests->where('crop_type', 'cotton')->sum('quantity_harvested');
                        $sesameTotal = $harvests->where('crop_type', 'sesame')->sum('quantity_harvested');
                        $totalAll = $cottonTotal + $sesameTotal;
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><i class="fas fa-circle text-success me-2"></i>Cotton</span>
                            <span>{{ number_format($cottonTotal) }} kg</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalAll > 0 ? ($cottonTotal / $totalAll * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><i class="fas fa-circle text-warning me-2"></i>Sesame</span>
                            <span>{{ number_format($sesameTotal) }} kg</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" style="width: {{ $totalAll > 0 ? ($sesameTotal / $totalAll * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Quality Distribution</h5>
                </div>
                <div class="card-body">
                    @php
                        $gradeA = $harvests->where('quality_grade', 'A')->count();
                        $gradeB = $harvests->where('quality_grade', 'B')->count();
                        $gradeC = $harvests->where('quality_grade', 'C')->count();
                        $totalGrades = $gradeA + $gradeB + $gradeC;
                    @endphp
                    <div class="d-flex justify-content-around text-center">
                        <div>
                            <h3 class="text-success mb-1">{{ $gradeA }}</h3>
                            <span class="badge bg-success">Grade A</span>
                        </div>
                        <div>
                            <h3 class="text-warning mb-1">{{ $gradeB }}</h3>
                            <span class="badge bg-warning">Grade B</span>
                        </div>
                        <div>
                            <h3 class="text-secondary mb-1">{{ $gradeC }}</h3>
                            <span class="badge bg-secondary">Grade C</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($harvests->isEmpty())
    <div class="text-center py-5 mt-4">
        <i class="fas fa-leaf fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">No Harvest Records</h5>
        <p class="text-muted">You haven't recorded any harvests yet.</p>
        <p class="text-muted small">Your cotton and sesame harvest records will appear here.</p>
    </div>
    @endif
@endsection
