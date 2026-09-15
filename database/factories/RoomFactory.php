<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_id' => Unit::factory(),
            'name' => fake()->randomElement([
                'Dnevni boravak', 'Kuhinja', 'Spavaća soba', 'Kupaonica', 'Hodnik', 'Balkon',
            ]),
            'area_m2' => fake()->randomFloat(2, 4, 30),
            'polygon' => null,
        ];
    }
}
