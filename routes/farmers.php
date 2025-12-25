<?php

use App\Http\Controllers\Farmers\FarmerController;
use App\Http\Controllers\Farmers\FarmerGroupController;
use App\Http\Controllers\Farmers\FarmerDocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Farmer Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Farmer Approval & Assignment Routes (must be before resource routes)
    Route::get('farmers-pending', [FarmerController::class, 'pending'])->name('farmers.pending');
    Route::get('farmers-assignments', [FarmerController::class, 'assignments'])->name('farmers.assignments');
    Route::get('farmers-export', [FarmerController::class, 'export'])->name('farmers.export');
    Route::post('farmers-bulk-approve', [FarmerController::class, 'bulkApprove'])->name('farmers.bulk-approve');
    Route::post('farmers-bulk-assign', [FarmerController::class, 'bulkAssign'])->name('farmers.bulk-assign');

    // Farmer CRUD
    Route::resource('farmers', FarmerController::class);

    // Farmer sub-resources
    Route::prefix('farmers/{farmer}')->name('farmers.')->group(function () {
        Route::post('approve', [FarmerController::class, 'approve'])->name('approve');
        Route::post('reject', [FarmerController::class, 'reject'])->name('reject');
        Route::post('assign', [FarmerController::class, 'assign'])->name('assign');
        Route::get('farms', [FarmerController::class, 'farms'])->name('farms');
        Route::get('activity-logs', [FarmerController::class, 'activityLogs'])->name('activity-logs');
        Route::get('inspections', [FarmerController::class, 'inspections'])->name('inspections');
        Route::get('training', [FarmerController::class, 'training'])->name('training');
        Route::get('distributions', [FarmerController::class, 'distributions'])->name('distributions');

        // Documents
        Route::resource('documents', FarmerDocumentController::class)->except(['index', 'show']);
    });

    // Farmer Groups
    Route::resource('farmer-groups', FarmerGroupController::class);
    Route::get('farmer-groups/{farmer_group}/members', [FarmerGroupController::class, 'members'])->name('farmer-groups.members');
});
