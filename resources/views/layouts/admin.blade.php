<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Remei Farm OS</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }

        .sidebar {
            background-color: var(--primary-color);
            color: white;
            height: 100vh;
            padding: 0;
            position: fixed;
            width: 250px;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .sidebar-header {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        .sidebar-header .logo {
            font-size: 2rem;
            color: var(--accent-color);
        }

        .sidebar .nav-link {
            color: #bdc3c7;
            padding: 15px 20px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--accent-color);
        }

        .sidebar .nav-link i {
            width: 25px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background-color: white;
            padding: 15px 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .topbar-left h5 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .topbar-icon {
            position: relative;
            color: #6c757d;
            font-size: 1.1rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .topbar-icon:hover {
            color: var(--secondary-color);
        }

        .topbar-icon .badge {
            position: absolute;
            top: -5px;
            right: -8px;
            font-size: 0.65rem;
        }

        .content-wrapper {
            flex: 1;
            padding: 25px;
            display: flex;
            justify-content: center;
        }

        .content-container {
            width: 100%;
            max-width: 1400px;
        }

        .header {
            background-color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .avatar-sm {
            width: 35px;
            height: 35px;
            font-size: 0.8rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Sidebar Dropdown Menu Styles */
        .sidebar .nav-item {
            position: relative;
        }

        .sidebar .nav-link[data-bs-toggle="collapse"] {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar .nav-link[data-bs-toggle="collapse"]::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            transition: transform 0.3s;
        }

        .sidebar .nav-link[data-bs-toggle="collapse"][aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        .sidebar .collapse-menu {
            background-color: rgba(0, 0, 0, 0.15);
        }

        .sidebar .collapse-menu .nav-link {
            padding-left: 45px;
            font-size: 0.9rem;
        }

        .sidebar .collapse-menu .nav-link:hover,
        .sidebar .collapse-menu .nav-link.active {
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* Role-Based Navigation Styles */
        .role-nav-divider {
            padding: 15px 20px 10px;
            margin-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .role-nav-divider span {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
        }

        .role-section-header {
            background-color: rgba(255, 255, 255, 0.03);
            border-left: 3px solid transparent !important;
            margin: 2px 0;
        }

        .role-section-header:hover {
            border-left-color: var(--accent-color) !important;
        }

        .role-section-header[aria-expanded="true"] {
            background-color: rgba(255, 255, 255, 0.08);
            border-left-color: var(--accent-color) !important;
        }

        .text-purple {
            color: #9b59b6 !important;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                min-height: auto;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-seedling"></i>
            </div>
            <h3>Remei Farm OS</h3>
            <small>Admin Dashboard</small>
        </div>

        <nav class="nav flex-column mt-4">
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
            @if(auth()->user()->hasAnyRole([\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPERVISOR]))
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> User Management
            </a>
            @endif

            <!-- Role-Based Navigation Sections -->
            @if(auth()->user()->hasRole(\App\Enums\UserRole::ADMIN))
            <div class="role-nav-divider">
                <span>Role Quick Access</span>
            </div>

            <!-- Accountant Section -->
            <a class="nav-link role-section-header" data-bs-toggle="collapse" href="#accountantMenu" role="button" aria-expanded="false">
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

            <!-- Production Manager Section -->
            <a class="nav-link role-section-header" data-bs-toggle="collapse" href="#productionMenu" role="button" aria-expanded="false">
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
                <a href="{{ route('farms.map.all') }}" class="nav-link {{ request()->routeIs('farms.map.all') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt"></i> Map View
                </a>
            </div>

            <!-- ICS Inspector Section -->
            <a class="nav-link role-section-header" data-bs-toggle="collapse" href="#icsMenu" role="button" aria-expanded="false">
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
                <a href="{{ route('farmers.index') }}" class="nav-link {{ request()->routeIs('farmers.index') ? 'active' : '' }}">
                    <i class="fas fa-eye"></i> View Farmers
                </a>
                <a href="{{ route('farms.index') }}" class="nav-link {{ request()->routeIs('farms.index') ? 'active' : '' }}">
                    <i class="fas fa-eye"></i> View Farms
                </a>
                <a href="{{ route('farm-records.index') }}" class="nav-link {{ request()->routeIs('farm-records.index') ? 'active' : '' }}">
                    <i class="fas fa-eye"></i> View Farm Records
                </a>
                <a href="{{ route('farms.map.all') }}" class="nav-link {{ request()->routeIs('farms.map.all') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt"></i> Map View
                </a>
                <a href="{{ route('catchment-areas.index') }}" class="nav-link {{ request()->routeIs('catchment-areas.*') ? 'active' : '' }}">
                    <i class="fas fa-globe-africa"></i> Catchment Areas
                </a>
            </div>

            <!-- Supervisor Section -->
            <a class="nav-link role-section-header" data-bs-toggle="collapse" href="#supervisorMenu" role="button" aria-expanded="false">
                <span><i class="fas fa-user-tie text-primary"></i> Supervisor</span>
            </a>
            <div class="collapse collapse-menu" id="supervisorMenu">
                <a href="#" class="nav-link">
                    <i class="fas fa-users-cog"></i> Officer Management
                </a>
                <a href="{{ route('farmers.index') }}" class="nav-link {{ request()->routeIs('farmers.index') ? 'active' : '' }}">
                    <i class="fas fa-eye"></i> View Farmers
                </a>
                <a href="{{ route('farmer-groups.index') }}" class="nav-link {{ request()->routeIs('farmer-groups.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i> Farmer Groups
                </a>
                <a href="{{ route('farms.index') }}" class="nav-link {{ request()->routeIs('farms.index') ? 'active' : '' }}">
                    <i class="fas fa-eye"></i> View Farms
                </a>
                <a href="{{ route('farm-records.index') }}" class="nav-link {{ request()->routeIs('farm-records.index') ? 'active' : '' }}">
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
                <a href="{{ route('farms.map.all') }}" class="nav-link {{ request()->routeIs('farms.map.all') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt"></i> Map View
                </a>
                <a href="{{ route('catchment-areas.index') }}" class="nav-link {{ request()->routeIs('catchment-areas.*') ? 'active' : '' }}">
                    <i class="fas fa-globe-africa"></i> Catchment Areas
                </a>
            </div>

            <!-- Extension Officer Section -->
            <a class="nav-link role-section-header" data-bs-toggle="collapse" href="#extensionMenu" role="button" aria-expanded="false">
                <span><i class="fas fa-seedling text-success"></i> Extension Officer</span>
            </a>
            <div class="collapse collapse-menu" id="extensionMenu">
                <a href="{{ route('farmers.index') }}" class="nav-link {{ request()->routeIs('farmers.index') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> All Farmers
                </a>
                <a href="{{ route('farmers.create') }}" class="nav-link {{ request()->routeIs('farmers.create') ? 'active' : '' }}">
                    <i class="fas fa-user-plus"></i> Add New Farmer
                </a>
                <a href="{{ route('farmer-groups.index') }}" class="nav-link {{ request()->routeIs('farmer-groups.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i> Farmer Groups
                </a>
                <a href="{{ route('farms.index') }}" class="nav-link {{ request()->routeIs('farms.index') ? 'active' : '' }}">
                    <i class="fas fa-tractor"></i> All Farms
                </a>
                <a href="{{ route('farms.create') }}" class="nav-link {{ request()->routeIs('farms.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i> New Farm
                </a>
                <a href="{{ route('farm-records.index') }}" class="nav-link {{ request()->routeIs('farm-records.index') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> All Farm Records
                </a>
                <a href="{{ route('farm-records.new.create') }}" class="nav-link {{ request()->routeIs('farm-records.new.create') ? 'active' : '' }}">
                    <i class="fas fa-file-alt text-success"></i> Records of a new farmer's farm
                </a>
                <a href="{{ route('farm-records.existing.create') }}" class="nav-link {{ request()->routeIs('farm-records.existing.create') ? 'active' : '' }}">
                    <i class="fas fa-file-alt text-primary"></i> Records of an old farmer's farm
                </a>
                <a href="{{ route('farm-records.new.history') }}" class="nav-link {{ request()->routeIs('farm-records.new.history') || request()->routeIs('farm-records.existing.history') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Farmer History
                </a>
                <a href="{{ route('farms.map.all') }}" class="nav-link {{ request()->routeIs('farms.map.all') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt"></i> Map View
                </a>
                <a href="{{ route('catchment-areas.index') }}" class="nav-link {{ request()->routeIs('catchment-areas.*') ? 'active' : '' }}">
                    <i class="fas fa-globe-africa"></i> Catchment Areas
                </a>
            </div>

            <!-- Stock Manager Section -->
            <a class="nav-link role-section-header" data-bs-toggle="collapse" href="#stockMenu" role="button" aria-expanded="false">
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

            <!-- Training Coordinator Section -->
            <a class="nav-link role-section-header" data-bs-toggle="collapse" href="#trainingMenu" role="button" aria-expanded="false">
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

            <!-- Farmer Portal Preview -->
            <a class="nav-link role-section-header" data-bs-toggle="collapse" href="#farmerPortalMenu" role="button" aria-expanded="false">
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
            @endif
        </nav>

    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="btn btn-link text-dark d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.admin') }}"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item active">@yield('title', 'Dashboard')</li>
                    </ol>
                </nav>
            </div>
            <div class="topbar-right">
                @include('partials.notification-dropdown')
                <div class="topbar-icon">
                    <i class="fas fa-envelope"></i>
                    <span class="badge bg-primary rounded-pill">5</span>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2" style="width: 35px; height: 35px; font-size: 0.8rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline text-dark">{{ auth()->user()->name ?? 'Admin' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Wrapper - Centered -->
        <div class="content-wrapper">
            <div class="content-container">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
