<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TanamanDitanam extends Model
{
    protected $table = 'tanaman_ditanam';

    private const PHASE_NOTE_PREFIX = '[fase:';

    protected $fillable = [
        'penanam_id',
        'tanaman_id',
        'ditanam_at',
        'quantity',
        'location',
        'catatan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'ditanam_at' => 'date',
        ];
    }

    public function penanam(): BelongsTo
    {
        return $this->belongsTo(Penanam::class);
    }

    public function tanaman(): BelongsTo
    {
        return $this->belongsTo(Tanaman::class);
    }

    public function jadwalPerawatan(): HasMany
    {
        return $this->hasMany(JadwalPerawatan::class);
    }

    public function aktivitasPerawatan(): HasMany
    {
        return $this->hasMany(AktivitasPerawatan::class);
    }

    public function panen(): HasMany
    {
        return $this->hasMany(Panen::class);
    }

    public function estimasiPanen(): ?array
    {
        if (! $this->ditanam_at || ! $this->tanaman) {
            return null;
        }

        $minHari = $this->tanaman->min_hari_panen;
        $maxHari = $this->tanaman->max_hari_panen ?? $minHari;

        if ($minHari === null && $maxHari === null) {
            return null;
        }

        $minHari = $minHari ?? $maxHari;

        return [
            'mulai' => $this->ditanam_at->copy()->addDays((int) $minHari),
            'sampai' => $this->ditanam_at->copy()->addDays((int) $maxHari),
        ];
    }
}