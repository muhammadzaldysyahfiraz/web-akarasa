<?php

namespace App\Http\Controllers;

use App\Models\JadwalPerawatan;
use App\Models\Penanam;
use App\Services\PerawatanScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class KebunController extends Controller
{
    public function show(Request $request, Penanam $penanam, PerawatanScheduleService $schedules): View
    {
        if (! $request->session()->get('garden_access.'.$penanam->id, false)) {
            return view('kebun.access', compact('penanam'));
        }

        $filters = $request->validate(['tanggal' => ['nullable', 'date_format:Y-m-d']]);
        $schedules->markMissed();
        $plantings = $penanam->tanamanDitanam()
            ->with(['tanaman', 'jadwalPerawatan.aturanPerawatan', 'panen'])
            ->orderByDesc('ditanam_at')
            ->get();

        foreach ($plantings as $planting) {
            $schedules->generateRollingHorizon($planting);
        }

        $plantings->load('jadwalPerawatan.aturanPerawatan');

        return view('kebun.show', [
            'penanam' => $penanam,
            'plantings' => $plantings,
            'selectedDate' => $filters['tanggal'] ?? null,
        ]);
    }

    public function access(Request $request, Penanam $penanam): RedirectResponse
    {
        $data = $request->validate(['access_code' => ['required', 'string', 'max:128']]);
        abort_unless(Hash::check($data['access_code'], $penanam->access_code_hash), 403, 'Kode akses kebun tidak sesuai.');
        $request->session()->regenerate();
        $request->session()->put('garden_access.'.$penanam->id, true);

        return redirect()->route('kebun.show', $penanam);
    }

    public function complete(Request $request, Penanam $penanam, JadwalPerawatan $jadwal): RedirectResponse
    {
        abort_unless($request->session()->get('garden_access.'.$penanam->id, false), 403);

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
                ],
            );
        });

        return redirect()->route('kebun.show', $penanam)->with('status', 'Perawatan berhasil dicatat.');
    }
}
