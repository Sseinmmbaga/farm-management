@extends('layouts.base')

@section('title', 'Farmer Dashboard')

@push('styles')
<style>
    .stats-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stats-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .card-primary { border-left: 4px solid #3498db; }
    .card-success { border-left: 4px solid #27ae60; }
    .card-warning { border-left: 4px solid #f39c12; }
    .card-info { border-left: 4px solid #17a2b8; }

    .recent-activity {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .activity-item {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    .activity-item:last-child {
        border-bottom: none;
    }
    .activity-time {
        color: #6c757d;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <h1 class="h3 mb-0">Farmer Dashboard</h1>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-success" id="refreshBtn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success">
                    <i class="fas fa-tractor"></i>
                </div>
                <div class="stats-number">{{ $activeFarms ?? 3 }}</div>
                <div class="stats-label">Active Farms</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="stats-number">{{ $plantedAcres ?? 45 }}</div>
                <div class="stats-label">Planted Acres</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stats-number">{{ $pendingTasks ?? 7 }}</div>
                <div class="stats-label">Pending Tasks</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card card-info">
                <div class="stats-icon text-info">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stats-number">{{ $trainings ?? 2 }}</div>
                <div class="stats-label">Trainings Attended</div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="recent-activity">
                <h4>Recent Activity</h4>
                <div class="mt-3">
                    @forelse($recentActivity ?? [] as $activity)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }} me-2"></i>
                                <strong>{{ $activity['title'] }}</strong>
                                <p class="mb-0 text-muted">{{ $activity['description'] }}</p>
                            </div>
                            <div class="activity-time">{{ $activity['time']->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="recent-activity">
                <h4>Quick Actions</h4>
                <div class="mt-3">
                    <a href="{{ route('dashboard.farmer.my-farms') }}" class="btn btn-outline-success w-100 mb-2">
                        <i class="fas fa-tractor"></i> View My Farms
                    </a>
                    @if($farmer)
                    <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                    @endif
                    <a href="{{ route('dashboard.farmer.trainings') }}" class="btn btn-outline-info w-100 mb-2">
                        <i class="fas fa-chalkboard-teacher"></i> My Trainings
                    </a>
                    <a href="{{ route('dashboard.farmer.distributions') }}" class="btn btn-outline-warning w-100 mb-2">
                        <i class="fas fa-box"></i> My Distributions
                    </a>
                    <a href="{{ route('service-requests.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="fas fa-headset"></i> Service Requests
                    </a>
                </div>

                <div class="mt-4">
                    <h5>Upcoming Events</h5>
                    <div class="mt-2">
                        @forelse($upcomingEvents ?? [] as $event)
                        <div class="d-flex justify-content-between mb-2">
                            <span>
                                <i class="fas fa-{{ $event['type'] === 'visit' ? 'calendar-check' : ($event['type'] === 'training' ? 'chalkboard-teacher' : 'tasks') }} text-{{ $event['color'] }} me-1"></i>
                                {{ Str::limit($event['title'], 20) }}
                            </span>
                            <span class="text-{{ $event['color'] }}">{{ $event['date']->format('M d') }}</span>
                        </div>
                        @empty
                        <div class="text-center text-muted py-2">
                            <p class="mb-0">No upcoming events</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                    location.reload();
                }, 1000);
            });
        }
    });
</script>
@endpush
