<?php

namespace Tests\Feature;

use App\Enums\UnitStatus;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Room;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataHierarchyTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_hierarchy_can_be_built_and_traversed(): void
    {
        $investorUser = User::factory()->investor()->create();
        $investor = Investor::factory()->for($investorUser, 'user')->create();
        $project = Project::factory()->for($investor)->create();
        $building = Building::factory()->for($project)->create();
        $floor = Floor::factory()->for($building)->create();
        $unit = Unit::factory()->for($building)->create(['floor_id' => $floor->id]);
        $room = Room::factory()->for($unit)->create();

        $this->assertTrue($investor->user->is($investorUser));
        $this->assertTrue($investor->projects->first()->is($project));
        $this->assertTrue($project->investor->is($investor));
        $this->assertTrue($project->buildings->first()->is($building));
        $this->assertTrue($building->project->is($project));
        $this->assertTrue($building->floors->first()->is($floor));
        $this->assertTrue($building->units->first()->is($unit));
        $this->assertTrue($floor->building->is($building));
        $this->assertTrue($floor->units->first()->is($unit));
        $this->assertTrue($unit->building->is($building));
        $this->assertTrue($unit->floor->is($floor));
        $this->assertTrue($unit->rooms->first()->is($room));
        $this->assertTrue($room->unit->is($unit));
    }

    public function test_unit_without_floor_is_allowed_for_standalone_houses(): void
    {
        $unit = Unit::factory()->create(['floor_id' => null]);

        $this->assertNull($unit->floor);
        $this->assertNotNull($unit->building);
    }

    public function test_unit_status_defaults_to_dostupno(): void
    {
        $unit = Unit::factory()->create();

        $this->assertInstanceOf(UnitStatus::class, $unit->status);
    }

    public function test_deleting_investor_cascades_to_its_projects(): void
    {
        $investor = Investor::factory()->create();
        $project = Project::factory()->for($investor)->create();

        $investor->delete();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_deleting_building_cascades_to_floors_and_units(): void
    {
        $building = Building::factory()->create();
        $floor = Floor::factory()->for($building)->create();
        $unit = Unit::factory()->for($building)->create(['floor_id' => $floor->id]);

        $building->delete();

        $this->assertDatabaseMissing('floors', ['id' => $floor->id]);
        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    }

    public function test_deleting_floor_does_not_delete_its_units(): void
    {
        $floor = Floor::factory()->create();
        $unit = Unit::factory()->for($floor->building, 'building')->create(['floor_id' => $floor->id]);

        $floor->delete();

        $this->assertDatabaseHas('units', ['id' => $unit->id, 'floor_id' => null]);
    }
}
