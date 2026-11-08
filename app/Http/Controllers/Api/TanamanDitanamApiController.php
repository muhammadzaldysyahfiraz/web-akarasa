<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TanamanDitanamResource;
use App\Models\Penanam;
use App\Models\TanamanDitanam;
use App\Services\PerawatanScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TanamanDitanamApiController extends Controller
{
    public function store(Request $request, PerawatanScheduleService $schedules): TanamanDitanamResource
    {
        $data = $request->validate([
            'penanam_id' => ['required', 'exists:penanam,id'],
            'access_code' => ['required', 'string', 'max:128'],
            'tanaman_id' => [
                'required',
                Rule::exists('tanaman', 'id')->where('is_active', true)->whereNull('deleted_at'),
            ],
            'ditanam_at' => ['required', 'date', 'before_or_equal:today'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:4294967295'],
            'location' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:5000'],
        ]);

        $planting = DB::transaction(function () use ($data, $schedules): TanamanDitanam {
            $penanam = Penanam::query()->lockForUpdate()->findOrFail($data['penanam_id']);
            abort_unless(Hash::check($data['access_code'], $penanam->access_code_hash), 403, 'Kode akses kebun tidak sesuai.');
            unset($data['access_code']);

            $planting = TanamanDitanam::create([
                ...$data,
                'quantity' => $data['quantity'] ?? 1,
                'status' => 'bertumbuh',
            ]);

            $schedules->generateRollingHorizon($planting);

            return $planting;
        });

        return new TanamanDitanamResource($planting->load(['tanaman.aturanPerawatan', 'jadwalPerawatan', 'panen']));
    }
}
