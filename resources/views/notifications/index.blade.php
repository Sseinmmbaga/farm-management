@extends('layouts.admin')

@section('title', 'Notification Center')

@section('content')
<div class="container-fluid">
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Notification Center</h4>
                <small class="text-muted">Manage your notifications and alerts</small>
            </div>
            <div class="d-flex gap-2">
                @if($counts['unread'] > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </button>
                </form>
                @endif
                @if($counts['read'] > 0)
                <form action="{{ route('notifications.delete-all-read') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete all read notifications?')">
                        <i class="fas fa-trash"></i> Clear Read
                    </button>
                </form>
                @endif
                <a href="{{ route('notifications.preferences') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-cog"></i> Preferences
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Filters</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('notifications.index', ['filter' => 'all', 'category' => $category]) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $filter === 'all' ? 'active' : '' }}">
                            <span><i class="fas fa-inbox me-2"></i> All</span>
                            <span class="badge bg-secondary rounded-pill">{{ $counts['all'] }}</span>
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => 'unread', 'category' => $category]) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $filter === 'unread' ? 'active' : '' }}">
                            <span><i class="fas fa-envelope me-2"></i> Unread</span>
                            <span class="badge bg-primary rounded-pill">{{ $counts['unread'] }}</span>
                        </a>
                        <a href="{{ route('notifications.index', ['filter' => 'read', 'category' => $category]) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $filter === 'read' ? 'active' : '' }}">
                            <span><i class="fas fa-envelope-open me-2"></i> Read</span>
                            <span class="badge bg-secondary rounded-pill">{{ $counts['read'] }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Categories</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('notifications.index', ['filter' => $filter]) }}"
                           class="list-group-item list-group-item-action {{ !$category ? 'active' : '' }}">
                            <i class="fas fa-layer-group me-2"></i> All Categories
                        </a>
                        @foreach($categories as $key => $label)
                        <a href="{{ route('notifications.index', ['filter' => $filter, 'category' => $key]) }}"
                           class="list-group-item list-group-item-action {{ $category === $key ? 'active' : '' }}">
                            <i class="fas {{ match($key) {
                                'certification' => 'fa-certificate',
                                'document' => 'fa-file-alt',
                                'training' => 'fa-chalkboard-teacher',
                                'service_request' => 'fa-headset',
                                'system' => 'fa-bullhorn',
                                default => 'fa-bell'
                            } }} me-2"></i> {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body p-0">
                    @if($notifications->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No notifications</h5>
                        <p class="text-muted mb-0">You're all caught up!</p>
                    </div>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $isUnread = is_null($notification->read_at);
                        @endphp
                        <div class="list-group-item {{ $isUnread ? 'bg-light' : '' }}">
                            <div class="d-flex">
                                <div class="notification-icon me-3">
                                    <div class="avatar-circle bg-{{ $data['color'] ?? 'primary' }} text-white">
                                        <i class="fas {{ $data['icon'] ?? 'fa-bell' }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 {{ $isUnread ? 'fw-bold' : '' }}">
                                                {{ $data['title'] ?? 'Notification' }}
                                                @if($isUnread)
                                                <span class="badge bg-primary ms-2">New</span>
                                                @endif
                                            </h6>
                                            <p class="mb-1 text-muted">{{ $data['message'] ?? '' }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                                @if(isset($data['category']))
                                                <span class="ms-2">
                                                    <i class="fas fa-tag me-1"></i>{{ ucfirst($data['category']) }}
                                                </span>
                                                @endif
                                            </small>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if($isUnread)
                                                <li>
                                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-check me-2"></i> Mark as Read
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                                @if(isset($data['action_url']))
                                                <li>
                                                    <a href="{{ $data['action_url'] }}" class="dropdown-item">
                                                        <i class="fas fa-external-link-alt me-2"></i> View Details
                                                    </a>
                                                </li>
                                                @endif
                                                <li>
                                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    @if(isset($data['action_url']))
                                    <a href="{{ $data['action_url'] }}" class="btn btn-sm btn-outline-primary mt-2">
                                        View Details <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @if($notifications->hasPages())
                <div class="card-footer">
                    {{ $notifications->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.notification-icon {
    flex-shrink: 0;
}

.list-group-item.active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}
</style>
@endsection
