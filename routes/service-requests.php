<?php

use App\Http\Controllers\ServiceRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Service Requests Routes (Farmer Self-Service Portal)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::resource('service-requests', ServiceRequestController::class)->names([
        'index' => 'service-requests.index',
        'create' => 'service-requests.create',
        'store' => 'service-requests.store',
        'show' => 'service-requests.show',
        'edit' => 'service-requests.edit',
        'update' => 'service-requests.update',
        'destroy' => 'service-requests.destroy',
    ]);

    // Additional actions
    Route::post('service-requests/{service_request}/assign', [ServiceRequestController::class, 'assign'])->name('service-requests.assign');
    Route::post('service-requests/{service_request}/resolve', [ServiceRequestController::class, 'resolve'])->name('service-requests.resolve');
    Route::post('service-requests/{service_request}/close', [ServiceRequestController::class, 'close'])->name('service-requests.close');
    Route::post('service-requests/{service_request}/cancel', [ServiceRequestController::class, 'cancel'])->name('service-requests.cancel');
});