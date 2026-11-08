<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penanam extends Model
{
    protected $table = 'penanam';

    protected $fillable = ['nama', 'phone', 'catatan', 'created_by', 'access_code_hash'];

    protected $hidden = ['access_code_hash'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tanamanDitanam(): HasMany
    {
        return $this->hasMany(TanamanDitanam::class);
    }
}
