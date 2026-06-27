<?php

namespace App\Services;

use App\Models\User;
use App\Models\TimeEntry;
use App\Models\Holiday;
use App\Models\AbsenceDay;
use App\Models\OvertimeBalance;
use Carbon\Carbon;

class OvertimeCalculator
{
    /**
     * Calculate and update the overtime balance for a specific user and month.
     */
    public function calculateForMonth(User $user, int $year, int $month): void
    {
        // Don't calculate for future months
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $today = Carbon::today();

        // If month is in the future, return early
        if ($startOfMonth->isAfter($today)) {
            return;
        }

        $dailyTarget = ($user->weekly_hours ?? 40) / 5; // Default 5 working days assumption
        $dailyTargetMinutes = $dailyTarget * 60;

        // Ensure we only calculate up to today if it's the current month
        $lastDayToCalculate = $endOfMonth->isAfter($today) ? $today : $endOfMonth;

        $expectedMinutes = 0;
        $currentDate = $startOfMonth->copy();

        // Fetch all holidays for the organization in this month
        $holidays = Holiday::where('organization_id', $user->organization_id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $lastDayToCalculate->format('Y-m-d')])
            ->get()
            ->keyBy(fn($h) => $h->date->format('Y-m-d'));

        // Fetch all approved absences for the user in this month
        $absences = AbsenceDay::whereHas('absenceRequest', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', 'approved');
            })
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $lastDayToCalculate->format('Y-m-d')])
            ->get()
            ->keyBy(fn($a) => $a->date->format('Y-m-d'));

        // Calculate Expected Minutes
        while ($currentDate->lte($lastDayToCalculate)) {
            $dateStr = $currentDate->format('Y-m-d');
            
            // Skip weekends
            if (!$currentDate->isWeekend()) {
                $dayExpected = $dailyTargetMinutes;

                // Check for full/half day holiday
                if ($holidays->has($dateStr)) {
                    $holiday = $holidays->get($dateStr);
                    if ($holiday->is_half_day) {
                        $dayExpected -= ($dailyTargetMinutes / 2);
                    } else {
                        $dayExpected = 0;
                    }
                }

                // Check for full/half day absence (sick/vacation)
                if ($dayExpected > 0 && $absences->has($dateStr)) {
                    $absence = $absences->get($dateStr);
                    if ($absence->is_half_day) {
                        $dayExpected -= ($dailyTargetMinutes / 2);
                    } else {
                        $dayExpected = 0;
                    }
                }

                $expectedMinutes += max(0, $dayExpected);
            }
            $currentDate->addDay();
        }

        // Fetch actual worked minutes
        $actualMinutes = TimeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $lastDayToCalculate->format('Y-m-d')])
            // We count all entries, even drafts, but usually only submitted/approved matter.
            // For now, count all to match user expectation.
            ->sum('total_minutes');

        $calculatedOvertime = $actualMinutes - $expectedMinutes;

        // Update or create the OvertimeBalance record
        $balance = OvertimeBalance::firstOrNew([
            'user_id' => $user->id,
            'year' => $year,
            'month' => $month,
        ]);

        $balance->organization_id = $user->organization_id;
        $balance->calculated_minutes = $calculatedOvertime;
        // Total balance = calculated + manual adjustments
        $balance->balance_minutes = $balance->calculated_minutes + $balance->adjustment_minutes;
        $balance->save();
    }
}
