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

        $teamMembers = $teamQuery->with('department')->orderBy('name')->paginate(15)->withQueryString();

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
            ->groupBy(fn($h) => $h->date->format('Y-m-d'));

        $absenceMap = [];
        foreach ($absences as $absence) {
            $start = Carbon::parse($absence->start_date)->startOfDay();
            $end = Carbon::parse($absence->end_date)->endOfDay();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $absenceMap[$absence->user_id][$date->format('Y-m-d')] = $absence;
            }
        }

        $monthDays = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d);
            $dateStr = $date->format('Y-m-d');
            $monthDays[$d] = [
                'date' => $date,
                'dateStr' => $dateStr,
                'is_weekend' => $date->isWeekend(),
                'holidays' => isset($holidays[$dateStr]) ? $holidays[$dateStr] : collect(),
            ];
        }

        $calendarData = [];
        foreach ($teamMembers as $member) {
            $days = [];
            foreach ($monthDays as $d => $mDay) {
                $days[$d] = [
                    'date' => $mDay['date'],
                    'is_weekend' => $mDay['is_weekend'],
                    'holidays' => $mDay['holidays'],
                    'absence' => $absenceMap[$member->id][$mDay['dateStr']] ?? null,
                ];
            }

            $calendarData[] = [
                'member' => $member,
                'days' => $days,
            ];
        }

        $departments = \App\Models\Department::where('organization_id', $user->organization_id)->get();

        return view('calendar.team', compact(
            'calendarData', 'teamMembers', 'month', 'year', 'daysInMonth', 'startOfMonth', 'departments', 'departmentId'
        ));
    }

    public function yearOverview(Request $request)
    {
        $user = $request->user();
        $year = (int) $request->get('year', now()->year);
        $departmentId = $request->get('department_id');

        $startOfYear = Carbon::create($year, 1, 1);
        $endOfYear = Carbon::create($year, 12, 31);

        $teamQuery = User::where('organization_id', $user->organization_id)
            ->where('is_active', true);

        if ($departmentId) {
            $teamQuery->where('department_id', $departmentId);
        }

        $teamMembers = $teamQuery->with('department')->orderBy('name')->paginate(15)->withQueryString();

        $absences = AbsenceRequest::where('organization_id', $user->organization_id)
            ->whereIn('user_id', $teamMembers->pluck('id'))
            ->where(function ($q) use ($startOfYear, $endOfYear) {
                $q->whereBetween('start_date', [$startOfYear, $endOfYear])
                  ->orWhereBetween('end_date', [$startOfYear, $endOfYear])
                  ->orWhere(function ($q2) use ($startOfYear, $endOfYear) {
                      $q2->where('start_date', '<=', $startOfYear)
                         ->where('end_date', '>=', $endOfYear);
                  });
            })
            ->whereIn('status', ['approved', 'pending'])
            ->with('absenceType')
            ->get();

        $holidays = Holiday::where('organization_id', $user->organization_id)
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->get()
            ->groupBy(fn($h) => $h->date->format('Y-m-d'));

        $absenceMap = [];
        foreach ($absences as $absence) {
            $start = Carbon::parse($absence->start_date)->startOfDay();
            $end = Carbon::parse($absence->end_date)->endOfDay();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $absenceMap[$absence->user_id][$date->format('Y-m-d')] = $absence;
            }
        }

        // Build month-by-month data: each month has header info + per-member days
        $monthsData = [];
        for ($m = 1; $m <= 12; $m++) {
            $startOfMonth = Carbon::create($year, $m, 1);
            $daysInMonth = $startOfMonth->daysInMonth;

            // Build day headers and pre-calculate month days
            $dayHeaders = [];
            $monthDays = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = Carbon::create($year, $m, $d);
                $dateStr = $date->format('Y-m-d');
                $isWeekend = $date->isWeekend();
                
                $dayHeaders[$d] = [
                    'date' => $date,
                    'weekday' => $date->locale('de')->isoFormat('dd'),
                    'week_number' => $date->isoWeek(),
                    'is_weekend' => $isWeekend,
                    'is_today' => $date->isToday(),
                ];

                $monthDays[$d] = [
                    'dateStr' => $dateStr,
                    'is_weekend' => $isWeekend,
                    'holidays' => isset($holidays[$dateStr]) ? $holidays[$dateStr] : collect(),
                ];
            }

            // Build per-member day data
            $membersData = [];
            foreach ($teamMembers as $member) {
                $days = [];
                foreach ($monthDays as $d => $mDay) {
                    $days[$d] = [
                        'absence' => $absenceMap[$member->id][$mDay['dateStr']] ?? null,
                        'holidays' => $mDay['holidays'],
                        'is_weekend' => $mDay['is_weekend'],
                    ];
                }

                $membersData[] = [
                    'member' => $member,
                    'days' => $days,
                ];
            }

            $monthsData[$m] = [
                'name' => $startOfMonth->translatedFormat('F Y'),
                'days_in_month' => $daysInMonth,
                'day_headers' => $dayHeaders,
                'members' => $membersData,
            ];
        }

        $departments = \App\Models\Department::where('organization_id', $user->organization_id)->get();
        $absenceTypes = \App\Models\AbsenceType::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('calendar.team-year', compact('monthsData', 'teamMembers', 'year', 'departments', 'departmentId', 'absenceTypes'));
    }

    public function yearPdf(Request $request)
    {
        $user = $request->user();
        $year = (int) $request->get('year', now()->year);
        $departmentId = $request->get('department_id');

        $startOfYear = Carbon::create($year, 1, 1);
        $endOfYear = Carbon::create($year, 12, 31);

        $teamQuery = User::where('organization_id', $user->organization_id)->where('is_active', true);
        if ($departmentId) {
            $teamQuery->where('department_id', $departmentId);
        }
        $teamMembers = $teamQuery->with('department')->orderBy('name')->get();

        $absences = AbsenceRequest::where('organization_id', $user->organization_id)
            ->whereIn('user_id', $teamMembers->pluck('id'))
            ->where(function ($q) use ($startOfYear, $endOfYear) {
                $q->whereBetween('start_date', [$startOfYear, $endOfYear])
                  ->orWhereBetween('end_date', [$startOfYear, $endOfYear])
                  ->orWhere(function ($q2) use ($startOfYear, $endOfYear) {
                      $q2->where('start_date', '<=', $startOfYear)->where('end_date', '>=', $endOfYear);
                  });
            })
            ->whereIn('status', ['approved', 'pending'])
            ->with('absenceType')
            ->get();

        $holidays = Holiday::where('organization_id', $user->organization_id)
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->get()
            ->groupBy(fn($h) => $h->date->format('Y-m-d'));

        $absenceMap = [];
        foreach ($absences as $absence) {
            $start = Carbon::parse($absence->start_date)->startOfDay();
            $end = Carbon::parse($absence->end_date)->endOfDay();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $absenceMap[$absence->user_id][$date->format('Y-m-d')] = $absence;
            }
        }

        $monthsData = [];
        for ($m = 1; $m <= 12; $m++) {
            $startOfMonth = Carbon::create($year, $m, 1);
            $daysInMonth = $startOfMonth->daysInMonth;

            $dayHeaders = [];
            $monthDays = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = Carbon::create($year, $m, $d);
                $dateStr = $date->format('Y-m-d');
                $isWeekend = $date->isWeekend();
                
                $dayHeaders[$d] = [
                    'date' => $date,
                    'weekday' => $date->locale('de')->isoFormat('dd'),
                    'week_number' => $date->isoWeek(),
                    'is_weekend' => $isWeekend,
                    'is_today' => $date->isToday(),
                ];

                $monthDays[$d] = [
                    'dateStr' => $dateStr,
                    'is_weekend' => $isWeekend,
                    'holiday' => isset($holidays[$dateStr]) ? $holidays[$dateStr]->first() : null,
                ];
            }

            $membersData = [];
            foreach ($teamMembers as $member) {
                $days = [];
                foreach ($monthDays as $d => $mDay) {
                    $days[$d] = [
                        'absence' => $absenceMap[$member->id][$mDay['dateStr']] ?? null,
                        'holidays' => $mDay['holidays'],
                        'is_weekend' => $mDay['is_weekend'],
                    ];
                }
                $membersData[] = ['member' => $member, 'days' => $days];
            }

            $monthsData[$m] = [
                'name' => $startOfMonth->translatedFormat('F Y'),
                'days_in_month' => $daysInMonth,
                'day_headers' => $dayHeaders,
                'members' => $membersData,
            ];
        }

        $absenceTypes = \App\Models\AbsenceType::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $orgName = $user->organization->name ?? 'Organization';
        $departmentName = $departmentId
            ? \App\Models\Department::find($departmentId)?->name
            : null;

        $pdf = Pdf::loadView('calendar.year-pdf', compact(
            'monthsData', 'year', 'absenceTypes', 'orgName', 'departmentName'
        ));
        $pdf->setPaper('A3', 'landscape');

        return $pdf->download("team-calendar-{$year}.pdf");
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
            ->groupBy(fn($h) => $h->date->format('Y-m-d'));

        $absenceMap = [];
        foreach ($absences as $absence) {
            $start = Carbon::parse($absence->start_date)->startOfDay();
            $end = Carbon::parse($absence->end_date)->endOfDay();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $absenceMap[$absence->user_id][$date->format('Y-m-d')] = $absence;
            }
        }

        $monthDays = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($year, $month, $d);
            $dateStr = $date->format('Y-m-d');
            $monthDays[$d] = [
                'date' => $date,
                'dateStr' => $dateStr,
                'is_weekend' => $date->isWeekend(),
                'holidays' => isset($holidays[$dateStr]) ? $holidays[$dateStr] : collect(),
            ];
        }

        $calendarData = [];
        foreach ($teamMembers as $member) {
            $days = [];
            foreach ($monthDays as $d => $mDay) {
                $days[$d] = [
                    'date' => $mDay['date'],
                    'is_weekend' => $mDay['is_weekend'],
                    'holidays' => $mDay['holidays'],
                    'absence' => $absenceMap[$member->id][$mDay['dateStr']] ?? null,
                ];
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
