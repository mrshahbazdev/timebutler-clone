<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRequest;
use App\Models\Holiday;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class TeamCalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $departmentId = $request->get('department_id');

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        $teamQuery = User::where('organization_id', $user->organization_id)
            ->where('is_active', true);

        if ($departmentId) {
            $teamQuery->where('department_id', $departmentId);
        }

        $teamMembers = $teamQuery->with('department')->orderBy('name')->get();

        $absences = AbsenceRequest::where('organization_id', $user->organization_id)
            ->whereIn('user_id', $teamMembers->pluck('id'))
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                  ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                      $q2->where('start_date', '<=', $startOfMonth)
                         ->where('end_date', '>=', $endOfMonth);
                  });
            })
            ->whereIn('status', ['approved', 'pending'])
            ->with('absenceType')
            ->get();

        $holidays = Holiday::where('organization_id', $user->organization_id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereIn('type', ['public_holiday', 'school_break'])
            ->get()
            ->keyBy(fn($h) => $h->date->format('Y-m-d'));

        $calendarData = [];
        foreach ($teamMembers as $member) {
            $memberAbsences = $absences->where('user_id', $member->id);
            $days = [];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = Carbon::create($year, $month, $d);
                $dateStr = $date->format('Y-m-d');
                $dayData = ['date' => $date, 'absence' => null, 'holiday' => null, 'is_weekend' => $date->isWeekend()];

                if (isset($holidays[$dateStr])) {
                    $dayData['holiday'] = $holidays[$dateStr];
                }

                foreach ($memberAbsences as $absence) {
                    if ($date->between($absence->start_date, $absence->end_date)) {
                        $dayData['absence'] = $absence;
                        break;
                    }
                }

                $days[$d] = $dayData;
            }

            $calendarData[] = [
                'member' => $member,
                'days' => $days,
            ];
        }

        $departments = \App\Models\Department::where('organization_id', $user->organization_id)->get();

        return view('calendar.team', compact(
            'calendarData', 'month', 'year', 'daysInMonth', 'startOfMonth', 'departments', 'departmentId'
        ));
    }

    public function pdf(Request $request)
    {
        $user = $request->user();
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $departmentId = $request->get('department_id');

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        $teamQuery = User::where('organization_id', $user->organization_id)->where('is_active', true);
        if ($departmentId) {
            $teamQuery->where('department_id', $departmentId);
        }
        $teamMembers = $teamQuery->with('department')->orderBy('name')->get();

        $absences = AbsenceRequest::where('organization_id', $user->organization_id)
            ->whereIn('user_id', $teamMembers->pluck('id'))
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                  ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                      $q2->where('start_date', '<=', $startOfMonth)->where('end_date', '>=', $endOfMonth);
                  });
            })
            ->whereIn('status', ['approved', 'pending'])
            ->with('absenceType')
            ->get();

        $holidays = Holiday::where('organization_id', $user->organization_id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereIn('type', ['public_holiday', 'school_break'])
            ->get()
            ->keyBy(fn($h) => $h->date->format('Y-m-d'));

        $calendarData = [];
        foreach ($teamMembers as $member) {
            $memberAbsences = $absences->where('user_id', $member->id);
            $days = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = Carbon::create($year, $month, $d);
                $dateStr = $date->format('Y-m-d');
                $dayData = ['date' => $date, 'absence' => null, 'holiday' => null, 'is_weekend' => $date->isWeekend()];
                if (isset($holidays[$dateStr])) {
                    $dayData['holiday'] = $holidays[$dateStr];
                }
                foreach ($memberAbsences as $absence) {
                    if ($date->between($absence->start_date, $absence->end_date)) {
                        $dayData['absence'] = $absence;
                        break;
                    }
                }
                $days[$d] = $dayData;
            }
            $calendarData[] = ['member' => $member, 'days' => $days];
        }

        $orgName = $user->organization->name ?? 'Organization';
        $monthName = $startOfMonth->translatedFormat('F Y');

        $pdf = Pdf::loadView('calendar.pdf', compact('calendarData', 'daysInMonth', 'startOfMonth', 'orgName', 'monthName'));
        $pdf->setPaper('A3', 'landscape');

        return $pdf->download("team-calendar-{$year}-{$month}.pdf");
    }
}
