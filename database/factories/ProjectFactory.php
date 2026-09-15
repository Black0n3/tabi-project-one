<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Investor;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'investor_id' => Investor::factory(),
            'name' => fake()->city().' Rezidencija',
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'status' => fake()->randomElement(ProjectStatus::cases()),
            'cover_image' => null,
            'is_featured' => fake()->boolean(30),
        ];
    }
}
