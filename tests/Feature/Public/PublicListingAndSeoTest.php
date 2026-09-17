<?php

namespace Tests\Feature\Public;

use App\Enums\ProjectStatus;
use App\Enums\UnitStatus;
use App\Livewire\Public\ProjectsIndex;
use App\Livewire\Public\UnitsIndex;
use App\Models\Building;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicListingAndSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_projects_index_lists_all_projects(): void
    {
        Project::factory()->create(['name' => 'Vidikovac']);
        Project::factory()->create(['name' => 'Sunčani Dvori']);

        $this->get(route('public.projects.index'))
            ->assertOk()
            ->assertSee('Vidikovac')
            ->assertSee('Sunčani Dvori');
    }

    public function test_projects_index_filters_by_location(): void
    {
        Project::factory()->create(['name' => 'Zagreb Projekt', 'location' => 'Zagreb']);
        Project::factory()->create(['name' => 'Split Projekt', 'location' => 'Split']);

        Livewire::test(ProjectsIndex::class)
            ->set('location', 'Zagreb')
            ->assertSee('Zagreb Projekt')
            ->assertDontSee('Split Projekt');
    }

    public function test_projects_index_filters_by_status(): void
    {
        Project::factory()->create(['name' => 'Gotov Projekt', 'status' => ProjectStatus::Completed]);
        Project::factory()->create(['name' => 'U Izgradnji Projekt', 'status' => ProjectStatus::InProgress]);

        Livewire::test(ProjectsIndex::class)
            ->set('status', ProjectStatus::Completed->value)
            ->assertSee('Gotov Projekt')
            ->assertDontSee('U Izgradnji Projekt');
    }

    public function test_units_index_lists_all_units(): void
    {
        Unit::factory()->create(['code' => 'U1']);
        Unit::factory()->create(['code' => 'U2']);

        $this->get(route('public.units.index'))
            ->assertOk()
            ->assertSee('U1')
            ->assertSee('U2');
    }

    public function test_units_index_filters_by_status_and_area_range(): void
    {
        Unit::factory()->create(['code' => 'Mali', 'status' => UnitStatus::Dostupno, 'area_m2' => 30]);
        Unit::factory()->create(['code' => 'Veliki', 'status' => UnitStatus::Dostupno, 'area_m2' => 120]);
        Unit::factory()->create(['code' => 'VecProdana', 'status' => UnitStatus::Prodano, 'area_m2' => 50]);

        Livewire::test(UnitsIndex::class)
            ->set('status', UnitStatus::Dostupno->value)
            ->set('minArea', '25')
            ->set('maxArea', '60')
            ->assertSee('Mali')
            ->assertDontSee('Veliki')
            ->assertDontSee('VecProdana');
    }

    public function test_units_index_filters_by_price_range(): void
    {
        Unit::factory()->create(['code' => 'Jeftin', 'price' => 50000]);
        Unit::factory()->create(['code' => 'Skup', 'price' => 500000]);

        Livewire::test(UnitsIndex::class)
            ->set('maxPrice', '100000')
            ->assertSee('Jeftin')
            ->assertDontSee('Skup');
    }

    public function test_units_index_filters_by_room_count(): void
    {
        Unit::factory()->create(['code' => 'Jednosoban', 'room_count' => 1]);
        Unit::factory()->create(['code' => 'Trosoban', 'room_count' => 3]);
        Unit::factory()->create(['code' => 'Peterosoban', 'room_count' => 5]);

        Livewire::test(UnitsIndex::class)
            ->set('roomCount', '3')
            ->assertSee('Trosoban')
            ->assertDontSee('Jednosoban')
            ->assertDontSee('Peterosoban');

        Livewire::test(UnitsIndex::class)
            ->set('roomCount', '4+')
            ->assertSee('Peterosoban')
            ->assertDontSee('Jednosoban')
            ->assertDontSee('Trosoban');
    }

    public function test_units_index_filters_by_project(): void
    {
        $projectA = Project::factory()->create(['name' => 'Projekt A']);
        $buildingA = Building::factory()->for($projectA)->create();
        Unit::factory()->for($buildingA, 'building')->create(['code' => 'A1']);

        $projectB = Project::factory()->create(['name' => 'Projekt B']);
        $buildingB = Building::factory()->for($projectB)->create();
        Unit::factory()->for($buildingB, 'building')->create(['code' => 'B1']);

        Livewire::test(UnitsIndex::class)
            ->set('projectId', (string) $projectA->id)
            ->assertSee('A1')
            ->assertDontSee('B1');
    }

    public function test_units_index_filters_by_project_location(): void
    {
        $zagrebBuilding = Building::factory()->create();
        $zagrebBuilding->project()->update(['location' => 'Zagreb']);
        Unit::factory()->for($zagrebBuilding, 'building')->create(['code' => 'ZG1']);

        $rijekaBuilding = Building::factory()->create();
        $rijekaBuilding->project()->update(['location' => 'Rijeka']);
        Unit::factory()->for($rijekaBuilding, 'building')->create(['code' => 'RI1']);

        Livewire::test(UnitsIndex::class)
            ->set('location', 'Zagreb')
            ->assertSee('ZG1')
            ->assertDontSee('RI1');
    }

    public function test_hidden_project_is_excluded_from_projects_index(): void
    {
        Project::factory()->create(['name' => 'Javni Projekt']);
        Project::factory()->create(['name' => 'Interni Projekt', 'is_hidden' => true]);

        $this->get(route('public.projects.index'))
            ->assertOk()
            ->assertSee('Javni Projekt')
            ->assertDontSee('Interni Projekt');
    }

    public function test_hidden_project_page_returns_404(): void
    {
        $project = Project::factory()->create(['is_hidden' => true]);

        $this->get(route('public.projects.show', $project))->assertNotFound();
    }

    public function test_hidden_project_building_and_unit_pages_return_404(): void
    {
        $project = Project::factory()->create(['is_hidden' => true]);
        $building = Building::factory()->for($project)->create();
        $unit = Unit::factory()->for($building, 'building')->create();

        $this->get(route('public.buildings.show', $building))->assertNotFound();
        $this->get(route('public.units.show', $unit))->assertNotFound();
    }

    public function test_units_of_hidden_project_are_excluded_from_units_index(): void
    {
        $hiddenProject = Project::factory()->create(['is_hidden' => true]);
        $hiddenBuilding = Building::factory()->for($hiddenProject)->create();
        Unit::factory()->for($hiddenBuilding, 'building')->create(['code' => 'SKRIVENA']);

        $visibleBuilding = Building::factory()->create();
        Unit::factory()->for($visibleBuilding, 'building')->create(['code' => 'JAVNA']);

        $this->get(route('public.units.index'))
            ->assertOk()
            ->assertSee('JAVNA')
            ->assertDontSee('SKRIVENA');
    }

    public function test_hidden_project_is_excluded_from_home_page_and_stats(): void
    {
        Project::factory()->create(['name' => 'Javni Projekt', 'is_featured' => true]);
        Project::factory()->create(['name' => 'Interni Projekt', 'is_featured' => true, 'is_hidden' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Javni Projekt')
            ->assertDontSee('Interni Projekt');
    }

    public function test_hidden_project_is_excluded_from_sitemap(): void
    {
        $visible = Project::factory()->create();
        $hidden = Project::factory()->create(['is_hidden' => true]);

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertSee(route('public.projects.show', $visible), false);
        $response->assertDontSee(route('public.projects.show', $hidden), false);
    }

    public function test_sitemap_lists_all_public_urls(): void
    {
        $project = Project::factory()->create();
        $building = Building::factory()->for($project)->create();
        $unit = Unit::factory()->for($building, 'building')->create();

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
        $response->assertSee(route('home'), false);
        $response->assertSee(route('public.projects.show', $project), false);
        $response->assertSee(route('public.buildings.show', $building), false);
        $response->assertSee(route('public.units.show', $unit), false);
    }

    public function test_project_page_has_meta_description_and_og_tags(): void
    {
        $project = Project::factory()->create([
            'name' => 'Meta Test Projekt',
            'description' => 'Opis za meta tag.',
        ]);

        $response = $this->get(route('public.projects.show', $project));

        $response->assertOk()
            ->assertSee('<meta name="description" content="Opis za meta tag.">', false)
            ->assertSee('<meta property="og:title" content="Meta Test Projekt — '.config('app.name').'">', false);
    }

    public function test_home_page_has_default_meta_description(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<meta name="description"', false);
    }
}
