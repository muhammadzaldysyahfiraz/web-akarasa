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
                'deskripsi' => 'Tanaman beraroma segar dengan daun bergerigi yang tumbuh cepat dan cocok ditanam di pot.',
            ],
            [
                'nama' => 'Daun Pandan',
                'nama_ilmiah' => 'Pandanus amaryllifolius',
                'kategori' => 'Tanaman Daun',
                'deskripsi' => 'Tanaman berdaun panjang dan harum yang tumbuh berumpun di lingkungan hangat dan lembap.',
            ],
            [
                'nama' => 'Jahe Merah',
                'nama_ilmiah' => 'Zingiber officinale var. rubrum',
                'kategori' => 'Tanaman Rimpang',
                'deskripsi' => 'Tanaman rimpang beraroma kuat dengan batang semu dan daun memanjang yang tumbuh baik di tanah gembur.',
            ],
            [
                'nama' => 'Kemangi',
                'nama_ilmiah' => 'Ocimum basilicum',
                'kategori' => 'Tanaman Daun',
                'deskripsi' => 'Tanaman berdaun harum dengan batang tegak yang mudah tumbuh di tempat hangat dan mendapat cukup cahaya.',
            ],
            [
                'nama' => 'Kencur',
                'nama_ilmiah' => 'Kaempferia galanga',
                'kategori' => 'Tanaman Rimpang',
                'deskripsi' => 'Tanaman rimpang berdaun lebar yang tumbuh rendah dan sesuai ditanam pada media gembur dengan naungan ringan.',
            ],
            [
                'nama' => 'Kumis Kucing',
                'nama_ilmiah' => 'Orthosiphon aristatus',
                'kategori' => 'Tanaman Herbal',
                'deskripsi' => 'Tanaman berbunga dengan benang sari panjang menyerupai kumis yang tumbuh baik di tempat terang.',
            ],
            [
                'nama' => 'Kunyit',
                'nama_ilmiah' => 'Curcuma longa',
                'kategori' => 'Tanaman Rimpang',
                'deskripsi' => 'Tanaman rimpang berdaun lebar yang tumbuh berumpun dan dapat dibudidayakan di kebun maupun pot besar.',
            ],
            [
                'nama' => 'Lengkuas',
                'nama_ilmiah' => 'Alpinia galanga',
                'kategori' => 'Tanaman Rimpang',
                'deskripsi' => 'Tanaman rimpang berbatang tegak dengan daun panjang yang menyukai tanah subur dan cukup lembap.',
            ],
            [
                'nama' => 'Lidah Buaya',
                'nama_ilmiah' => 'Aloe vera',
                'kategori' => 'Tanaman Herbal',
                'deskripsi' => 'Tanaman sukulen berdaun tebal yang menyimpan air dan tumbuh baik pada media porous dengan banyak cahaya.',
            ],
            [
                'nama' => 'Rosella',
                'nama_ilmiah' => 'Hibiscus sabdariffa',
                'kategori' => 'Tanaman Bunga',
                'deskripsi' => 'Tanaman berbunga dengan kelopak merah mencolok yang dapat tumbuh di kebun pada area terbuka.',
            ],
            [
                'nama' => 'Sambiloto',
                'nama_ilmiah' => 'Andrographis paniculata',
                'kategori' => 'Tanaman Herbal',
                'deskripsi' => 'Tanaman herba berbatang tegak dan berdaun hijau yang dapat ditanam pada tanah gembur di tempat terang.',
            ],
            [
                'nama' => 'Seledri',
                'nama_ilmiah' => 'Apium graveolens',
                'kategori' => 'Tanaman Daun',
                'deskripsi' => 'Tanaman berdaun majemuk dengan aroma khas yang menyukai media subur, lembap, dan tidak tergenang.',
            ],
            [
                'nama' => 'Serai',
                'nama_ilmiah' => 'Cymbopogon citratus',
                'kategori' => 'Tanaman Herbal',
                'deskripsi' => 'Rumput aromatik yang tumbuh berumpun dengan daun panjang dan cocok ditanam di halaman yang mendapat sinar matahari.',
            ],
            [
                'nama' => 'Temulawak',
                'nama_ilmiah' => 'Curcuma xanthorrhiza',
                'kategori' => 'Tanaman Rimpang',
                'deskripsi' => 'Tanaman rimpang tropis dengan daun lebar yang tumbuh berumpun pada tanah gembur dan kaya bahan organik.',
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
