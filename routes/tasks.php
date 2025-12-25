<?php

use App\Http\Controllers\Tasks\TaskController;
use App\Http\Controllers\Tasks\LaborController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Task & Labor Management Routes
|--------------------------------------------------------------------------
| Routes for managing tasks, task assignments, and labor tracking.
| Phase 4 - Task, Labor & Equipment Management
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Task Routes
    |--------------------------------------------------------------------------
    */

    // Task listing views
    Route::get('tasks/calendar', [TaskController::class, 'calendar'])->name('tasks.calendar');
    Route::get('tasks/overdue', [TaskController::class, 'overdue'])->name('tasks.overdue');
    Route::get('tasks/my-tasks', [TaskController::class, 'myTasks'])->name('tasks.my-tasks');

    // Task CRUD
    Route::resource('tasks', TaskController::class);

    // Task actions
    Route::prefix('tasks/{task}')->name('tasks.')->group(function () {
        // Status transitions
        Route::post('start', [TaskController::class, 'start'])->name('start');
        Route::post('complete', [TaskController::class, 'complete'])->name('complete');
        Route::post('cancel', [TaskController::class, 'cancel'])->name('cancel');
        Route::post('hold', [TaskController::class, 'hold'])->name('hold');
        Route::post('resume', [TaskController::class, 'resume'])->name('resume');

        // Assignment management
        Route::post('assign', [TaskController::class, 'assign'])->name('assign');
        Route::delete('assignments/{assignment}', [TaskController::class, 'unassign'])->name('unassign');

        // Labor for task
        Route::get('labor', [LaborController::class, 'taskSummary'])->name('labor');
    });

    // API endpoint for dynamic field loading
    Route::get('api/farms/{farm}/fields', [TaskController::class, 'getFieldsForFarm'])->name('api.farms.fields');

    /*
    |--------------------------------------------------------------------------
    | Labor Routes
    |--------------------------------------------------------------------------
    */

    // Labor reports and exports
    Route::get('labor/report', [LaborController::class, 'report'])->name('labor.report');
    Route::get('labor/export', [LaborController::class, 'export'])->name('labor.export');

    // Labor CRUD
    Route::resource('labor', LaborController::class);

    // Labor actions
    Route::prefix('labor/{labor}')->name('labor.')->group(function () {
        Route::post('verify', [LaborController::class, 'verify'])->name('verify');
        Route::post('approve', [LaborController::class, 'approve'])->name('approve');
        Route::post('pay', [LaborController::class, 'markAsPaid'])->name('pay');
    });

    // Bulk actions for labor
    Route::post('labor-bulk/approve', [LaborController::class, 'bulkApprove'])->name('labor.bulk.approve');
    Route::post('labor-bulk/pay', [LaborController::class, 'bulkPay'])->name('labor.bulk.pay');

});
