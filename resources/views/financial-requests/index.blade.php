@extends('layouts.base')

@section('title', 'Financial Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i> Financial Requests
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('financial-requests.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> New Financial Request
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('financial-requests.index') }}" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                placeholder="Request #, user, purpose..." 
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="disbursed" {{ request('status') == 'disbursed' ? 'selected' : '' }}>Disbursed</option>
                                <option value="repaid" {{ request('status') == 'repaid' ? 'selected' : '' }}>Repaid</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Request Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="loan" {{ request('type') == 'loan' ? 'selected' : '' }}>Loan</option>
                                <option value="salary_advance" {{ request('type') == 'salary_advance' ? 'selected' : '' }}>Salary Advance</option>
                                <option value="imprest" {{ request('type') == 'imprest' ? 'selected' : '' }}>Imprest</option>
                                <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="min_amount" class="form-label">Min Amount</label>
                            <input type="number" class="form-control" id="min_amount" name="min_amount" 
                                value="{{ request('min_amount') }}" step="0.01">
                        </div>
                        <div class="col-md-2">
                            <label for="max_amount" class="form-label">Max Amount</label>
                            <input type="number" class="form-control" id="max_amount" name="max_amount" 
                                value="{{ request('max_amount') }}" step="0.01">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('financial-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Clear
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Request #</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($financialRequests as $request)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $request->request_number }}</strong>
                                        </td>
                                        <td>
                                            {{ $request->user->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $request->type_display }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $request->formatted_amount }}</strong>
                                        </td>
                                        <td>
                                            {{ Str::limit($request->purpose, 30) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->status_color }}">
                                                {{ $request->status_display }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $request->created_at->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('financial-requests.show', $request) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($request->is_pending)
                                                    <a href="{{ route('financial-requests.edit', $request) }}" 
                                                       class="btn btn-outline-warning" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if($request->is_pending)
                                                    <button type="button" 
                                                            class="btn btn-outline-danger" 
                                                            title="Delete"
                                                            onclick="confirmDelete('{{ route('financial-requests.destroy', $request) }}', '{{ $request->request_number }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-money-bill-wave fa-3x mb-3"></i>
                                                <h5>No financial requests found</h5>
                                                <p>Start by creating your first financial request</p>
                                                <a href="{{ route('financial-requests.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> New Financial Request
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($financialRequests->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $financialRequests->firstItem() }} to {{ $financialRequests->lastItem() }} of {{ $financialRequests->total() }} requests
                                </div>
                                <div>
                                    {{ $financialRequests->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete financial request <strong id="requestNumber"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, requestNumber) {
        document.getElementById('requestNumber').textContent = requestNumber;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endpush
@endsection