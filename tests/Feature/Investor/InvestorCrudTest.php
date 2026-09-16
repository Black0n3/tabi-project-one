<?php

namespace Tests\Feature\Investor;

use App\Livewire\Shared\Buildings\Form as BuildingForm;
use App\Livewire\Shared\Floors\Form as FloorForm;
use App\Livewire\Shared\Projects\Form as ProjectForm;
use App\Livewire\Shared\Rooms\Form as RoomForm;
use App\Livewire\Shared\Units\Form as UnitForm;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Room;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvestorCrudTest extends TestCase
{
    use RefreshDatabase;

    protected Investor $investor;

    protected User $investorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->investorUser = User::factory()->investor()->create();
        $this->investor = Investor::factory()->for($this->investorUser, 'user')->create();
    }

    public function test_investor_dashboard_lists_own_projects(): void
    {
        $ownProject = Project::factory()->for($this->investor)->create(['name' => 'Moj projekt']);
        $otherProject = Project::factory()->create(['name' => 'Tuđi projekt']);

        $this->actingAs($this->investorUser)
            ->get(route('investitor.dashboard'))
            ->assertOk()
            ->assertSee('Moj projekt')
            ->assertDontSee('Tuđi projekt');
    }

    public function test_investor_can_view_own_hierarchy_via_investitor_routes(): void
    {
        $project = Project::factory()->for($this->investor)->create();
        $building = Building::factory()->for($project)->create();
        $floor = Floor::factory()->for($building)->create();
        $unit = Unit::factory()->for($building)->create(['floor_id' => $floor->id]);
        Room::factory()->for($unit)->create(['name' => 'Kuhinja']);

        $this->actingAs($this->investorUser);

        $this->get(route('investitor.projects.show', $project))->assertOk()->assertSee($building->name);
        $this->get(route('investitor.buildings.show', $building))->assertOk()->assertSee($floor->label)->assertSee($unit->code);
        $this->get(route('investitor.units.show', $unit))->assertOk()->assertSee('Kuhinja');
    }

    public function test_investor_cannot_view_another_investors_project(): void
    {
        $otherProject = Project::factory()->create();

        $this->actingAs($this->investorUser)
            ->get(route('investitor.projects.show', $otherProject))
            ->assertForbidden();
    }

    public function test_investor_cannot_view_another_investors_building_or_unit(): void
    {
        $otherBuilding = Building::factory()->create();
        $otherUnit = Unit::factory()->for($otherBuilding, 'building')->create();

        $this->actingAs($this->investorUser);

        $this->get(route('investitor.buildings.show', $otherBuilding))->assertForbidden();
        $this->get(route('investitor.units.show', $otherUnit))->assertForbidden();
    }

    public function test_investor_cannot_open_edit_form_for_another_investors_project(): void
    {
        $otherProject = Project::factory()->create();

        $this->actingAs($this->investorUser)
            ->get(route('investitor.projects.edit', $otherProject))
            ->assertForbidden();
    }

    public function test_investor_cannot_create_building_under_another_investors_project(): void
    {
        $otherProject = Project::factory()->create();

        $this->actingAs($this->investorUser)
            ->get(route('investitor.buildings.create', ['project' => $otherProject->id]))
            ->assertForbidden();
    }

    public function test_investor_cannot_create_unit_or_room_under_another_investors_hierarchy(): void
    {
        $otherBuilding = Building::factory()->create();
        $otherUnit = Unit::factory()->for($otherBuilding, 'building')->create();

        $this->actingAs($this->investorUser);

        $this->get(route('investitor.units.create', ['building' => $otherBuilding->id]))->assertForbidden();
        $this->get(route('investitor.rooms.create', ['unit' => $otherUnit->id]))->assertForbidden();
    }

    public function test_investor_cannot_delete_a_floor_belonging_to_another_investor(): void
    {
        $ownProject = Project::factory()->for($this->investor)->create();
        $ownBuilding = Building::factory()->for($ownProject)->create();

        $foreignFloor = Floor::factory()->create();

        $this->actingAs($this->investorUser);

        Livewire::test(\App\Livewire\Shared\Buildings\ShowPage::class, ['building' => $ownBuilding])
            ->call('deleteFloor', $foreignFloor->id)
            ->assertForbidden();

        $this->assertDatabaseHas('floors', ['id' => $foreignFloor->id]);
    }

    public function test_investor_can_create_full_hierarchy_for_their_own_investor(): void
    {
        $this->actingAs($this->investorUser);

        Livewire::test(ProjectForm::class)
            ->set('name', 'Novi Projekt')
            ->call('save')
            ->assertHasNoErrors();

        $project = Project::where('name', 'Novi Projekt')->firstOrFail();
        $this->assertSame($this->investor->id, $project->investor_id);

        Livewire::test(BuildingForm::class, ['projectId' => $project->id])
            ->set('name', 'Zgrada X')
            ->call('save')
            ->assertHasNoErrors();

        $building = Building::where('name', 'Zgrada X')->firstOrFail();

        Livewire::test(FloorForm::class, ['buildingId' => $building->id])
            ->set('label', 'Prizemlje')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('floors', ['building_id' => $building->id, 'label' => 'Prizemlje']);

        Livewire::test(UnitForm::class, ['buildingId' => $building->id])
            ->set('code', 'X1')
            ->set('area_m2', '40')
            ->call('save')
            ->assertHasNoErrors();

        $unit = Unit::where('code', 'X1')->firstOrFail();

        Livewire::test(RoomForm::class, ['unitId' => $unit->id])
            ->set('name', 'Soba')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('rooms', ['unit_id' => $unit->id, 'name' => 'Soba']);
    }

    public function test_investor_cannot_spoof_investor_id_when_creating_a_project(): void
    {
        $otherInvestor = Investor::factory()->create();

        $this->actingAs($this->investorUser);

        Livewire::test(ProjectForm::class, ['investorId' => $otherInvestor->id])
            ->set('name', 'Pokušaj prevare')
            ->call('save')
            ->assertHasNoErrors();

        $project = Project::where('name', 'Pokušaj prevare')->firstOrFail();
        $this->assertSame($this->investor->id, $project->investor_id);
        $this->assertNotSame($otherInvestor->id, $project->investor_id);
    }

    public function test_investor_cannot_attach_own_unit_to_a_floor_from_another_investors_building(): void
    {
        $ownProject = Project::factory()->for($this->investor)->create();
        $ownBuilding = Building::factory()->for($ownProject)->create();
        $unit = Unit::factory()->for($ownBuilding, 'building')->create(['floor_id' => null]);

        $foreignBuilding = Building::factory()->create();
        $foreignFloor = Floor::factory()->for($foreignBuilding)->create();

        $this->actingAs($this->investorUser);

        Livewire::test(UnitForm::class, ['unit' => $unit])
            ->set('code', $unit->code)
            ->set('area_m2', (string) $unit->area_m2)
            ->set('floor_id', $foreignFloor->id)
            ->call('save')
            ->assertHasErrors(['floor_id']);

        $this->assertNull($unit->fresh()->floor_id);
    }

    public function test_investor_form_pages_render_with_investor_layout(): void
    {
        $this->actingAs($this->investorUser)
            ->get(route('investitor.projects.create'))
            ->assertOk()
            ->assertSee('Tabi', false)
            ->assertSee('Investitor', false);
    }
}
