@extends('layouts.base')

@section('title', 'Pending Farmers Approval')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-clock me-2"></i> Pending Farmers Approval
                        </h4>
                        <a href="{{ route('farmers.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Back to All Farmers
                        </a>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Pending Approval</h6>
                                            <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
                                        </div>
                                        <div class="bg-warning text-dark rounded-circle p-3">
                                            <i class="fas fa-hourglass-half fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Approved Today</h6>
                                            <h3 class="mb-0 text-success">{{ $stats['approved_today'] }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Rejected Today</h6>
                                            <h3 class="mb-0 text-danger">{{ $stats['rejected_today'] }}</h3>
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

                <!-- Search & Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('farmers.pending') }}" class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by name, phone, or ID..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select name="region_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Regions</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        @if(request('search') || request('region_id'))
                            <div class="col-md-2">
                                <a href="{{ route('farmers.pending') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Bulk Actions -->
                @if($farmers->count() > 0)
                <div class="card-body border-bottom bg-light">
                    <form id="bulkApproveForm" method="POST" action="{{ route('farmers.bulk-approve') }}">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    <strong>Select All</strong>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-success" id="bulkApproveBtn" disabled>
                                <i class="fas fa-check-double me-1"></i> Approve Selected (<span id="selectedCount">0</span>)
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- Farmers Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">
                                        <i class="fas fa-check-square"></i>
                                    </th>
                                    <th>Farmer ID</th>
                                    <th>Full Name</th>
                                    <th>Phone Number</th>
                                    <th>Village/Location</th>
                                    <th>Registration Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($farmers as $farmer)
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                   class="form-check-input farmer-checkbox"
                                                   name="farmer_ids[]"
                                                   value="{{ $farmer->id }}"
                                                   form="bulkApproveForm">
                                        </td>
                                        <td>
                                            <strong class="text-primary">{{ $farmer->registration_number }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($farmer->first_name, 0, 1) }}{{ substr($farmer->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $farmer->first_name }} {{ $farmer->last_name }}</strong>
                                                    @if($farmer->group)
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-users fa-xs me-1"></i>
                                                            {{ $farmer->group->name }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="tel:{{ $farmer->phone }}" class="text-decoration-none">
                                                <i class="fas fa-phone fa-xs me-1"></i>
                                                {{ $farmer->phone }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($farmer->village)
                                                {{ $farmer->village->name }}
                                                <br>
                                                <small class="text-muted">{{ $farmer->district->name ?? '' }}</small>
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $farmer->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $farmer->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('farmers.show', $farmer) }}"
                                                   class="btn btn-outline-primary"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('farmers.approve', $farmer) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-success"
                                                            title="Approve"
                                                            onclick="return confirm('Approve this farmer?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <button type="button"
                                                        class="btn btn-danger"
                                                        title="Reject"
                                                        onclick="openRejectModal('{{ $farmer->id }}', '{{ $farmer->full_name }}')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                                <h5>No pending farmers</h5>
                                                <p>All farmer registrations have been processed</p>
                                                <a href="{{ route('farmers.index') }}" class="btn btn-primary">
                                                    <i class="fas fa-users me-1"></i> View All Farmers
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($farmers->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $farmers->firstItem() }} to {{ $farmers->lastItem() }} of {{ $farmers->total() }} pending farmers
                                </div>
                                <div>
                                    {{ $farmers->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i> Reject Farmer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to reject farmer <strong id="rejectFarmerName"></strong>?</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control"
                                  id="rejection_reason"
                                  name="rejection_reason"
                                  rows="3"
                                  required
                                  placeholder="Please provide a reason for rejection..."></textarea>
                        <div class="form-text">This reason will be recorded for reference.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Reject Farmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal(farmerId, farmerName) {
        document.getElementById('rejectFarmerName').textContent = farmerName;
        document.getElementById('rejectForm').action = '/farmers/' + farmerId + '/reject';
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }

    // Select All functionality
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.farmer-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    // Update selected count
    document.querySelectorAll('.farmer-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    function updateSelectedCount() {
        const count = document.querySelectorAll('.farmer-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('bulkApproveBtn').disabled = count === 0;
    }
</script>
@endpush
@endsection
