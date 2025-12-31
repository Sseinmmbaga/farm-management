@extends('layouts.base')

@section('title', 'Stock Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i> Stock Requests
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock.requests.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> New Request
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Request #</th>
                                    <th>Farmer</th>
                                    <th>Requested By</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Items</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $request->request_number }}</strong>
                                        </td>
                                        <td>
                                            {{ $request->farmer->full_name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $request->requestedBy->name ?? 'Unknown' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->status_color }}">
                                                {{ $request->status_display }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->priority_color }}">
                                                {{ $request->priority_display }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-box me-1"></i>
                                                {{ $request->items_count ?? $request->items->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $request->created_at->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('stock.requests.show', $request) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($request->is_draft)
                                                    <a href="{{ route('stock.requests.edit', $request) }}" 
                                                       class="btn btn-outline-warning" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger" 
                                                            title="Delete"
                                                            onclick="confirmDelete('{{ route('stock.requests.destroy', $request) }}', '{{ $request->request_number }}')">
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
                                                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                                <h5>No stock requests found</h5>
                                                <p>Start by creating your first stock request</p>
                                                <a href="{{ route('stock.requests.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> New Request
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($requests->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} requests
                                </div>
                                <div>
                                    {{ $requests->links() }}
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
                <p>Are you sure you want to delete request <strong id="requestNumber"></strong>?</p>
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