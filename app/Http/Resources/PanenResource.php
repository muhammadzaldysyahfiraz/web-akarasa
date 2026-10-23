<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PanenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dipanen_at' => $this->dipanen_at?->toDateString(),
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'catatan' => $this->catatan,
        ];
    }
}
