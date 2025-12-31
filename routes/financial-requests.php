<?php

use App\Http\Controllers\FinancialRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Financial Requests Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::resource('financial-requests', FinancialRequestController::class)->names([
        'index' => 'financial-requests.index',
        'create' => 'financial-requests.create',
        'store' => 'financial-requests.store',
        'show' => 'financial-requests.show',
        'edit' => 'financial-requests.edit',
        'update' => 'financial-requests.update',
        'destroy' => 'financial-requests.destroy',
    ]);

    // Additional actions
    Route::post('financial-requests/{financial_request}/approve', [FinancialRequestController::class, 'approve'])->name('financial-requests.approve');
    Route::post('financial-requests/{financial_request}/reject', [FinancialRequestController::class, 'reject'])->name('financial-requests.reject');
    Route::post('financial-requests/{financial_request}/cancel', [FinancialRequestController::class, 'cancel'])->name('financial-requests.cancel');
    Route::post('financial-requests/{financial_request}/disburse', [FinancialRequestController::class, 'disburse'])->name('financial-requests.disburse');
    Route::post('financial-requests/{financial_request}/recordRepayment', [FinancialRequestController::class, 'recordRepayment'])->name('financial-requests.recordRepayment');
});