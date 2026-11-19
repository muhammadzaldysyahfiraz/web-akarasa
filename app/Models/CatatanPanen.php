<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanPanen extends Model
{
    protected $fillable = [
        'tanaman_ditanam_id',
        'tanggal',
        'catatan',
        'is_reminder',
        'is_done',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_reminder' => 'boolean',
        'is_done' => 'boolean',
    ];

    public function planting()
    {
        return $this->belongsTo(TanamanDitanam::class, 'tanaman_ditanam_id');
    }
}