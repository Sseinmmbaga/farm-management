# Remei Farm OS - Laravel

A comprehensive farm management system for REMEI agricultural operations, combining custom farmer management features with farmOS-inspired asset tracking and activity logging.

## 🌾 Overview

Remei Farm OS is designed for managing cotton (Pamba) and oil seed (Ufuta) farming operations in Tanzania. It provides tools for farmer registration, field extension management, quality control, and inventory management.

## 📁 Project Structure

```
remei-laravel/
│
├── app/
│   ├── Models/                     # Eloquent Models
│   │   ├── User.php
│   │   │
│   │   ├── Farmers/                # Farmer Management
│   │   │   ├── Farmer.php
│   │   │   ├── FarmerGroup.php
│   │   │   └── FarmerDocument.php
│   │   │
│   │   ├── Farms/                  # Farm/Shamba Records
│   │   │   ├── Farm.php
│   │   │   ├── FarmHistory.php
│   │   │   ├── FarmBoundary.php
│   │   │   └── FarmSeason.php
│   │   │
│   │   ├── Assets/                 # farmOS-style Assets
│   │   │   ├── Asset.php           # Base asset
│   │   │   ├── LandAsset.php
│   │   │   ├── CropAsset.php
│   │   │   ├── EquipmentAsset.php
│   │   │   ├── MaterialAsset.php
│   │   │   └── GroupAsset.php
│   │   │
│   │   ├── Logs/                   # farmOS-style Activity Logs
│   │   │   ├── Log.php             # Base log
│   │   │   ├── SeedingLog.php
│   │   │   ├── InputLog.php
│   │   │   ├── ObservationLog.php
│   │   │   ├── HarvestLog.php
│   │   │   ├── ActivityLog.php
│   │   │   ├── TrainingLog.php
│   │   │   └── InspectionLog.php
│   │   │
│   │   ├── Quantities/             # Measurements
│   │   │   ├── Quantity.php
│   │   │   └── Unit.php
│   │   │
│   │   ├── Stock/                  # Inventory Management
│   │   │   ├── StockItem.php
│   │   │   ├── StockTransaction.php
│   │   │   ├── StockCategory.php
│   │   │   └── StockDistribution.php
│   │   │
│   │   ├── Training/               # Training Module
│   │   │   ├── Training.php
│   │   │   ├── TrainingAttendance.php
│   │   │   └── TrainingMaterial.php
│   │   │
│   │   ├── ICS/                    # Quality Control
│   │   │   ├── Inspection.php
│   │   │   ├── ComplianceStandard.php
│   │   │   ├── Finding.php
│   │   │   └── CorrectiveAction.php
│   │   │
│   │   └── Location/               # Geospatial
│   │       ├── Location.php
│   │       ├── Region.php
│   │       ├── District.php
│   │       └── Village.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/               # Authentication
│   │   │   │   ├── LoginController.php
│   │   │   │   └── LogoutController.php
│   │   │   │
│   │   │   ├── Dashboard/          # Role-based Dashboards
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── SupervisorDashboardController.php
│   │   │   │   ├── ExtensionDashboardController.php
│   │   │   │   ├── ICSDashboardController.php
│   │   │   │   ├── StockDashboardController.php
│   │   │   │   ├── AccountantDashboardController.php
│   │   │   │   ├── TrainingDashboardController.php
│   │   │   │   ├── ProductionDashboardController.php
│   │   │   │   └── FarmerDashboardController.php
│   │   │   │
│   │   │   ├── Farmers/            # Farmer Management
│   │   │   │   ├── FarmerController.php
│   │   │   │   ├── FarmerGroupController.php
│   │   │   │   └── FarmerDocumentController.php
│   │   │   │
│   │   │   ├── Farms/              # Farm Management
│   │   │   │   ├── FarmController.php
│   │   │   │   ├── FarmHistoryController.php
│   │   │   │   └── FarmSeasonController.php
│   │   │   │
│   │   │   ├── Assets/             # Asset Management
│   │   │   │   ├── AssetController.php
│   │   │   │   ├── LandAssetController.php
│   │   │   │   ├── CropAssetController.php
│   │   │   │   ├── EquipmentAssetController.php
│   │   │   │   └── MaterialAssetController.php
│   │   │   │
│   │   │   ├── Logs/               # Activity Logs
│   │   │   │   ├── LogController.php
│   │   │   │   ├── SeedingLogController.php
│   │   │   │   ├── InputLogController.php
│   │   │   │   ├── ObservationLogController.php
│   │   │   │   ├── HarvestLogController.php
│   │   │   │   └── InspectionLogController.php
│   │   │   │
│   │   │   ├── Stock/              # Stock Management
│   │   │   │   ├── StockController.php
│   │   │   │   ├── StockTransactionController.php
│   │   │   │   └── StockDistributionController.php
│   │   │   │
│   │   │   ├── Training/           # Training
│   │   │   │   ├── TrainingController.php
│   │   │   │   └── AttendanceController.php
│   │   │   │
│   │   │   ├── ICS/                # Quality Control
│   │   │   │   ├── InspectionController.php
│   │   │   │   ├── FindingController.php
│   │   │   │   └── CorrectiveActionController.php
│   │   │   │
│   │   │   ├── Reports/            # Reports
│   │   │   │   ├── FarmerReportController.php
│   │   │   │   ├── YieldReportController.php
│   │   │   │   ├── StockReportController.php
│   │   │   │   └── ComplianceReportController.php
│   │   │   │
│   │   │   └── Map/                # Mapping
│   │   │       └── MapController.php
│   │   │
│   │   ├── Requests/               # Form Validation
│   │   │   ├── Farmers/
│   │   │   ├── Farms/
│   │   │   ├── Assets/
│   │   │   ├── Logs/
│   │   │   ├── Stock/
│   │   │   └── ICS/
│   │   │
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php
│   │       └── CheckPermission.php
│   │
│   ├── Services/                   # Business Logic Layer
│   │   ├── Farmers/
│   │   │   └── FarmerService.php
│   │   ├── Farms/
│   │   │   └── FarmService.php
│   │   ├── Assets/
│   │   │   └── AssetService.php
│   │   ├── Logs/
│   │   │   └── LogService.php
│   │   ├── Stock/
│   │   │   └── StockService.php
│   │   ├── Training/
│   │   │   └── TrainingService.php
│   │   ├── ICS/
│   │   │   └── InspectionService.php
│   │   ├── Reports/
│   │   │   └── ReportService.php
│   │   └── Geo/
│   │       └── GeoService.php
│   │
│   ├── Enums/                      # Enumerations
│   │   ├── UserRole.php
│   │   ├── AssetType.php
│   │   ├── AssetStatus.php
│   │   ├── LogType.php
│   │   ├── CropType.php
│   │   ├── StockTransactionType.php
│   │   └── InspectionStatus.php
│   │
│   ├── Traits/                     # Reusable Traits
│   │   ├── HasLocation.php
│   │   ├── HasQuantities.php
│   │   ├── HasAuditTrail.php
│   │   ├── BelongsToFarmer.php
│   │   └── BelongsToFarm.php
│   │
│   └── Observers/                  # Model Observers
│       ├── FarmerObserver.php
│       ├── FarmObserver.php
│       ├── AssetObserver.php
│       └── LogObserver.php
│
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_users_table.php
│   │   ├── 2024_01_01_000002_create_roles_permissions_tables.php
│   │   │
│   │   ├── Locations/              # Location Tables
│   │   ├── Farmers/                # Farmer Tables
│   │   ├── Farms/                  # Farm Tables
│   │   ├── Assets/                 # Asset Tables
│   │   ├── Logs/                   # Log Tables
│   │   ├── Quantities/             # Quantity Tables
│   │   ├── Stock/                  # Stock Tables
│   │   ├── Training/               # Training Tables
│   │   └── ICS/                    # ICS Tables
│   │
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RoleSeeder.php
│   │   ├── UserSeeder.php
│   │   ├── UnitSeeder.php
│   │   ├── CropTypeSeeder.php
│   │   ├── LocationSeeder.php
│   │   └── StockCategorySeeder.php
│   │
│   └── factories/
│       ├── FarmerFactory.php
│       ├── FarmFactory.php
│       └── AssetFactory.php
│
├── routes/
│   ├── web.php                     # Web Routes
│   ├── auth.php                    # Authentication Routes
│   ├── dashboard.php               # Dashboard Routes
│   ├── farmers.php                 # Farmer Routes
│   ├── farms.php                   # Farm Routes
│   ├── assets.php                  # Asset Routes
│   ├── logs.php                    # Log Routes
│   ├── stock.php                   # Stock Routes
│   ├── training.php                # Training Routes
│   ├── ics.php                     # ICS Routes
│   └── reports.php                 # Report Routes
│
├── config/
│   ├── remei.php                   # App Configuration
│   ├── assets.php                  # Asset Types Config
│   ├── logs.php                    # Log Types Config
│   ├── roles.php                   # Roles & Permissions
│   └── units.php                   # Units of Measurement
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── components/
│   │   ├── dashboard/
│   │   ├── farmers/
│   │   ├── farms/
│   │   ├── assets/
│   │   ├── logs/
│   │   ├── stock/
│   │   ├── training/
│   │   ├── ics/
│   │   ├── reports/
│   │   └── map/
│   │
│   ├── lang/
│   │   ├── en/                     # English
│   │   └── sw/                     # Swahili
│   │
│   └── js/
│       └── app.js
│
└── tests/
    ├── Feature/
    └── Unit/
```

