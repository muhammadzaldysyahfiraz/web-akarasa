<?php

use App\Models\TanamanDitanam;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('kebun:hapus-panen-lama', function () {
    $deleted = TanamanDitanam::query()
        ->where('status', 'dipanen')
        ->whereHas('panen', fn ($query) => $query->whereDate('dipanen_at', '<=', today()->subMonths(2)))
        ->delete();

    $this->info("{$deleted} tanaman dipanen dihapus.");
})->purpose('Hapus tanaman yang sudah dipanen lebih dari dua bulan');

Schedule::command('kebun:hapus-panen-lama')->dailyAt('02:00');
