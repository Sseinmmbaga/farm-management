{{-- Training Coordinator Sidebar Navigation --}}
<a href="{{ route('dashboard.training') }}" class="nav-link {{ request()->routeIs('dashboard.training') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

{{-- Training Programs Section --}}
<div class="nav-section-header">
    <span>Training Programs</span>
</div>

<a href="{{ route('training-programs.index') }}" class="nav-link {{ request()->routeIs('training-programs.index') ? 'active' : '' }}">
    <i class="fas fa-calendar-check"></i> All Programs
</a>

<a href="{{ route('training-programs.create') }}" class="nav-link {{ request()->routeIs('training-programs.create') ? 'active' : '' }}">
    <i class="fas fa-plus-circle"></i> Create Program
</a>

<a href="{{ route('training.sessions.index') }}" class="nav-link {{ request()->routeIs('training.sessions.index') ? 'active' : '' }}">
    <i class="fas fa-calendar-alt"></i> Schedule
</a>

{{-- Attendance Section --}}
<div class="nav-section-header">
    <span>Attendance</span>
</div>

<a href="{{ route('training.attendance.list') }}" class="nav-link {{ request()->routeIs('training.attendance.list') || request()->routeIs('training.attendance.index') ? 'active' : '' }}">
    <i class="fas fa-user-check"></i> Record Attendance
</a>

<a href="{{ route('training.reports.attendance') }}" class="nav-link {{ request()->routeIs('training.reports.attendance') ? 'active' : '' }}">
    <i class="fas fa-list-alt"></i> Attendance History
</a>

{{-- Certifications Section --}}
<div class="nav-section-header">
    <span>Certifications</span>
</div>

<a href="{{ route('training.completed') }}" class="nav-link {{ request()->routeIs('training.completed') ? 'active' : '' }}">
    <i class="fas fa-certificate"></i> Issue Certificates
</a>

<a href="{{ route('training.certificates.index') }}" class="nav-link {{ request()->routeIs('training.certificates.index') || request()->routeIs('training.certificates.show') ? 'active' : '' }}">
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

<a href="{{ route('training.reports.summary') }}" class="nav-link {{ request()->routeIs('training.reports.summary') ? 'active' : '' }}">
    <i class="fas fa-chart-bar"></i> Training Reports
</a>

<a href="{{ route('training.reports.summary') }}" class="nav-link {{ request()->routeIs('training.reports.summary') ? 'active' : '' }}">
    <i class="fas fa-download"></i> Export Reports
</a>
