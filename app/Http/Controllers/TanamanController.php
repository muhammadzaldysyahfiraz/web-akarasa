<?php

namespace App\Http\Controllers;

use App\Models\Tanaman;
use App\Models\TanamanDitanam;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TanamanController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (view()->exists('tanaman.index')) {
            return view('tanaman.index', [
                'tanamans' => Tanaman::where('is_active', true)->orderBy('nama')->get(),
            ]);
        }

        if (view()->exists('tanaman')) {
            return view('tanaman', [
                'tanamans' => Tanaman::where('is_active', true)->orderBy('nama')->get(),
            ]);
        }

        $tanamanId = session('tanaman_terakhir_dilihat');

        if (!$tanamanId) {
            return redirect()->route(
                auth()->user()?->role === 'admin'
                    ? 'admin.dashboard'
                    : 'user.dashboard'
            );
        }

        $tanaman = Tanaman::where('is_active', true)->find($tanamanId);

        if (!$tanaman) {
            session()->forget('tanaman_terakhir_dilihat');

            return redirect()->route(
                auth()->user()?->role === 'admin'
                    ? 'admin.dashboard'
                    : 'user.dashboard'
            );
        }

        return redirect()->route('tanaman.show', $tanaman);
    }

    public function show(Tanaman $tanaman): View
    {
        abort_unless($tanaman->is_active, 404);

        session(['tanaman_terakhir_dilihat' => $tanaman->id]);

        $plantings = TanamanDitanam::query()
            ->with([
                'penanam',
                'jadwalPerawatan.aturanPerawatan',
                'jadwalPerawatan.aktivitasPerawatan',
            ])
            ->where('tanaman_id', $tanaman->id)
            ->latest('ditanam_at')
            ->get();

        return view('tanaman.show', [
            'tanaman' => $tanaman->load([
                'aturanPerawatan' => fn ($query) => $query->where('is_active', true),
            ]),
            'plantings' => $plantings,
        ]);
    }
}