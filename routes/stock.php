<?php

use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Stock\StockTransactionController;
use App\Http\Controllers\Stock\StockDistributionController;
use App\Http\Controllers\Stock\StockCategoryController;
use App\Http\Controllers\Stock\StockRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Stock/Inventory Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Stock Categories
    Route::resource('stock-categories', StockCategoryController::class);

    // Stock Items
    Route::resource('stock', StockController::class);

    Route::prefix('stock')->name('stock.')->group(function () {
        // Stock Alerts
        Route::get('alerts/low-stock', [StockController::class, 'lowStock'])->name('low-stock');
        Route::get('alerts/critical', [StockController::class, 'criticalStock'])->name('critical');
        Route::get('alerts/out-of-stock', [StockController::class, 'outOfStock'])->name('out-of-stock');

        // Stock Transactions
        Route::get('transactions/all', [StockTransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/intake', [StockTransactionController::class, 'intake'])->name('transactions.intake');
        Route::post('transactions/intake', [StockTransactionController::class, 'storeIntake'])->name('transactions.intake.store');
        Route::get('transactions/issuance', [StockTransactionController::class, 'issuance'])->name('transactions.issuance');
        Route::post('transactions/issuance', [StockTransactionController::class, 'storeIssuance'])->name('transactions.issuance.store');

        // Stock Distributions (to farmers)
        Route::get('distributions', [StockDistributionController::class, 'index'])->name('distributions.index');
        Route::get('distributions/create', [StockDistributionController::class, 'create'])->name('distributions.create');
        Route::post('distributions', [StockDistributionController::class, 'store'])->name('distributions.store');
        Route::get('distributions/{distribution}', [StockDistributionController::class, 'show'])->name('distributions.show');
        Route::get('distributions/farmer/{farmer}', [StockDistributionController::class, 'farmerDistributions'])->name('distributions.farmer');

        // Stock Requests
        Route::resource('requests', StockRequestController::class)->names([
            'index' => 'requests.index',
            'create' => 'requests.create',
            'store' => 'requests.store',
            'show' => 'requests.show',
            'edit' => 'requests.edit',
            'update' => 'requests.update',
            'destroy' => 'requests.destroy',
        ]);
        Route::post('requests/{request}/approve', [StockRequestController::class, 'approve'])->name('requests.approve');
        Route::post('requests/{request}/reject', [StockRequestController::class, 'reject'])->name('requests.reject');
        Route::post('requests/{request}/fulfill', [StockRequestController::class, 'fulfill'])->name('requests.fulfill');

        // Reports
        Route::get('reports/summary', [StockController::class, 'reportSummary'])->name('reports.summary');
        Route::get('reports/movements', [StockController::class, 'reportMovements'])->name('reports.movements');
        Route::get('reports/valuation', [StockController::class, 'reportValuation'])->name('reports.valuation');
    });
});
