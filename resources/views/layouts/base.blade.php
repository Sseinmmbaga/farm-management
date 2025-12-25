<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard') - Remei Farm OS</title>

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
            /* Role-specific colors */
            --admin-color: #e74c3c;
            --supervisor-color: #3498db;
            --extension-color: #2ecc71;
            --ics-color: #e67e22;
            --stock-color: #9b59b6;
            --accountant-color: #f39c12;
            --training-color: #1abc9c;
            --production-color: #34495e;
            --farmer-color: #27ae60;
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

        .sidebar-header .role-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.7rem;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        /* Navigation Section Styles */
        .nav-section-header {
            padding: 15px 20px 10px;
            margin-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-section-header span {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
        }

        /* Role-specific accent colors */
        .role-admin .sidebar-header .role-badge { background-color: var(--admin-color); }
        .role-supervisor .sidebar-header .role-badge { background-color: var(--supervisor-color); }
        .role-extension_officer .sidebar-header .role-badge { background-color: var(--extension-color); }
        .role-ics_inspector .sidebar-header .role-badge { background-color: var(--ics-color); }
        .role-stock_manager .sidebar-header .role-badge { background-color: var(--stock-color); }
        .role-accountant .sidebar-header .role-badge { background-color: var(--accountant-color); }
        .role-training_coordinator .sidebar-header .role-badge { background-color: var(--training-color); }
        .role-production_manager .sidebar-header .role-badge { background-color: var(--production-color); }
        .role-farmer .sidebar-header .role-badge { background-color: var(--farmer-color); }

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
<body class="role-{{ auth()->user()->getRoleValue() }}">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-seedling"></i>
            </div>
            <h3>Remei Farm OS</h3>
            <span class="role-badge">{{ auth()->user()->getRoleLabel() }}</span>
        </div>

        <nav class="nav flex-column mt-4">
            @include('layouts.sidebars.' . auth()->user()->getRoleValue())
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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item active">@yield('title', 'Dashboard')</li>
                    </ol>
                </nav>
            </div>
            <div class="topbar-right">
                <div class="topbar-icon">
                    <i class="fas fa-bell"></i>
                    <span class="badge bg-danger rounded-pill">3</span>
                </div>
                <div class="topbar-icon">
                    <i class="fas fa-envelope"></i>
                    <span class="badge bg-primary rounded-pill">5</span>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2" style="width: 35px; height: 35px; font-size: 0.8rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline text-dark">{{ auth()->user()->name ?? 'User' }}</span>
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
