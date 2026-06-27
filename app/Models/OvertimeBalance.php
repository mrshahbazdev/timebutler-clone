<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeBalance extends Model
{
    protected $fillable = [
        'user_id', 'organization_id', 'year', 'month',
        'balance_minutes', 'calculated_minutes', 'adjustment_minutes', 'carry_over_minutes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function getFormattedBalanceAttribute(): string
    {
        $abs = abs($this->balance_minutes);
        $sign = $this->balance_minutes >= 0 ? '+' : '-';
        return sprintf('%s%d:%02d', $sign, intdiv($abs, 60), $abs % 60);
    }

    public function getFormattedCarryOverAttribute(): string
    {
        $abs = abs($this->carry_over_minutes);
        $sign = $this->carry_over_minutes >= 0 ? '+' : '-';
        return sprintf('%s%d:%02d', $sign, intdiv($abs, 60), $abs % 60);
    }

    public function getFormattedCalculatedAttribute(): string
    {
        $abs = abs($this->calculated_minutes);
        $sign = $this->calculated_minutes >= 0 ? '+' : '-';
        return sprintf('%s%d:%02d', $sign, intdiv($abs, 60), $abs % 60);
    }

    public function getFormattedAdjustmentAttribute(): string
    {
        $abs = abs($this->adjustment_minutes);
        $sign = $this->adjustment_minutes >= 0 ? '+' : '-';
        return sprintf('%s%d:%02d', $sign, intdiv($abs, 60), $abs % 60);
    }
}
