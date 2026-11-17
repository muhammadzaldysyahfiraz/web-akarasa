<?php

namespace App\Http\Controllers;

use App\Models\AktivitasPerawatan;
use App\Models\JadwalPerawatan;
use App\Models\Penanam;
use App\Models\TanamanDitanam;
use App\Services\PerawatanScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KebunController extends Controller
{
    public function show(Request $request, Penanam $penanam, PerawatanScheduleService $schedules): View
    {
        $filters = $request->validate(['tanggal' => ['nullable', 'date_format:Y-m-d']]);
        $schedules->markMissed();
        $plantings = $penanam->tanamanDitanam()
            ->with(['tanaman', 'jadwalPerawatan.aturanPerawatan', 'aktivitasPerawatan', 'panen'])
            ->orderByDesc('ditanam_at')
            ->get();

        foreach ($plantings as $planting) {
            $schedules->generateRollingHorizon($planting);
        }

        $plantings->load(['jadwalPerawatan.aturanPerawatan', 'aktivitasPerawatan']);

        return view('kebun.show', [
            'penanam' => $penanam,
            'plantings' => $plantings,
            'selectedDate' => $filters['tanggal'] ?? null,
        ]);
    }

    public function access(Request $request, Penanam $penanam): RedirectResponse
    {
        return redirect()->route('kebun.show', $penanam);
    }

    public function quickLog(Request $request, Penanam $penanam, TanamanDitanam $tanamanDitanam): RedirectResponse
    {
        abort_unless($tanamanDitanam->penanam_id === $penanam->id, 404);

        $data = $request->validate([
            'aktivitas' => ['required', 'string', 'max:255'],
        ]);

        AktivitasPerawatan::create([
            'tanaman_ditanam_id' => $tanamanDitanam->id,
            'performed_at' => now(),
            'nama_pelaksana' => $penanam->nama,
            'catatan' => $data['aktivitas'],
        ]);

        if (str_contains(strtolower($data['aktivitas']), 'siram')) {
            $tanamanDitanam->update([
                'sudah_disiram' => true,
                'terakhir_disiram_at' => today()->toDateString(),
            ]);
        }

        return redirect()
            ->route('kebun.show', $penanam)
            ->with('status', 'Aktivitas "' . $data['aktivitas'] . '" berhasil dicatat.');
    }

    public function cancelActivity(Penanam $penanam, AktivitasPerawatan $aktivitas): RedirectResponse
    {
        abort_unless($aktivitas->tanamanDitanam?->penanam_id === $penanam->id, 404);

        DB::transaction(function () use ($aktivitas): void {
            if ($aktivitas->jadwal_perawatan_id) {
                JadwalPerawatan::query()
                    ->whereKey($aktivitas->jadwal_perawatan_id)
                    ->update(['status' => 'pending']);
            }

            $aktivitas->delete();
        });

        return redirect()
            ->route('kebun.show', $penanam)
            ->with('status', 'Catatan perawatan berhasil dibatalkan.');
    }

    public function complete(Request $request, Penanam $penanam, JadwalPerawatan $jadwal): RedirectResponse
    {
        DB::transaction(function () use ($penanam, $jadwal): void {
            $lockedSchedule = JadwalPerawatan::query()
                ->whereKey($jadwal->id)
                ->whereHas('tanamanDitanam', fn ($query) => $query->where('penanam_id', $penanam->id))
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSchedule->status === 'completed') {
                return;
            }

            abort_unless($lockedSchedule->status === 'pending', 409, 'Jadwal yang terlewat tidak dapat ditandai selesai.');

            $lockedSchedule->update(['status' => 'completed']);
            $lockedSchedule->aktivitasPerawatan()->firstOrCreate(
                [],
                [
                    'tanaman_ditanam_id' => $lockedSchedule->tanaman_ditanam_id,
                    'performed_at' => now(),
                    'nama_pelaksana' => $penanam->nama,
                    'catatan' => $lockedSchedule->aturanPerawatan?->nama ?? 'Penyiraman terjadwal',
                ],
            );
        });

        return redirect()->route('kebun.show', $penanam)->with('status', 'Perawatan berhasil dicatat.');
    }

    public function recordActivity(Request $request, JadwalPerawatan $jadwal): RedirectResponse
    {
        DB::transaction(function () use ($jadwal): void {
            $lockedSchedule = JadwalPerawatan::query()
                ->whereKey($jadwal->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($lockedSchedule->status === 'pending', 409, 'Jadwal ini sudah ditandai selesai atau terlewat.');

            $lockedSchedule->update(['status' => 'completed']);
            $lockedSchedule->aktivitasPerawatan()->firstOrCreate(
                [],
                [
                    'tanaman_ditanam_id' => $lockedSchedule->tanaman_ditanam_id,
                    'performed_at' => now(),
                ],
            );
        });

        return redirect()
            ->route('jadwal-perawatan.index', [
                'month' => $jadwal->perawatan_at->format('Y-m'),
                'tanggal' => $jadwal->perawatan_at->toDateString(),
            ])
            ->with('status', 'Aktivitas perawatan berhasil dicatat.');
    }
}