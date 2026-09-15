<?php

namespace App\Models;

use Database\Factories\FloorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['building_id', 'label', 'order', 'polygon', 'floor_plan_image'])]
class Floor extends Model
{
    /** @use HasFactory<FloorFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'polygon' => 'array',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class)->orderBy('code');
    }
}
