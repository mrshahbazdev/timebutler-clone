<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRequest;
use App\Models\TimeEntry;
use App\Models\VacationBalance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $pendingRequests = AbsenceRequest::where('organization_id', $user->organization_id)
            ->where('status', 'pending')
            ->when(!$user->hasRole('admin'), function ($query) use ($user) {
                $query->whereHas('user', fn($q) => $q->where('manager_id', $user->id));
            })
            ->count();

        $todayAbsences = AbsenceRequest::where('organization_id', $user->organization_id)
            ->where('status', 'approved')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->with(['user', 'absenceType'])
            ->get();

        $vacationBalance = VacationBalance::where('user_id', $user->id)
            ->where('year', now()->year)
            ->first();

        // Auto-sync: ensure VacationBalance matches employee's vacation_days_per_year
        if ($vacationBalance && $vacationBalance->total_days != $user->vacation_days_per_year) {
            $diff = $user->vacation_days_per_year - $vacationBalance->total_days;
            $vacationBalance->update([
                'total_days' => $user->vacation_days_per_year,
                'remaining_days' => max(0, $vacationBalance->remaining_days + $diff),
            ]);
            $vacationBalance->refresh();
        } elseif (!$vacationBalance) {
            $vacationBalance = VacationBalance::create([
                'user_id' => $user->id,
                'organization_id' => $user->organization_id,
                'year' => now()->year,
                'total_days' => $user->vacation_days_per_year,
                'used_days' => 0,
                'remaining_days' => $user->vacation_days_per_year,
            ]);
        }

        $todayEntry = TimeEntry::where('user_id', $user->id)
            ->where('date', today())
            ->first();

        $recentRequests = AbsenceRequest::where('user_id', $user->id)
            ->with('absenceType')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'pendingRequests',
            'todayAbsences',
            'vacationBalance',
            'todayEntry',
            'recentRequests'
        ));
    }
}
