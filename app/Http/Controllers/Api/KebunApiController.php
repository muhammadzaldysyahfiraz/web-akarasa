<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JadwalPerawatanResource;
use App\Http\Resources\TanamanDitanamResource;
use App\Models\JadwalPerawatan;
use App\Models\Penanam;
use App\Services\PerawatanScheduleService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KebunApiController extends Controller
{
    public function garden(Penanam $penanam, PerawatanScheduleService $schedules): AnonymousResourceCollection
    {
        $plantings = $penanam->tanamanDitanam()->with(['tanaman', 'jadwalPerawatan.aturanPerawatan', 'panen'])->get();
        foreach ($plantings as $planting) {
            $schedules->generateRollingHorizon($planting);
        }

        return TanamanDitanamResource::collection($plantings->load('jadwalPerawatan.aturanPerawatan'));
    }

    public function care(Request $request, Penanam $penanam, PerawatanScheduleService $schedules): AnonymousResourceCollection
    {
        $filters = $request->validate(['tanggal' => ['nullable', 'date_format:Y-m-d']]);

        $plantings = $penanam->tanamanDitanam()->with('tanaman')->get();
        foreach ($plantings as $planting) {
            $schedules->generateRollingHorizon($planting);
        }

        $query = JadwalPerawatan::query()
            ->whereHas('tanamanDitanam', fn ($query) => $query->where('penanam_id', $penanam->id))
            ->with(['aturanPerawatan', 'tanamanDitanam.tanaman'])
            ->orderBy('perawatan_at');

        if (isset($filters['tanggal'])) {
            $query->whereDate('perawatan_at', $filters['tanggal']);
        }

        return JadwalPerawatanResource::collection($query->get());
    }
}
