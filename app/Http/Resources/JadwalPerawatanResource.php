<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JadwalPerawatanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'perawatan_at' => $this->perawatan_at?->toDateString(),
            'status' => $this->status,
            'aturan' => new AturanPerawatanResource($this->whenLoaded('aturanPerawatan')),
            'tanaman_ditanam_id' => $this->tanaman_ditanam_id,
        ];
    }
}
