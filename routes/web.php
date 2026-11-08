<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\KebunController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenanamController;
use App\Http\Controllers\TanamanController;
use App\Http\Controllers\TanamanDitanamController;
use App\Models\Tanaman;
use Illuminate\Support\Facades\Route;

// HALAMAN UTAMA
Route::get('/', function () {
    return redirect()->route('warga.login');
})->name('home');

// LOGIN ADMIN
Route::get('/admin/login', [AuthController::class, 'create'])
    ->name('admin.login');

Route::post('/admin/login', [AuthController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('admin.login.store');

// LOGIN WARGA
Route::get('/warga/login', [AuthController::class, 'showWargaLogin'])
    ->name('warga.login');

Route::post('/warga/login', [AuthController::class, 'loginWarga'])
    ->middleware('throttle:5,1')
    ->name('warga.login.store');

// REGISTER WARGA
Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register.store');

// LOGOUT WARGA
Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// KATALOG TANAMAN DAN DETAIL
Route::middleware('auth')->group(function () {

    Route::get('/tanaman', [TanamanController::class, 'index'])
        ->name('tanaman.index');

    Route::get('/tanaman/{tanaman}', [TanamanController::class, 'show'])
        ->name('tanaman.show');

    Route::get('/jadwal-perawatan', [ResourceController::class, 'schedule'])
        ->name('jadwal-perawatan.index');

    Route::get('/laporan', LaporanController::class)
        ->name('laporan.index');
});

// FITUR KHUSUS WARGA
Route::middleware(['auth', 'user'])->group(function () {

    // DASHBOARD WARGA
    Route::get('/dashboard', DashboardController::class)
        ->name('user.dashboard');

    // KEBUN SAYA
    Route::get('/kebunku', [PenanamController::class, 'index'])
        ->name('penanam.index');

    Route::get('/tanam', [TanamanDitanamController::class, 'create'])
        ->name('tanaman-ditanam.create');

    Route::post('/tanaman-ditanam', [TanamanDitanamController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('tanaman-ditanam.store');

    Route::get('/kebun/{penanam}', [KebunController::class, 'show'])
        ->name('kebun.show');

    Route::patch(
        '/kebun/{penanam}/jadwal/{jadwal}/complete',
        [KebunController::class, 'complete']
    )->name('kebun.complete');

    Route::post('/jadwal-perawatan/{jadwal}/aktivitas', [KebunController::class, 'recordActivity'])
        ->middleware('throttle:10,1')
        ->name('jadwal-perawatan.record');
});

// FITUR KHUSUS ADMIN
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // DASHBOARD ADMIN
        Route::get('/', DashboardController::class)
            ->name('dashboard');

        // LOGOUT ADMIN
        Route::post('/logout', [AuthController::class, 'destroy'])
            ->name('logout');

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
