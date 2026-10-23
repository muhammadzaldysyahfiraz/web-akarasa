<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tanaman extends Model
{
    use SoftDeletes;

    protected $table = 'tanaman';

    protected $fillable = [
        'nama', 'nama_ilmiah', 'kategori', 'deskripsi', 'manfaat',
        'panduan_penanaman', 'panduan_perawatan', 'kebutuhan_cahaya',
        'kebutuhan_air', 'min_hari_panen', 'max_hari_panen', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function tanamanDitanam(): HasMany
    {
        return $this->hasMany(TanamanDitanam::class);
    }

    public function aturanPerawatan(): HasMany
    {
        return $this->hasMany(AturanPerawatan::class);
    }
}
