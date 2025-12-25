<?php

use App\Http\Controllers\Training\TrainingController;
use App\Http\Controllers\Training\AttendanceController;
use App\Http\Controllers\Training\TrainingMaterialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Training Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Training Programs
    Route::resource('training-programs', TrainingController::class);

    // Training Sessions
    Route::resource('training-sessions', TrainingController::class)->names('training');

    Route::prefix('training')->name('training.')->group(function () {
        // Session Management
        Route::get('sessions/upcoming', [TrainingController::class, 'upcoming'])->name('upcoming');
        Route::get('sessions/completed', [TrainingController::class, 'completed'])->name('completed');
        Route::post('sessions/{session}/cancel', [TrainingController::class, 'cancel'])->name('cancel');
        Route::post('sessions/{session}/complete', [TrainingController::class, 'complete'])->name('complete');

        // Attendance
        Route::get('sessions/{session}/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('sessions/{session}/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::put('sessions/{session}/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::post('sessions/{session}/attendance/bulk', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk');

        // Check-in
        Route::post('sessions/{session}/check-in/{farmer}', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('sessions/{session}/check-out/{farmer}', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');

        // Certificates
        Route::post('sessions/{session}/issue-certificates', [TrainingController::class, 'issueCertificates'])->name('issue-certificates');

        // Materials
        Route::resource('materials', TrainingMaterialController::class)->except(['index']);
        Route::get('programs/{program}/materials', [TrainingMaterialController::class, 'programMaterials'])->name('materials.program');

        // Reports
        Route::get('reports/summary', [TrainingController::class, 'reportSummary'])->name('reports.summary');
        Route::get('reports/attendance', [TrainingController::class, 'reportAttendance'])->name('reports.attendance');
        Route::get('reports/farmer/{farmer}', [TrainingController::class, 'farmerTrainingHistory'])->name('reports.farmer');
    });
});
