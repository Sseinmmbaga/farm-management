<?php

use App\Http\Controllers\FieldEntriesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Field Entries (Digital Forms) Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('field-entries')->name('field-entries.')->group(function () {
    
    // Field Entries Index
    Route::get('/', [FieldEntriesController::class, 'index'])->name('index');
    
    // Field Entry Create
    Route::get('/create', [FieldEntriesController::class, 'create'])->name('create');
    Route::post('/', [FieldEntriesController::class, 'store'])->name('store');
    
    // Field Entry Show
    Route::get('/{entry}', [FieldEntriesController::class, 'show'])->name('show');
    
    // Field Entry Edit
    Route::get('/{entry}/edit', [FieldEntriesController::class, 'edit'])->name('edit');
    Route::put('/{entry}', [FieldEntriesController::class, 'update'])->name('update');
    Route::patch('/{entry}', [FieldEntriesController::class, 'update']);
    
    // Field Entry Delete
    Route::delete('/{entry}', [FieldEntriesController::class, 'destroy'])->name('destroy');
    
    // Field Entry Workflow Actions
    Route::post('/{entry}/submit', [FieldEntriesController::class, 'submit'])->name('submit');
    Route::post('/{entry}/approve', [FieldEntriesController::class, 'approve'])->name('approve');
    Route::post('/{entry}/reject', [FieldEntriesController::class, 'reject'])->name('reject');
    
    // Field Entry Comments
    Route::post('/{entry}/comments', [FieldEntriesController::class, 'addComment'])->name('comments.store');
    Route::delete('/comments/{comment}', [FieldEntriesController::class, 'deleteComment'])->name('comments.destroy');
    
    // Field Entry Attachments
    Route::post('/{entry}/attachments', [FieldEntriesController::class, 'addAttachment'])->name('attachments.store');
    Route::delete('/attachments/{attachment}', [FieldEntriesController::class, 'deleteAttachment'])->name('attachments.destroy');
    
    // AJAX endpoints for form management
    Route::get('/api/form-definition', [FieldEntriesController::class, 'getFormDefinition'])->name('api.form-definition');
    Route::get('/api/form-subtypes', [FieldEntriesController::class, 'getFormSubtypes'])->name('api.form-subtypes');
});
