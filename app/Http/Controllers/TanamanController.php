<?php

namespace App\Http\Controllers;

use App\Models\Tanaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TanamanController extends Controller
{
    public function index(): RedirectResponse
    {
        // Ambil ID tanaman terakhir yang dipilih.
        $tanamanId = session('tanaman_terakhir_dilihat');

        // Jika belum ada tanaman yang dipilih, kembali ke Dashboard.
        if (!$tanamanId) {
            return redirect()->route(
                auth()->user()->role === 'admin'
                    ? 'admin.dashboard'
                    : 'user.dashboard'
            );
        }

        // Cari tanaman yang terakhir dipilih.
        $tanaman = Tanaman::where('is_active', true)
            ->find($tanamanId);

        // Jika tanaman sudah tidak aktif atau tidak ditemukan.
        if (!$tanaman) {
            session()->forget('tanaman_terakhir_dilihat');

            return redirect()->route(
                auth()->user()->role === 'admin'
                    ? 'admin.dashboard'
                    : 'user.dashboard'
            );
        }

        // Arahkan ke halaman detail tanaman tersebut.
        return redirect()->route('tanaman.show', $tanaman);
    }

    public function show(Tanaman $tanaman): View
    {
        abort_unless($tanaman->is_active, 404);

        // Simpan tanaman terakhir yang dibuka.
        session(['tanaman_terakhir_dilihat' => $tanaman->id]);

        return view('tanaman.show', [
            'tanaman' => $tanaman->load([
                'aturanPerawatan' => fn ($query) => $query->where('is_active', true),
            ]),
        ]);
    }
}
