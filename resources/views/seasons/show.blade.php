@extends('layouts.base')

@section('title', 'Season Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar-alt text-info"></i> Season: {{ $season->name }}</h2>
        <div>
            <a href="{{ route('seasons.edit', $season) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('seasons.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Seasons
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Season Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Season Name:</th>
                            <td>{{ $season->name }}</td>
                        </tr>
                        <tr>
                            <th>Start Date:</th>
                            <td>{{ $season->start_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>End Date:</th>
                            <td>{{ $season->end_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Duration:</th>
                            <td>{{ $season->duration_days }} days</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($season->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                                @if($season->is_ongoing)
                                    <span class="badge bg-info">Ongoing</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Current Season:</th>
                            <td>
                                @if($season->is_current)
                                    <span class="badge bg-primary"><i class="fas fa-star"></i> Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Notes:</th>
                            <td>{{ $season->notes ?: 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Season Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h3 class="text-primary mb-0">{{ $season->farmRecords->count() }}</h3>
                                <small class="text-muted">Farm Records</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h3 class="text-success mb-0">{{ $season->farmSeasons->count() }}</h3>
                                <small class="text-muted">Farm Seasons</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
