<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CompanyRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TeamCalendarController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\TimeTrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('home');

// Company Registration
Route::get('/register-company', [CompanyRegistrationController::class, 'showForm'])->name('register-company');
Route::post('/register-company', [CompanyRegistrationController::class, 'register'])->name('register-company.store');

// Language switching
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Absences
    Route::get('/absences/team', [AbsenceController::class, 'managerIndex'])->name('absences.team');
    Route::resource('absences', AbsenceController::class)->except(['show']);
    Route::post('/absences/{absence}/approve', [AbsenceController::class, 'approve'])->name('absences.approve');
    Route::post('/absences/{absence}/reject', [AbsenceController::class, 'reject'])->name('absences.reject');
    Route::post('/absences/{absence}/cancel', [AbsenceController::class, 'cancel'])->name('absences.cancel');
    Route::post('/absences/{absence}/convert', [AbsenceController::class, 'convert'])->name('absences.convert');
    Route::get('/absences/{absence}/decision-pdf', [AbsenceController::class, 'decisionPdf'])->name('absences.decision-pdf');

    // Time Tracking
    Route::resource('time-tracking', TimeTrackingController::class);
    Route::post('/time-tracking/clock-in', [TimeTrackingController::class, 'clockIn'])->name('time-tracking.clock-in');
    Route::post('/time-tracking/clock-out', [TimeTrackingController::class, 'clockOut'])->name('time-tracking.clock-out');

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/team', [TeamCalendarController::class, 'index'])->name('calendar.team');
    Route::get('/calendar/team/year', [TeamCalendarController::class, 'yearOverview'])->name('calendar.team.year');
    Route::get('/calendar/team/year/pdf', [TeamCalendarController::class, 'yearPdf'])->name('calendar.team.year.pdf');
    Route::get('/calendar/team/pdf', [TeamCalendarController::class, 'pdf'])->name('calendar.team.pdf');

    // Holidays
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays/import', [HolidayController::class, 'import'])->name('holidays.import');
    Route::delete('/holidays', [HolidayController::class, 'destroy'])->name('holidays.destroy');

    // Overtime
    Route::get('/overtime', [OvertimeController::class, 'index'])->name('overtime.index');
    Route::get('/overtime/admin', [OvertimeController::class, 'admin'])->name('overtime.admin');
    Route::post('/overtime/adjustment', [OvertimeController::class, 'storeAdjustment'])->name('overtime.store-adjustment');
    Route::post('/overtime/bulk', [OvertimeController::class, 'bulkStore'])->name('overtime.bulk-store');

    // Employees
    Route::resource('employees', EmployeeController::class);

    // Departments
    Route::resource('departments', DepartmentController::class)->except(['show']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/absences', [ReportController::class, 'absences'])->name('reports.absences');
    Route::get('/reports/absences/pdf', [ReportController::class, 'absencesPdf'])->name('reports.absences.pdf');
    Route::get('/reports/absences/excel', [ReportController::class, 'absencesExcel'])->name('reports.absences.excel');
    Route::get('/reports/time-tracking', [ReportController::class, 'timeTracking'])->name('reports.time-tracking');
    Route::get('/reports/time-tracking/pdf', [ReportController::class, 'timeTrackingPdf'])->name('reports.time-tracking.pdf');
    Route::get('/reports/time-tracking/excel', [ReportController::class, 'timeTrackingExcel'])->name('reports.time-tracking.excel');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', function (string $id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? '/dashboard');
    })->name('notifications.mark-read');
});

require __DIR__.'/auth.php';
