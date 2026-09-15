<?php

namespace App\Models;

use App\Enums\UnitStatus;
use App\Enums\UnitType;
use Database\Factories\UnitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'building_id',
    'floor_id',
    'code',
    'type',
    'area_m2',
    'price',
    'status',
    'description',
    'floor_plan_image',
    'polygon',
    'is_featured',
])]
class Unit extends Model
{
    /** @use HasFactory<UnitFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => UnitType::class,
            'status' => UnitStatus::class,
            'area_m2' => 'decimal:2',
            'price' => 'decimal:2',
            'polygon' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
