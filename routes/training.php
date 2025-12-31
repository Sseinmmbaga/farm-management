<?php

use App\Http\Controllers\Training\TrainingController;
use App\Http\Controllers\Training\SessionController;
use App\Http\Controllers\Training\AttendanceController;
use App\Http\Controllers\Training\TrainingMaterialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Training Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Training Programs (CRUD)
    Route::resource('training-programs', TrainingController::class);

    // Training Sessions (CRUD)
    Route::resource('training-sessions', SessionController::class)->names('training.sessions');

    Route::prefix('training')->name('training.')->group(function () {
        // Session Listing Views
        Route::get('sessions/upcoming', [SessionController::class, 'upcoming'])->name('upcoming');
        Route::get('sessions/completed', [SessionController::class, 'completed'])->name('completed');

        // Session Actions
        Route::post('sessions/{session}/cancel', [SessionController::class, 'cancel'])->name('cancel');
        Route::post('sessions/{session}/complete', [SessionController::class, 'complete'])->name('complete');

        // Attendance - General listing (for sidebar navigation)
        Route::get('attendance', [AttendanceController::class, 'list'])->name('attendance.list');

        // Attendance - Session specific
        Route::get('sessions/{session}/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('sessions/{session}/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::put('sessions/{session}/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('sessions/{session}/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
        Route::post('sessions/{session}/attendance/bulk', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk');

        // Check-in/Check-out
        Route::post('sessions/{session}/check-in/{farmer}', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('sessions/{session}/check-out/{farmer}', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');

        // Certificates
        Route::post('sessions/{session}/issue-certificates', [SessionController::class, 'issueCertificates'])->name('issue-certificates');
        Route::get('certificates', [TrainingController::class, 'certificates'])->name('certificates.index');
        Route::get('certificates/{certificate}', [TrainingController::class, 'showCertificate'])->name('certificates.show');
        Route::get('certificates/{certificate}/download', [TrainingController::class, 'downloadCertificate'])->name('certificates.download');

        // Materials
        Route::resource('materials', TrainingMaterialController::class)->except(['index']);
        Route::get('programs/{program}/materials', [TrainingMaterialController::class, 'programMaterials'])->name('materials.program');

        // Reports
        Route::get('reports/summary', [TrainingController::class, 'reportSummary'])->name('reports.summary');
        Route::get('reports/attendance', [TrainingController::class, 'reportAttendance'])->name('reports.attendance');
        Route::get('reports/farmer/{farmer}', [TrainingController::class, 'farmerTrainingHistory'])->name('reports.farmer');
    });
});
