@extends('layouts.base')

@section('title', 'Farm Visit Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i> Farm Visit: {{ $visit->visit_number }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('visits.edit', $visit) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            @if($visit->isScheduled())
                                <form action="{{ route('visits.start', $visit) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-play me-1"></i> Start Visit
                                    </button>
                                </form>
                            @endif
                            @if($visit->status === 'in_progress')
                                <form action="{{ route('visits.complete', $visit) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check me-1"></i> Mark as Completed
                                    </button>
                                </form>
                            @endif
                            @if(in_array($visit->status, ['scheduled', 'in_progress']))
                                <form action="{{ route('visits.cancel', $visit) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times me-1"></i> Cancel Visit
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('visits.destroy', $visit) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this visit?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-light">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Left Column: Basic Info -->
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i> Basic Information</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%">Visit Number:</th>
                                    <td><strong class="text-primary">{{ $visit->visit_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $visit->status_color }}">
                                            <i class="fas fa-circle me-1"></i> {{ $visit->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Purpose:</th>
                                    <td>
                                        <span class="badge bg-secondary">{{ $visit->purpose_label }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Scheduled Date:</th>
                                    <td>{{ $visit->scheduled_date->format('F j, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Actual Date:</th>
                                    <td>
                                        @if($visit->actual_date)
                                            {{ $visit->actual_date->format('F j, Y H:i') }}
                                        @else
                                            <span class="text-muted">Not yet visited</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $visit->created_at->format('F j, Y H:i') }} by {{ $visit->creator?->name ?? 'System' }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $visit->updated_at->format('F j, Y H:i') }} by {{ $visit->updater?->name ?? 'System' }}</td>
                                </tr>
                            </table>

                            <h5 class="mt-4 mb-3"><i class="fas fa-users me-2"></i> People</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%">Farm:</th>
                                    <td>
                                        @if($visit->farm)
                                            <a href="{{ route('farms.show', $visit->farm) }}">{{ $visit->farm->code }} - {{ $visit->farm->name }}</a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Field:</th>
                                    <td>
                                        @if($visit->field)
                                            <a href="{{ route('fields.show', $visit->field) }}">{{ $visit->field->name }}</a>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Farmer:</th>
                                    <td>
                                        @if($visit->farmer)
                                            <a href="{{ route('farmers.show', $visit->farmer) }}">{{ $visit->farmer->full_name }}</a> ({{ $visit->farmer->registration_number }})
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Supervisor:</th>
                                    <td>
                                        @if($visit->supervisor)
                                            {{ $visit->supervisor->name }} ({{ $visit->supervisor->email }})
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Right Column: Location & Notes -->
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i> Location</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%">Region:</th>
                                    <td>{{ $visit->region?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>District:</th>
                                    <td>{{ $visit->district?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Village:</th>
                                    <td>{{ $visit->village?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Coordinates:</th>
                                    <td>
                                        @if($visit->latitude && $visit->longitude)
                                            {{ $visit->latitude }}, {{ $visit->longitude }}
                                            <a href="https://www.google.com/maps?q={{ $visit->latitude }},{{ $visit->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-external-link-alt"></i> View on Map
                                            </a>
                                        @else
                                            <span class="text-muted">Not recorded</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <h5 class="mt-4 mb-3"><i class="fas fa-sticky-note me-2"></i> Notes & Report</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Notes:</h6>
                                    <p>{{ $visit->notes ?? 'No notes provided.' }}</p>

                                    <h6 class="mt-3">Report:</h6>
                                    <p>{{ $visit->report ?? 'No report submitted.' }}</p>
                                </div>
                            </div>

                            <h5 class="mt-4 mb-3"><i class="fas fa-camera me-2"></i> Photos</h5>
                            @if($visit->has_photos)
                                <div class="row g-2">
                                    @foreach($visit->photos as $photo)
                                        <div class="col-4">
                                            <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $photo) }}" class="img-thumbnail" alt="Photo">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No photos uploaded.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('visits.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection