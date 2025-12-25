<?php

use App\Http\Controllers\Logs\LogController;
use App\Http\Controllers\Logs\SeedingLogController;
use App\Http\Controllers\Logs\InputLogController;
use App\Http\Controllers\Logs\ObservationLogController;
use App\Http\Controllers\Logs\HarvestLogController;
use App\Http\Controllers\Logs\InspectionLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Activity Log Routes (farmOS-style)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Activity Logs CRUD
    Route::resource('logs', LogController::class);

    // Log type-specific views
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('type/seeding', [LogController::class, 'seeding'])->name('seeding');
        Route::get('type/inputs', [LogController::class, 'inputs'])->name('inputs');
        Route::get('type/observations', [LogController::class, 'observations'])->name('observations');
        Route::get('type/harvests', [LogController::class, 'harvests'])->name('harvests');

        // Quick Entry
        Route::get('quick-entry', [LogController::class, 'quickEntry'])->name('quick-entry');
        Route::post('quick-entry', [LogController::class, 'storeQuickEntry'])->name('quick-entry.store');
    });

    // Seeding Logs
    Route::resource('seeding-logs', SeedingLogController::class)->except(['index']);

    // Input Logs
    Route::resource('input-logs', InputLogController::class)->except(['index']);

    // Observation Logs
    Route::resource('observation-logs', ObservationLogController::class)->except(['index']);

    // Harvest Logs
    Route::resource('harvest-logs', HarvestLogController::class)->except(['index']);

    // Inspection Logs (linked to ICS)
    Route::resource('inspection-logs', InspectionLogController::class)->except(['index']);
});
