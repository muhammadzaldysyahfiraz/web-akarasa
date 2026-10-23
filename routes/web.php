<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\KebunController;
use App\Http\Controllers\PenanamController;
use App\Http\Controllers\TanamanController;
use App\Http\Controllers\TanamanDitanamController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/tanaman', [TanamanController::class, 'index'])->name('tanaman.index');
Route::get('/tanaman/{tanaman}', [TanamanController::class, 'show'])->name('tanaman.show');
Route::get('/kebunku', [PenanamController::class, 'index'])->name('penanam.index');
Route::get('/tanam', [TanamanDitanamController::class, 'create'])->name('tanaman-ditanam.create');
Route::post('/tanaman-ditanam', [TanamanDitanamController::class, 'store'])->middleware('throttle:10,1')->name('tanaman-ditanam.store');
Route::get('/kebun/{penanam}', [KebunController::class, 'show'])->name('kebun.show');
Route::post('/kebun/{penanam}/akses', [KebunController::class, 'access'])->middleware('throttle:10,1')->name('kebun.access');
Route::patch('/kebun/{penanam}/jadwal/{jadwal}/complete', [KebunController::class, 'complete'])->name('kebun.complete');

Route::get('/admin/login', [AuthController::class, 'create'])->middleware('guest')->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'store'])->middleware('guest')->middleware('throttle:5,1')->name('admin.login.store');
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/{resource}', [ResourceController::class, 'index'])
        ->whereIn('resource', ['tanaman', 'penamans', 'aturan-perawatan'])
        ->name('resources.index');
    Route::get('/{resource}/create', [ResourceController::class, 'create'])
        ->whereIn('resource', ['tanaman', 'penamans', 'aturan-perawatan'])
        ->name('resources.create');
    Route::post('/{resource}', [ResourceController::class, 'store'])
        ->whereIn('resource', ['tanaman', 'penamans', 'aturan-perawatan'])
        ->name('resources.store');
    Route::get('/{resource}/{id}/edit', [ResourceController::class, 'edit'])
        ->whereIn('resource', ['tanaman', 'penamans', 'aturan-perawatan'])
        ->whereNumber('id')
        ->name('resources.edit');
    Route::match(['PUT', 'PATCH'], '/{resource}/{id}', [ResourceController::class, 'update'])
        ->whereIn('resource', ['tanaman', 'penamans', 'aturan-perawatan'])
        ->whereNumber('id')
        ->name('resources.update');
    Route::delete('/{resource}/{id}', [ResourceController::class, 'destroy'])
        ->whereIn('resource', ['tanaman', 'penamans', 'aturan-perawatan'])
        ->whereNumber('id')
        ->name('resources.destroy');
});
