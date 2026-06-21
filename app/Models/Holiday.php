<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    protected $fillable = [
        'organization_id',
        'date',
        'name',
        'name_de',
        'type',
        'federal_state',
        'year',
        'is_half_day',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_half_day' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
