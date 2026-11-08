<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AturanPerawatan extends Model
{
    protected $table = 'aturan_perawatan';

    protected $fillable = [
        'tanaman_id', 'nama', 'type', 'instruksi', 'interval_days',
        'mulai_setelah_hari', 'berakhir_setelah_hari', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function tanaman(): BelongsTo
    {
        return $this->belongsTo(Tanaman::class);
    }

    public function jadwalPerawatan(): HasMany
    {
        return $this->hasMany(JadwalPerawatan::class);
    }
}
