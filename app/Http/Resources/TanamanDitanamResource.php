<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TanamanDitanamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $dates = $this->estimasiPanen();
        $estimate = null;

        if ($dates) {
            $estimate = [
                'mulai' => $dates['mulai']->toDateString(),
                'sampai' => $dates['sampai']->toDateString(),
            ];
        }

        return [
            'id' => $this->id,
            'tanaman' => new TanamanResource($this->whenLoaded('tanaman')),
            'ditanam_at' => $this->ditanam_at?->toDateString(),
            'quantity' => $this->quantity,
            'location' => $this->location,
            'catatan' => $this->catatan,
            'status' => $this->status,
            'estimasi_panen' => $estimate,
            'jadwal_perawatan' => JadwalPerawatanResource::collection($this->whenLoaded('jadwalPerawatan')),
            'panen' => PanenResource::collection($this->whenLoaded('panen')),
        ];
    }
}
