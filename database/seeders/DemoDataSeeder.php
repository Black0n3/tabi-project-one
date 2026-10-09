<?php

namespace Database\Seeders;

use App\Enums\BuildingType;
use App\Enums\ProjectStatus;
use App\Enums\UnitStatus;
use App\Enums\UnitType;
use App\Models\Building;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Jedan investitor ("TabarDI - Investitor") i jedan projekt -- "Plješevička".
 *
 * Svi podaci (površine, prostorije, terase) i vizuali preuzeti su iz idejnog rješenja
 * "Obiteljske zgrade P11 i P13" (A-Z Arhitektura, listopad 2023). Slike i poligoni
 * zona (katovi na fasadi, stanovi na tlocrtu kata, prostorije u stanu) nalaze se u
 * database/seeders/data/pljesevicka/ -- poligoni su u % dimenzija pripadajuće slike.
 */
class DemoDataSeeder extends Seeder
{
    protected string $dataDir;

    /** @var array{gallery: array, facades: array, plans: array} */
    protected array $polygons;

    /**
     * Etaže ponavljaju se u obje zgrade (A = Plješevička 11, B = Plješevička 13).
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $levels = [
        'pri' => [
            'floor' => 'Prizemlje', 'order' => 0, 'number' => 1, 'facade' => 'Prizemlje',
            'area' => 64.39, 'room_count' => 2,
            'description' => 'Stan u prizemlju s dnevnim boravkom, kuhinjom i blagovanjem u jednom prostoru, spavaćom sobom i kupaonicom. Uz stan idu terasa i privatni vrt (ukupno 27,32 m² vanjskog prostora), a pristup vrtu stana 2 na 1. katu vodi vanjskim stubištem.',
            'rooms' => [
                '1' => ['Ulaz', 4.66],
                '2' => ['Dnevni boravak + kuhinja + blagovanje', 34.78],
                '3' => ['Hodnik', 2.75],
                '4' => ['Kupaonica', 7.15],
                '5' => ['Spavaća soba', 13.35],
                '6' => ['Spremište', 1.70],
            ],
            'terraces' => [
                ['Terasa T1 (nenatkriveno u vrtu)', 22.07],
                ['Terasa T2 (nenatkriveno u vrtu)', 5.25],
            ],
        ],
        'k1' => [
            'floor' => '1. kat', 'order' => 1, 'number' => 2, 'facade' => 'I. kat',
            'area' => 97.46, 'room_count' => 4,
            'description' => 'Četverosobni stan na 1. katu s dnevnim boravkom i kuhinjom, spavaćom sobom, dvjema dječjim sobama i dvjema kupaonicama. Uz stan idu natkrivena lođa, terasa i vrtna terasa (ukupno 48,91 m² vanjskog prostora).',
            'rooms' => [
                '1' => ['Hodnik', 9.94],
                '2' => ['Dnevni boravak + kuhinja', 39.10],
                '3' => ['Kupaonica 1', 4.32],
                '4' => ['Spremište', 1.67],
                '5' => ['Dječja soba 2', 10.67],
                '6' => ['Dječja soba 1', 10.67],
                '7' => ['Spavaća soba', 16.56],
                '8' => ['Kupaonica 2', 4.53],
            ],
            'terraces' => [
                ['Terasa T1 (natkriveno)', 17.73],
                ['Terasa T2 (nenatkriveno)', 14.70],
                ['Terasa T3 (nenatkriveno u vrtu)', 16.48],
            ],
        ],
        'k2' => [
            'floor' => '2. kat', 'order' => 2, 'number' => 3, 'facade' => 'II. kat',
            'area' => 87.22, 'room_count' => 3,
            'description' => 'Trosoban stan na 2. katu s dnevnim boravkom i kuhinjom, spavaćom sobom, dječjom sobom i dvjema kupaonicama. Uz stan idu natkrivena lođa i terasa (ukupno 29,51 m² vanjskog prostora).',
            'rooms' => [
                '1' => ['Hodnik', 9.18],
                '2' => ['Dnevni boravak + kuhinja', 39.11],
                '3' => ['Kupaonica 1', 4.08],
                '4' => ['Spremište', 1.55],
                '5' => ['Dječja soba', 14.06],
                '6' => ['Spavaća soba', 15.17],
                '7' => ['Kupaonica 2', 4.07],
            ],
            'terraces' => [
                ['Terasa T1 (natkriveno)', 17.75],
                ['Terasa T2 (nenatkriveno)', 11.76],
            ],
        ],
    ];

    public function run(): void
    {
        $this->dataDir = __DIR__.'/data/pljesevicka';
        $this->polygons = json_decode(file_get_contents($this->dataDir.'/polygons.json'), true);

        $investorUser = User::where('email', 'investitor@tabi.hr')->first();

        if (! $investorUser) {
            return;
        }

        $investor = Investor::firstOrCreate(
            ['user_id' => $investorUser->id],
            ['company_name' => 'TabarDI - Investitor']
        );

        $this->seedPljesevicka($investor);
    }

    protected function seedPljesevicka(Investor $investor): void
    {
        $project = $investor->projects()->create([
            'name' => 'Plješevička',
            'description' => "Dvije obiteljske zgrade (Plješevička ulica 11 i 13) u Osijeku, svaka s tri stana: stan u prizemlju s privatnim vrtom te stanovi na 1. i 2. katu s lođama i terasama. Projekt je u fazi idejnog rješenja (A-Z Arhitektura, listopad 2023.).\n\n"
                ."Konstrukcija: nosivi zidovi od armiranog betona i ziđa od blok opeke, ploče od armiranog betona (polu-montažne). Pregradni zidovi su od pjenobetona, toplinska izolacija EPS/kamena vuna, a stolarija PVC/ALU s 3-slojnim staklima. Podno grijanje i hlađenje rješava toplinska dizalica zrak-voda, uz prirodnu ventilaciju i fotonaponsku elektranu od cca 11 kW. Za stanare su predviđena parkirna mjesta ispred zgrade.",
            'location' => 'Osijek, Plješevička ulica',
            'status' => ProjectStatus::Planned,
            'is_featured' => true,
            'cover_image' => $this->copyAsset('cover.webp', 'projects/covers/pljesevicka-cover.webp'),
            'gallery' => collect($this->polygons['gallery'])->map(fn (array $item) => [
                'path' => $this->copyAsset($item['file'], 'projects/gallery/pljesevicka-'.$item['file']),
                'caption' => $item['caption'],
            ])->all(),
        ]);

        foreach ([
            ['house' => '11', 'suffix' => 'A', 'plan' => 'A'],
            ['house' => '13', 'suffix' => 'B', 'plan' => 'B'],
        ] as $def) {
            $building = $project->buildings()->create([
                'name' => 'Plješevička '.$def['house'],
                'type' => BuildingType::Zgrada,
                'address' => 'Plješevička ulica '.$def['house'].', Osijek',
                'facade_image' => $this->copyAsset("facade-{$def['house']}.webp", "buildings/facades/pljesevicka-{$def['house']}.webp"),
            ]);

            foreach ($this->levels as $levelKey => $level) {
                $this->seedFloor($building, $def, $levelKey, $level);
            }
        }
    }

    protected function seedFloor(Building $building, array $def, string $levelKey, array $level): void
    {
        $planName = $def['plan'].'_'.$levelKey;
        $plan = $this->polygons['plans'][$planName];
        $code = $level['number'].$def['suffix'];

        $floor = $building->floors()->create([
            'label' => $level['floor'],
            'order' => $level['order'],
            'polygon' => $this->polygons['facades'][$def['house']][$level['facade']],
            'floor_plan_image' => $this->copyAsset("plan-{$planName}.webp", "floors/plans/pljesevicka-{$def['house']}-{$levelKey}.webp"),
        ]);

        $unit = Unit::create([
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'code' => $code,
            'type' => UnitType::Stan,
            'area_m2' => $level['area'],
            'room_count' => $level['room_count'],
            'status' => UnitStatus::Dostupno,
            'description' => $level['description'],
            'polygon' => $plan['unit'],
            'floor_plan_image' => $this->copyAsset("plan-{$planName}.webp", "units/plans/pljesevicka-{$code}.webp"),
        ]);

        foreach ($level['rooms'] as $label => [$name, $area]) {
            $unit->rooms()->create([
                'name' => $name,
                'area_m2' => $area,
                'polygon' => $plan['rooms'][$label] ?? null,
            ]);
        }

        foreach ($level['terraces'] as [$name, $area]) {
            $unit->rooms()->create(['name' => $name, 'area_m2' => $area]);
        }
    }

    /**
     * Kopira sliku iz seed direktorija na javni disk (stabilna putanja, pa se ponovnim
     * seedanjem samo prepisuje) i vraća relativnu putanju za bazu.
     */
    protected function copyAsset(string $file, string $target): string
    {
        Storage::disk('public')->put($target, file_get_contents($this->dataDir.'/'.$file));

        return $target;
    }
}
