<?php

use App\Http\Controllers\Notifications\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
|
| Routes for the notification center and notification management.
|
*/

Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    // Notification center
    Route::get('/', [NotificationController::class, 'index'])->name('index');

    // Notification preferences
    Route::get('/preferences', [NotificationController::class, 'preferences'])->name('preferences');
    Route::put('/preferences', [NotificationController::class, 'updatePreferences'])->name('preferences.update');

    // AJAX endpoints
    Route::get('/dropdown', [NotificationController::class, 'getDropdown'])->name('dropdown');
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');

    // Mark as read
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');

    // Delete notifications
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/clear/read', [NotificationController::class, 'deleteAllRead'])->name('delete-all-read');
});
