<?php

use App\Http\Controllers\Farms\FarmController;
use App\Http\Controllers\Farms\FarmerHistoryController;
use App\Http\Controllers\Farms\FarmHistoryController;
use App\Http\Controllers\Farms\FarmRecordController;
use App\Http\Controllers\Farms\FarmSeasonController;
use App\Http\Controllers\Farms\SeasonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Farm/Shamba Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Seasons
    Route::resource('seasons', SeasonController::class);
    Route::post('seasons/{season}/set-current', [SeasonController::class, 'setCurrent'])->name('seasons.set-current');

    // Farms CRUD
    Route::resource('farms', FarmController::class);

    // Farm sub-resources
    Route::prefix('farms/{farm}')->name('farms.')->group(function () {
        // Farm History
        Route::resource('histories', FarmHistoryController::class)->except(['index']);
        Route::get('history', [FarmController::class, 'history'])->name('history');

        // Farm Seasons
        Route::resource('seasons', FarmSeasonController::class)->except(['index']);
        Route::get('seasons-list', [FarmController::class, 'seasons'])->name('seasons-list');

        // Boundaries
        Route::get('boundaries', [FarmController::class, 'boundaries'])->name('boundaries');
        Route::post('boundaries', [FarmController::class, 'storeBoundaries'])->name('boundaries.store');

        // Activity Logs for this farm
        Route::get('logs', [FarmController::class, 'logs'])->name('logs');

        // Map view
        Route::get('map', [FarmController::class, 'map'])->name('map');
    });

    // Farm Map (all farms)
    Route::get('farms-map', [FarmController::class, 'allFarmsMap'])->name('farms.map.all');

    // Farm Records (Form 2 & Form 3)
    Route::prefix('farm-records')->name('farm-records.')->group(function () {
        // Main resource routes
        Route::get('/', [FarmRecordController::class, 'index'])->name('index');
        Route::get('/create', [FarmRecordController::class, 'create'])->name('create');
        Route::post('/', [FarmRecordController::class, 'store'])->name('store');
        Route::get('/{farmRecord}', [FarmRecordController::class, 'show'])->name('show');
        Route::get('/{farmRecord}/edit', [FarmRecordController::class, 'edit'])->name('edit');
        Route::put('/{farmRecord}', [FarmRecordController::class, 'update'])->name('update');
        Route::delete('/{farmRecord}', [FarmRecordController::class, 'destroy'])->name('destroy');

        // New Farm Records (Form 2)
        Route::get('/new/list', [FarmRecordController::class, 'indexNew'])->name('new.index');
        Route::get('/new/create', [FarmRecordController::class, 'createNew'])->name('new.create');
        Route::get('/new/history', [FarmerHistoryController::class, 'newFarmHistory'])->name('new.history');

        // Existing Farm Records (Form 3)
        Route::get('/existing/list', [FarmRecordController::class, 'indexExisting'])->name('existing.index');
        Route::get('/existing/create', [FarmRecordController::class, 'createExisting'])->name('existing.create');
        Route::get('/existing/history', [FarmerHistoryController::class, 'existingFarmHistory'])->name('existing.history');
    });

    // Farmer History
    Route::get('/farmer-history/{farmer}', [FarmerHistoryController::class, 'show'])->name('farmer-history.show');
});
