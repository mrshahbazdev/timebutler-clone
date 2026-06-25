<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsenceDay extends Model
{
    protected $fillable = [
        'absence_request_id',
        'date',
        'deducted_days',
    ];

    protected $casts = [
        'date' => 'date',
        'deducted_days' => 'float',
    ];

    public function absenceRequest(): BelongsTo
    {
        return $this->belongsTo(AbsenceRequest::class);
    }
}
