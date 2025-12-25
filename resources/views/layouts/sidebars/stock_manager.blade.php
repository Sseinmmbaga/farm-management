{{-- Stock Manager Sidebar Navigation --}}
<a href="{{ route('dashboard.stock') }}" class="nav-link {{ request()->routeIs('dashboard.stock') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

{{-- Inventory Section --}}
<div class="nav-section-header">
    <span>Inventory</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-warehouse"></i> Stock Items
</a>

<a href="#" class="nav-link">
    <i class="fas fa-dolly"></i> Stock Movements
</a>

<a href="#" class="nav-link">
    <i class="fas fa-bell"></i> Low Stock Alerts
</a>

{{-- Distribution Section --}}
<div class="nav-section-header">
    <span>Distribution</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-truck"></i> Deliveries
</a>

<a href="#" class="nav-link">
    <i class="fas fa-seedling"></i> Seed Distribution
</a>

<a href="#" class="nav-link">
    <i class="fas fa-hand-holding"></i> Input Distribution
</a>

{{-- Requests Section --}}
<div class="nav-section-header">
    <span>Requests</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-inbox"></i> Pending Requests
</a>

<a href="#" class="nav-link">
    <i class="fas fa-check-circle"></i> Approved Requests
</a>

<a href="#" class="nav-link">
    <i class="fas fa-history"></i> Request History
</a>

{{-- Data Access Section --}}
<div class="nav-section-header">
    <span>Data Access</span>
</div>

<a href="{{ route('farmers.index') }}" class="nav-link {{ request()->routeIs('farmers.index') ? 'active' : '' }}">
    <i class="fas fa-users"></i> View Farmers
</a>

{{-- Reports Section --}}
<div class="nav-section-header">
    <span>Reports</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-chart-bar"></i> Stock Reports
</a>

<a href="#" class="nav-link">
    <i class="fas fa-download"></i> Export Reports
</a>
