<?php

namespace Database\Factories;

use App\Models\Investor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Investor>
 */
class InvestorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->investor(),
            'company_name' => fake()->company(),
            'oib' => fake()->numerify('###########'),
            'contact_phone' => fake()->phoneNumber(),
            'contact_email' => fake()->companyEmail(),
            'logo_path' => null,
        ];
    }
}
