@extends('layouts.base')

@section('title', 'Farmer History - Existing Farm Records (Form 3)')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-history text-primary"></i> Farmer History - Existing Farm Records (Form 3)</h2>
        <a href="{{ route('farm-records.existing.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Record
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Farmers with Existing Farm Records</h5>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchFarmer" placeholder="Search farmer...">
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($farmers->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No farmer records found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="farmersTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Farmer Name</th>
                                <th>Farmer Code</th>
                                <th>Total Farms</th>
                                <th>Existing Records (Form 3)</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($farmers as $index => $farmer)
                                @php
                                    $existingRecordsCount = $farmer->farms->sum(function($farm) {
                                        return $farm->farmRecords->where('record_type', 'existing')->count();
                                    });
                                @endphp
                                <tr class="farmer-row">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $farmer->full_name }}</strong>
                                    </td>
                                    <td>{{ $farmer->farmer_code ?? 'N/A' }}</td>
                                    <td>{{ $farmer->farms->count() }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $existingRecordsCount }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('farmer-history.show', ['farmer' => $farmer->id, 'type' => 'existing']) }}"
                                           class="btn btn-sm btn-info" title="View History">
                                            <i class="fas fa-eye"></i> View History
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('searchFarmer').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        document.querySelectorAll('.farmer-row').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
</script>
@endpush
