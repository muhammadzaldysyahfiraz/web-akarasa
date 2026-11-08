<?php

namespace App\Http\Controllers;

use App\Models\TanamanDitanam;
use App\Services\PerawatanScheduleService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenanamController extends Controller
{
    public function index(
        Request $request,
        PerawatanScheduleService $schedules,
    ): View {
        $filters = $request->validate([
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $schedules->markMissed();
        $plantings = TanamanDitanam::query()
            ->with([
                'penanam',
                'tanaman',
                'jadwalPerawatan.aturanPerawatan',
                'jadwalPerawatan.aktivitasPerawatan.user',
                'panen',
            ])
            ->orderByDesc('ditanam_at')
            ->get();

        foreach ($plantings as $planting) {
            $schedules->generateRollingHorizon($planting);
        }

        $plantings->load('jadwalPerawatan.aturanPerawatan', 'jadwalPerawatan.aktivitasPerawatan.user');

        return view('kebun.show', [
            'plantings' => $plantings,
            'selectedDate' => $filters['tanggal'] ?? null,
        ]);
    }
}
