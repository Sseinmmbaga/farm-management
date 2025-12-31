@extends('layouts.base')

@section('title', 'Certification Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-certificate me-2"></i>
                        Certification Reports
                    </h1>
                    <p class="text-muted mb-0">Farmer certification status and tracking</p>
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

            <!-- Status Summary -->
            <div class="row mb-4">
                @foreach($statusCounts as $status => $count)
                    @php
                        $statusColors = [
                            'active' => 'success',
                            'in_conversion' => 'warning',
                            'pending' => 'info',
                            'suspended' => 'danger',
                            'expired' => 'secondary',
                            'revoked' => 'dark'
                        ];
                        $statusIcons = [
                            'active' => 'check-circle',
                            'in_conversion' => 'sync-alt',
                            'pending' => 'clock',
                            'suspended' => 'pause-circle',
                            'expired' => 'times-circle',
                            'revoked' => 'ban'
                        ];
                    @endphp
                    <div class="col-md-2 col-sm-4 mb-3">
                        <div class="card border-{{ $statusColors[$status] ?? 'secondary' }} h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-{{ $statusIcons[$status] ?? 'circle' }} fa-2x text-{{ $statusColors[$status] ?? 'secondary' }} mb-2"></i>
                                <h3 class="mb-1">{{ number_format($count) }}</h3>
                                <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $status)) }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Expiration Timeline -->
            @if($expirationTimeline->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Expiration Timeline (Next 12 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="expirationChart" height="100"></canvas>
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.compliance.certifications') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="in_conversion" {{ request('status') === 'in_conversion' ? 'selected' : '' }}>In Conversion</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Revoked</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Expiring Soon</label>
                                <select name="expiring_soon" class="form-select">
                                    <option value="">All Certifications</option>
                                    <option value="1" {{ request('expiring_soon') === '1' ? 'selected' : '' }}>Expiring in 30 days</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('reports.compliance.certifications') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Certifications Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Certifications ({{ $certifications->total() }} total)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Farmer</th>
                                    <th>Certificate #</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Certification Date</th>
                                    <th class="text-center">Expiry Date</th>
                                    <th>Approved By</th>
                                    <th>Last Inspection</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certifications as $certification)
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'in_conversion' => 'warning',
                                            'pending' => 'info',
                                            'suspended' => 'danger',
                                            'expired' => 'secondary',
                                            'revoked' => 'dark'
                                        ];
                                        $isExpiringSoon = $certification->expiry_date && $certification->expiry_date->diffInDays(now()) <= 30 && $certification->expiry_date->isFuture();
                                    @endphp
                                    <tr class="{{ $isExpiringSoon ? 'table-warning' : '' }}">
                                        <td>
                                            <strong>{{ $certification->farmer?->full_name ?? 'N/A' }}</strong>
                                            @if($certification->farmer?->farmer_code)
                                                <br><small class="text-muted">{{ $certification->farmer->farmer_code }}</small>
                                            @endif
                                        </td>
                                        <td><code>{{ $certification->certificate_number ?? 'N/A' }}</code></td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $statusColors[$certification->status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $certification->status ?? 'Unknown')) }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $certification->certification_date?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            @if($certification->expiry_date)
                                                <span class="{{ $isExpiringSoon ? 'text-danger fw-bold' : '' }}">
                                                    {{ $certification->expiry_date->format('M d, Y') }}
                                                </span>
                                                @if($isExpiringSoon)
                                                    <br><small class="text-danger">Expiring soon!</small>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $certification->approvedBy?->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($certification->lastInspection)
                                                {{ $certification->lastInspection->inspection_date?->format('M d, Y') }}
                                                <br>
                                                @if($certification->lastInspection->result === 'passed')
                                                    <span class="badge bg-success">Passed</span>
                                                @elseif($certification->lastInspection->result === 'failed')
                                                    <span class="badge bg-danger">Failed</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($certification->lastInspection->result ?? 'N/A') }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No inspection</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-certificate fa-3x mb-3 d-block"></i>
                                            No certifications found matching your criteria
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($certifications->hasPages())
                <div class="card-footer">
                    {{ $certifications->links() }}
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
    @if($expirationTimeline->isNotEmpty())
    const expirationCtx = document.getElementById('expirationChart').getContext('2d');
    const expirationData = @json($expirationTimeline);

    new Chart(expirationCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(expirationData).map(key => {
                const [year, month] = key.split('-');
                const date = new Date(year, month - 1);
                return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
            }),
            datasets: [{
                label: 'Expiring Certifications',
                data: Object.values(expirationData),
                backgroundColor: 'rgba(255, 193, 7, 0.7)',
                borderColor: '#ffc107',
                borderWidth: 1
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
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush
