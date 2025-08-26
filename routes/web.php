<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallerController;

// Attendance dashboard
Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');

// Fetch today's attendance records (AJAX)
Route::get('/attendance', [AttendanceController::class, 'getAttendance'])->name('attendance.get');

// Store new attendance record (AJAX)
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

// Export attendance as PDF
// Supports query parameters: ?date=YYYY-MM-DD or ?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD
Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');



