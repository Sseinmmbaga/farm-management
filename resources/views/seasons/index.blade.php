@extends('layouts.base')

@section('title', 'Season Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar-alt text-primary"></i> Season Management</h2>
        <a href="{{ route('seasons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Season
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> All Seasons</h5>
        </div>
        <div class="card-body">
            @if($seasons->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No seasons found. Create your first season.</p>
                    <a href="{{ route('seasons.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Season
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Season Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Current</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seasons as $index => $season)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $season->name }}</strong>
                                        @if($season->is_ongoing)
                                            <span class="badge bg-info ms-1">Ongoing</span>
                                        @endif
                                    </td>
                                    <td>{{ $season->start_date->format('M d, Y') }}</td>
                                    <td>{{ $season->end_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($season->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($season->is_current)
                                            <span class="badge bg-primary"><i class="fas fa-star"></i> Current</span>
                                        @else
                                            <form action="{{ route('seasons.set-current', $season) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Set as Current">
                                                    <i class="fas fa-check"></i> Set Current
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('seasons.show', $season) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('seasons.edit', $season) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('seasons.destroy', $season) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this season?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
