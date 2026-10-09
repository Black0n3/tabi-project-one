<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Room;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PljesevickaSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed();
    }

    public function test_seeds_a_single_investor_with_the_pljesevicka_project(): void
    {
        $this->assertSame(1, Investor::count());
        $this->assertSame('TabarDI - Investitor', Investor::first()->company_name);

        $this->assertSame(1, Project::count());
        $project = Project::first();
        $this->assertSame('Plješevička', $project->name);
        $this->assertCount(2, $project->buildings);
        $this->assertSame(['Plješevička 11', 'Plješevička 13'], $project->buildings->pluck('name')->sort()->values()->all());

        $this->assertSame(6, Floor::count());
        $this->assertSame(6, Unit::count());
        $this->assertEqualsCanonicalizing(['1A', '2A', '3A', '1B', '2B', '3B'], Unit::pluck('code')->all());
    }

    public function test_unit_areas_and_rooms_match_the_design_documents(): void
    {
        $this->assertEqualsWithDelta(64.39, (float) Unit::where('code', '1A')->value('area_m2'), 0.001);
        $this->assertEqualsWithDelta(97.46, (float) Unit::where('code', '2B')->value('area_m2'), 0.001);
        $this->assertEqualsWithDelta(87.22, (float) Unit::where('code', '3A')->value('area_m2'), 0.001);

        // Neto površina stana je zbroj prostorija (bez terasa) -- kako je navedeno u tablicama.
        foreach (Unit::with('rooms')->get() as $unit) {
            $sum = $unit->rooms->filter(fn (Room $r) => ! str_starts_with($r->name, 'Terasa'))->sum(fn (Room $r) => (float) $r->area_m2);
            $this->assertEqualsWithDelta((float) $unit->area_m2, $sum, 0.011, "Zbroj prostorija stana {$unit->code}");
        }
    }

    public function test_every_zone_is_drawn_on_its_image(): void
    {
        $project = Project::first();
        Storage::disk('public')->assertExists($project->cover_image);
        $this->assertNotEmpty($project->gallery);

        foreach ($project->gallery as $item) {
            Storage::disk('public')->assertExists($item['path']);
        }

        foreach (Building::all() as $building) {
            Storage::disk('public')->assertExists($building->facade_image);
        }

        foreach (Floor::all() as $floor) {
            $this->assertGreaterThanOrEqual(3, count($floor->polygon), "Kat {$floor->label} nema poligon na fasadi");
            Storage::disk('public')->assertExists($floor->floor_plan_image);
        }

        foreach (Unit::with('rooms')->get() as $unit) {
            $this->assertGreaterThanOrEqual(3, count($unit->polygon), "Stan {$unit->code} nema poligon na tlocrtu kata");
            Storage::disk('public')->assertExists($unit->floor_plan_image);

            $drawn = $unit->rooms->filter(fn (Room $r) => ! empty($r->polygon));
            $this->assertGreaterThanOrEqual(6, $drawn->count(), "Stan {$unit->code} nema ucrtane prostorije");
        }
    }

    public function test_project_page_shows_the_gallery(): void
    {
        $project = Project::first();

        $this->get(route('public.projects.show', $project))
            ->assertOk()
            ->assertSee('Galerija')
            ->assertSee('Vrt i terasa stana u prizemlju');
    }
}
