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
