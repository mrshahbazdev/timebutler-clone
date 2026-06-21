<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacationBalance extends Model
{
    protected $fillable = [
        'user_id', 'organization_id', 'year',
        'total_days', 'used_days', 'remaining_days',
        'carried_over_days', 'expired_days',
    ];

    protected $casts = [
        'total_days' => 'integer',
        'used_days' => 'decimal:1',
        'remaining_days' => 'decimal:1',
        'carried_over_days' => 'decimal:1',
        'expired_days' => 'decimal:1',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
