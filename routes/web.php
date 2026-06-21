<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimeTrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Language switching
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Absences
    Route::resource('absences', AbsenceController::class);
    Route::post('/absences/{absence}/approve', [AbsenceController::class, 'approve'])->name('absences.approve');
    Route::post('/absences/{absence}/reject', [AbsenceController::class, 'reject'])->name('absences.reject');

    // Time Tracking
    Route::resource('time-tracking', TimeTrackingController::class);
    Route::post('/time-tracking/clock-in', [TimeTrackingController::class, 'clockIn'])->name('time-tracking.clock-in');
    Route::post('/time-tracking/clock-out', [TimeTrackingController::class, 'clockOut'])->name('time-tracking.clock-out');

    // Calendar
    Route::get('/calendar', function () {
        return view('calendar.index');
    })->name('calendar');

    // Overtime
    Route::get('/overtime', function () {
        return view('overtime.index');
    })->name('overtime.index');

    // Employees
    Route::get('/employees', function () {
        return view('employees.index');
    })->name('employees.index');

    // Departments
    Route::get('/departments', function () {
        return view('departments.index');
    })->name('departments.index');

    // Reports
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

    // Settings
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
