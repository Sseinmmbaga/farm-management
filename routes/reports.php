<?php

use App\Http\Controllers\Reports\FarmerReportController;
use App\Http\Controllers\Reports\YieldReportController;
use App\Http\Controllers\Reports\StockReportController;
use App\Http\Controllers\Reports\ComplianceReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Report Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {

    // Farmer Reports
    Route::prefix('farmers')->name('farmers.')->group(function () {
        Route::get('/', [FarmerReportController::class, 'index'])->name('index');
        Route::get('by-region', [FarmerReportController::class, 'byRegion'])->name('by-region');
        Route::get('by-status', [FarmerReportController::class, 'byStatus'])->name('by-status');
        Route::get('by-certification', [FarmerReportController::class, 'byCertification'])->name('by-certification');
        Route::get('registrations', [FarmerReportController::class, 'registrations'])->name('registrations');
        Route::get('export', [FarmerReportController::class, 'export'])->name('export');
    });

    // Yield Reports
    Route::prefix('yields')->name('yields.')->group(function () {
        Route::get('/', [YieldReportController::class, 'index'])->name('index');
        Route::get('by-season', [YieldReportController::class, 'bySeason'])->name('by-season');
        Route::get('by-crop', [YieldReportController::class, 'byCrop'])->name('by-crop');
        Route::get('by-region', [YieldReportController::class, 'byRegion'])->name('by-region');
        Route::get('comparison', [YieldReportController::class, 'comparison'])->name('comparison');
        Route::get('export', [YieldReportController::class, 'export'])->name('export');
    });

    // Stock Reports
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [StockReportController::class, 'index'])->name('index');
        Route::get('inventory', [StockReportController::class, 'inventory'])->name('inventory');
        Route::get('movements', [StockReportController::class, 'movements'])->name('movements');
        Route::get('distributions', [StockReportController::class, 'distributions'])->name('distributions');
        Route::get('valuation', [StockReportController::class, 'valuation'])->name('valuation');
        Route::get('export', [StockReportController::class, 'export'])->name('export');
    });

    // Compliance Reports
    Route::prefix('compliance')->name('compliance.')->group(function () {
        Route::get('/', [ComplianceReportController::class, 'index'])->name('index');
        Route::get('inspections', [ComplianceReportController::class, 'inspections'])->name('inspections');
        Route::get('findings', [ComplianceReportController::class, 'findings'])->name('findings');
        Route::get('certifications', [ComplianceReportController::class, 'certifications'])->name('certifications');
        Route::get('export', [ComplianceReportController::class, 'export'])->name('export');
    });

    // Seasonal Reports
    Route::prefix('seasonal')->name('seasonal.')->group(function () {
        Route::get('/', function () {
            return view('reports.seasonal.index');
        })->name('index');
        Route::get('{season}', function ($season) {
            return view('reports.seasonal.show', compact('season'));
        })->name('show');
    });

    // Export all
    Route::get('export-all', function () {
        // Export comprehensive report
    })->name('export-all');
});
