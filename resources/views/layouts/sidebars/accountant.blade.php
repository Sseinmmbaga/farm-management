{{-- Accountant Sidebar Navigation --}}
<a href="{{ route('dashboard.accountant') }}" class="nav-link {{ request()->routeIs('dashboard.accountant') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

{{-- Financial Section --}}
<div class="nav-section-header">
    <span>Financial</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-file-invoice-dollar"></i> Financial Reports
</a>

<a href="#" class="nav-link">
    <i class="fas fa-exchange-alt"></i> Transactions
</a>

<a href="#" class="nav-link">
    <i class="fas fa-receipt"></i> Payment Records
</a>

<a href="#" class="nav-link">
    <i class="fas fa-money-bill-wave"></i> Disbursements
</a>

{{-- Distribution Section --}}
<div class="nav-section-header">
    <span>Distribution Records</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-hand-holding-usd"></i> Distribution Records
</a>

<a href="#" class="nav-link">
    <i class="fas fa-box"></i> Stock Transactions
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
    <i class="fas fa-chart-pie"></i> Financial Analytics
</a>

<a href="#" class="nav-link">
    <i class="fas fa-download"></i> Export Reports
</a>
