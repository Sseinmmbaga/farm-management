<?php

use App\Http\Controllers\PerformanceReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Resource routes
    Route::resource('performance-reports', PerformanceReportController::class)
        ->except(['create', 'store', 'edit', 'update', 'destroy'])
        ->names('performance-reports');

    // Create and store (only for authorized users)
    Route::middleware(['can:create,App\Models\PerformanceReport'])->group(function () {
        Route::get('performance-reports/create', [PerformanceReportController::class, 'create'])
            ->name('performance-reports.create');
        Route::post('performance-reports', [PerformanceReportController::class, 'store'])
            ->name('performance-reports.store');
    });

    // Edit and update (only for authorized users)
    Route::middleware(['can:update,performance_report'])->group(function () {
        Route::get('performance-reports/{performance_report}/edit', [PerformanceReportController::class, 'edit'])
            ->name('performance-reports.edit');
        Route::put('performance-reports/{performance_report}', [PerformanceReportController::class, 'update'])
            ->name('performance-reports.update');
    });

    // Delete (only for authorized users)
    Route::middleware(['can:delete,performance_report'])->group(function () {
        Route::delete('performance-reports/{performance_report}', [PerformanceReportController::class, 'destroy'])
            ->name('performance-reports.destroy');
    });

    // Custom actions
    Route::post('performance-reports/{performance_report}/submit', [PerformanceReportController::class, 'submit'])
        ->name('performance-reports.submit')
        ->middleware('can:submit,performance_report');

    Route::post('performance-reports/{performance_report}/review', [PerformanceReportController::class, 'review'])
        ->name('performance-reports.review')
        ->middleware('can:review,performance_report');

    Route::post('performance-reports/{performance_report}/approve', [PerformanceReportController::class, 'approve'])
        ->name('performance-reports.approve')
        ->middleware('can:approve,performance_report');

    Route::post('performance-reports/{performance_report}/reject', [PerformanceReportController::class, 'reject'])
        ->name('performance-reports.reject')
        ->middleware('can:reject,performance_report');

    Route::get('performance-reports/{performance_report}/print', [PerformanceReportController::class, 'print'])
        ->name('performance-reports.print')
        ->middleware('can:view,performance_report');
});