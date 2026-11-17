<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\JadwalPerawatanController;
use App\Http\Controllers\KebunController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenanamController;
use App\Http\Controllers\TanamanController;
use App\Http\Controllers\TanamanDitanamController;
use App\Models\Tanaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// HALAMAN UTAMA (Langsung ke Dashboard tanpa login)
Route::get('/', function () {
    return redirect()->route('user.dashboard');
})->name('home');

// LOGIN & LOGOUT ADMIN
Route::get('/login', function () {
    return view('auth.admin-login');
})->name('login');

Route::get('/admin/login', function () { return view('auth.admin-login'); })->name('admin.login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt(array_merge($credentials, ['role' => 'admin']))) {
        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password admin salah.',
    ])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('user.dashboard');
})->name('logout');

// KATALOG TANAMAN DAN DETAIL
Route::get('/tanaman', [TanamanController::class, 'index'])
    ->name('tanaman.index');

Route::get('/tanaman/{tanaman}', [TanamanController::class, 'show'])
    ->name('tanaman.show');

Route::get('/jadwal-perawatan', [JadwalPerawatanController::class, 'index'])
    ->name('jadwal-perawatan.index');

Route::post('/jadwal-perawatan/toggle', [JadwalPerawatanController::class, 'toggle'])
    ->name('jadwal-perawatan.toggle');

Route::get('/laporan', LaporanController::class)
    ->name('laporan.index');

Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])
    ->name('laporan.pdf');

// FITUR WARGA (Tanpa middleware auth & user)
Route::get('/dashboard', DashboardController::class)
    ->name('user.dashboard');

Route::get('/kebunku', [PenanamController::class, 'index'])
    ->name('penanam.index');

Route::get('/tanam', [TanamanDitanamController::class, 'create'])
    ->name('tanaman-ditanam.create');

Route::post('/tanaman-ditanam', [TanamanDitanamController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('tanaman-ditanam.store');

Route::get('/kebun/{penanam}', [KebunController::class, 'show'])
    ->name('kebun.show');

Route::post('/kebun/{penanam}/akses', [KebunController::class, 'access'])
    ->middleware('throttle:10,1')
    ->name('kebun.access');

Route::patch(
    '/kebun/{penanam}/jadwal/{jadwal}/complete',
    [KebunController::class, 'complete']
)->name('kebun.complete');

Route::post('/kebun/{penanam}/tanaman/{tanamanDitanam}/aktivitas', [KebunController::class, 'quickLog'])->name('kebun.quick-log');

Route::delete('/kebun/{penanam}/aktivitas/{aktivitas}', [KebunController::class, 'cancelActivity'])->name('kebun.activity.cancel');

Route::post('/jadwal-perawatan/{jadwal}/aktivitas', [KebunController::class, 'recordActivity'])
    ->middleware('throttle:10,1')
    ->name('jadwal-perawatan.record');

// FITUR ADMIN
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        // DASHBOARD ADMIN
        Route::get('/', DashboardController::class)
            ->name('dashboard');

        // DAFTAR DATA
        Route::get('/{resource}', [ResourceController::class, 'index'])
            ->whereIn('resource', [
                'tanaman',
                'penamans',
                'aturan-perawatan',
            ])
            ->name('resources.index');

        // TAMBAH DATA
        Route::get('/{resource}/create', [ResourceController::class, 'create'])
            ->whereIn('resource', [
                'tanaman',
                'penamans',
                'aturan-perawatan',
            ])
            ->name('resources.create');

        Route::post('/{resource}', [ResourceController::class, 'store'])
            ->whereIn('resource', [
                'tanaman',
                'penamans',
                'aturan-perawatan',
            ])
            ->name('resources.store');

        // EDIT DATA
        Route::get('/{resource}/{id}/edit', [ResourceController::class, 'edit'])
            ->whereIn('resource', [
                'tanaman',
                'penamans',
                'aturan-perawatan',
            ])
            ->whereNumber('id')
            ->name('resources.edit');

        Route::match(
            ['PUT', 'PATCH'],
            '/{resource}/{id}',
            [ResourceController::class, 'update']
        )
            ->whereIn('resource', [
                'tanaman',
                'penamans',
                'aturan-perawatan',
            ])
            ->whereNumber('id')
            ->name('resources.update');

        // HAPUS DATA
        Route::delete('/{resource}/{id}', [ResourceController::class, 'destroy'])
            ->whereIn('resource', [
                'tanaman',
                'penamans',
                'aturan-perawatan',
            ])
            ->whereNumber('id')
            ->name('resources.destroy');
    });

// CEK JUMLAH TANAMAN
Route::get('/cek-tanaman', function () {
    return response()->json([
        'total_tanaman' => Tanaman::count(),
        'tanaman_aktif' => Tanaman::where('is_active', true)->count(),
        'daftar_tanaman' => Tanaman::where('is_active', true)
            ->orderBy('nama')
            ->pluck('nama'),
    ]);
});

// HAPUS TANAMAN JAHE, TETAP PERTAHANKAN JAHE MERAH
Route::get('/hapus-jahe', function () {
    $jumlah = Tanaman::where('nama', 'Jahe')->delete();

    return response()->json([
        'pesan' => 'Proses penghapusan selesai',
        'jumlah_dihapus' => $jumlah,
    ]);
});

Route::post('/admin/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('user.dashboard');
})->name('admin.logout');
