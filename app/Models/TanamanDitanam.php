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

    public static function catatanDenganFase(?string $catatan, string $fase): ?string
    {
        return self::PHASE_NOTE_PREFIX.$fase.']'.($catatan === null || $catatan === '' ? '' : "\n".$catatan);
    }

    public function getFasePertumbuhanAttribute(): string
    {
        preg_match('/^\[fase:(bibit|anakan|dewasa)\](?:\n|$)/', $this->attributes['catatan'] ?? '', $matches);

        return $matches[1] ?? 'bibit';
    }

    public function getCatatanAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $note = preg_replace('/^\[fase:(?:bibit|anakan|dewasa)\](?:\n|$)/', '', $value);

        return $note === '' ? null : $note;
    }

    /**
     * The catalog range represents the full growing cycle; the selected phase
     * determines the remaining portion without adding columns to the schema.
     *
     * @return array{mulai: CarbonImmutable, sampai: CarbonImmutable}|null
     */
    public function estimasiPanen(): ?array
    {
        $plantingDate = $this->ditanam_at;
        $minimumDays = $this->tanaman?->min_hari_panen;
        $maximumDays = $this->tanaman?->max_hari_panen;

        if (! $plantingDate || $minimumDays === null || $maximumDays === null) {
            return null;
        }

        $remainingCycle = match ($this->fase_pertumbuhan) {
            'anakan' => 2 / 3,
            'dewasa' => 1 / 3,
            default => 1,
        };

        return [
            'mulai' => CarbonImmutable::parse($plantingDate)->addDays((int) round($minimumDays * $remainingCycle)),
            'sampai' => CarbonImmutable::parse($plantingDate)->addDays((int) round($maximumDays * $remainingCycle)),
        ];
    }
}
