@extends('layouts.base')

@section('title', 'Farm History - ' . $farm->display_name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-history text-primary"></i> Farm History</h2>
            <p class="text-muted mb-0">{{ $farm->display_name }} - {{ $farm->code }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('farms.show', $farm) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Farm
            </a>
            <a href="{{ route('farms.logs', $farm) }}" class="btn btn-outline-info">
                <i class="fas fa-clipboard-list"></i> View Logs
            </a>
        </div>
    </div>

    <!-- Farm Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-muted">Farm Code</h6>
                    <p class="mb-0"><strong>{{ $farm->code }}</strong></p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Farmer</h6>
                    <p class="mb-0">{{ $farm->farmer->full_name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Location</h6>
                    <p class="mb-0">{{ $farm->village->name ?? '' }}, {{ $farm->district->name ?? '' }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Total Area</h6>
                    <p class="mb-0">{{ $farm->total_area }} ha</p>
                </div>
            </div>
        </div>
    </div>

    <!-- History Timeline -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-timeline"></i> History Timeline</h5>
        </div>
        <div class="card-body">
            @if($histories->count() > 0)
                <div class="timeline">
                    @foreach($histories as $history)
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker bg-{{ 
                                $history->action == 'created' ? 'success' : 
                                ($history->action == 'updated' ? 'warning' : 
                                ($history->action == 'deleted' ? 'danger' : 'info')) 
                            }}"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            @if($history->action == 'created')
                                                <i class="fas fa-plus-circle text-success me-1"></i>
                                                Farm Created
                                            @elseif($history->action == 'updated')
                                                <i class="fas fa-edit text-warning me-1"></i>
                                                Farm Updated
                                            @elseif($history->action == 'deleted')
                                                <i class="fas fa-trash text-danger me-1"></i>
                                                Farm Deleted
                                            @elseif($history->action == 'boundary_updated')
                                                <i class="fas fa-draw-polygon text-info me-1"></i>
                                                Boundaries Updated
                                            @else
                                                <i class="fas fa-history text-secondary me-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $history->action)) }}
                                            @endif
                                        </h6>
                                        <p class="mb-1">{{ $history->description }}</p>
                                        
                                        @if(!empty($history->changes))
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#changes-{{ $history->id }}">
                                                    <i class="fas fa-code-branch me-1"></i> View Changes
                                                </button>
                                                <div class="collapse mt-2" id="changes-{{ $history->id }}">
                                                    <div class="card card-body">
                                                        <table class="table table-sm table-borderless">
                                                            <thead>
                                                                <tr>
                                                                    <th>Field</th>
                                                                    <th>Old Value</th>
                                                                    <th>New Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($history->changes as $field => $change)
                                                                    <tr>
                                                                        <td><code>{{ $field }}</code></td>
                                                                        <td>
                                                                            @if(is_array($change['old']))
                                                                                <pre class="mb-0">{{ json_encode($change['old'], JSON_PRETTY_PRINT) }}</pre>
                                                                            @else
                                                                                {{ $change['old'] ?? 'null' }}
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if(is_array($change['new']))
                                                                                <pre class="mb-0">{{ json_encode($change['new'], JSON_PRETTY_PRINT) }}</pre>
                                                                            @else
                                                                                {{ $change['new'] ?? 'null' }}
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $history->user->name ?? 'System' }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $history->created_at->format('M d, Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($histories->hasPages())
                    <div class="mt-4">
                        {{ $histories->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5>No History Found</h5>
                    <p class="text-muted">No history records available for this farm.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 40px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    .timeline-marker {
        position: absolute;
        left: -40px;
        top: 5px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 3px var(--bs-primary);
    }
    .timeline-content {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid var(--bs-primary);
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: -31px;
        top: 25px;
        bottom: -20px;
        width: 2px;
        background-color: #dee2e6;
    }
</style>
@endsection