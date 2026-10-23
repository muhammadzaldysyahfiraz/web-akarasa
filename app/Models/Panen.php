<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Panen extends Model
{
    protected $table = 'panen';

    protected $fillable = [
        'tanaman_ditanam_id', 'dipanen_at', 'quantity', 'unit', 'catatan',
    ];

    protected function casts(): array
    {
        return ['dipanen_at' => 'date'];
    }

    public function tanamanDitanam(): BelongsTo
    {
        return $this->belongsTo(TanamanDitanam::class);
    }
}
