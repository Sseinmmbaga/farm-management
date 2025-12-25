<?php

use App\Http\Controllers\ICS\InspectionController;
use App\Http\Controllers\ICS\FindingController;
use App\Http\Controllers\ICS\CorrectiveActionController;
use App\Http\Controllers\ICS\ComplianceController;
use App\Http\Controllers\ICS\ChecklistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ICS (Internal Control System) Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Compliance Standards
    Route::resource('compliance-standards', ComplianceController::class);

    // Inspection Checklists
    Route::resource('checklists', ChecklistController::class);
    Route::prefix('checklists/{checklist}')->name('checklists.')->group(function () {
        Route::post('items', [ChecklistController::class, 'storeItem'])->name('items.store');
        Route::put('items/{item}', [ChecklistController::class, 'updateItem'])->name('items.update');
        Route::delete('items/{item}', [ChecklistController::class, 'destroyItem'])->name('items.destroy');
        Route::post('items/reorder', [ChecklistController::class, 'reorderItems'])->name('items.reorder');
    });

    // Inspections
    Route::resource('inspections', InspectionController::class);
    Route::prefix('inspections')->name('inspections.')->group(function () {
        // Scheduled/Pending
        Route::get('status/scheduled', [InspectionController::class, 'scheduled'])->name('scheduled');
        Route::get('status/in-progress', [InspectionController::class, 'inProgress'])->name('in-progress');
        Route::get('status/completed', [InspectionController::class, 'completed'])->name('completed');

        // Inspection Actions
        Route::post('{inspection}/start', [InspectionController::class, 'start'])->name('start');
        Route::post('{inspection}/complete', [InspectionController::class, 'complete'])->name('complete');
        Route::post('{inspection}/cancel', [InspectionController::class, 'cancel'])->name('cancel');

        // Responses
        Route::post('{inspection}/responses', [InspectionController::class, 'storeResponses'])->name('responses.store');
        Route::put('{inspection}/responses', [InspectionController::class, 'updateResponses'])->name('responses.update');

        // Sign-off
        Route::post('{inspection}/sign', [InspectionController::class, 'sign'])->name('sign');

        // Schedule Follow-up
        Route::post('{inspection}/schedule-followup', [InspectionController::class, 'scheduleFollowUp'])->name('schedule-followup');
    });

    // Findings
    Route::resource('findings', FindingController::class);
    Route::prefix('findings')->name('findings.')->group(function () {
        Route::get('status/open', [FindingController::class, 'open'])->name('open');
        Route::get('status/in-progress', [FindingController::class, 'inProgress'])->name('in-progress');
        Route::get('status/resolved', [FindingController::class, 'resolved'])->name('resolved');
        Route::post('{finding}/resolve', [FindingController::class, 'resolve'])->name('resolve');
    });

    // Corrective Actions
    Route::resource('corrective-actions', CorrectiveActionController::class);
    Route::prefix('corrective-actions')->name('corrective-actions.')->group(function () {
        Route::post('{action}/complete', [CorrectiveActionController::class, 'complete'])->name('complete');
        Route::post('{action}/verify', [CorrectiveActionController::class, 'verify'])->name('verify');
    });

    // Farmer Certifications
    Route::prefix('certifications')->name('certifications.')->group(function () {
        Route::get('/', [ComplianceController::class, 'certifications'])->name('index');
        Route::get('farmer/{farmer}', [ComplianceController::class, 'farmerCertifications'])->name('farmer');
        Route::post('farmer/{farmer}/update-status', [ComplianceController::class, 'updateCertificationStatus'])->name('update-status');
    });

    // ICS Reports
    Route::prefix('ics-reports')->name('ics-reports.')->group(function () {
        Route::get('summary', [InspectionController::class, 'reportSummary'])->name('summary');
        Route::get('compliance', [InspectionController::class, 'reportCompliance'])->name('compliance');
        Route::get('findings', [FindingController::class, 'report'])->name('findings');
        Route::get('inspector/{inspector}', [InspectionController::class, 'inspectorReport'])->name('inspector');
    });
});
