{{-- Supervisor Sidebar Navigation --}}
<a href="{{ route('dashboard.supervisor') }}" class="nav-link {{ request()->routeIs('dashboard.supervisor') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

{{-- Team Management Section --}}
<div class="nav-section-header">
    <span>Team Management</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-users-cog"></i> Officer Management
</a>

<a href="#" class="nav-link">
    <i class="fas fa-chart-line"></i> Performance Tracking
</a>

<a href="#" class="nav-link">
    <i class="fas fa-chart-bar"></i> Monthly Performance
</a>

{{-- Farmer Management Section --}}
<div class="nav-section-header">
    <span>Farmer Management</span>
</div>

<a href="{{ route('farmers.index') }}" class="nav-link {{ request()->routeIs('farmers.index') ? 'active' : '' }}">
    <i class="fas fa-users"></i> All Farmers
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

<a href="{{ route('farm-records.index') }}" class="nav-link {{ request()->routeIs('farm-records.index') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list"></i> All Farm Records
</a>

<a href="{{ route('visits.index') }}" class="nav-link {{ request()->routeIs('visits.*') ? 'active' : '' }}">
    <i class="fas fa-calendar-check"></i> Farm Visits
</a>

<a href="{{ route('farm-records.new.history') }}" class="nav-link {{ request()->routeIs('farm-records.new.history') || request()->routeIs('farm-records.existing.history') ? 'active' : '' }}">
    <i class="fas fa-history"></i> Farmer History
</a>

<a href="{{ route('farms.map.all') }}" class="nav-link {{ request()->routeIs('farms.map.all') ? 'active' : '' }}">
    <i class="fas fa-map-marked-alt"></i> Map View
</a>

{{-- Location Section --}}
<div class="nav-section-header">
    <span>Locations</span>
</div>

<a href="{{ route('catchment-areas.index') }}" class="nav-link {{ request()->routeIs('catchment-areas.*') ? 'active' : '' }}">
    <i class="fas fa-globe-africa"></i> Catchment Areas
</a>

{{-- Reports Section --}}
<div class="nav-section-header">
    <span>Reports</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-file-alt"></i> Data Review
</a>

<a href="#" class="nav-link">
    <i class="fas fa-download"></i> Export Reports
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
