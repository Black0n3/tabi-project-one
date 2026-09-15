<?php

namespace App\Models;

use App\Enums\BuildingType;
use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['project_id', 'name', 'type', 'address', 'facade_image'])]
class Building extends Model
{
    /** @use HasFactory<BuildingFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => BuildingType::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class)->orderBy('order');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
