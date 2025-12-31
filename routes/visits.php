<?php

use App\Http\Controllers\Visits\FarmVisitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Farm Visit Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Calendar view
    Route::get('visits-calendar', [FarmVisitController::class, 'calendar'])->name('visits.calendar');

    // Export
    Route::get('visits-export', [FarmVisitController::class, 'export'])->name('visits.export');

    // Visit actions
    Route::prefix('visits/{visit}')->name('visits.')->group(function () {
        Route::post('complete', [FarmVisitController::class, 'complete'])->name('complete');
        Route::post('start', [FarmVisitController::class, 'start'])->name('start');
        Route::post('cancel', [FarmVisitController::class, 'cancel'])->name('cancel');
    });

    // Visit CRUD
    Route::resource('visits', FarmVisitController::class);
});