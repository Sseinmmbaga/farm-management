<?php

use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\SupervisorDashboardController;
use App\Http\Controllers\Dashboard\ExtensionDashboardController;
use App\Http\Controllers\Dashboard\ICSDashboardController;
use App\Http\Controllers\Dashboard\StockDashboardController;
use App\Http\Controllers\Dashboard\AccountantDashboardController;
use App\Http\Controllers\Dashboard\TrainingDashboardController;
use App\Http\Controllers\Dashboard\ProductionDashboardController;
use App\Http\Controllers\Dashboard\FarmerDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {

    // Admin Dashboard
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin');
        Route::get('/admin/statistics', [AdminDashboardController::class, 'statistics'])->name('admin.statistics');
    });

    // Supervisor Dashboard
    Route::middleware(['role:supervisor'])->group(function () {
        Route::get('/supervisor', [SupervisorDashboardController::class, 'index'])->name('supervisor');
        Route::get('/supervisor/team', [SupervisorDashboardController::class, 'team'])->name('supervisor.team');
        Route::get('/supervisor/data-review', [SupervisorDashboardController::class, 'dataReview'])->name('supervisor.data-review');
    });

    // Extension Officer Dashboard
    Route::middleware(['role:extension_officer'])->group(function () {
        Route::get('/extension', [ExtensionDashboardController::class, 'index'])->name('extension');
        Route::get('/extension/my-farmers', [ExtensionDashboardController::class, 'myFarmers'])->name('extension.my-farmers');
        Route::get('/extension/data-collection', [ExtensionDashboardController::class, 'dataCollection'])->name('extension.data-collection');
    });

    // ICS Inspector Dashboard
    Route::middleware(['role:ics_inspector'])->group(function () {
        Route::get('/ics', [ICSDashboardController::class, 'index'])->name('ics');
        Route::get('/ics/inspections', [ICSDashboardController::class, 'inspections'])->name('ics.inspections');
        Route::get('/ics/findings', [ICSDashboardController::class, 'findings'])->name('ics.findings');
    });

    // Stock Manager Dashboard
    Route::middleware(['role:stock_manager'])->group(function () {
        Route::get('/stock', [StockDashboardController::class, 'index'])->name('stock');
        Route::get('/stock/inventory', [StockDashboardController::class, 'inventory'])->name('stock.inventory');
        Route::get('/stock/alerts', [StockDashboardController::class, 'alerts'])->name('stock.alerts');
        Route::get('/stock/distributions', [StockDashboardController::class, 'distributions'])->name('stock.distributions');
        Route::get('/stock/requests', [StockDashboardController::class, 'requests'])->name('stock.requests');
    });

    // Accountant Dashboard
    Route::middleware(['role:accountant'])->group(function () {
        Route::get('/accountant', [AccountantDashboardController::class, 'index'])->name('accountant');
        Route::get('/accountant/payments', [AccountantDashboardController::class, 'payments'])->name('accountant.payments');
        Route::get('/accountant/reports', [AccountantDashboardController::class, 'reports'])->name('accountant.reports');
    });

    // Training Coordinator Dashboard
    Route::middleware(['role:training_coordinator'])->group(function () {
        Route::get('/training', [TrainingDashboardController::class, 'index'])->name('training');
        Route::get('/training/trainings', [TrainingDashboardController::class, 'trainings'])->name('training.trainings');
        Route::get('/training/calendar', [TrainingDashboardController::class, 'calendar'])->name('training.calendar');
    });

    // Production Manager Dashboard
    Route::middleware(['role:production_manager'])->group(function () {
        Route::get('/production', [ProductionDashboardController::class, 'index'])->name('production');
        Route::get('/production/plans', [ProductionDashboardController::class, 'plans'])->name('production.plans');
        Route::get('/production/harvests', [ProductionDashboardController::class, 'harvests'])->name('production.harvests');
    });

    // Farmer Dashboard
    Route::middleware(['role:farmer'])->group(function () {
        Route::get('/farmer', [FarmerDashboardController::class, 'index'])->name('farmer');
        Route::get('/farmer/my-farms', [FarmerDashboardController::class, 'myFarms'])->name('farmer.my-farms');
        Route::get('/farmer/payments', [FarmerDashboardController::class, 'payments'])->name('farmer.payments');
        Route::get('/farmer/trainings', [FarmerDashboardController::class, 'myTrainings'])->name('farmer.trainings');
        Route::get('/farmer/distributions', [FarmerDashboardController::class, 'myDistributions'])->name('farmer.distributions');
        Route::get('/farmer/service-requests', [FarmerDashboardController::class, 'serviceRequests'])->name('farmer.service-requests');
        Route::get('/farmer/profile', [FarmerDashboardController::class, 'viewProfile'])->name('farmer.profile.view');
        Route::get('/farmer/profile/edit', [FarmerDashboardController::class, 'editProfile'])->name('farmer.profile.edit');
        Route::get('/farmer/farm-records', [FarmerDashboardController::class, 'farmRecords'])->name('farmer.farm-records');
        Route::get('/farmer/activity-log', [FarmerDashboardController::class, 'myActivityLog'])->name('farmer.activity-log');
        Route::get('/farmer/harvest-history', [FarmerDashboardController::class, 'harvestHistory'])->name('farmer.harvest-history');
        Route::get('/farmer/certificates', [FarmerDashboardController::class, 'certificates'])->name('farmer.certificates');
        Route::get('/farmer/seeds-received', [FarmerDashboardController::class, 'seedsReceived'])->name('farmer.seeds-received');
    });

    // Redirect based on role
    Route::get('/', function () {
        return redirect()->route(auth()->user()->getDashboardRoute());
    })->name('home');
});

