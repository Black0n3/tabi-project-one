<?php

namespace Tests\Feature\Admin;

use App\Livewire\Shared\Buildings\ZonesPage as BuildingZones;
use App\Livewire\Shared\Floors\ZonesPage as FloorZones;
use App\Livewire\Shared\Units\ZonesPage as UnitZones;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Investor;
use App\Models\Room;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ZoneEditorTest extends TestCase
{
    use RefreshDatabase;

    protected array $triangle = [[10, 10], [50, 10], [30, 40]];

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_saving_a_zone_without_a_target_creates_a_new_floor(): void
    {
        $building = Building::factory()->create();

        Livewire::test(BuildingZones::class, ['building' => $building])
            ->call('saveZone', null, '3. kat', $this->triangle)
            ->assertHasNoErrors();

        $floor = Floor::where('building_id', $building->id)->where('label', '3. kat')->firstOrFail();
        $this->assertEquals($this->triangle, $floor->polygon);
    }

    public function test_saving_a_zone_with_a_target_attaches_it_to_the_existing_floor(): void
    {
        $building = Building::factory()->create();
        $floor = Floor::factory()->for($building)->create(['polygon' => null]);

        Livewire::test(BuildingZones::class, ['building' => $building])
            ->call('saveZone', $floor->id, null, $this->triangle);

        $this->assertEquals($this->triangle, $floor->fresh()->polygon);
    }

    public function test_deleting_a_zone_clears_the_polygon_but_keeps_the_floor(): void
    {
        $building = Building::factory()->create();
        $floor = Floor::factory()->for($building)->create(['polygon' => $this->triangle]);

        Livewire::test(BuildingZones::class, ['building' => $building])
            ->call('deleteZone', $floor->id);

        $floor->refresh();
        $this->assertNull($floor->polygon);
        $this->assertDatabaseHas('floors', ['id' => $floor->id]);
    }

    public function test_saving_a_zone_rejects_a_polygon_with_fewer_than_three_points(): void
    {
        $building = Building::factory()->create();

        Livewire::test(BuildingZones::class, ['building' => $building])
            ->call('saveZone', null, 'Kat', [[10, 10], [20, 20]])
            ->assertStatus(422);
    }

    public function test_admin_cannot_attach_a_zone_using_a_floor_from_another_building(): void
    {
        $building = Building::factory()->create();
        $foreignFloor = Floor::factory()->create();

        Livewire::test(BuildingZones::class, ['building' => $building])
            ->call('saveZone', $foreignFloor->id, null, $this->triangle)
            ->assertForbidden();
    }

    public function test_saving_a_unit_zone_without_a_target_creates_a_new_unit_with_default_values(): void
    {
        $floor = Floor::factory()->create();

        Livewire::test(FloorZones::class, ['floor' => $floor])
            ->call('saveZone', null, 'A9', $this->triangle)
            ->assertHasNoErrors();

        $unit = Unit::where('floor_id', $floor->id)->where('code', 'A9')->firstOrFail();
        $this->assertSame($floor->building_id, $unit->building_id);
        $this->assertEquals($this->triangle, $unit->polygon);
        $this->assertEquals(0.0, (float) $unit->area_m2);
    }

    public function test_saving_a_room_zone_without_a_target_creates_a_new_room(): void
    {
        $unit = Unit::factory()->create();

        Livewire::test(UnitZones::class, ['unit' => $unit])
            ->call('saveZone', null, 'Kuhinja', $this->triangle)
            ->assertHasNoErrors();

        $room = Room::where('unit_id', $unit->id)->where('name', 'Kuhinja')->firstOrFail();
        $this->assertEquals($this->triangle, $room->polygon);
    }

    public function test_investor_cannot_open_zones_editor_for_another_investors_building(): void
    {
        $investorUser = User::factory()->investor()->create();
        Investor::factory()->for($investorUser, 'user')->create();

        $foreignBuilding = Building::factory()->create();

        $this->actingAs($investorUser);

        Livewire::test(BuildingZones::class, ['building' => $foreignBuilding])
            ->assertForbidden();
    }

    public function test_investor_cannot_open_zones_editor_for_another_investors_floor(): void
    {
        $investorUser = User::factory()->investor()->create();
        Investor::factory()->for($investorUser, 'user')->create();

        $foreignFloor = Floor::factory()->create();

        $this->actingAs($investorUser);

        Livewire::test(FloorZones::class, ['floor' => $foreignFloor])
            ->assertForbidden();
    }

    public function test_investor_cannot_open_zones_editor_for_another_investors_unit(): void
    {
        $investorUser = User::factory()->investor()->create();
        Investor::factory()->for($investorUser, 'user')->create();

        $foreignUnit = Unit::factory()->create();

        $this->actingAs($investorUser);

        Livewire::test(UnitZones::class, ['unit' => $foreignUnit])
            ->assertForbidden();
    }
}
