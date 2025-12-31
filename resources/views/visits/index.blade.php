@extends('layouts.base')

@section('title', 'Farm Visits')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i> Farm Visits
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('visits.calendar') }}" class="btn btn-info">
                                <i class="fas fa-calendar me-1"></i> Calendar View
                            </a>
                            <a href="{{ route('visits.export') }}" class="btn btn-success">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </a>
                            <a href="{{ route('visits.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-1"></i> Schedule Visit
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('visits.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by visit number, notes..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <select name="farm_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Farms</option>
                                @foreach($farms as $farm)
                                    <option value="{{ $farm->id }}" {{ request('farm_id') == $farm->id ? 'selected' : '' }}>
                                        {{ $farm->code }} - {{ $farm->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="farmer_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Farmers</option>
                                @foreach($farmers as $farmer)
                                    <option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                        {{ $farmer->registration_number }} - {{ $farmer->first_name }} {{ $farmer->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="purpose" class="form-select" onchange="this.form.submit()">
                                <option value="">All Purposes</option>
                                <option value="inspection" {{ request('purpose') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                <option value="training" {{ request('purpose') == 'training' ? 'selected' : '' }}>Training</option>
                                <option value="support" {{ request('purpose') == 'support' ? 'selected' : '' }}>Support</option>
                                <option value="monitoring" {{ request('purpose') == 'monitoring' ? 'selected' : '' }}>Monitoring</option>
                                <option value="other" {{ request('purpose') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        @if(request('search') || request('farm_id') || request('farmer_id') || request('status') || request('purpose'))
                            <div class="col-md-1">
                                <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Visits Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Visit Number</th>
                                    <th>Farm</th>
                                    <th>Farmer</th>
                                    <th>Supervisor</th>
                                    <th>Scheduled Date</th>
                                    <th>Status</th>
                                    <th>Purpose</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $visit->visit_number }}</strong>
                                        </td>
                                        <td>
                                            @if($visit->farm)
                                                <a href="{{ route('farms.show', $visit->farm) }}">{{ $visit->farm->code }}</a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($visit->farmer)
                                                <a href="{{ route('farmers.show', $visit->farmer) }}">{{ $visit->farmer->full_name }}</a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($visit->supervisor)
                                                {{ $visit->supervisor->name }}
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $visit->scheduled_date->format('Y-m-d H:i') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $visit->status_color }}">
                                                <i class="fas fa-circle me-1"></i> {{ $visit->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $visit->purpose_label }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('visits.show', $visit) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('visits.edit', $visit) }}" 
                                                   class="btn btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($visit->isScheduled())
                                                    <form action="{{ route('visits.start', $visit) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-info btn-sm" title="Start Visit">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($visit->status === 'in_progress')
                                                    <form action="{{ route('visits.complete', $visit) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success btn-sm" title="Mark as Completed">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(in_array($visit->status, ['scheduled', 'in_progress']))
                                                    <form action="{{ route('visits.cancel', $visit) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Cancel Visit">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                                <h5>No farm visits found</h5>
                                                <p>Start by scheduling a new farm visit</p>
                                                <a href="{{ route('visits.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> Schedule Visit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($visits->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $visits->firstItem() }} to {{ $visits->lastItem() }} of {{ $visits->total() }} visits
                                </div>
                                <div>
                                    {{ $visits->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection