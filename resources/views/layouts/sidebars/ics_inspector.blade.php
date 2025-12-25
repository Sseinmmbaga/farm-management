{{-- ICS Inspector Sidebar Navigation --}}
<a href="{{ route('dashboard.ics') }}" class="nav-link {{ request()->routeIs('dashboard.ics') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

{{-- Inspection Section --}}
<div class="nav-section-header">
    <span>Inspections</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-clipboard-check"></i> Internal Inspection
</a>

<a href="#" class="nav-link">
    <i class="fas fa-leaf"></i> Social/Environmental
</a>

<a href="#" class="nav-link">
    <i class="fas fa-exclamation-triangle"></i> Non-Conformities
</a>

<a href="#" class="nav-link">
    <i class="fas fa-tasks"></i> Corrective Actions
</a>

{{-- Compliance Section --}}
<div class="nav-section-header">
    <span>Compliance</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-file-alt"></i> Compliance Reports
</a>

<a href="#" class="nav-link">
    <i class="fas fa-certificate"></i> Certifications
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

<a href="{{ route('farm-records.index') }}" class="nav-link {{ request()->routeIs('farm-records.index') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list"></i> View Farm Records
</a>

<a href="{{ route('farms.map.all') }}" class="nav-link {{ request()->routeIs('farms.map.all') ? 'active' : '' }}">
    <i class="fas fa-map-marked-alt"></i> Map View
</a>

<a href="{{ route('catchment-areas.index') }}" class="nav-link {{ request()->routeIs('catchment-areas.*') ? 'active' : '' }}">
    <i class="fas fa-globe-africa"></i> Catchment Areas
</a>

{{-- Reports Section --}}
<div class="nav-section-header">
    <span>Reports</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-download"></i> Export Reports
</a>
