<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AturanPerawatan;
use App\Models\JadwalPerawatan;
use App\Models\Penanam;
use App\Models\Tanaman;
use App\Models\TanamanDitanam;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'tanaman' => Tanaman::where('is_active', true)->count(),
                'penanam' => Penanam::count(),
                'ditanam' => TanamanDitanam::count(),
                'jadwal' => JadwalPerawatan::where('status', 'pending')->count(),
                'aturan' => AturanPerawatan::count(),
            ],
            // Mengambil semua tanaman aktif
            'featuredPlants' => Tanaman::where('is_active', true)
                ->orderBy('nama')
                ->get(),

            'upcoming' => JadwalPerawatan::with([
                    'tanamanDitanam.tanaman',
                    'tanamanDitanam.penanam',
                    'aturanPerawatan'
                ])
                ->where('status', 'pending')
                ->orderBy('perawatan_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
