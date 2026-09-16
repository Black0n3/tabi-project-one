<?php

namespace Database\Seeders;

use App\Enums\BuildingType;
use App\Enums\ProjectStatus;
use App\Enums\UnitStatus;
use App\Enums\UnitType;
use App\Models\Building;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Room;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    protected array $locations = [
        'Zagreb', 'Split', 'Rijeka', 'Osijek', 'Zadar', 'Pula', 'Varaždin', 'Dubrovnik',
    ];

    /**
     * Seed jedan ručno posložen Investitor -> Projekt -> Objekat -> Kat -> Jedinica -> Prostorija
     * "izlog" primjer (stabilan, uvijek isti podaci -- koristan za screenshotove/demo), plus
     * dva dodatna investitora s nasumično generiranim podacima (4-6 projekata svaki) za
     * realističnije testiranje pretrage, filtera i paginacije.
     */
    public function run(): void
    {
        $this->seedShowcaseInvestor();

        $this->seedRandomInvestor('investitor2@tabi.hr', 'Jadranka Nekretnine d.o.o.');
        $this->seedRandomInvestor('investitor3@tabi.hr', 'Kontinent Gradnja d.o.o.');
    }

    protected function seedShowcaseInvestor(): void
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

    protected function seedRandomInvestor(string $email, string $companyName): void
    {
        if (User::where('email', $email)->exists()) {
            return;
        }

        $user = User::factory()->investor()->create([
            'name' => $companyName,
            'email' => $email,
        ]);

        $investor = Investor::factory()->for($user, 'user')->create([
            'company_name' => $companyName,
        ]);

        Project::factory()
            ->for($investor)
            ->count(fake()->numberBetween(4, 6))
            ->create()
            ->each(fn (Project $project) => $this->seedProjectContent($project));
    }

    protected function seedProjectContent(Project $project): void
    {
        $project->forceFill(['location' => fake()->randomElement($this->locations)])->save();

        Building::factory()
            ->for($project)
            ->count(fake()->numberBetween(1, 3))
            ->create()
            ->each(fn (Building $building) => $this->seedBuildingContent($building));
    }

    protected function seedBuildingContent(Building $building): void
    {
        if ($building->type === BuildingType::Kuca) {
            $this->seedStandaloneUnits($building, fake()->numberBetween(1, 2), UnitType::Kuca);

            return;
        }

        $floorLabels = ['Prizemlje', '1. kat', '2. kat', '3. kat'];
        $floorCount = fake()->numberBetween(2, 4);

        for ($order = 0; $order < $floorCount; $order++) {
            $floor = $building->floors()->create([
                'label' => $floorLabels[$order] ?? ($order.'. kat'),
                'order' => $order,
            ]);

            $unitsOnFloor = fake()->numberBetween(1, 3);

            Unit::factory()
                ->for($building)
                ->count($unitsOnFloor)
                ->create(['floor_id' => $floor->id, 'type' => UnitType::Stan])
                ->each(fn ($unit) => $this->seedRoomsFor($unit));
        }

        if (fake()->boolean(25)) {
            $this->seedStandaloneUnits($building, 1, UnitType::Stan);
        }
    }

    protected function seedStandaloneUnits(Building $building, int $count, UnitType $type): void
    {
        Unit::factory()
            ->for($building)
            ->count($count)
            ->create(['floor_id' => null, 'type' => $type])
            ->each(fn ($unit) => $this->seedRoomsFor($unit));
    }

    protected function seedRoomsFor(Unit $unit): void
    {
        Room::factory()
            ->for($unit)
            ->count(fake()->numberBetween(2, 4))
            ->create();
    }
}
