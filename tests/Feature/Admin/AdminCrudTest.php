<?php

namespace Tests\Feature\Admin;

use App\Enums\UnitStatus;
use App\Livewire\Admin\Buildings\Form as BuildingForm;
use App\Livewire\Admin\Floors\Form as FloorForm;
use App\Livewire\Admin\Investors\Form as InvestorForm;
use App\Livewire\Admin\Investors\IndexPage as InvestorsIndex;
use App\Livewire\Admin\Projects\Form as ProjectForm;
use App\Livewire\Admin\Rooms\Form as RoomForm;
use App\Livewire\Admin\Units\Form as UnitForm;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Room;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_admin_can_create_an_investor_with_a_login_and_logo(): void
    {
        Livewire::test(InvestorForm::class)
            ->set('name', 'Marko Marić')
            ->set('email', 'marko@primjer.hr')
            ->set('password', 'lozinka123')
            ->set('company_name', 'Marić Gradnja d.o.o.')
            ->set('oib', '11122233344')
            ->set('contact_phone', '+385 91 000 0000')
            ->set('contact_email', 'kontakt@maric.hr')
            ->set('logo', UploadedFile::fake()->image('logo.png'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'marko@primjer.hr', 'role' => 'investor']);

        $investor = Investor::where('company_name', 'Marić Gradnja d.o.o.')->firstOrFail();
        $this->assertNotNull($investor->logo_path);
        Storage::disk('public')->assertExists($investor->logo_path);
    }

    public function test_investor_creation_requires_unique_email(): void
    {
        $existing = User::factory()->create();

        Livewire::test(InvestorForm::class)
            ->set('name', 'Netko')
            ->set('email', $existing->email)
            ->set('password', 'lozinka123')
            ->set('company_name', 'Tvrtka d.o.o.')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    public function test_admin_can_edit_investor_without_changing_password(): void
    {
        $investorUser = User::factory()->investor()->create(['password' => bcrypt('staralozinka')]);
        $investor = Investor::factory()->for($investorUser, 'user')->create();

        Livewire::test(InvestorForm::class, ['investor' => $investor])
            ->set('company_name', 'Novi naziv d.o.o.')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Novi naziv d.o.o.', $investor->fresh()->company_name);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('staralozinka', $investorUser->fresh()->password));
    }

    public function test_investors_index_lists_and_searches_by_company_name(): void
    {
        Investor::factory()->create(['company_name' => 'Alfa Gradnja']);
        Investor::factory()->create(['company_name' => 'Beta Nekretnine']);

        Livewire::test(InvestorsIndex::class)
            ->assertSee('Alfa Gradnja')
            ->assertSee('Beta Nekretnine')
            ->set('search', 'Alfa')
            ->assertSee('Alfa Gradnja')
            ->assertDontSee('Beta Nekretnine');
    }

    public function test_deleting_investor_from_index_removes_login_and_cascades(): void
    {
        $investor = Investor::factory()->create();
        $project = Project::factory()->for($investor)->create();

        Livewire::test(InvestorsIndex::class)
            ->call('delete', $investor->id);

        $this->assertDatabaseMissing('users', ['id' => $investor->user_id]);
        $this->assertDatabaseMissing('investors', ['id' => $investor->id]);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_admin_can_create_project_for_an_investor(): void
    {
        $investor = Investor::factory()->create();

        Livewire::test(ProjectForm::class, ['investorId' => $investor->id])
            ->set('name', 'Rezidencija Test')
            ->set('location', 'Split')
            ->set('cover_image', UploadedFile::fake()->image('cover.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $project = Project::where('name', 'Rezidencija Test')->firstOrFail();
        $this->assertSame($investor->id, $project->investor_id);
        Storage::disk('public')->assertExists($project->cover_image);
    }

    public function test_admin_can_create_building_for_a_project(): void
    {
        $project = Project::factory()->create();

        Livewire::test(BuildingForm::class, ['projectId' => $project->id])
            ->set('name', 'Zgrada B')
            ->set('type', 'urbana_vila')
            ->set('facade_image', UploadedFile::fake()->image('facade.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $building = Building::where('name', 'Zgrada B')->firstOrFail();
        $this->assertSame($project->id, $building->project_id);
        Storage::disk('public')->assertExists($building->facade_image);
    }

    public function test_admin_can_create_floor_for_a_building(): void
    {
        $building = Building::factory()->create();

        Livewire::test(FloorForm::class, ['buildingId' => $building->id])
            ->set('label', '3. kat')
            ->set('order', 3)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('floors', ['building_id' => $building->id, 'label' => '3. kat']);
    }

    public function test_admin_can_create_unit_with_status_and_price(): void
    {
        $building = Building::factory()->create();
        $floor = Floor::factory()->for($building)->create();

        Livewire::test(UnitForm::class, ['buildingId' => $building->id])
            ->set('code', 'B12')
            ->set('area_m2', '55.5')
            ->set('price', '175000')
            ->set('status', UnitStatus::Rezervirano->value)
            ->set('floor_id', $floor->id)
            ->call('save')
            ->assertHasNoErrors();

        $unit = Unit::where('code', 'B12')->firstOrFail();
        $this->assertSame($building->id, $unit->building_id);
        $this->assertSame($floor->id, $unit->floor_id);
        $this->assertSame(UnitStatus::Rezervirano, $unit->status);
    }

    public function test_admin_can_create_room_for_a_unit(): void
    {
        $unit = Unit::factory()->create();

        Livewire::test(RoomForm::class, ['unitId' => $unit->id])
            ->set('name', 'Kuhinja')
            ->set('area_m2', '9.5')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('rooms', ['unit_id' => $unit->id, 'name' => 'Kuhinja']);
    }

    public function test_admin_can_delete_a_room(): void
    {
        $room = Room::factory()->create();

        $this->get(route('admin.units.show', $room->unit))
            ->assertOk();

        $room->delete();

        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    public function test_deleting_a_floor_keeps_its_units_but_unassigns_them(): void
    {
        $floor = Floor::factory()->create();
        $unit = Unit::factory()->for($floor->building, 'building')->create(['floor_id' => $floor->id]);

        $floor->delete();

        $this->assertDatabaseHas('units', ['id' => $unit->id, 'floor_id' => null]);
    }

    public function test_show_pages_render_full_hierarchy(): void
    {
        $investor = Investor::factory()->create();
        $project = Project::factory()->for($investor)->create();
        $building = Building::factory()->for($project)->create();
        $floor = Floor::factory()->for($building)->create();
        $unit = Unit::factory()->for($building)->create(['floor_id' => $floor->id]);
        Room::factory()->for($unit)->create(['name' => 'Dnevni boravak']);

        $this->get(route('admin.investors.show', $investor))->assertOk()->assertSee($project->name);
        $this->get(route('admin.projects.show', $project))->assertOk()->assertSee($building->name);
        $this->get(route('admin.buildings.show', $building))->assertOk()->assertSee($floor->label)->assertSee($unit->code);
        $this->get(route('admin.units.show', $unit))->assertOk()->assertSee('Dnevni boravak');
    }
}
