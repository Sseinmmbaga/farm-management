{{-- Extension Officer Sidebar Navigation --}}
<a href="{{ route('dashboard.extension') }}" class="nav-link {{ request()->routeIs('dashboard.extension') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

{{-- Farmer Management Section --}}
<div class="nav-section-header">
    <span>Farmer Management</span>
</div>

<a href="{{ route('farmers.index') }}" class="nav-link {{ request()->routeIs('farmers.index') ? 'active' : '' }}">
    <i class="fas fa-users"></i> All Farmers
</a>

<a href="{{ route('farmers.create') }}" class="nav-link {{ request()->routeIs('farmers.create') ? 'active' : '' }}">
    <i class="fas fa-user-plus"></i> Add New Farmer
</a>

<a href="{{ route('farmer-groups.index') }}" class="nav-link {{ request()->routeIs('farmer-groups.*') ? 'active' : '' }}">
    <i class="fas fa-layer-group"></i> Farmer Groups
</a>

{{-- Farm Management Section --}}
<div class="nav-section-header">
    <span>Farm Management</span>
</div>

<a href="{{ route('farms.index') }}" class="nav-link {{ request()->routeIs('farms.index') ? 'active' : '' }}">
    <i class="fas fa-tractor"></i> All Farms
</a>

<a href="{{ route('farms.create') }}" class="nav-link {{ request()->routeIs('farms.create') ? 'active' : '' }}">
    <i class="fas fa-plus-circle"></i> New Farm
</a>

<a href="{{ route('farms.map.all') }}" class="nav-link {{ request()->routeIs('farms.map.all') ? 'active' : '' }}">
    <i class="fas fa-map-marked-alt"></i> Map View
</a>

{{-- Farm Records Section --}}
<div class="nav-section-header">
    <span>Farm Records</span>
</div>

<a href="{{ route('farm-records.index') }}" class="nav-link {{ request()->routeIs('farm-records.index') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list"></i> All Farm Records
</a>

<a href="{{ route('visits.index') }}" class="nav-link {{ request()->routeIs('visits.*') ? 'active' : '' }}">
    <i class="fas fa-calendar-check"></i> Farm Visits
</a>

<a href="{{ route('farm-records.new.create') }}" class="nav-link {{ request()->routeIs('farm-records.new.create') ? 'active' : '' }}">
    <i class="fas fa-file-alt text-success"></i> New Farmer's Farm Record
</a>

<a href="{{ route('farm-records.existing.create') }}" class="nav-link {{ request()->routeIs('farm-records.existing.create') ? 'active' : '' }}">
    <i class="fas fa-file-alt text-primary"></i> Existing Farmer's Farm Record
</a>

<a href="{{ route('farm-records.new.history') }}" class="nav-link {{ request()->routeIs('farm-records.new.history') || request()->routeIs('farm-records.existing.history') ? 'active' : '' }}">
    <i class="fas fa-history"></i> Farmer History
</a>

{{-- Location Section --}}
<div class="nav-section-header">
    <span>Locations</span>
</div>

<a href="{{ route('catchment-areas.index') }}" class="nav-link {{ request()->routeIs('catchment-areas.*') ? 'active' : '' }}">
    <i class="fas fa-globe-africa"></i> Catchment Areas
</a>

{{-- Tasks Section --}}
<div class="nav-section-header">
    <span>Tasks</span>
</div>

<a href="{{ route('tasks.my-tasks') }}" class="nav-link {{ request()->routeIs('tasks.my-tasks') ? 'active' : '' }}">
    <i class="fas fa-tasks"></i> My Tasks
</a>

<a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}">
    <i class="fas fa-list-check"></i> All Tasks
</a>

<a href="{{ route('tasks.calendar') }}" class="nav-link {{ request()->routeIs('tasks.calendar') ? 'active' : '' }}">
    <i class="fas fa-calendar-alt"></i> Task Calendar
</a>

{{-- Training Section --}}
<div class="nav-section-header">
    <span>Training</span>
</div>

<a href="{{ route('training.upcoming') }}" class="nav-link {{ request()->routeIs('training.upcoming') ? 'active' : '' }}">
    <i class="fas fa-chalkboard-teacher"></i> Upcoming Trainings
</a>

<a href="{{ route('training-programs.index') }}" class="nav-link {{ request()->routeIs('training-programs.*') ? 'active' : '' }}">
    <i class="fas fa-graduation-cap"></i> Training Programs
</a>

<a href="{{ route('training.certificates.index') }}" class="nav-link {{ request()->routeIs('training.certificates.*') ? 'active' : '' }}">
    <i class="fas fa-certificate"></i> Certificates
</a>

{{-- Notifications Section --}}
<div class="nav-section-header">
    <span>Notifications</span>
</div>

<a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
    <i class="fas fa-bell"></i> Notification Center
    @php
        $unreadCount = auth()->user()->unreadNotifications()->count();
    @endphp
    @if($unreadCount > 0)
        <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
    @endif
</a>

<a href="{{ route('notifications.preferences') }}" class="nav-link {{ request()->routeIs('notifications.preferences') ? 'active' : '' }}">
    <i class="fas fa-cog"></i> Notification Settings
</a>

{{-- Service Requests Section --}}
<div class="nav-section-header">
    <span>Service Requests</span>
</div>

<a href="{{ route('service-requests.index') }}" class="nav-link {{ request()->routeIs('service-requests.index') ? 'active' : '' }}">
    <i class="fas fa-headset"></i> All Requests
</a>

<a href="{{ route('service-requests.create') }}" class="nav-link {{ request()->routeIs('service-requests.create') ? 'active' : '' }}">
    <i class="fas fa-plus-circle"></i> New Request
</a>

{{-- Digital Forms Section --}}
<div class="nav-section-header">
    <span>Digital Forms</span>
</div>

<a href="{{ route('leave-requests.index') }}" class="nav-link {{ request()->routeIs('leave-requests.*') ? 'active' : '' }}">
    <i class="fas fa-calendar-minus"></i> Leave Requests
</a>

<a href="{{ route('financial-requests.index') }}" class="nav-link {{ request()->routeIs('financial-requests.*') ? 'active' : '' }}">
    <i class="fas fa-money-bill-wave"></i> Financial Requests
</a>

<a href="{{ route('stock-requisitions.index') }}" class="nav-link {{ request()->routeIs('stock-requisitions.*') ? 'active' : '' }}">
    <i class="fas fa-boxes"></i> Stock Requisitions
</a>

<a href="{{ route('performance-reports.index') }}" class="nav-link {{ request()->routeIs('performance-reports.*') ? 'active' : '' }}">
    <i class="fas fa-chart-line"></i> Performance Reports
</a>
