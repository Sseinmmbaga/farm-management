@extends('layouts.base')

@section('title', $farmerGroup->name . ' - Members')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    {{ $farmerGroup->name }} - Members
                    <span class="badge bg-{{ $farmerGroup->group_type === 'simba' ? 'warning' : 'primary' }} ms-2">
                        {{ $farmerGroup->group_type_label }}
                    </span>
                </h4>
                <a href="{{ route('farmer-groups.show', $farmerGroup) }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-1"></i> Back to Group
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            @if($farmers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Farmer ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Village</th>
                                <th>Farms</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($farmers as $farmer)
                                <tr>
                                    <td><strong>{{ $farmer->registration_number }}</strong></td>
                                    <td>{{ $farmer->first_name }} {{ $farmer->last_name }}</td>
                                    <td><a href="tel:{{ $farmer->phone }}">{{ $farmer->phone }}</a></td>
                                    <td>{{ $farmer->village->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-primary">{{ $farmer->farms_count }}</span></td>
                                    <td>
                                        <span class="badge bg-{{ $farmer->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($farmer->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($farmers->hasPages())
                    <div class="card-footer">
                        {{ $farmers->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h5>No members in this group</h5>
                    <a href="{{ route('farmers.create') }}?group_id={{ $farmerGroup->id }}" class="btn btn-primary mt-2">
                        <i class="fas fa-user-plus me-1"></i> Add Farmer to Group
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
