<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AturanPerawatanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'type' => $this->type,
            'instruksi' => $this->instruksi,
            'interval_days' => $this->interval_days,
            'mulai_setelah_hari' => $this->mulai_setelah_hari,
            'berakhir_setelah_hari' => $this->berakhir_setelah_hari,
        ];
    }
}
