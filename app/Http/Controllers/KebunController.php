<?php

namespace App\Http\Controllers;

use App\Models\AktivitasPerawatan;
use App\Models\JadwalPerawatan;
use App\Models\Panen;
use App\Models\Penanam;
use App\Models\TanamanDitanam;
use App\Services\PerawatanScheduleService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KebunController extends Controller
{
    public function show(Penanam $penanam, PerawatanScheduleService $schedules): View
    {
        $schedules->markMissed();
        $plantings = $penanam->tanamanDitanam()
            ->with([
                'tanaman',
                'jadwalPerawatan.aturanPerawatan',
                'jadwalPerawatan.aktivitasPerawatan',
                'panen',
            ])
            ->orderByDesc('ditanam_at')
            ->get();

        foreach ($plantings as $planting) {
            $schedules->generateRollingHorizon($planting);
        }

        $plantings->load([
            'jadwalPerawatan.aturanPerawatan',
            'jadwalPerawatan.aktivitasPerawatan',
        ]);

        return view('kebun.show', [
            'penanam' => $penanam,
            'plantings' => $plantings,
        ]);
    }

    public function complete(Penanam $penanam, JadwalPerawatan $jadwal): RedirectResponse
    {
        DB::transaction(function () use ($penanam, $jadwal): void {
            $lockedSchedule = JadwalPerawatan::query()
                ->whereKey($jadwal->id)
                ->whereHas('tanamanDitanam', fn ($query) => $query->where('penanam_id', $penanam->id))
                ->whereHas('aturanPerawatan', fn ($query) => $query->where('type', 'penyiraman'))
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSchedule->status === 'completed') {
                return;
            }

            abort_unless(in_array($lockedSchedule->status, ['pending', 'missed'], true), 409, 'Aktivitas ini tidak dapat ditandai selesai.');

            $lockedSchedule->update(['status' => 'completed']);
            $lockedSchedule->aktivitasPerawatan()->firstOrCreate(
                [],
                [
                    'tanaman_ditanam_id' => $lockedSchedule->tanaman_ditanam_id,
                    'performed_at' => now(),
                    'nama_pelaksana' => $penanam->nama,
                ],
            );
        });

        return redirect()->route('kebun.show', $penanam)->with('status', 'Perawatan berhasil dicatat.');
    }

    public function cancelActivity(Penanam $penanam, AktivitasPerawatan $aktivitas): RedirectResponse
    {
        DB::transaction(function () use ($penanam, $aktivitas): void {
            $lockedActivity = AktivitasPerawatan::query()
                ->whereKey($aktivitas->id)
                ->whereNotNull('jadwal_perawatan_id')
                ->whereHas('tanamanDitanam', fn ($query) => $query->where('penanam_id', $penanam->id))
                ->whereHas('jadwalPerawatan.aturanPerawatan', fn ($query) => $query->where('type', 'penyiraman'))
                ->lockForUpdate()
                ->firstOrFail();
            $performedAt = CarbonImmutable::parse($lockedActivity->performed_at);
            $weekStart = CarbonImmutable::today()->startOfWeek(CarbonImmutable::MONDAY);

            abort_unless(
                $performedAt->betweenIncluded($weekStart, $weekStart->endOfWeek(CarbonImmutable::SUNDAY)),
                409,
                'Aktivitas hanya dapat dibatalkan pada minggu yang sama.',
            );

            if ($lockedActivity->jadwal_perawatan_id) {
                JadwalPerawatan::query()
                    ->whereKey($lockedActivity->jadwal_perawatan_id)
                    ->update(['status' => 'pending']);
            }

            $lockedActivity->delete();
        });

        return redirect()->route('kebun.show', $penanam)->with('status', 'Aktivitas berhasil dibatalkan.');
    }

    public function harvest(
        Penanam $penanam,
        TanamanDitanam $planting,
        PerawatanScheduleService $schedules
    ): RedirectResponse {
        DB::transaction(function () use ($penanam, $planting, $schedules): void {
            $lockedPlanting = TanamanDitanam::query()
                ->whereKey($planting->id)
                ->where('penanam_id', $penanam->id)
                ->with('tanaman')
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($lockedPlanting->status === 'bertumbuh', 409, 'Tanaman ini sudah tidak dalam masa pertumbuhan.');
            $estimate = $lockedPlanting->estimasiPanen();
            abort_unless(
                $estimate && $estimate['mulai']->lte(CarbonImmutable::today()),
                409,
                'Tanaman belum memasuki perkiraan masa panen.',
            );

            Panen::create([
                'tanaman_ditanam_id' => $lockedPlanting->id,
                'dipanen_at' => today(),
            ]);
            $lockedPlanting->update(['status' => 'dipanen']);
            $schedules->generateRollingHorizon($lockedPlanting);
        });

        return redirect()->route('kebun.show', $penanam)->with('status', 'Panen berhasil dicatat.');
    }

    public function cancelHarvest(Penanam $penanam, TanamanDitanam $planting): RedirectResponse
    {
        DB::transaction(function () use ($penanam, $planting): void {
            $lockedPlanting = TanamanDitanam::query()
                ->whereKey($planting->id)
                ->where('penanam_id', $penanam->id)
                ->lockForUpdate()
                ->firstOrFail();
            $harvest = $lockedPlanting->panen()->latest('dipanen_at')->lockForUpdate()->firstOrFail();
            $harvestDate = CarbonImmutable::parse($harvest->dipanen_at);
            $weekStart = CarbonImmutable::today()->startOfWeek(CarbonImmutable::MONDAY);

            abort_unless(
                $harvestDate->betweenIncluded($weekStart, $weekStart->endOfWeek(CarbonImmutable::SUNDAY)),
                409,
                'Panen hanya dapat dibatalkan pada minggu yang sama.',
            );

            $harvest->delete();
            $lockedPlanting->update(['status' => 'bertumbuh']);
        });

        return redirect()->route('kebun.show', $penanam)->with('status', 'Panen berhasil dibatalkan.');
    }
}
