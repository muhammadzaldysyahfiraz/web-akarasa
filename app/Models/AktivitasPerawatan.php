<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasPerawatan extends Model
{
    protected $table = 'aktivitas_perawatan';

    protected $fillable = [
        'tanaman_ditanam_id', 'jadwal_perawatan_id', 'performed_at',
        'user_id', 'nama_pelaksana', 'catatan',
    ];

    protected function casts(): array
    {
        return ['performed_at' => 'datetime'];
    }

    public function tanamanDitanam(): BelongsTo
    {
        return $this->belongsTo(TanamanDitanam::class);
    }

    public function jadwalPerawatan(): BelongsTo
    {
        return $this->belongsTo(JadwalPerawatan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
