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
            $diff = $user->vacation_days_per_year - $balance->total_days;
            $balance->update([
                'total_days' => $user->vacation_days_per_year,
                'remaining_days' => max(0, $balance->remaining_days + $diff),
            ]);
        }
    }

    /**
     * Deduct or refund days from a user's vacation balance.
     */
    public function adjustBalanceForAbsence(AbsenceRequest $absence, float $days)
    {
        $absence->loadMissing(['absenceType', 'user']);
        
        if ($absence->absenceType && $absence->absenceType->deducts_vacation) {
            $year = Carbon::parse($absence->start_date)->year;
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
