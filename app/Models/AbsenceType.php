<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AbsenceType extends Model
{
    protected $fillable = [
        'organization_id', 'name_en', 'name_de', 'color', 'icon',
        'requires_approval', 'requires_certificate', 'is_paid',
        'deducts_vacation', 'max_days_per_year', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'requires_certificate' => 'boolean',
        'is_paid' => 'boolean',
        'deducts_vacation' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function absenceRequests(): HasMany
    {
        return $this->hasMany(AbsenceRequest::class);
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'de' ? $this->name_de : $this->name_en;
    }
}
