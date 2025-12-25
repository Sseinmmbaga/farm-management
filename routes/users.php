<?php

use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,supervisor'])->group(function () {
    // User CRUD
    Route::resource('users', UserController::class);

    // Additional user actions
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status');

    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');

    Route::get('users/{user}/activity', [UserController::class, 'activity'])
        ->name('users.activity');

    // Bulk actions
    Route::post('users-bulk-action', [UserController::class, 'bulkAction'])
        ->name('users.bulk-action');

    // Export
    Route::get('users-export', [UserController::class, 'export'])
        ->name('users.export');
});