## 🎯 Features

### Core Modules (Remei Custom)

1. **Farmer Management**
   - Farmer registration with documents
   - Farmer groups/cooperatives
   - Assignment to extension officers

2. **Farm/Shamba Records**
   - Farm registration with GPS
   - Farm history tracking
   - Seasonal records

3. **Field Extension**
   - Officer-to-farmer assignments
   - Field visits and data collection
   - Performance tracking

4. **Training Management**
   - Training sessions
   - Attendance tracking
   - Certificates

5. **ICS Quality Control**
   - Inspections
   - Compliance standards
   - Findings and corrective actions

6. **Stock Management**
   - Inventory tracking
   - Intake and distribution
   - Low stock alerts

7. **9 User Roles**
   - Admin
   - Supervisor
   - Extension Officer
   - ICS Inspector
   - Stock Manager
   - Accountant
   - Training Coordinator
   - Production Manager
   - Farmer

### farmOS-Inspired Features

1. **Asset Management**
   - Land assets (mashamba)
   - Crop assets (Pamba, Ufuta)
   - Equipment assets
   - Material assets (seeds, fertilizers)
   - Group assets

2. **Activity Logs**
   - Seeding logs
   - Input logs (fertilizer, pesticide)
   - Observation logs
   - Harvest logs
   - Activity logs
   - Training logs
   - Inspection logs

3. **Quantities & Measurements**
   - Area (hectares, acres)
   - Weight (kg, tons)
   - Volume (liters)
   - Custom units

4. **Geospatial/Mapping**
   - GPS coordinates
   - Farm boundaries (polygons)
   - Map visualization
   - Location history

### Reports

- Farmer reports
- Yield reports
- Stock reports
- Compliance reports
- Seasonal reports
- Officer performance reports

## 🔐 Roles & Permissions

| Role | Access Level |
|------|-------------|
| Admin | Full system access |
| Supervisor | Team management, data review |
| Extension Officer | Farmer data collection |
| ICS Inspector | Quality control |
| Stock Manager | Inventory management |
| Accountant | Financial data |
| Training Coordinator | Training management |
| Production Manager | Production oversight |
| Farmer | Personal data, forms |

## 🛠️ Installation

```bash
# Clone repository
cd remei-laravel

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Start server
php artisan serve
```

## 📦 Requirements

- PHP >= 8.2
- Laravel 11.x
- MySQL 8.0 / PostgreSQL 14+
- Composer

## 📄 License

Proprietary - REMEI AG

## 👥 Contact

For questions, contact the development team.
