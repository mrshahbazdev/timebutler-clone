<?php

namespace App\Observers;

use App\Models\User;
use App\Services\VacationBalanceService;

class UserObserver
{
    protected $vacationBalanceService;

    public function __construct(VacationBalanceService $vacationBalanceService)
    {
        $this->vacationBalanceService = $vacationBalanceService;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->vacation_days_per_year !== null) {
            $this->vacationBalanceService->syncUserBalance($user);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged('vacation_days_per_year') && $user->vacation_days_per_year !== null) {
            $this->vacationBalanceService->syncUserBalance($user);
        }
    }
}
