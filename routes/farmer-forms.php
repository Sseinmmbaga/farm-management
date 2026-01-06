<?php

use App\Http\Controllers\Forms\FarmerFormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Farmer Forms Routes
|--------------------------------------------------------------------------
| Routes for all 12 farmer data collection forms (Form 1-13, excluding Form 7)
| Each form type is accessible based on user role permissions
*/

Route::middleware(['auth'])->group(function () {

    // Main forms listing
    Route::get('farmer-forms', [FarmerFormController::class, 'index'])->name('farmer-forms.index');

    // Select farmer first (before choosing form type)
    Route::get('farmer-forms/select-farmer', [FarmerFormController::class, 'selectFarmer'])->name('farmer-forms.select-farmer');

    // Form type selection page (after farmer is selected)
    Route::get('farmer-forms/select/{farmer}', [FarmerFormController::class, 'select'])->name('farmer-forms.select');

    // Create form routes (by form type with farmer)
    Route::get('farmer-forms/create/{farmer}/{formType}', [FarmerFormController::class, 'create'])
        ->name('farmer-forms.create')
        ->where('formType', 'form[0-9]+');

    // Store form routes (by form type)
    Route::post('farmer-forms/store/{formType}', [FarmerFormController::class, 'store'])
        ->name('farmer-forms.store')
        ->where('formType', 'form[0-9]+');

    // Show individual form
    Route::get('farmer-forms/{farmerForm}', [FarmerFormController::class, 'show'])->name('farmer-forms.show');

    // Edit form
    Route::get('farmer-forms/{farmerForm}/edit', [FarmerFormController::class, 'edit'])->name('farmer-forms.edit');

    // Update form
    Route::put('farmer-forms/{farmerForm}', [FarmerFormController::class, 'update'])->name('farmer-forms.update');

    // Delete form
    Route::delete('farmer-forms/{farmerForm}', [FarmerFormController::class, 'destroy'])->name('farmer-forms.destroy');

    // Review form (supervisors/admins only)
    Route::post('farmer-forms/{farmerForm}/review', [FarmerFormController::class, 'review'])->name('farmer-forms.review');

    // Print form
    Route::get('farmer-forms/{farmerForm}/print', [FarmerFormController::class, 'print'])->name('farmer-forms.print');
});
