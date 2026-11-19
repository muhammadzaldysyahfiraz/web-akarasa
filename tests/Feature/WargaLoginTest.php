<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WargaLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_is_separate_from_public_grower_identity(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Login sebagai Admin')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertDontSee('Login sebagai warga');
    }

    public function test_admin_can_sign_in_and_open_admin_pages(): void
    {
        $admin = User::factory()->create([
            'nama' => 'Administrator',
            'email' => 'admin@example.test',
            'password' => 'password-admin',
            'role' => 'admin',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'admin@example.test',
            'password' => 'password-admin',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.resources.index', 'tanaman'))->assertOk();
        $this->post(route('admin.logout'))->assertRedirect(route('user.dashboard'));
        $this->assertGuest();
    }

    public function test_regular_account_cannot_open_admin_pages(): void
    {
        $regularUser = User::factory()->create(['role' => 'user']);

        $this->actingAs($regularUser)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs($regularUser)
            ->getJson('/api/v1/admin/tanaman')
            ->assertForbidden();
    }

    public function test_admin_pages_require_authentication_and_public_users_do_not_have_login_routes(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
        $this->getJson('/api/v1/admin/tanaman')->assertUnauthorized();
        $this->get('/warga/login')->assertNotFound();
        $this->get('/register')->assertNotFound();
    }
}
