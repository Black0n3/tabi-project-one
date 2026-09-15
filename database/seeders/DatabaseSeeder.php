<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Tabi Admin',
            'email' => 'admin@tabi.hr',
            'role' => UserRole::Admin,
        ]);

        User::factory()->create([
            'name' => 'Demo Investitor',
            'email' => 'investitor@tabi.hr',
            'role' => UserRole::Investor,
        ]);
    }
}
