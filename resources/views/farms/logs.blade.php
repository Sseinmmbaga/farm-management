@extends('layouts.base')

@section('title', 'Farm Activity Logs - ' . $farm->display_name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-clipboard-list text-info"></i> Activity Logs</h2>
            <p class="text-muted mb-0">{{ $farm->display_name }} - {{ $farm->code }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('farms.show', $farm) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Farm
            </a>
            <a href="{{ route('farms.history', $farm) }}" class="btn btn-outline-primary">
                <i class="fas fa-history"></i> View History
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
                    <h6 class="text-muted">Status</h6>
                    <p class="mb-0">
                        <span class="badge bg-{{ 
                            $farm->status == 'active' ? 'success' : 
                            ($farm->status == 'inactive' ? 'warning' : 'danger') 
                        }}">
                            {{ ucfirst($farm->status) }}
                        </span>
                    </p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Certification</h6>
                    <p class="mb-0">
                        @if($farm->certification_status)
                            <span class="badge bg-info">
                                {{ ucfirst($farm->certification_status) }}
                            </span>
                        @else
                            <span class="text-muted">Not certified</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Last Activity</h6>
                    <p class="mb-0">
                        @if($logs->count() > 0)
                            {{ $logs->first()->created_at->diffForHumans() }}
                        @else
                            <span class="text-muted">No activity</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Logs -->
    <div class="card">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-stream"></i> Recent Activity</h5>
            <span class="badge bg-light text-dark">{{ $logs->total() }} logs</span>
        </div>
        <div class="card-body p-0">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Activity</th>
                                <th>User</th>
                                <th>IP Address</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td width="15%">
                                        <div>
                                            <strong>{{ $log->created_at->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                    <td width="30%">
                                        <div>
                                            <strong>{{ $log->activity_type }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($log->description, 100) }}</small>
                                        </div>
                                    </td>
                                    <td width="15%">
                                        @if($log->user)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $log->user->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $log->user->role ?? 'User' }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td width="10%">
                                        <code>{{ $log->ip_address ?? 'N/A' }}</code>
                                    </td>
                                    <td width="30%">
                                        @if($log->metadata)
                                            <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#metadata-{{ $log->id }}">
                                                <i class="fas fa-eye"></i> View Details
                                            </button>
                                            <div class="collapse mt-2" id="metadata-{{ $log->id }}">
                                                <div class="card card-body">
                                                    <pre class="mb-0" style="font-size: 0.8rem;">{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No additional data</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($logs->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} logs
                            </div>
                            <div>
                                {{ $logs->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5>No Activity Logs</h5>
                    <p class="text-muted">No activity logs found for this farm.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Activity Summary -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Activity Types</h6>
                </div>
                <div class="card-body">
                    @php
                        $activityTypes = $logs->groupBy('activity_type')->map->count();
                    @endphp
                    @if($activityTypes->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($activityTypes as $type => $count)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $type }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No activity data</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Top Users</h6>
                </div>
                <div class="card-body">
                    @php
                        $topUsers = $logs->whereNotNull('user_id')->groupBy('user_id')->map->count();
                    @endphp
                    @if($topUsers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topUsers->take(5) as $userId => $count)
                                @php
                                    $user = $logs->firstWhere('user_id', $userId)->user;
                                @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ substr($user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <span>{{ $user->name ?? 'Unknown' }}</span>
                                    </div>
                                    <span class="badge bg-info rounded-pill">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No user data</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-calendar me-2"></i>Recent Days</h6>
                </div>
                <div class="card-body">
                    @php
                        $recentDays = $logs->groupBy(function($log) {
                            return $log->created_at->format('Y-m-d');
                        })->map->count()->take(5);
                    @endphp
                    @if($recentDays->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentDays as $date => $count)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ \Carbon\Carbon::parse($date)->format('M d') }}</span>
                                    <span class="badge bg-success rounded-pill">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No recent activity</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-xs {
        width: 24px;
        height: 24px;
        font-size: 0.7rem;
    }
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
    }
</style>
@endsection