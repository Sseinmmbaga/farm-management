@extends('layouts.base')

@section('title', 'Regions')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-map me-2"></i> Regions Management
                </h4>
                <div>
                    <a href="{{ route('catchment-areas.index') }}" class="btn btn-light btn-sm me-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    @if(!auth()->user()->hasViewOnlyAccess())
                    <a href="{{ route('regions.create') }}" class="btn btn-light">
                        <i class="fas fa-plus me-1"></i> Add Region
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('regions.index') }}" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Search by name or code..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success m-3">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger m-3">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th class="text-center">Districts</th>
                            <th class="text-center">Villages</th>
                            <th class="text-center">Farmers</th>
                            <th class="text-center">Farms</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($regions as $region)
                            <tr>
                                <td>
                                    <strong>{{ $region->name }}</strong>
                                    @if($region->name_sw)
                                        <br><small class="text-muted">{{ $region->name_sw }}</small>
                                    @endif
                                </td>
                                <td><code>{{ $region->code }}</code></td>
                                <td class="text-center"><span class="badge bg-success">{{ $region->districts_count }}</span></td>
                                <td class="text-center"><span class="badge bg-warning">{{ $region->villages_count }}</span></td>
                                <td class="text-center"><span class="badge bg-info">{{ $region->farmers_count }}</span></td>
                                <td class="text-center"><span class="badge bg-primary">{{ $region->farms_count }}</span></td>
                                <td class="text-center">
                                    @if($region->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('regions.show', $region) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!auth()->user()->hasViewOnlyAccess())
                                        <a href="{{ route('regions.edit', $region) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="confirmDelete('{{ route('regions.destroy', $region) }}', '{{ $region->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-map fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No regions found</p>
                                    @if(!auth()->user()->hasViewOnlyAccess())
                                    <a href="{{ route('regions.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Add First Region
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($regions->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $regions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="itemName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(url, name) {
        document.getElementById('itemName').textContent = name;
        document.getElementById('deleteForm').action = url;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endpush
@endsection
