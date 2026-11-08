<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JadwalPerawatanResource;
use App\Models\JadwalPerawatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class JadwalPerawatanApiController extends Controller
{
    public function complete(Request $request, JadwalPerawatan $perawatan): JadwalPerawatanResource
    {
        $data = $request->validate([
            'access_code' => ['required', 'string', 'max:128'],
            'catatan' => ['nullable', 'string', 'max:5000'],
        ]);

        $schedule = DB::transaction(function () use ($data, $perawatan): JadwalPerawatan {
            $schedule = JadwalPerawatan::query()
                ->with('tanamanDitanam.penanam')
                ->lockForUpdate()
                ->findOrFail($perawatan->id);

            abort_unless(Hash::check($data['access_code'], $schedule->tanamanDitanam->penanam->access_code_hash), 403, 'Kode akses kebun tidak sesuai.');

            if ($schedule->status === 'completed') {
                return $schedule;
            }

            abort_unless($schedule->status === 'pending', 409, 'Jadwal yang terlewat tidak dapat ditandai selesai.');

            $schedule->update(['status' => 'completed']);
            $schedule->aktivitasPerawatan()->firstOrCreate(
                [],
                [
                    'tanaman_ditanam_id' => $schedule->tanaman_ditanam_id,
                    'performed_at' => now(),
                    'catatan' => $data['catatan'] ?? null,
                ],
            );

            return $schedule;
        });

        return new JadwalPerawatanResource($schedule->load('aturanPerawatan'));
    }
}
