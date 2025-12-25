<?php

use App\Http\Controllers\Farms\FieldController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Field/Plot Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Fields CRUD (nested under farms)
    Route::prefix('farms/{farm}')->name('farms.')->group(function () {
        // Fields Index
        Route::get('fields', [FieldController::class, 'index'])->name('fields.index');
        
        // Field Create
        Route::get('fields/create', [FieldController::class, 'create'])->name('fields.create');
        Route::post('fields', [FieldController::class, 'store'])->name('fields.store');
        
        // Field Show
        Route::get('fields/{field}', [FieldController::class, 'show'])->name('fields.show');
        
        // Field Edit
        Route::get('fields/{field}/edit', [FieldController::class, 'edit'])->name('fields.edit');
        Route::put('fields/{field}', [FieldController::class, 'update'])->name('fields.update');
        Route::patch('fields/{field}', [FieldController::class, 'update']);
        
        // Field Delete
        Route::delete('fields/{field}', [FieldController::class, 'destroy'])->name('fields.destroy');
        
        // Field Boundaries
        Route::get('fields/{field}/boundaries', [FieldController::class, 'boundaries'])->name('fields.boundaries');
        Route::post('fields/{field}/boundaries', [FieldController::class, 'storeBoundaries'])->name('fields.boundaries.store');
        
        // Field History
        Route::get('fields/{field}/history', [FieldController::class, 'history'])->name('fields.history');
        
        // Field Map
        Route::get('fields/{field}/map', [FieldController::class, 'map'])->name('fields.map');
        
        // Quick Actions
        Route::post('fields/{field}/update-status', [FieldController::class, 'updateStatus'])->name('fields.update-status');
        Route::post('fields/{field}/record-harvest', [FieldController::class, 'recordHarvest'])->name('fields.record-harvest');
    });
});