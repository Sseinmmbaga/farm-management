@extends('layouts.base')

@section('title', 'Select Farmer')

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">Select Farmer</h4>
        <small class="text-muted">Choose a farmer to fill out a form for</small>
    </div>
    <a href="{{ route('farmer-forms.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Forms
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('farmer-forms.select-farmer') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search by name or registration number..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Search
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Registration #</th>
                        <th>Name</th>
                        <th>Village</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($farmers as $farmer)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $farmer->registration_number ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $farmer->first_name }} {{ $farmer->last_name }}</div>
                                @if($farmer->phone)
                                    <small class="text-muted">{{ $farmer->phone }}</small>
                                @endif
                            </td>
                            <td>{{ $farmer->village->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $farmer->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($farmer->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('farmer-forms.select', $farmer) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-arrow-right me-1"></i>Select
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                No farmers found. Try adjusting your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($farmers->hasPages())
        <div class="card-footer">
            {{ $farmers->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
