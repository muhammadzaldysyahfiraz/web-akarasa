<?php

use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\JadwalPerawatanApiController;
use App\Http\Controllers\Api\KebunApiController;
use App\Http\Controllers\Api\PenanamApiController;
use App\Http\Controllers\Api\TanamanApiController;
use App\Http\Controllers\Api\TanamanDitanamApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->prefix('v1')->group(function () {
    Route::get('/tanaman', [TanamanApiController::class, 'index']);
    Route::get('/tanaman/{tanaman}', [TanamanApiController::class, 'show']);
    Route::get('/penanam', [PenanamApiController::class, 'index']);
    Route::post('/tanaman-tanaman', [TanamanDitanamApiController::class, 'store']);
    Route::get('/penamans/{penanam}/garden', [KebunApiController::class, 'garden']);
    Route::get('/penamans/{penanam}/perawatan', [KebunApiController::class, 'care']);
    Route::patch('/jadwal-perawatan/{perawatan}/complete', [JadwalPerawatanApiController::class, 'complete']);

    Route::middleware('auth')->prefix('admin')->group(function () {
        foreach ([
            'tanaman' => 'tanaman',
            'penamans' => 'penamans',
            'aturan-perawatan' => 'aturan-perawatan',
        ] as $uri => $resource) {
            Route::get($uri, [AdminApiController::class, 'index'])->defaults('resource', $resource);
            Route::post($uri, [AdminApiController::class, 'store'])->defaults('resource', $resource);
            Route::get($uri.'/{id}', [AdminApiController::class, 'show'])->defaults('resource', $resource);
            Route::patch($uri.'/{id}', [AdminApiController::class, 'update'])->defaults('resource', $resource);
            Route::delete($uri.'/{id}', [AdminApiController::class, 'destroy'])->defaults('resource', $resource);
        }

        Route::get('/tanaman-tanaman', [AdminApiController::class, 'index'])->defaults('resource', 'tanaman-tanaman');
        Route::get('/jadwal-perawatan', [AdminApiController::class, 'index'])->defaults('resource', 'jadwal-perawatan');
    });
});
