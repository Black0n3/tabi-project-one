<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Floor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Floor>
 */
class FloorFactory extends Factory
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
            'label' => 'Kat '.fake()->numberBetween(1, 5),
            'order' => fake()->numberBetween(0, 5),
            'polygon' => null,
            'floor_plan_image' => null,
        ];
    }
}
