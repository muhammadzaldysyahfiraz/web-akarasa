<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TanamanSeeder extends Seeder
{
    public function run(): void
    {
        $tanamans = [
            [
                'nama' => 'Daun Mint',
                'nama_ilmiah' => 'Mentha',
                'kategori' => 'Tanaman Daun',
            ],
            [
                'nama' => 'Daun Pandan',
                'nama_ilmiah' => 'Pandanus amaryllifolius',
                'kategori' => 'Tanaman Daun',
            ],
            [
                'nama' => 'Jahe Merah',
                'nama_ilmiah' => 'Zingiber officinale var. rubrum',
                'kategori' => 'Tanaman Rimpang',
            ],
            [
                'nama' => 'Kemangi',
                'nama_ilmiah' => 'Ocimum basilicum',
                'kategori' => 'Tanaman Daun',
            ],
            [
                'nama' => 'Kencur',
                'nama_ilmiah' => 'Kaempferia galanga',
                'kategori' => 'Tanaman Rimpang',
            ],
            [
                'nama' => 'Kumis Kucing',
                'nama_ilmiah' => 'Orthosiphon aristatus',
                'kategori' => 'Tanaman Herbal',
            ],
            [
                'nama' => 'Kunyit',
                'nama_ilmiah' => 'Curcuma longa',
                'kategori' => 'Tanaman Rimpang',
            ],
            [
                'nama' => 'Lengkuas',
                'nama_ilmiah' => 'Alpinia galanga',
                'kategori' => 'Tanaman Rimpang',
            ],
            [
                'nama' => 'Lidah Buaya',
                'nama_ilmiah' => 'Aloe vera',
                'kategori' => 'Tanaman Herbal',
            ],
            [
                'nama' => 'Rosella',
                'nama_ilmiah' => 'Hibiscus sabdariffa',
                'kategori' => 'Tanaman Bunga',
            ],
            [
                'nama' => 'Sambiloto',
                'nama_ilmiah' => 'Andrographis paniculata',
                'kategori' => 'Tanaman Herbal',
            ],
            [
                'nama' => 'Seledri',
                'nama_ilmiah' => 'Apium graveolens',
                'kategori' => 'Tanaman Daun',
            ],
            [
                'nama' => 'Serai',
                'nama_ilmiah' => 'Cymbopogon citratus',
                'kategori' => 'Tanaman Herbal',
            ],
            [
                'nama' => 'Temulawak',
                'nama_ilmiah' => 'Curcuma xanthorrhiza',
                'kategori' => 'Tanaman Rimpang',
            ],
        ];

        foreach ($tanamans as $tanaman) {
            DB::table('tanaman')->updateOrInsert(
                ['nama' => $tanaman['nama']],
                array_merge($tanaman, [
                    'kebutuhan_cahaya' => 'Sinar matahari cukup',
                    'kebutuhan_air' => 'Penyiraman teratur',
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
