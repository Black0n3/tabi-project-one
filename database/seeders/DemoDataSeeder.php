<?php

namespace Database\Seeders;

use App\Enums\BuildingType;
use App\Enums\ProjectStatus;
use App\Enums\UnitStatus;
use App\Enums\UnitType;
use App\Models\Investor;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed a realistic Investitor -> Projekt -> Objekat -> Kat -> Jedinica -> Prostorija
     * hijerarhija for local development and demoing the admin/investor panels.
     */
    public function run(): void
    {
        $investorUser = User::where('email', 'investitor@tabi.hr')->first();

        if (! $investorUser) {
            return;
        }

        $investor = Investor::firstOrCreate(
            ['user_id' => $investorUser->id],
            [
                'company_name' => 'Sunčani Vrt d.o.o.',
                'oib' => '12345678901',
                'contact_phone' => '+385 1 234 5678',
                'contact_email' => 'info@suncanivrt.hr',
            ]
        );

        $project = $investor->projects()->create([
            'name' => 'Rezidencija Sunčani Vrt',
            'description' => 'Suvremeno naselje od tri zgrade s uređenim okolišem, u mirnom dijelu grada.',
            'location' => 'Zagreb',
            'status' => ProjectStatus::InProgress,
            'is_featured' => true,
        ]);

        $building = $project->buildings()->create([
            'name' => 'Zgrada A',
            'type' => BuildingType::Zgrada,
            'address' => 'Sunčana ulica 1, Zagreb',
        ]);

        $floorsData = [
            ['label' => 'Prizemlje', 'order' => 0],
            ['label' => '1. kat', 'order' => 1],
            ['label' => '2. kat', 'order' => 2],
        ];

        $unitsData = [
            [
                'code' => 'A1', 'area_m2' => 45.5, 'price' => 145000, 'status' => UnitStatus::Dostupno,
                'is_featured' => true,
                'description' => 'Jednosoban stan s balkonom, orijentacija jug.',
                'rooms' => [
                    ['name' => 'Dnevni boravak s kuhinjom', 'area_m2' => 24],
                    ['name' => 'Spavaća soba', 'area_m2' => 13],
                    ['name' => 'Kupaonica', 'area_m2' => 4.5],
                    ['name' => 'Balkon', 'area_m2' => 4],
                ],
            ],
            [
                'code' => 'A2', 'area_m2' => 62, 'price' => 198000, 'status' => UnitStatus::Rezervirano,
                'is_featured' => false,
                'description' => 'Dvosoban stan s pogledom na park.',
                'rooms' => [
                    ['name' => 'Dnevni boravak s kuhinjom', 'area_m2' => 28],
                    ['name' => 'Spavaća soba 1', 'area_m2' => 14],
                    ['name' => 'Spavaća soba 2', 'area_m2' => 11],
                    ['name' => 'Kupaonica', 'area_m2' => 5],
                    ['name' => 'Balkon', 'area_m2' => 4],
                ],
            ],
            [
                'code' => 'A3', 'area_m2' => 78, 'price' => 245000, 'status' => UnitStatus::Prodano,
                'is_featured' => false,
                'description' => 'Trosoban stan na najvišem katu s krovnom terasom.',
                'rooms' => [
                    ['name' => 'Dnevni boravak s kuhinjom', 'area_m2' => 32],
                    ['name' => 'Spavaća soba 1', 'area_m2' => 15],
                    ['name' => 'Spavaća soba 2', 'area_m2' => 12],
                    ['name' => 'Spavaća soba 3', 'area_m2' => 10],
                    ['name' => 'Kupaonica', 'area_m2' => 6],
                    ['name' => 'Terasa', 'area_m2' => 12],
                ],
            ],
        ];

        foreach ($floorsData as $index => $floorData) {
            $floor = $building->floors()->create($floorData);

            $unit = $unitsData[$index];
            $rooms = $unit['rooms'];
            unset($unit['rooms']);

            $createdUnit = $floor->units()->create([
                ...$unit,
                'building_id' => $building->id,
                'type' => UnitType::Stan,
            ]);

            foreach ($rooms as $room) {
                $createdUnit->rooms()->create($room);
            }
        }
    }
}
