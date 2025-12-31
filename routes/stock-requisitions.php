<?php

use App\Http\Controllers\StockRequisitionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Stock Requisitions Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::resource('stock-requisitions', StockRequisitionController::class)->names([
        'index' => 'stock-requisitions.index',
        'create' => 'stock-requisitions.create',
        'store' => 'stock-requisitions.store',
        'show' => 'stock-requisitions.show',
        'edit' => 'stock-requisitions.edit',
        'update' => 'stock-requisitions.update',
        'destroy' => 'stock-requisitions.destroy',
    ]);

    // Additional actions
    Route::post('stock-requisitions/{stock_requisition}/approve', [StockRequisitionController::class, 'approve'])->name('stock-requisitions.approve');
    Route::post('stock-requisitions/{stock_requisition}/reject', [StockRequisitionController::class, 'reject'])->name('stock-requisitions.reject');
    Route::post('stock-requisitions/{stock_requisition}/issue', [StockRequisitionController::class, 'issue'])->name('stock-requisitions.issue');
    Route::post('stock-requisitions/{stock_requisition}/cancel', [StockRequisitionController::class, 'cancel'])->name('stock-requisitions.cancel');
    Route::post('stock-requisitions/{stock_requisition}/add-item', [StockRequisitionController::class, 'addItem'])->name('stock-requisitions.addItem');
    Route::delete('stock-requisitions/{stock_requisition}/items/{item}', [StockRequisitionController::class, 'removeItem'])->name('stock-requisitions.removeItem');
});