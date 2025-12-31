@extends('layouts.base')

@section('title', 'Training Certificates')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-certificate me-2"></i> Training Certificates
                        </h4>
                        <div>
                            <a href="{{ route('training.reports.summary') }}" class="btn btn-light">
                                <i class="fas fa-chart-bar me-1"></i> Reports
                            </a>
                            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Certificates</h6>
                                            <h3 class="mb-0">{{ $certificates->total() }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-certificate fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Active</h6>
                                            <h3 class="mb-0">{{ $certificates->where('is_valid', true)->count() }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Expiring Soon</h6>
                                            <h3 class="mb-0">{{ $certificates->where('days_to_expiry', '<', 30)->where('days_to_expiry', '>=', 0)->count() }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Expired/Revoked</h6>
                                            <h3 class="mb-0">{{ $certificates->where('is_expired', true)->count() + $certificates->where('status', 'revoked')->count() }}</h3>
                                        </div>
                                        <div class="bg-danger text-white rounded-circle p-3">
                                            <i class="fas fa-times-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('training.certificates.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by farmer, certificate number..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>Revoked</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="program" class="form-select" onchange="this.form.submit()">
                                <option value="">All Programs</option>
                                @foreach($programs ?? [] as $program)
                                    <option value="{{ $program->id }}" {{ request('program') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="month"
                                   name="month"
                                   class="form-control"
                                   value="{{ request('month') }}"
                                   placeholder="Issue Month">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Apply
                            </button>
                        </div>
                        @if(request('search') || request('status') || request('program') || request('month'))
                        <div class="col-md-1">
                            <a href="{{ route('training.certificates.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                        @endif
                    </form>
                </div>

                <!-- Certificates Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Certificate Number</th>
                                    <th>Farmer</th>
                                    <th>Training Program</th>
                                    <th>Session</th>
                                    <th>Issue Date</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certificates as $certificate)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $certificate->certificate_number }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $certificate->certificate_number }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($certificate->farmer->first_name ?? '?', 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $certificate->farmer->first_name ?? 'N/A' }} {{ $certificate->farmer->last_name ?? '' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $certificate->farmer->registration_number ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $certificate->program->name ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $certificate->program->code ?? '' }}</small>
                                        </td>
                                        <td>
                                            {{ $certificate->session->title ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">{{ $certificate->session->scheduled_date->format('M d, Y') ?? '' }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $certificate->issue_date->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $certificate->issue_date->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            @if($certificate->expiry_date)
                                                <strong class="{{ $certificate->is_expired ? 'text-danger' : ($certificate->days_to_expiry < 30 ? 'text-warning' : 'text-success') }}">
                                                    {{ $certificate->expiry_date->format('M d, Y') }}
                                                </strong>
                                                <br>
                                                <small class="text-muted">
                                                    @if($certificate->days_to_expiry !== null)
                                                        {{ abs($certificate->days_to_expiry) }} days {{ $certificate->days_to_expiry < 0 ? 'ago' : 'left' }}
                                                    @endif
                                                </small>
                                            @else
                                                <span class="badge bg-secondary">No expiry</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $certificate->status == 'active' ? 'success' : ($certificate->status == 'expired' ? 'warning' : 'danger') }}">
                                                {{ $certificate->status_label }}
                                            </span>
                                            @if($certificate->is_valid)
                                                <br>
                                                <small class="text-success">Valid</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('training.certificates.show', $certificate) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('training.certificates.download', $certificate) }}"
                                                   class="btn btn-outline-success"
                                                   title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if($certificate->status == 'active')
                                                    <button type="button"
                                                            class="btn btn-outline-warning"
                                                            title="Revoke"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#revokeModal{{ $certificate->id }}">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Revoke Modal -->
                                            <div class="modal fade" id="revokeModal{{ $certificate->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('training.certificates.revoke', $certificate) }}">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Revoke Certificate</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to revoke certificate <strong>{{ $certificate->certificate_number }}</strong>?</p>
                                                                <div class="mb-3">
                                                                    <label for="revocation_reason" class="form-label">Revocation Reason</label>
                                                                    <textarea class="form-control" id="revocation_reason" name="revocation_reason" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Confirm Revocation</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-certificate fa-3x mb-3"></i>
                                                <h5>No certificates found</h5>
                                                <p>Issue certificates to farmers after training sessions</p>
                                                <a href="{{ route('training.completed') }}" class="btn btn-primary">
                                                    <i class="fas fa-calendar-check me-1"></i> View Completed Sessions
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($certificates->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $certificates->firstItem() }} to {{ $certificates->lastItem() }} of {{ $certificates->total() }} certificates
                                </div>
                                <div>
                                    {{ $certificates->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal (placeholder) -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="GET" action="{{ route('training.certificates.index') }}">
                <div class="modal-header">
                    <h5 class="modal-title">Advanced Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Date Range</label>
                            <div class="input-group mb-3">
                                <input type="date" name="date_from" class="form-control" placeholder="From">
                                <span class="input-group-text">to</span>
                                <input type="date" name="date_to" class="form-control" placeholder="To">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expiry Range</label>
                            <div class="input-group mb-3">
                                <input type="date" name="expiry_from" class="form-control" placeholder="From">
                                <span class="input-group-text">to</span>
                                <input type="date" name="expiry_to" class="form-control" placeholder="To">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Village</label>
                            <select name="village_id" class="form-select">
                                <option value="">All Villages</option>
                                <!-- Populate villages -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Issued By</label>
                            <select name="issued_by" class="form-select">
                                <option value="">All Users</option>
                                <!-- Populate users -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection