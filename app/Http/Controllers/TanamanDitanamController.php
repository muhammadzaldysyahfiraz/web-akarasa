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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TanamanDitanamController extends Controller
{
    public function create(): View
    {
        return view('tanaman-ditanam.create', [
            'penanams' => Penanam::query()->orderBy('nama')->get(['id', 'nama']),
            'tanamans' => Tanaman::query()
                ->where('is_active', true)
                ->orderBy('nama')
                ->get(),
        ]);
    }

    public function store(
        Request $request,
        PerawatanScheduleService $schedules
    ): RedirectResponse {
        $data = $request->validate([
            'penanam_id' => ['nullable', 'required_without:penanam_nama_baru', 'exists:penanam,id'],
            'penanam_nama_baru' => ['nullable', 'required_without:penanam_id', 'string', 'max:255'],

            'tanaman_id' => [
                'required',
                Rule::exists('tanaman', 'id')
                    ->where('is_active', true)
                    ->whereNull('deleted_at'),
            ],

            'ditanam_at' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'fase_pertumbuhan' => ['required', Rule::in(['bibit', 'anakan', 'dewasa'])],
            'quantity' => [
                'sometimes',
                'integer',
                'min:1',
                'max:4294967295',
            ],

            'location' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:5000'],
        ]);

        if (! empty($data['penanam_id']) && ! empty($data['penanam_nama_baru'])) {
            throw ValidationException::withMessages([
                'penanam_nama_baru' => 'Pilih penanam yang sudah terdaftar atau masukkan nama baru, jangan keduanya.',
            ]);
        }

        $penanam = DB::transaction(function () use (
            $data,
            $schedules
        ): Penanam {
            if (! empty($data['penanam_id'])) {
                $penanam = Penanam::query()->findOrFail($data['penanam_id']);
            } else {
                $nama = trim($data['penanam_nama_baru']);
                $duplicate = Penanam::query()
                    ->whereRaw('LOWER(TRIM(nama)) = ?', [mb_strtolower($nama)])
                    ->exists();

                if ($duplicate) {
                    throw ValidationException::withMessages([
                        'penanam_nama_baru' => 'Nama penanam tersebut sudah terdaftar. Silakan pilih namanya dari daftar.',
                    ]);
                }

                $penanam = Penanam::create([
                    'nama' => $nama,
                    'access_code_hash' => Hash::make(Str::random(64)),
                ]);
            }

            $planting = TanamanDitanam::create([
                'penanam_id' => $penanam->id,
                'tanaman_id' => $data['tanaman_id'],
                'ditanam_at' => $data['ditanam_at'],
                'quantity' => $data['quantity'] ?? 1,
                'location' => $data['location'] ?? null,
                'catatan' => TanamanDitanam::catatanDenganFase(
                    $data['catatan'] ?? null,
                    $data['fase_pertumbuhan'],
                ),
                'status' => 'bertumbuh',
            ]);

            $schedules->generateRollingHorizon($planting);

            return $penanam;
        });

        return redirect()
            ->route('kebun.show', $penanam)
            ->with('status', 'Tanaman berhasil ditambahkan ke kebun.');
    }
}
