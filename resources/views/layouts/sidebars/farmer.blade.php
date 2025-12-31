{{-- Farmer Sidebar Navigation --}}
<a href="{{ route('dashboard.farmer') }}" class="nav-link {{ request()->routeIs('dashboard.farmer') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Dashboard
</a>

{{-- Profile Section --}}
<div class="nav-section-header">
    <span>My Profile</span>
</div>

<a href="{{ route('dashboard.farmer.profile.view') }}" class="nav-link {{ request()->routeIs('dashboard.farmer.profile.view') ? 'active' : '' }}">
    <i class="fas fa-user"></i> View Profile
</a>

<a href="{{ route('dashboard.farmer.profile.edit') }}" class="nav-link {{ request()->routeIs('dashboard.farmer.profile.edit') ? 'active' : '' }}">
    <i class="fas fa-edit"></i> Edit Profile
</a>

{{-- My Farms Section --}}
<div class="nav-section-header">
    <span>My Farms</span>
</div>

<a href="{{ route('dashboard.farmer.my-farms') }}" class="nav-link {{ request()->routeIs('dashboard.farmer.my-farms') ? 'active' : '' }}">
    <i class="fas fa-tractor"></i> View My Farms
</a>

<a href="{{ route('dashboard.farmer.farm-records') }}" class="nav-link {{ request()->routeIs('dashboard.farmer.farm-records') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list"></i> Farm Records
</a>

{{-- Activity Section --}}
<div class="nav-section-header">
    <span>Activity Logs</span>
</div>

<a href="{{ route('dashboard.farmer.activity-log') }}" class="nav-link {{ request()->routeIs('dashboard.farmer.activity-log') ? 'active' : '' }}">
    <i class="fas fa-history"></i> My Activity Log
</a>

<a href="{{ route('dashboard.farmer.harvest-history') }}" class="nav-link {{ request()->routeIs('dashboard.farmer.harvest-history') ? 'active' : '' }}">
    <i class="fas fa-leaf"></i> Harvest History
</a>

{{-- Training Section --}}
<div class="nav-section-header">
    <span>Training</span>
</div>

<a href="{{ route('dashboard.farmer.trainings') }}" class="nav-link {{ request()->routeIs('dashboard.farmer.trainings') ? 'active' : '' }}">
    <i class="fas fa-chalkboard-teacher"></i> My Trainings
</a>

<a href="{{ route('dashboard.farmer.certificates') }}" class="nav-link {{ request()->routeIs('dashboard.farmer.certificates') ? 'active' : '' }}">
    <i class="fas fa-certificate"></i> Certificates
</a>

{{-- Distribution Section --}}
<div class="nav-section-header">
    <span>Distributions</span>
</div>

<a href="{{ route('dashboard.farmer.distributions') }}" class="nav-link {{ request()->routeIs('dashboard.farmer.distributions') ? 'active' : '' }}">
    <i class="fas fa-box"></i> My Distributions
</a>

<a href="{{ route('dashboard.farmer.seeds-received') }}" class="nav-link {{ request()->routeIs('dashboard.farmer.seeds-received') ? 'active' : '' }}">
    <i class="fas fa-seedling"></i> Seeds Received
</a>
