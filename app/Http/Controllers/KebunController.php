<?php

namespace App\Http\Controllers;

use App\Models\JadwalPerawatan;
use App\Models\Penanam;
use App\Models\User;
use App\Services\PerawatanActivityRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KebunController extends Controller
{
    public function show(Penanam $penanam): RedirectResponse
    {
        return redirect()->route('penanam.index');
    }

    public function complete(
        Request $request,
        Penanam $penanam,
        JadwalPerawatan $jadwal,
        PerawatanActivityRecorder $activities,
    ): RedirectResponse
    {
        abort_unless(
            JadwalPerawatan::query()
                ->whereKey($jadwal->id)
                ->whereHas('tanamanDitanam', fn ($query) => $query->where('penanam_id', $penanam->id))
                ->exists(),
            404,
        );
        $user = $request->user();
        abort_unless($user instanceof User, 401);
        $recorded = $activities->complete($jadwal, $user);

        return redirect()
            ->route('penanam.index')
            ->with('status', $recorded ? 'Perawatan berhasil dicatat.' : 'Perawatan ini sudah selesai dicatat.');
    }

    public function recordActivity(
        Request $request,
        JadwalPerawatan $jadwal,
        PerawatanActivityRecorder $activities,
    ): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);
        $recorded = $activities->complete($jadwal, $user);

        return redirect()
            ->route('jadwal-perawatan.index', [
                'month' => $jadwal->perawatan_at->format('Y-m'),
                'tanggal' => $jadwal->perawatan_at->toDateString(),
            ])
            ->with('status', $recorded
                ? 'Perawatan berhasil dicatat.'
                : 'Perawatan ini sudah selesai dicatat.');
    }
}
