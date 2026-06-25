<?php

namespace App\Services;

use App\Models\User;
use App\Models\VacationBalance;
use App\Models\AbsenceRequest;
use Carbon\Carbon;

class VacationBalanceService
{
    /**
     * Ensure the user's vacation balance is initialized and matches their yearly quota.
     */
    public function syncUserBalance(User $user, int $year = null)
    {
        if ($user->vacation_days_per_year === null) {
            return;
        }

        $year = $year ?? Carbon::now()->year;

        $balance = VacationBalance::where('user_id', $user->id)
            ->where('year', $year)
            ->first();

        if (!$balance) {
            VacationBalance::create([
                'user_id' => $user->id,
                'organization_id' => $user->organization_id,
                'year' => $year,
                'total_days' => $user->vacation_days_per_year,
                'used_days' => 0,
                'remaining_days' => $user->vacation_days_per_year,
            ]);
            return;
        }

        if ($balance->total_days != $user->vacation_days_per_year) {
            $balance->total_days = $user->vacation_days_per_year;
        }

        $usedDays = \App\Models\AbsenceDay::whereHas('absenceRequest', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('status', 'approved')
              ->whereHas('absenceType', fn($t) => $t->where('deducts_vacation', true));
        })
        ->whereYear('date', $year)
        ->sum('deducted_days');

        $balance->used_days = $usedDays;
        $balance->remaining_days = max(0, $balance->total_days - $usedDays);
        $balance->save();
    }

    /**
     * Deduct or refund days from a user's vacation balance.
     */
    public function adjustBalanceForAbsence(AbsenceRequest $absence, float $days)
    {
        $absence->loadMissing(['absenceType', 'user']);
        
        if ($absence->absenceType && $absence->absenceType->deducts_vacation) {
            $year = Carbon::parse($absence->start_date)->year;
            $this->syncUserBalance($absence->user, $year);
            
            // If the absence spans multiple years, sync the other years as well
            $endYear = Carbon::parse($absence->end_date)->year;
            if ($endYear !== $year) {
                for ($y = $year + 1; $y <= $endYear; $y++) {
                    $this->syncUserBalance($absence->user, $y);
                }
            }
        }
    }
}
