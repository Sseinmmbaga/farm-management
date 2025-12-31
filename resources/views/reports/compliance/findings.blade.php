@extends('layouts.base')

@section('title', 'Inspection Findings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Inspection Findings
                    </h1>
                    <p class="text-muted mb-0">Analysis of compliance findings from inspections</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.compliance.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Overview
                    </a>
                    <a href="{{ route('reports.compliance.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
                </div>
            </div>

            <!-- Severity Distribution -->
            <div class="row mb-4">
                <div class="col-lg-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Severity Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($severityDistribution as $severity => $count)
                                    @php
                                        $severityColors = [
                                            'critical' => 'danger',
                                            'major' => 'warning',
                                            'minor' => 'info',
                                            'observation' => 'secondary'
                                        ];
                                        $total = $severityDistribution->sum();
                                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                    @endphp
                                    <div class="col-6 col-md-3 mb-3">
                                        <div class="text-center">
                                            <h3 class="text-{{ $severityColors[$severity] ?? 'secondary' }} mb-1">{{ number_format($count) }}</h3>
                                            <small class="text-muted d-block">{{ ucfirst($severity) }}</small>
                                            <div class="progress mt-2" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $severityColors[$severity] ?? 'secondary' }}" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $percentage }}%</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Compliance Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($complianceDistribution as $status => $count)
                                    @php
                                        $statusColors = [
                                            'compliant' => 'success',
                                            'non-compliant' => 'danger',
                                            'partially-compliant' => 'warning',
                                            'not-applicable' => 'secondary'
                                        ];
                                        $total = $complianceDistribution->sum();
                                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                    @endphp
                                    <div class="col-6 col-md-3 mb-3">
                                        <div class="text-center">
                                            <h3 class="text-{{ $statusColors[$status] ?? 'secondary' }} mb-1">{{ number_format($count) }}</h3>
                                            <small class="text-muted d-block">{{ ucfirst(str_replace('-', ' ', $status)) }}</small>
                                            <div class="progress mt-2" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $statusColors[$status] ?? 'secondary' }}" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $percentage }}%</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Non-Compliant Categories -->
            @if($topCategories->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Top Non-Compliant Categories</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($topCategories as $category)
                            @php
                                $maxCount = $topCategories->max('count');
                                $percentage = $maxCount > 0 ? round(($category->count / $maxCount) * 100) : 0;
                            @endphp
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>{{ ucfirst($category->category ?? 'Unknown') }}</span>
                                    <span class="badge bg-danger">{{ $category->count }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.compliance.findings') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="form-label">Severity</label>
                                <select name="severity" class="form-select">
                                    <option value="">All Severity</option>
                                    <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="major" {{ request('severity') === 'major' ? 'selected' : '' }}>Major</option>
                                    <option value="minor" {{ request('severity') === 'minor' ? 'selected' : '' }}>Minor</option>
                                    <option value="observation" {{ request('severity') === 'observation' ? 'selected' : '' }}>Observation</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Compliance Status</label>
                                <select name="compliance_status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="compliant" {{ request('compliance_status') === 'compliant' ? 'selected' : '' }}>Compliant</option>
                                    <option value="non-compliant" {{ request('compliance_status') === 'non-compliant' ? 'selected' : '' }}>Non-Compliant</option>
                                    <option value="partially-compliant" {{ request('compliance_status') === 'partially-compliant' ? 'selected' : '' }}>Partially Compliant</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('reports.compliance.findings') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Findings Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Findings ({{ $findings->total() }} total)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Inspection</th>
                                    <th>Farmer</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th class="text-center">Severity</th>
                                    <th class="text-center">Compliance</th>
                                    <th class="text-end">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($findings as $finding)
                                    <tr>
                                        <td><code>{{ $finding->inspection?->inspection_number ?? 'N/A' }}</code></td>
                                        <td>{{ $finding->inspection?->farmer?->full_name ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($finding->category ?? 'N/A') }}</td>
                                        <td>{{ Str::limit($finding->description ?? 'N/A', 50) }}</td>
                                        <td class="text-center">
                                            @php
                                                $severityColors = [
                                                    'critical' => 'danger',
                                                    'major' => 'warning',
                                                    'minor' => 'info',
                                                    'observation' => 'secondary'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $severityColors[$finding->severity] ?? 'secondary' }}">
                                                {{ ucfirst($finding->severity ?? 'Unknown') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusColors = [
                                                    'compliant' => 'success',
                                                    'non-compliant' => 'danger',
                                                    'partially-compliant' => 'warning',
                                                    'not-applicable' => 'secondary'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$finding->compliance_status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('-', ' ', $finding->compliance_status ?? 'Unknown')) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ $finding->inspection?->inspection_date?->format('M d, Y') ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-search fa-3x mb-3 d-block"></i>
                                            No findings found matching your criteria
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($findings->hasPages())
                <div class="card-footer">
                    {{ $findings->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
