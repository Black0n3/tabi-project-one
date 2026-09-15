<?php

namespace Database\Factories;

use App\Enums\UnitStatus;
use App\Enums\UnitType;
use App\Models\Building;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'floor_id' => null,
            'code' => strtoupper(fake()->bothify('?#')),
            'type' => UnitType::Stan,
            'area_m2' => fake()->randomFloat(2, 35, 120),
            'price' => fake()->randomFloat(2, 80000, 350000),
            'status' => fake()->randomElement(UnitStatus::cases()),
            'description' => fake()->sentence(),
            'floor_plan_image' => null,
            'polygon' => null,
            'is_featured' => fake()->boolean(20),
        ];
    }
}
