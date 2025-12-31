@extends('layouts.base')

@section('title', 'Subvillages')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-map-pin me-2"></i> Subvillages Management
                </h4>
                <div>
                    <a href="{{ route('catchment-areas.index') }}" class="btn btn-light btn-sm me-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    @if(!auth()->user()->hasViewOnlyAccess())
                    <a href="{{ route('subvillages.create') }}" class="btn btn-light">
                        <i class="fas fa-plus me-1"></i> Add Subvillage
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('subvillages.index') }}" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Search by name..." value="{{ request('search') }}">
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
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info w-100">
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
                            <th>Subvillage Name</th>
                            <th>Village</th>
                            <th>District</th>
                            <th>Region</th>
                            <th class="text-center">Farmers</th>
                            <th class="text-center">Farms</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subvillages as $subvillage)
                            <tr>
                                <td>
                                    <strong>{{ $subvillage->name }}</strong>
                                    @if($subvillage->name_sw)
                                        <br><small class="text-muted">{{ $subvillage->name_sw }}</small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('villages.show', $subvillage->village) }}">{{ $subvillage->village->name }}</a>
                                </td>
                                <td>
                                    <a href="{{ route('districts.show', $subvillage->district) }}">{{ $subvillage->district->name }}</a>
                                </td>
                                <td>
                                    <a href="{{ route('regions.show', $subvillage->region) }}">{{ $subvillage->region->name }}</a>
                                </td>
                                <td class="text-center"><span class="badge bg-primary">{{ $subvillage->farmers_count }}</span></td>
                                <td class="text-center"><span class="badge bg-success">{{ $subvillage->farms_count }}</span></td>
                                <td class="text-center">
                                    @if($subvillage->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('subvillages.show', $subvillage) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!auth()->user()->hasViewOnlyAccess())
                                        <a href="{{ route('subvillages.edit', $subvillage) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="confirmDelete('{{ route('subvillages.destroy', $subvillage) }}', '{{ $subvillage->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-map-pin fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No subvillages found</p>
                                    @if(!auth()->user()->hasViewOnlyAccess())
                                    <a href="{{ route('subvillages.create') }}" class="btn btn-info">
                                        <i class="fas fa-plus me-1"></i> Add First Subvillage
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subvillages->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $subvillages->links() }}
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
