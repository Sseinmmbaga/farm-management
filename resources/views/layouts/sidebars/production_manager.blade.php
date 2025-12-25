{{-- Production Manager Sidebar Navigation --}}
<a href="{{ route('dashboard.production') }}" class="nav-link {{ request()->routeIs('dashboard.production') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

{{-- Production Section --}}
<div class="nav-section-header">
    <span>Production</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-tasks"></i> Production Plans
</a>

<a href="#" class="nav-link">
    <i class="fas fa-clipboard-list"></i> Production Records
</a>

<a href="#" class="nav-link">
    <i class="fas fa-chart-pie"></i> Yield Reports
</a>

{{-- Harvest Section --}}
<div class="nav-section-header">
    <span>Harvest</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-leaf"></i> Harvest Records
</a>

<a href="#" class="nav-link">
    <i class="fas fa-weight"></i> Yield Tracking
</a>

{{-- Data Access Section --}}
<div class="nav-section-header">
    <span>Data Access</span>
</div>

<a href="{{ route('farmers.index') }}" class="nav-link {{ request()->routeIs('farmers.index') ? 'active' : '' }}">
    <i class="fas fa-users"></i> View Farmers
</a>

<a href="{{ route('farms.index') }}" class="nav-link {{ request()->routeIs('farms.index') ? 'active' : '' }}">
    <i class="fas fa-tractor"></i> View Farms
</a>

<a href="{{ route('farms.map.all') }}" class="nav-link {{ request()->routeIs('farms.map.all') ? 'active' : '' }}">
    <i class="fas fa-map-marked-alt"></i> Map View
</a>

{{-- Reports Section --}}
<div class="nav-section-header">
    <span>Reports</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-chart-bar"></i> Production Reports
</a>

<a href="#" class="nav-link">
    <i class="fas fa-download"></i> Export Reports
</a>
