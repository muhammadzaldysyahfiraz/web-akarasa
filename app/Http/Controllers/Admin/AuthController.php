<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login admin.
     */
    public function create(): View
    {
        return view('admin.login');
    }

    /**
     * Menampilkan halaman login warga.
     */
    public function showWargaLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Menampilkan halaman registrasi warga.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Memproses registrasi warga.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'Pendaftaran berhasil. Selamat datang!');
    }

    /**
     * Memproses login admin.
     * Admin hanya memasukkan nama dan password bersama.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'nama.required' => 'Nama admin wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $sharedPassword = config('auth.admin_password');

        if (!is_string($sharedPassword) || $sharedPassword === '') {
            return back()
                ->withErrors([
                    'nama' => 'Password admin belum dikonfigurasi. Periksa ADMIN_PASSWORD di file .env.',
                ])
                ->onlyInput('nama');
        }

        // Memeriksa password admin.
        if (!hash_equals($sharedPassword, $credentials['password'])) {
            return back()
                ->withErrors([
                    'nama' => 'Nama admin atau kata sandi tidak sesuai.',
                ])
                ->onlyInput('nama');
        }

        // Mencari akun admin.
        $admin = User::where('role', 'admin')->first();

        // Membuat akun admin otomatis jika belum ada.
        if (!$admin) {
            try {
                $admin = User::create([
                    'nama' => $credentials['nama'],
                    'email' => 'admin@akarasa.local',
                    'password' => Hash::make($sharedPassword),
                    'role' => 'admin',
                ]);
            } catch (QueryException $exception) {
                $admin = User::where('role', 'admin')->first();

                if (!$admin) {
                    throw $exception;
                }
            }
        }

        // Mengganti nama admin sesuai input login.
        $admin->nama = $credentials['nama'];
        $admin->save();

        Auth::login($admin, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    /**
     * Memproses login warga.
     */
    public function loginWarga(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'nama' => ['nullable', 'string', 'max:255'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $created = false;

        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            if (!Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ], $request->boolean('remember'))) {
                return back()
                    ->withErrors([
                        'email' => 'Email atau kata sandi tidak sesuai.',
                    ])
                    ->onlyInput('email');
            }
        } else {
            $validated = $request->validate([
                'nama' => ['required', 'string', 'max:255'],
                'password' => [
                    'required',
                    'string',
                    Password::min(8),
                ],
            ], [
                'nama.required' => 'Nama wajib diisi saat pertama kali masuk.',
                'nama.max' => 'Nama maksimal 255 karakter.',
                'password.min' => 'Kata sandi minimal 8 karakter.',
            ]);

            try {
                $user = User::create([
                    'nama' => $validated['nama'],
                    'email' => $credentials['email'],
                    'password' => Hash::make($credentials['password']),
                    'role' => 'user',
                ]);

                Auth::login($user, $request->boolean('remember'));

                $created = true;

            } catch (QueryException $exception) {
                $user = User::where('email', $credentials['email'])->first();

                if (!$user || !Auth::attempt([
                    'email' => $credentials['email'],
                    'password' => $credentials['password'],
                ], $request->boolean('remember'))) {
                    return back()
                        ->withErrors([
                            'email' => 'Email atau kata sandi tidak sesuai.',
                        ])
                        ->onlyInput('email');
                }
            }
        }

        $request->session()->regenerate();

        // Admin tidak boleh login melalui halaman warga.
        if (Auth::user()->role !== 'user') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('warga.login')
                ->withErrors([
                    'email' => 'Silakan gunakan halaman login admin.',
                ]);
        }

        $response = redirect()->route('user.dashboard');

        return $created
            ? $response->with('success', 'Akun berhasil dibuat. Selamat datang!')
            : $response;
    }

    /**
     * Logout admin dan warga.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Menyimpan role sebelum logout.
        $role = Auth::user()?->role;

        Auth::logout();

        // Menghapus session lama.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Mengarahkan sesuai jenis akun.
        if ($role === 'admin') {
            return redirect()
                ->route('admin.login')
                ->with('success', 'Admin berhasil logout.');
        }

        return redirect()
            ->route('warga.login')
            ->with('success', 'Kamu berhasil logout.');
    }
}
