@extends('layouts.base')

@section('title', 'Farmer History - ' . $farmer->full_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-history text-info"></i> Farmer History: {{ $farmer->full_name }}
        </h2>
        <div>
            @if($recordType === 'new')
                <a href="{{ route('farm-records.new.history') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Form 2 History
                </a>
            @elseif($recordType === 'existing')
                <a href="{{ route('farm-records.existing.history') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Form 3 History
                </a>
            @else
                <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Farmer
                </a>
            @endif
        </div>
    </div>

    <!-- Farmer Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Farmer Code:</strong> {{ $farmer->farmer_code ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Phone:</strong> {{ $farmer->phone ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Total Farms:</strong> {{ $farmer->farms->count() }}
                </div>
                <div class="col-md-3">
                    <strong>Total Records:</strong> {{ $records->count() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Filter by Type -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <a href="{{ route('farmer-history.show', ['farmer' => $farmer->id, 'type' => 'all']) }}"
                   class="btn {{ $recordType === 'all' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    All Records
                </a>
                <a href="{{ route('farmer-history.show', ['farmer' => $farmer->id, 'type' => 'new']) }}"
                   class="btn {{ $recordType === 'new' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-file-alt"></i> Form 2 (New)
                </a>
                <a href="{{ route('farmer-history.show', ['farmer' => $farmer->id, 'type' => 'existing']) }}"
                   class="btn {{ $recordType === 'existing' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-file-alt"></i> Form 3 (Existing)
                </a>
            </div>
        </div>
    </div>

    <!-- Records Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Farm Records History</h5>
        </div>
        <div class="card-body">
            @if($records->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No records found for this farmer.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Record Type</th>
                                <th>Farm</th>
                                <th>Season</th>
                                <th>Certification</th>
                                <th>Area (ha)</th>
                                <th>Created</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $index => $record)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($record->record_type === 'new')
                                            <span class="badge bg-success">Form 2 (New)</span>
                                        @else
                                            <span class="badge bg-primary">Form 3 (Existing)</span>
                                        @endif
                                    </td>
                                    <td>{{ $record->farm->code ?? 'N/A' }} - {{ $record->farm->display_name ?? 'N/A' }}</td>
                                    <td>{{ $record->season->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge
                                            @if($record->certification_status === 'O') bg-success
                                            @elseif($record->certification_status === 'C2') bg-info
                                            @elseif($record->certification_status === 'C1') bg-warning
                                            @else bg-secondary
                                            @endif">
                                            {{ $record->certification_status }}
                                        </span>
                                    </td>
                                    <td>{{ $record->area_size ?? 'N/A' }}</td>
                                    <td>{{ $record->created_at->format('M d, Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('farm-records.show', $record) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('farm-records.edit', $record) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
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
