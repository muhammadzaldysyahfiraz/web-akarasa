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
            ->assertSee('Login sebagai admin')
            ->assertSee('href="'.route('register').'"', false)
            ->assertSee('Buat akun baru')
            ->assertDontSee('name="nama"', false)
            ->assertDontSee('Akun baru dibuat otomatis saat pertama kali masuk.');

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Login sebagai warga')
            ->assertSee('Login sebagai admin')
            ->assertSee('name="nama"', false)
            ->assertDontSee('name="email"', false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('name="nama"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('href="'.route('warga.login').'"', false);
    }

    public function test_new_warga_can_register_then_log_in(): void
    {
        $response = $this->from(route('register'))->post(route('register.store'), [
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $user = User::where('email', 'budi@example.com')->firstOrFail();

        $response->assertRedirect(route('warga.login'));
        $response->assertSessionHas('success', 'Akun berhasil dibuat. Silakan masuk.');
        $this->get(route('warga.login'))
            ->assertOk()
            ->assertSee('Akun berhasil dibuat. Silakan masuk.');
        $this->assertGuest();
        $this->assertSame('Budi Santoso', $user->nama);
        $this->assertSame('user', $user->role);
        $this->assertTrue(password_verify('rahasia123', $user->password));

        $this->post(route('warga.login.store'), [
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
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
            'email' => 'warga@example.com',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::where('email', 'warga@example.com')->count());
        $this->assertSame('Nama Lama', $user->fresh()->nama);
    }

    public function test_login_rejects_an_existing_email_with_a_different_password(): void
    {
        $user = User::factory()->create([
            'nama' => 'Nama Lama',
            'email' => 'warga@example.com',
            'password' => 'password-asli',
            'role' => 'user',
        ]);

        $response = $this->from(route('warga.login'))->post(route('warga.login.store'), [
            'email' => 'warga@example.com',
            'password' => 'password-salah',
        ]);

        $response->assertRedirect(route('warga.login'));
        $response->assertSessionHasErrors([
            'email' => 'Email atau kata sandi tidak sesuai. Periksa kembali, atau buat akun baru jika belum mendaftar.',
        ]);
        $this->assertGuest();
        $this->assertSame(1, User::where('email', 'warga@example.com')->count());
        $this->assertSame('Nama Lama', $user->fresh()->nama);
    }

    public function test_login_does_not_create_an_account_for_an_unregistered_email(): void
    {
        $response = $this->from(route('warga.login'))->post(route('warga.login.store'), [
            'email' => 'baru@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('warga.login'));
        $response->assertSessionHasErrors('email');
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
