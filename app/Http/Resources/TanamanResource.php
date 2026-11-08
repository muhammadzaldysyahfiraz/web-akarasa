<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TanamanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nama_ilmiah' => $this->nama_ilmiah,
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi,
            'manfaat' => $this->manfaat,
            'panduan_penanaman' => $this->panduan_penanaman,
            'panduan_perawatan' => $this->panduan_perawatan,
            'kebutuhan_cahaya' => $this->kebutuhan_cahaya,
            'kebutuhan_air' => $this->kebutuhan_air,
            'min_hari_panen' => $this->min_hari_panen,
            'max_hari_panen' => $this->max_hari_panen,
            'aturan_perawatan' => AturanPerawatanResource::collection($this->whenLoaded('aturanPerawatan')),
        ];
    }
}
