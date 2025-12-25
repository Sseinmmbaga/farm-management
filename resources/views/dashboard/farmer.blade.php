<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Farmer Dashboard - Remei Farm OS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #27ae60;
            --secondary-color: #2ecc71;
            --accent-color: #f39c12;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }
        
        .sidebar {
            background-color: var(--primary-color);
            color: white;
            min-height: 100vh;
            padding: 0;
            position: fixed;
            width: 250px;
            z-index: 1000;
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
            color: white;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 15px 20px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--accent-color);
        }
        
        .nav-link i {
            width: 25px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
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
        
        .stats-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .card-primary { border-left: 4px solid #3498db; }
        .card-success { border-left: 4px solid var(--secondary-color); }
        .card-warning { border-left: 4px solid var(--accent-color); }
        .card-info { border-left: 4px solid #17a2b8; }
        
        .recent-activity {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .activity-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-time {
            color: #6c757d;
            font-size: 0.85rem;
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
            background-color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
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
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-seedling"></i>
            </div>
            <h3>Remei Farm OS</h3>
            <small>Farmer Dashboard</small>
        </div>
        
        <nav class="nav flex-column mt-4">
            <a href="#" class="nav-link active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-tractor"></i> My Farms
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-chart-line"></i> Farm Analytics
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-calendar-alt"></i> Planting Schedule
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-box"></i> My Inventory
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-file-invoice-dollar"></i> Reports
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-question-circle"></i> Support
            </a>
        </nav>
        
        <!-- Logout Button -->
        <a href="#" class="nav-link text-warning mt-4" id="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        
        <div class="sidebar-footer mt-auto p-3">
            <div class="user-profile">
                <div class="user-avatar">
                    @if(auth()->check())
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @else
                        F
                    @endif
                </div>
                <div>
                    <strong>
                        @if(auth()->check())
                            {{ auth()->user()->name }}
                        @else
                            Farmer
                        @endif
                    </strong><br>
                    <small>Farmer Account</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1 class="h3 mb-0">Farmer Dashboard</h1>
            <div class="d-flex gap-3">
                <button class="btn btn-outline-success">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Farm
                </button>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card card-success">
                    <div class="stats-icon text-success">
                        <i class="fas fa-tractor"></i>
                    </div>
                    <div class="stats-number">3</div>
                    <div class="stats-label">Active Farms</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> 1 new this month</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card card-primary">
                    <div class="stats-icon text-primary">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="stats-number">45</div>
                    <div class="stats-label">Planted Acres</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> 5% growth</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card card-warning">
                    <div class="stats-icon text-warning">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stats-number">7</div>
                    <div class="stats-label">Pending Tasks</div>
                    <small class="text-danger">Attention needed</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card card-info">
                    <div class="stats-icon text-info">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stats-number">₵ 12,450</div>
                    <div class="stats-label">Monthly Income</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> 8% from last month</small>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity and Quick Actions -->
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="recent-activity">
                    <h4>Recent Activity</h4>
                    <div class="mt-3">
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Farm Inspection Completed</strong>
                                    <p class="mb-0">Farm "Green Valley" inspection passed</p>
                                </div>
                                <div class="activity-time">2 days ago</div>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Seed Delivery</strong>
                                    <p class="mb-0">Received 50kg of maize seeds</p>
                                </div>
                                <div class="activity-time">3 days ago</div>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Training Session</strong>
                                    <p class="mb-0">Attended sustainable farming workshop</p>
                                </div>
                                <div class="activity-time">1 week ago</div>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Payment Received</strong>
                                    <p class="mb-0">₵ 5,200 for maize harvest</p>
                                </div>
                                <div class="activity-time">2 weeks ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="recent-activity">
                    <h4>Quick Actions</h4>
                    <div class="mt-3">
                        <button class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-plus-circle"></i> Register New Farm
                        </button>
                        <button class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-calendar-plus"></i> Schedule Planting
                        </button>
                        <button class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-clipboard-list"></i> Submit Report
                        </button>
                        <button class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-question-circle"></i> Request Support
                        </button>
                        <button class="btn btn-outline-danger w-100">
                            <i class="fas fa-exclamation-triangle"></i> Report Issue
                        </button>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Upcoming Events</h5>
                        <div class="mt-2">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Next Inspection</span>
                                <span class="text-primary">May 15</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Training Session</span>
                                <span class="text-success">May 20</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Harvest Date</span>
                                <span class="text-warning">June 5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar navigation active state
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                    
                    console.log('Navigating to:', this.textContent.trim());
                });
            });
            
            // Refresh button
            const refreshBtn = document.querySelector('.btn-outline-success');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                        alert('Dashboard data refreshed!');
                    }, 1000);
                });
            }
            
            // Logout functionality
            const logoutBtn = document.getElementById("logout-btn");
            if (logoutBtn) {
                logoutBtn.addEventListener("click", function(e) {
                    e.preventDefault();
                    if (confirm("Are you sure you want to logout?")) {
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
                        
                        setTimeout(() => {
                            alert('You have been logged out successfully!');
                            // In a real Laravel app, redirect to logout route
                            // window.location.href = "{{ route('logout') }}";
                        }, 1000);
                    }
                });
            }
        });
    </script>
</body>
</html>
