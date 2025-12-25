<?php

use App\Http\Controllers\Settings\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Settings Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function () {

    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::post('/general', [SettingsController::class, 'updateGeneral'])->name('update.general');
    Route::post('/farm', [SettingsController::class, 'updateFarm'])->name('update.farm');
    Route::post('/certification', [SettingsController::class, 'updateCertification'])->name('update.certification');
    Route::post('/notifications', [SettingsController::class, 'updateNotifications'])->name('update.notifications');

});
