<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JadwalPerawatan extends Model
{
    protected $table = 'jadwal_perawatan';

    protected $fillable = [
        'tanaman_ditanam_id', 'aturan_perawatan_id', 'perawatan_at', 'status',
    ];

    protected function casts(): array
    {
        return ['perawatan_at' => 'date'];
    }

    public function tanamanDitanam(): BelongsTo
    {
        return $this->belongsTo(TanamanDitanam::class);
    }

    public function aturanPerawatan(): BelongsTo
    {
        return $this->belongsTo(AturanPerawatan::class);
    }

    public function aktivitasPerawatan(): HasOne
    {
        return $this->hasOne(AktivitasPerawatan::class);
    }
}
