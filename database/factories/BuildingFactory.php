<?php

namespace Database\Factories;

use App\Enums\BuildingType;
use App\Models\Building;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => 'Zgrada '.fake()->randomLetter(),
            'type' => fake()->randomElement(BuildingType::cases()),
            'address' => fake()->streetAddress(),
            'facade_image' => null,
        ];
    }
}
