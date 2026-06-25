<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRequest;
use App\Models\AbsenceType;
use App\Models\Holiday;
use App\Models\User;
use App\Models\VacationBalance;
use App\Notifications\AbsenceDecisionNotification;
use App\Notifications\AbsenceRequestNotification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->get('status');

        $absences = AbsenceRequest::where('user_id', $user->id)
            ->when($status, fn($q) => $q->where('status', $status))
            ->with('absenceType')
            ->latest()
            ->paginate(15);

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

        $requestedDays = AbsenceRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereHas('absenceType', fn($q) => $q->where('deducts_vacation', true))
            ->sum('total_days');

        return view('absences.index', compact('absences', 'vacationBalance', 'requestedDays'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $requestType = $request->get('type', 'request');

        $absenceTypes = AbsenceType::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $colleagues = User::where('organization_id', $user->organization_id)
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('absences.create', compact('absenceTypes', 'colleagues', 'requestType'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'absence_type_id' => 'required|exists:absence_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'half_day_start' => 'boolean',
            'half_day_end' => 'boolean',
            'substitute_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
            'request_type' => 'in:request,blocked',
        ]);

        $user = $request->user();
        $requestType = $validated['request_type'] ?? 'request';

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        // Check for overlapping absences (same user, non-cancelled/rejected)
        $overlap = AbsenceRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['start_date' => __('app.absence_overlap_error')]);
        }

        // Count weekdays excluding public holidays
        $holidayDates = Holiday::where('organization_id', $user->organization_id)
            ->where('type', 'public_holiday')
            ->whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        $totalDays = 0;
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            if ($date->isWeekend()) {
                continue;
            }
            if (in_array($date->format('Y-m-d'), $holidayDates)) {
                continue;
            }
            $totalDays++;
        }

        if ($request->boolean('half_day_start')) {
            $totalDays -= 0.5;
        }
        if ($request->boolean('half_day_end')) {
            $totalDays -= 0.5;
        }

        $status = $requestType === 'blocked' ? 'approved' : 'pending';

        $absence = AbsenceRequest::create([
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'absence_type_id' => $validated['absence_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'half_day_start' => $request->boolean('half_day_start'),
            'half_day_end' => $request->boolean('half_day_end'),
            'total_days' => $totalDays,
            'status' => $status,
            'request_type' => $requestType,
            'substitute_id' => $validated['substitute_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'approved_by' => $requestType === 'blocked' ? $user->id : null,
            'approved_at' => $requestType === 'blocked' ? now() : null,
        ]);

        if ($status === 'approved') {
            $this->updateVacationBalance($absence, $totalDays);
        }

        if ($requestType === 'request' && $user->manager) {
            try {
                $user->manager->notify(new AbsenceRequestNotification($absence));
            } catch (\Exception $e) {
                // Mail not configured, continue silently
            }
        }

        return redirect()->route('absences.index')
            ->with('success', __('app.success'));
    }

    public function cancel(AbsenceRequest $absence)
    {
        $user = auth()->user();

        if ($absence->user_id !== $user->id) {
            abort(403);
        }

        if (!in_array($absence->status, ['pending', 'approved'])) {
            return redirect()->back()->with('error', __('app.cannot_cancel'));
        }

        if ($absence->status === 'approved') {
            $this->updateVacationBalance($absence, -$absence->total_days);
        }

        $absence->update(['status' => 'cancelled']);

        return redirect()->route('absences.index')
            ->with('success', __('app.cancelled'));
    }

    public function approve(AbsenceRequest $absence)
    {
        $absence->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->updateVacationBalance($absence, $absence->total_days);

        try {
            $absence->user->notify(new AbsenceDecisionNotification($absence, 'approved'));
        } catch (\Exception $e) {
            // Mail not configured
        }

        return redirect()->back()->with('success', __('app.approved'));
    }

    public function reject(Request $request, AbsenceRequest $absence)
    {
        if ($absence->status === 'approved') {
            $this->updateVacationBalance($absence, -$absence->total_days);
        }

        $absence->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason'),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        try {
            $absence->user->notify(new AbsenceDecisionNotification($absence, 'rejected'));
        } catch (\Exception $e) {
            // Mail not configured
        }

        return redirect()->back()->with('success', __('app.rejected'));
    }

    public function managerIndex(Request $request)
    {
        $user = $request->user();

        $query = AbsenceRequest::where('organization_id', $user->organization_id);

        if (!$user->hasRole('admin')) {
            $teamMembers = User::where('manager_id', $user->id)->pluck('id');
            $query->whereIn('user_id', $teamMembers);
        }

        $absences = $query
            ->when($request->get('status'), fn($q, $s) => $q->where('status', $s))
            ->with(['user', 'absenceType'])
            ->latest()
            ->paginate(20);

        return view('absences.manager-index', compact('absences'));
    }

    public function decisionPdf(AbsenceRequest $absence)
    {
        $absence->load(['user', 'absenceType', 'approver']);
        $orgName = $absence->user->organization->name ?? 'Organization';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('absences.pdf.decision', compact('absence', 'orgName'));

        return $pdf->download("decision-{$absence->id}.pdf");
    }

    private function updateVacationBalance(AbsenceRequest $absence, float $days)
    {
        $absence->loadMissing(['absenceType', 'user']);
        
        if ($absence->absenceType && $absence->absenceType->deducts_vacation) {
            $year = \Carbon\Carbon::parse($absence->start_date)->year;
            $balance = VacationBalance::firstOrCreate(
                [
                    'user_id' => $absence->user_id,
                    'year' => $year,
                ],
                [
                    'organization_id' => $absence->organization_id,
                    'total_days' => $absence->user->vacation_days_per_year ?? 0,
                    'used_days' => 0,
                    'remaining_days' => $absence->user->vacation_days_per_year ?? 0,
                ]
            );

            $balance->used_days += $days;
            $balance->remaining_days -= $days;
            $balance->save();
        }
    }
}
