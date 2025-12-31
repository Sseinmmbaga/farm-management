<?php

use App\Http\Controllers\LeaveRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Leave Requests Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::resource('leave-requests', LeaveRequestController::class)->names([
        'index' => 'leave-requests.index',
        'create' => 'leave-requests.create',
        'store' => 'leave-requests.store',
        'show' => 'leave-requests.show',
        'edit' => 'leave-requests.edit',
        'update' => 'leave-requests.update',
        'destroy' => 'leave-requests.destroy',
    ]);

    // Additional actions
    Route::post('leave-requests/{leave_request}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{leave_request}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::post('leave-requests/{leave_request}/cancel', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');
    Route::post('leave-requests/{leave_request}/markAsTaken', [LeaveRequestController::class, 'markAsTaken'])->name('leave-requests.markAsTaken');
});