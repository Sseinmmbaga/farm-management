@extends('layouts.base')

@section('title', 'User Profile - ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- User Profile Card --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-{{ $user->is_active ? 'primary' : 'secondary' }} text-white text-center py-4">
                    <div class="avatar-lg bg-white text-{{ $user->is_active ? 'primary' : 'secondary' }} rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <span class="badge bg-{{ $user->isAdmin() ? 'danger' : ($user->isSupervisor() ? 'warning' : 'light text-dark') }}">
                        {{ $user->role->label() }}
                    </span>
                    @if($user->id === auth()->id())
                        <span class="badge bg-info ms-1">You</span>
                    @endif
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-envelope text-muted me-2"></i> Email</span>
                            <span><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-phone text-muted me-2"></i> Phone</span>
                            <span>
                                @if($user->phone)
                                    <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-toggle-on text-muted me-2"></i> Status</span>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-sign-in-alt text-muted me-2"></i> Last Login</span>
                            <span>
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->diffForHumans() }}
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-calendar-plus text-muted me-2"></i> Joined</span>
                            <span>{{ $user->created_at->format('M d, Y') }}</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="d-grid gap-2">
                        @can('update', $user)
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit User
                        </a>
                        @endcan

                        @can('toggleStatus', $user)
                        <form method="POST" action="{{ route('users.toggle-status', $user) }}">
                            @csrf
                            <button type="submit" class="btn btn-{{ $user->is_active ? 'outline-danger' : 'outline-success' }} w-100">
                                <i class="fas fa-{{ $user->is_active ? 'user-slash' : 'user-check' }} me-1"></i>
                                {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
                            </button>
                        </form>
                        @endcan

                        @can('resetPassword', $user)
                        <form method="POST" action="{{ route('users.reset-password', $user) }}"
                              onsubmit="return confirm('Are you sure you want to reset this user\'s password?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-info w-100">
                                <i class="fas fa-key me-1"></i> Reset Password
                            </button>
                        </form>
                        @endcan

                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Timeline --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i> Recent Activity
                        </h5>
                        <a href="{{ route('users.activity', $user) }}" class="btn btn-light btn-sm">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($activities->count() > 0)
                        <div class="timeline">
                            @foreach($activities as $activity)
                                <div class="timeline-item d-flex mb-3">
                                    <div class="timeline-icon bg-{{ $activity->action_color }} text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; min-width: 40px;">
                                        <i class="fas {{ $activity->action_icon }}"></i>
                                    </div>
                                    <div class="timeline-content flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $activity->action_label }}</strong>
                                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="text-muted small">
                                            @if($activity->performedBy)
                                                By {{ $activity->performedBy->name }}
                                                @if($activity->performedBy->id === $user->id)
                                                    (self)
                                                @endif
                                            @else
                                                System
                                            @endif
                                            @if($activity->ip_address)
                                                &bull; IP: {{ $activity->ip_address }}
                                            @endif
                                        </div>
                                        @if($activity->details)
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    @foreach($activity->details as $key => $value)
                                                        <span class="badge bg-light text-dark">{{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}</span>
                                                    @endforeach
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-history fa-3x mb-3"></i>
                            <p>No activity recorded yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i> Account Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-primary mb-0">{{ $user->activities()->count() }}</h3>
                                <small class="text-muted">Total Activities</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-success mb-0">{{ $user->activities()->where('action', 'login')->count() }}</h3>
                                <small class="text-muted">Total Logins</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div>
                                <h3 class="text-info mb-0">{{ $user->created_at->diffInDays(now()) }}</h3>
                                <small class="text-muted">Days Since Joined</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-item:not(:last-child) .timeline-content {
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
}
</style>
@endsection
