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

{{-- Training Section --}}
<div class="nav-section-header">
    <span>Training</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-chalkboard-teacher"></i> My Trainings
</a>

<a href="#" class="nav-link">
    <i class="fas fa-certificate"></i> Certificates
</a>
