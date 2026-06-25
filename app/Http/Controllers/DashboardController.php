<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRequest;
use App\Models\TimeEntry;
use App\Models\VacationBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Auto-cancel overlapping duplicate absences for the organization
        $this->cleanupOverlappingAbsences($user->organization_id);

        $pendingRequests = AbsenceRequest::where('organization_id', $user->organization_id)
            ->where('status', 'pending')
            ->when(!$user->can('view_all_absences'), function ($query) use ($user) {
                $query->whereHas('user', fn($q) => $q->where('manager_id', $user->id));
            })
            ->count();

        $todayAbsences = AbsenceRequest::where('organization_id', $user->organization_id)
            ->where('status', 'approved')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->with(['user', 'absenceType'])
            ->get();

        app(\App\Services\VacationBalanceService::class)->syncUserBalance($user);
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

    /**
     * Detect and cancel overlapping absences per user.
     * Keeps the earliest-created entry, cancels later duplicates.
     */
    private function cleanupOverlappingAbsences(int $organizationId): void
    {
        $absences = AbsenceRequest::where('organization_id', $organizationId)
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('user_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('user_id');

        foreach ($absences as $userAbsences) {
            $kept = collect();

            foreach ($userAbsences as $absence) {
                $hasOverlap = $kept->contains(function ($existing) use ($absence) {
                    return $absence->start_date->lte($existing->end_date)
                        && $absence->end_date->gte($existing->start_date);
                });

                if ($hasOverlap) {
                    $absence->update(['status' => 'cancelled']);
                } else {
                    $kept->push($absence);
                }
            }
        }
    }
}
