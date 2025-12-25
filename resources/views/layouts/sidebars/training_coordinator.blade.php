{{-- Training Coordinator Sidebar Navigation --}}
<a href="{{ route('dashboard.training') }}" class="nav-link {{ request()->routeIs('dashboard.training') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

{{-- Training Programs Section --}}
<div class="nav-section-header">
    <span>Training Programs</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-calendar-check"></i> All Programs
</a>

<a href="#" class="nav-link">
    <i class="fas fa-plus-circle"></i> Create Program
</a>

<a href="#" class="nav-link">
    <i class="fas fa-calendar-alt"></i> Schedule
</a>

{{-- Attendance Section --}}
<div class="nav-section-header">
    <span>Attendance</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-user-check"></i> Record Attendance
</a>

<a href="#" class="nav-link">
    <i class="fas fa-list-alt"></i> Attendance History
</a>

{{-- Certifications Section --}}
<div class="nav-section-header">
    <span>Certifications</span>
</div>

<a href="#" class="nav-link">
    <i class="fas fa-certificate"></i> Issue Certificates
</a>

<a href="#" class="nav-link">
    <i class="fas fa-award"></i> Certified Farmers
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
    <i class="fas fa-chart-bar"></i> Training Reports
</a>

<a href="#" class="nav-link">
    <i class="fas fa-download"></i> Export Reports
</a>
