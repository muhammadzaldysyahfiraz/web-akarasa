<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WargaLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_warga_and_admin_login_pages_offer_a_role_choice(): void
    {
        $this->get(route('warga.login'))
            ->assertOk()
            ->assertSee('Login sebagai warga')
            ->assertSee('Login sebagai admin');

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Login sebagai warga')
            ->assertSee('Login sebagai admin')
            ->assertSee('name="nama"', false)
            ->assertDontSee('name="email"', false);
    }

    public function test_new_warga_can_create_an_account_on_first_login(): void
    {
        $response = $this->from(route('warga.login'))->post(route('warga.login.store'), [
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
        ]);

        $user = User::where('email', 'budi@example.com')->firstOrFail();

        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('success', 'Akun berhasil dibuat. Selamat datang!');
        $this->assertAuthenticatedAs($user);
        $this->assertSame('Budi Santoso', $user->nama);
        $this->assertSame('user', $user->role);
        $this->assertTrue(password_verify('rahasia123', $user->password));
    }

    public function test_existing_warga_can_log_in_without_creating_another_account(): void
    {
        $user = User::factory()->create([
            'nama' => 'Nama Lama',
            'email' => 'warga@example.com',
            'password' => 'rahasia123',
            'role' => 'user',
        ]);

        $response = $this->post(route('warga.login.store'), [
            'nama' => 'Nama Baru',
            'email' => 'warga@example.com',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::where('email', 'warga@example.com')->count());
        $this->assertSame('Nama Lama', $user->fresh()->nama);
    }

    public function test_existing_email_cannot_be_claimed_with_a_different_password(): void
    {
        $user = User::factory()->create([
            'nama' => 'Nama Lama',
            'email' => 'warga@example.com',
            'password' => 'password-asli',
            'role' => 'user',
        ]);

        $response = $this->from(route('warga.login'))->post(route('warga.login.store'), [
            'nama' => 'Nama Penyerang',
            'email' => 'warga@example.com',
            'password' => 'password-salah',
        ]);

        $response->assertRedirect(route('warga.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSame(1, User::where('email', 'warga@example.com')->count());
        $this->assertSame('Nama Lama', $user->fresh()->nama);
    }

    public function test_first_login_requires_a_name_and_a_long_enough_password(): void
    {
        $response = $this->from(route('warga.login'))->post(route('warga.login.store'), [
            'email' => 'baru@example.com',
            'password' => 'pendek',
        ]);

        $response->assertRedirect(route('warga.login'));
        $response->assertSessionHasErrors(['nama', 'password']);
        $this->assertDatabaseMissing('users', ['email' => 'baru@example.com']);
    }

    public function test_admin_cannot_log_in_through_the_warga_page(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'kata-sandi-admin',
            'role' => 'admin',
        ]);

        $response = $this->post(route('warga.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'kata-sandi-admin',
        ]);

        $response->assertRedirect(route('warga.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_different_admin_names_can_use_the_same_configured_password(): void
    {
        Config::set('auth.admin_password', 'password-bersama');

        $adminSatu = User::factory()->create([
            'nama' => 'Admin Satu',
            'email' => 'admin-satu@example.com',
            'password' => 'kata-sandi-lama-satu',
            'role' => 'admin',
        ]);
        $adminDua = User::factory()->create([
            'nama' => 'Admin Dua',
            'email' => 'admin-dua@example.com',
            'password' => 'kata-sandi-lama-dua',
            'role' => 'admin',
        ]);

        $this->post(route('admin.login.store'), [
            'nama' => 'Admin Satu',
            'password' => 'password-bersama',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($adminSatu);

        $this->post(route('admin.logout'));
        $this->post(route('admin.login.store'), [
            'nama' => 'Admin Dua',
            'password' => 'password-bersama',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($adminDua);
    }

    public function test_warga_cannot_log_in_as_admin_even_with_the_shared_password(): void
    {
        Config::set('auth.admin_password', 'password-bersama');
        User::factory()->create([
            'nama' => 'Warga',
            'email' => 'warga@example.com',
            'role' => 'user',
        ]);

        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [
                'nama' => 'Warga',
                'password' => 'password-bersama',
            ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('nama');

        $this->assertGuest();
    }

    public function test_admin_login_requires_a_configured_shared_password(): void
    {
        Config::set('auth.admin_password', null);

        $response = $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'nama' => 'Administrator',
            'password' => 'password-bersama',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('nama');
        $this->assertGuest();
    }
}
