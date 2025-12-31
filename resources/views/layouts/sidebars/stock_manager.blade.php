{{-- Stock Manager Sidebar Navigation --}}
<a href="{{ route('dashboard.stock') }}" class="nav-link {{ request()->routeIs('dashboard.stock') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

{{-- Inventory Section --}}
<div class="nav-section-header">
    <span>Inventory</span>
</div>

<a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.index') || request()->routeIs('stock.show') || request()->routeIs('stock.create') || request()->routeIs('stock.edit') ? 'active' : '' }}">
    <i class="fas fa-warehouse"></i> Stock Items
</a>

<a href="{{ route('stock-categories.index') }}" class="nav-link {{ request()->routeIs('stock-categories.*') ? 'active' : '' }}">
    <i class="fas fa-tags"></i> Categories
</a>

<a href="{{ route('stock.transactions.index') }}" class="nav-link {{ request()->routeIs('stock.transactions.*') ? 'active' : '' }}">
    <i class="fas fa-dolly"></i> Stock Movements
</a>

<a href="{{ route('stock.low-stock') }}" class="nav-link {{ request()->routeIs('stock.low-stock') || request()->routeIs('stock.critical') || request()->routeIs('stock.out-of-stock') ? 'active' : '' }}">
    <i class="fas fa-bell"></i> Low Stock Alerts
</a>

{{-- Transactions Section --}}
<div class="nav-section-header">
    <span>Transactions</span>
</div>

<a href="{{ route('stock.transactions.intake') }}" class="nav-link {{ request()->routeIs('stock.transactions.intake') ? 'active' : '' }}">
    <i class="fas fa-arrow-down"></i> Stock Intake
</a>

<a href="{{ route('stock.transactions.issuance') }}" class="nav-link {{ request()->routeIs('stock.transactions.issuance') ? 'active' : '' }}">
    <i class="fas fa-arrow-up"></i> Stock Issuance
</a>

{{-- Distribution Section --}}
<div class="nav-section-header">
    <span>Distribution</span>
</div>

<a href="{{ route('stock.distributions.index') }}" class="nav-link {{ request()->routeIs('stock.distributions.index') || request()->routeIs('stock.distributions.show') ? 'active' : '' }}">
    <i class="fas fa-truck"></i> All Distributions
</a>

<a href="{{ route('stock.distributions.create') }}" class="nav-link {{ request()->routeIs('stock.distributions.create') ? 'active' : '' }}">
    <i class="fas fa-plus-circle"></i> New Distribution
</a>

{{-- Requests Section --}}
<div class="nav-section-header">
    <span>Requests</span>
</div>

<a href="{{ route('stock.requests.index') }}" class="nav-link {{ request()->routeIs('stock.requests.*') ? 'active' : '' }}">
    <i class="fas fa-inbox"></i> All Requests
</a>

<a href="{{ route('stock.requests.create') }}" class="nav-link {{ request()->routeIs('stock.requests.create') ? 'active' : '' }}">
    <i class="fas fa-plus-circle"></i> New Request
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

<a href="{{ route('stock.reports.summary') }}" class="nav-link {{ request()->routeIs('stock.reports.summary') ? 'active' : '' }}">
    <i class="fas fa-chart-bar"></i> Summary Report
</a>

<a href="{{ route('stock.reports.movements') }}" class="nav-link {{ request()->routeIs('stock.reports.movements') ? 'active' : '' }}">
    <i class="fas fa-exchange-alt"></i> Movements Report
</a>

<a href="{{ route('stock.reports.valuation') }}" class="nav-link {{ request()->routeIs('stock.reports.valuation') ? 'active' : '' }}">
    <i class="fas fa-dollar-sign"></i> Valuation Report
</a>

<a href="{{ route('stock.export.csv') }}" class="nav-link">
    <i class="fas fa-download"></i> Export to CSV
</a>
