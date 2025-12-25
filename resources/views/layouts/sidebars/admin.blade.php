{{-- Admin Sidebar Navigation --}}
<a href="{{ route('dashboard.admin') }}" class="nav-link {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

<a href="{{ route('farms.map.all') }}" class="nav-link {{ request()->routeIs('farms.map.all') ? 'active' : '' }}">
    <i class="fas fa-map-marked-alt"></i> Map View
</a>

<a href="{{ route('catchment-areas.index') }}" class="nav-link {{ request()->routeIs('catchment-areas.*') || request()->routeIs('regions.*') || request()->routeIs('districts.*') || request()->routeIs('villages.*') || request()->routeIs('subvillages.*') ? 'active' : '' }}">
    <i class="fas fa-globe-africa"></i> Catchment Areas
</a>

<a href="#" class="nav-link">
    <i class="fas fa-chart-line"></i> Analytics
</a>

<a href="#" class="nav-link">
    <i class="fas fa-box"></i> Inventory
</a>

<a href="{{ route('seasons.index') }}" class="nav-link {{ request()->routeIs('seasons.*') ? 'active' : '' }}">
    <i class="fas fa-calendar-alt"></i> Seasons
</a>

<a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
    <i class="fas fa-users-cog"></i> User Management
</a>

{{-- Role Quick Access Section --}}
<div class="nav-section-header">
    <span>Role Quick Access</span>
</div>

{{-- Accountant Section --}}
<a class="nav-link" data-bs-toggle="collapse" href="#accountantMenu" role="button" aria-expanded="false">
    <span><i class="fas fa-calculator text-warning"></i> Accountant</span>
</a>
<div class="collapse collapse-menu" id="accountantMenu">
    <a href="#" class="nav-link">
        <i class="fas fa-file-invoice-dollar"></i> Financial Reports
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-exchange-alt"></i> Transactions
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-receipt"></i> Payment Records
    </a>
</div>

{{-- Production Manager Section --}}
<a class="nav-link" data-bs-toggle="collapse" href="#productionMenu" role="button" aria-expanded="false">
    <span><i class="fas fa-industry text-info"></i> Production Manager</span>
</a>
<div class="collapse collapse-menu" id="productionMenu">
    <a href="#" class="nav-link">
        <i class="fas fa-tasks"></i> Production Plans
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-clipboard-list"></i> Production Records
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-chart-pie"></i> Yield Reports
    </a>
    <a href="{{ route('farmers.index') }}" class="nav-link {{ request()->routeIs('farmers.index') ? 'active' : '' }}">
        <i class="fas fa-eye"></i> View Farmers
    </a>
    <a href="{{ route('farms.index') }}" class="nav-link {{ request()->routeIs('farms.index') ? 'active' : '' }}">
        <i class="fas fa-eye"></i> View Farms
    </a>
</div>

{{-- ICS Inspector Section --}}
<a class="nav-link" data-bs-toggle="collapse" href="#icsMenu" role="button" aria-expanded="false">
    <span><i class="fas fa-search text-danger"></i> ICS Inspector</span>
</a>
<div class="collapse collapse-menu" id="icsMenu">
    <a href="#" class="nav-link">
        <i class="fas fa-clipboard-check"></i> Internal Inspection
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-leaf"></i> Social/Environmental
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-file-alt"></i> Compliance Reports
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-exclamation-triangle"></i> Non-Conformities
    </a>
    <a href="{{ route('farmers.index') }}" class="nav-link">
        <i class="fas fa-eye"></i> View Farmers
    </a>
    <a href="{{ route('farms.index') }}" class="nav-link">
        <i class="fas fa-eye"></i> View Farms
    </a>
    <a href="{{ route('farm-records.index') }}" class="nav-link {{ request()->routeIs('farm-records.index') ? 'active' : '' }}">
        <i class="fas fa-eye"></i> View Farm Records
    </a>
</div>

{{-- Supervisor Section --}}
<a class="nav-link" data-bs-toggle="collapse" href="#supervisorMenu" role="button" aria-expanded="false">
    <span><i class="fas fa-user-tie text-primary"></i> Supervisor</span>
