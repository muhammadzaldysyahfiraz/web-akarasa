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
use Illuminate\Support\Facades\Hash;

class KebunApiController extends Controller
{
    public function garden(Request $request, Penanam $penanam, PerawatanScheduleService $schedules): AnonymousResourceCollection
    {
        $this->authorizeGrower($request, $penanam);

        $plantings = $penanam->tanamanDitanam()->with(['tanaman', 'jadwalPerawatan.aturanPerawatan', 'panen'])->get();
        foreach ($plantings as $planting) {
            $schedules->generateRollingHorizon($planting);
        }

        return TanamanDitanamResource::collection($plantings->load('jadwalPerawatan.aturanPerawatan'));
    }

    public function care(Request $request, Penanam $penanam, PerawatanScheduleService $schedules): AnonymousResourceCollection
    {
        $this->authorizeGrower($request, $penanam);
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

    private function authorizeGrower(Request $request, Penanam $penanam): void
    {
        $code = $request->header('X-Kode-Akses');
        abort_unless(is_string($code) && Hash::check($code, $penanam->access_code_hash), 403, 'Kode akses kebun tidak sesuai.');
    }
}
