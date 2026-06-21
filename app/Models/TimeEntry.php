<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    protected $fillable = [
        'user_id', 'organization_id', 'date', 'start_time', 'end_time',
        'break_minutes', 'total_minutes', 'project', 'category',
        'notes', 'status', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getFormattedHoursAttribute(): string
    {
        $hours = intdiv($this->total_minutes, 60);
        $minutes = $this->total_minutes % 60;
        return sprintf('%d:%02d', $hours, $minutes);
    }
}
