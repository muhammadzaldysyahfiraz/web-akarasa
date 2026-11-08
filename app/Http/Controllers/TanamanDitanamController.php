<?php

namespace App\Http\Controllers;

use App\Models\Penanam;
use App\Models\Tanaman;
use App\Models\TanamanDitanam;
use App\Services\PerawatanScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TanamanDitanamController extends Controller
{
    public function create(): View
    {
        return view('tanaman-ditanam.create', [
            'tanamans' => Tanaman::query()->where('is_active', true)->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request, PerawatanScheduleService $schedules): RedirectResponse
    {
        $data = $request->validate([
            'penanam_nama' => ['required', 'string', 'max:255'],
            'access_code' => ['required', 'string', 'min:8', 'max:128'],
            'tanaman_id' => [
                'required',
                Rule::exists('tanaman', 'id')->where('is_active', true)->whereNull('deleted_at'),
            ],
            'ditanam_at' => ['required', 'date', 'before_or_equal:today'],
            'quantity' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'location' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:5000'],
        ]);

        $penanam = DB::transaction(function () use ($data, $request, $schedules): Penanam {
            $penanam = Penanam::query()
                ->where('nama', $data['penanam_nama'])
                ->lockForUpdate()
                ->first();

            if ($penanam) {
                abort_unless(Hash::check($data['access_code'], $penanam->access_code_hash), 403, 'Kode akses kebun tidak sesuai.');
            } else {
                $penanam = Penanam::create([
                    'nama' => $data['penanam_nama'],
                    'access_code_hash' => Hash::make($data['access_code']),
                ]);
            }

            $planting = TanamanDitanam::create([
                'penanam_id' => $penanam->id,
                'tanaman_id' => $data['tanaman_id'],
                'ditanam_at' => $data['ditanam_at'],
                'quantity' => $data['quantity'],
                'location' => $data['location'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'status' => 'bertumbuh',
            ]);

            $schedules->generateRollingHorizon($planting);
            $request->session()->regenerate();
            $request->session()->put('garden_access.'.$penanam->id, true);

            return $penanam;
        });

        return redirect()
            ->route('kebun.show', $penanam)
            ->with('status', 'Tanaman berhasil ditambahkan ke kebun.');
    }
}