</a>
<div class="collapse collapse-menu" id="supervisorMenu">
    <a href="#" class="nav-link">
        <i class="fas fa-users-cog"></i> Officer Management
    </a>
    <a href="{{ route('farmers.index') }}" class="nav-link">
        <i class="fas fa-eye"></i> View Farmers
    </a>
    <a href="{{ route('farmer-groups.index') }}" class="nav-link {{ request()->routeIs('farmer-groups.*') ? 'active' : '' }}">
        <i class="fas fa-layer-group"></i> Farmer Groups
    </a>
    <a href="{{ route('farms.index') }}" class="nav-link">
        <i class="fas fa-eye"></i> View Farms
    </a>
    <a href="{{ route('farm-records.index') }}" class="nav-link">
        <i class="fas fa-clipboard-list"></i> All Farm Records
    </a>
    <a href="{{ route('farm-records.new.history') }}" class="nav-link {{ request()->routeIs('farm-records.new.history') || request()->routeIs('farm-records.existing.history') ? 'active' : '' }}">
        <i class="fas fa-history"></i> Farmer History
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-chart-line"></i> Performance Tracking
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-chart-bar"></i> Monthly Performance
    </a>
</div>

{{-- Extension Officer Section --}}
<a class="nav-link" data-bs-toggle="collapse" href="#extensionMenu" role="button" aria-expanded="false">
    <span><i class="fas fa-seedling text-success"></i> Extension Officer</span>
</a>
<div class="collapse collapse-menu" id="extensionMenu">
    <a href="{{ route('farmers.index') }}" class="nav-link">
        <i class="fas fa-users"></i> All Farmers
    </a>
    <a href="{{ route('farmers.create') }}" class="nav-link {{ request()->routeIs('farmers.create') ? 'active' : '' }}">
        <i class="fas fa-user-plus"></i> Add New Farmer
    </a>
    <a href="{{ route('farmer-groups.index') }}" class="nav-link">
        <i class="fas fa-layer-group"></i> Farmer Groups
    </a>
    <a href="{{ route('farms.index') }}" class="nav-link">
        <i class="fas fa-tractor"></i> All Farms
    </a>
    <a href="{{ route('farms.create') }}" class="nav-link {{ request()->routeIs('farms.create') ? 'active' : '' }}">
        <i class="fas fa-plus-circle"></i> New Farm
    </a>
    <a href="{{ route('farm-records.index') }}" class="nav-link">
        <i class="fas fa-clipboard-list"></i> All Farm Records
    </a>
    <a href="{{ route('farm-records.new.create') }}" class="nav-link {{ request()->routeIs('farm-records.new.create') ? 'active' : '' }}">
        <i class="fas fa-file-alt text-success"></i> Records of a new farmer's farm
    </a>
    <a href="{{ route('farm-records.existing.create') }}" class="nav-link {{ request()->routeIs('farm-records.existing.create') ? 'active' : '' }}">
        <i class="fas fa-file-alt text-primary"></i> Records of an old farmer's farm
    </a>
    <a href="{{ route('farm-records.new.history') }}" class="nav-link">
        <i class="fas fa-history"></i> Farmer History
    </a>
</div>

{{-- Stock Manager Section --}}
<a class="nav-link" data-bs-toggle="collapse" href="#stockMenu" role="button" aria-expanded="false">
    <span><i class="fas fa-boxes text-secondary"></i> Stock Manager</span>
</a>
<div class="collapse collapse-menu" id="stockMenu">
    <a href="#" class="nav-link">
        <i class="fas fa-warehouse"></i> Inventory
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-dolly"></i> Stock Movements
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-truck"></i> Deliveries
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-seedling"></i> Seed Distribution
    </a>
</div>

{{-- Training Coordinator Section --}}
<a class="nav-link" data-bs-toggle="collapse" href="#trainingMenu" role="button" aria-expanded="false">
    <span><i class="fas fa-chalkboard-teacher text-purple"></i> Training Coordinator</span>
</a>
<div class="collapse collapse-menu" id="trainingMenu">
    <a href="#" class="nav-link">
        <i class="fas fa-calendar-check"></i> Training Programs
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-user-graduate"></i> Training Records
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-certificate"></i> Certifications
    </a>
</div>

{{-- Farmer Portal Preview --}}
<a class="nav-link" data-bs-toggle="collapse" href="#farmerPortalMenu" role="button" aria-expanded="false">
    <span><i class="fas fa-user-circle text-success"></i> Farmer Portal</span>
</a>
<div class="collapse collapse-menu" id="farmerPortalMenu">
    <a href="#" class="nav-link">
        <i class="fas fa-eye"></i> Portal Preview
    </a>
    <a href="#" class="nav-link">
        <i class="fas fa-id-card"></i> Farmer Dashboard
    </a>
</div>
