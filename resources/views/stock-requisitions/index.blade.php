@extends('layouts.base')

@section('title', 'Stock Requisitions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i> Stock Requisitions
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock-requisitions.create') }}" class="btn btn-light">
                                <i class="fas fa-plus me-1"></i> New Requisition
                            </a>
                            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('stock-requisitions.index', ['status' => 'pending']) }}">Pending</a></li>
                                <li><a class="dropdown-item" href="{{ route('stock-requisitions.index', ['status' => 'approved']) }}">Approved</a></li>
                                <li><a class="dropdown-item" href="{{ route('stock-requisitions.index', ['status' => 'rejected']) }}">Rejected</a></li>
                                <li><a class="dropdown-item" href="{{ route('stock-requisitions.index', ['status' => 'issued']) }}">Issued</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('stock-requisitions.index') }}">All</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($requisitions->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>No stock requisitions found.</h5>
                            <p>Create your first stock requisition to get started.</p>
                            <a href="{{ route('stock-requisitions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Create Requisition
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Requisition #</th>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Requested Date</th>
                                        <th>Required Date</th>
                                        <th>Total Estimated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requisitions as $requisition)
                                        <tr>
                                            <td>
                                                <strong>{{ $requisition->requisition_number }}</strong>
                                                <small class="d-block text-muted">{{ $requisition->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                {{ $requisition->user->name ?? 'N/A' }}
                                                @if($requisition->user)
                                                    <small class="d-block text-muted">{{ $requisition->user->email }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $requisition->department->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $requisition->status_color }}">
                                                    {{ $requisition->status_display }}
                                                </span>
                                                @if($requisition->approved_by)
                                                    <small class="d-block text-muted">Approved by {{ $requisition->approvedBy->name ?? 'N/A' }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $requisition->requested_date->format('d/m/Y') }}</td>
                                            <td>{{ $requisition->required_date ? $requisition->required_date->format('d/m/Y') : 'Not specified' }}</td>
                                            <td>
                                                @if($requisition->estimated_total)
                                                    <strong class="text-success">{{ number_format($requisition->estimated_total, 2) }}</strong>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('stock-requisitions.show', $requisition) }}" class="btn btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($requisition->isEditable())
                                                        <a href="{{ route('stock-requisitions.edit', $requisition) }}" class="btn btn-outline-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @can('delete', $requisition)
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="confirmDelete('{{ route('stock-requisitions.destroy', $requisition) }}', '{{ $requisition->requisition_number }}')" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $requisitions->firstItem() }} to {{ $requisitions->lastItem() }} of {{ $requisitions->total() }} requisitions
                            </div>
                            <div>
                                {{ $requisitions->links() }}
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
                <p>Are you sure you want to delete stock requisition <strong id="requisitionNumber"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Requisition</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(url, requisitionNumber) {
        document.getElementById('requisitionNumber').textContent = requisitionNumber;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endpush