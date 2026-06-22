<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRequest;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        if ($month < 1) {
            $month = 12;
            $year--;
        } elseif ($month > 12) {
            $month = 1;
            $year++;
        }

        $currentDate = Carbon::createFromDate($year, $month, 1);
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $absences = AbsenceRequest::where('user_id', $user->id)
            ->where(function ($q) use ($startOfCalendar, $endOfCalendar) {
                $q->whereBetween('start_date', [$startOfCalendar, $endOfCalendar])
                  ->orWhereBetween('end_date', [$startOfCalendar, $endOfCalendar])
                  ->orWhere(function ($q2) use ($startOfCalendar, $endOfCalendar) {
                      $q2->where('start_date', '<=', $startOfCalendar)
                          ->where('end_date', '>=', $endOfCalendar);
                  });
            })
            ->whereIn('status', ['approved', 'pending'])
            ->with('absenceType')
            ->get();

        $absenceMap = [];
        foreach ($absences as $absence) {
            $start = Carbon::parse($absence->start_date);
            $end = Carbon::parse($absence->end_date);
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $key = $d->format('Y-m-d');
                $absenceMap[$key][] = [
                    'type' => $absence->absenceType->name ?? 'Unknown',
                    'color' => $absence->absenceType->color ?? '#6b7280',
                    'status' => $absence->status,
                ];
            }
        }

        $holidays = Holiday::where('organization_id', $user->organization_id)
            ->whereBetween('date', [$startOfCalendar, $endOfCalendar])
            ->get()
            ->keyBy(fn($h) => Carbon::parse($h->date)->format('Y-m-d'));

        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        return view('calendar.index', compact(
            'currentDate', 'startOfMonth', 'endOfMonth',
            'startOfCalendar', 'endOfCalendar',
            'absenceMap', 'holidays',
            'year', 'month',
            'prevYear', 'prevMonth',
            'nextYear', 'nextMonth'
        ));
    }
}
