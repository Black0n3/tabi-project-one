<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin_panel(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_from_investor_panel(): void
    {
        $this->get('/investitor/dashboard')->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Nadzorna ploča');
    }

    public function test_investor_can_access_investor_dashboard(): void
    {
        $investor = User::factory()->investor()->create();

        $this->actingAs($investor)
            ->get('/investitor/dashboard')
            ->assertOk()
            ->assertSee('Nadzorna ploča');
    }

    public function test_investor_cannot_access_admin_panel(): void
    {
        $investor = User::factory()->investor()->create();

        $this->actingAs($investor)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_admin_cannot_access_investor_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/investitor/dashboard')
            ->assertForbidden();
    }

    public function test_registration_route_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
    }
}
