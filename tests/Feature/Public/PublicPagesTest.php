<?php

namespace Tests\Feature\Public;

use App\Enums\UnitStatus;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Project;
use App\Models\Room;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_lists_featured_projects_and_units(): void
    {
        $project = Project::factory()->create(['name' => 'Vidikovac Park', 'is_featured' => true]);
        $unit = Unit::factory()->create(['code' => 'V1', 'is_featured' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Vidikovac Park')
            ->assertSee('V1');
    }

    public function test_project_page_lists_its_buildings(): void
    {
        $project = Project::factory()->create();
        $building = Building::factory()->for($project)->create(['name' => 'Zgrada B']);

        $this->get(route('public.projects.show', $project))
            ->assertOk()
            ->assertSee($project->name)
            ->assertSee('Zgrada B');
    }

    public function test_project_page_without_buildings_shows_empty_state(): void
    {
        $project = Project::factory()->create();

        $this->get(route('public.projects.show', $project))
            ->assertOk()
            ->assertSee('još nema objavljenih objekata');
    }

    public function test_building_page_shows_floors_and_units(): void
    {
        $building = Building::factory()->create();
        $floor = Floor::factory()->for($building)->create(['label' => '5. kat']);
        Unit::factory()->for($building)->create(['floor_id' => $floor->id, 'code' => 'B5', 'status' => UnitStatus::Dostupno]);

        $this->get(route('public.buildings.show', $building))
            ->assertOk()
            ->assertSee('5. kat')
            ->assertSee('B5');
    }

    public function test_building_page_lists_units_without_a_floor_separately(): void
    {
        $building = Building::factory()->create();
        $house = Unit::factory()->for($building)->create(['floor_id' => null, 'code' => 'Kuca 1']);

        $this->get(route('public.buildings.show', $building))
            ->assertOk()
            ->assertSee('Kuca 1');
    }

    public function test_building_page_without_facade_image_shows_placeholder(): void
    {
        $building = Building::factory()->create(['facade_image' => null]);

        $this->get(route('public.buildings.show', $building))
            ->assertOk()
            ->assertSee('Fasada objekta još nije dodana');
    }

    public function test_unit_page_shows_status_price_description_and_rooms(): void
    {
        $unit = Unit::factory()->create([
            'code' => 'C3',
            'status' => UnitStatus::Rezervirano,
            'price' => 199000,
            'description' => 'Prostran stan s pogledom na more.',
        ]);
        Room::factory()->for($unit)->create(['name' => 'Kuhinja', 'area_m2' => 12]);

        $response = $this->get(route('public.units.show', $unit));

        $response->assertOk()
            ->assertSee('C3')
            ->assertSee('Rezervirano')
            ->assertSee('199.000')
            ->assertSee('Prostran stan s pogledom na more.')
            ->assertSee('Kuhinja');
    }

    public function test_unit_page_without_rooms_shows_empty_state(): void
    {
        $unit = Unit::factory()->create();

        $this->get(route('public.units.show', $unit))
            ->assertOk()
            ->assertSee('raspored prostorija još nije unesen');
    }

    public function test_public_pages_require_no_authentication(): void
    {
        $project = Project::factory()->create();
        $building = Building::factory()->for($project)->create();
        $unit = Unit::factory()->for($building)->create();

        $this->get(route('home'))->assertOk();
        $this->get(route('public.projects.show', $project))->assertOk();
        $this->get(route('public.buildings.show', $building))->assertOk();
        $this->get(route('public.units.show', $unit))->assertOk();
    }
}
