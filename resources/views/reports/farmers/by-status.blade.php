@extends('layouts.base')

@section('title', 'Farmers by Status')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-check me-2"></i>
                        Farmers by Status
                    </h1>
                    <p class="text-muted mb-0">Distribution of farmers by registration status</p>
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
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Farmers</h6>
                            <h2 class="mb-0">{{ $total }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Active Farmers</h6>
                            <h2 class="mb-0">{{ $statusData['active']['count'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>
                                Status Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($statusData as $status => $data)
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

                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-table me-2"></i>
                                Status Details
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Status</th>
                                            <th class="text-end">Count</th>
                                            <th class="text-end">Percentage</th>
                                            <th class="text-end">Color</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($statusData as $status => $data)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-{{ $data['color'] }}">{{ $data['label'] }}</span>
                                                </td>
                                                <td class="text-end">{{ $data['count'] }}</td>
                                                <td class="text-end">{{ $data['percentage'] }}%</td>
                                                <td class="text-end">
                                                    <span class="badge bg-{{ $data['color'] }}">{{ ucfirst($data['color']) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            <td class="text-end">{{ $total }}</td>
                                            <td class="text-end">100%</td>
                                            <td class="text-end">-</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Status Definitions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-success me-2">Active</span>
                                <span class="small">Fully registered and participating farmers</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-warning me-2">Pending</span>
                                <span class="small">Awaiting approval from extension officer</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-secondary me-2">Inactive</span>
                                <span class="small">Registered but not currently active</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-danger me-2">Suspended</span>
                                <span class="small">Temporarily suspended from program</span>
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