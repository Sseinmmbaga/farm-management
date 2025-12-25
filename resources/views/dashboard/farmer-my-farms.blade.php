<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Farms - Farmer Dashboard - Remei Farm OS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
        }
        
        .navbar {
            background-color: #27ae60;
            margin-bottom: 20px;
        }
        
        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        
        .farm-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        
        .farm-card:hover {
            transform: translateY(-5px);
        }
        
        .farm-status-active {
            border-left: 4px solid #2ecc71;
        }
        
        .farm-status-inactive {
            border-left: 4px solid #e74c3c;
        }
        
        .farm-status-pending {
            border-left: 4px solid #f39c12;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/dashboard/farmer">
                <i class="fas fa-seedling"></i> Remei Farm OS - Farmer
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/dashboard/farmer">Dashboard</a>
                <a class="nav-link active" href="/dashboard/farmer/my-farms">My Farms</a>
                <a class="nav-link" href="#">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-tractor"></i> My Farms</h1>
            <button class="btn btn-success">
                <i class="fas fa-plus"></i> Add New Farm
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card farm-card farm-status-active">
                    <div class="card-body">
                        <h5 class="card-title">Green Valley Farm</h5>
                        <p class="card-text">
                            <i class="fas fa-map-marker-alt"></i> Kumasi, Ashanti Region<br>
                            <i class="fas fa-ruler-combined"></i> 15 acres<br>
                            <i class="fas fa-seedling"></i> Maize, Cassava<br>
                            <span class="badge bg-success">Active</span>
                        </p>
                        <a href="#" class="btn btn-outline-primary btn-sm">View Details</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card farm-card farm-status-active">
                    <div class="card-body">
                        <h5 class="card-title">Sunrise Plantation</h5>
                        <p class="card-text">
                            <i class="fas fa-map-marker-alt"></i> Tamale, Northern Region<br>
                            <i class="fas fa-ruler-combined"></i> 25 acres<br>
                            <i class="fas fa-seedling"></i> Rice, Soybeans<br>
                            <span class="badge bg-success">Active</span>
                        </p>
                        <a href="#" class="btn btn-outline-primary btn-sm">View Details</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card farm-card farm-status-pending">
                    <div class="card-body">
                        <h5 class="card-title">River Side Farm</h5>
                        <p class="card-text">
                            <i class="fas fa-map-marker-alt"></i> Cape Coast, Central Region<br>
                            <i class="fas fa-ruler-combined"></i> 8 acres<br>
                            <i class="fas fa-seedling"></i> Vegetables, Fruits<br>
                            <span class="badge bg-warning">Pending Inspection</span>
                        </p>
                        <a href="#" class="btn btn-outline-warning btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Farm Statistics</h3>
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h2 class="text-success">48</h2>
                            <p>Total Acres</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h2 class="text-primary">3</h2>
                            <p>Active Farms</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h2 class="text-warning">1</h2>
                            <p>Pending Farms</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h2 class="text-info">5</h2>
                            <p>Crop Types</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>