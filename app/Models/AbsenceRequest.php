<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsenceRequest extends Model
{
    protected $fillable = [
        'user_id', 'organization_id', 'absence_type_id',
        'start_date', 'end_date', 'half_day_start', 'half_day_end',
        'total_days', 'status', 'substitute_id', 'notes',
        'rejection_reason', 'approved_by', 'approved_at', 'certificate_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'half_day_start' => 'boolean',
        'half_day_end' => 'boolean',
        'total_days' => 'decimal:1',
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

    public function absenceType(): BelongsTo
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function substitute(): BelongsTo
    {
        return $this->belongsTo(User::class, 'substitute_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
