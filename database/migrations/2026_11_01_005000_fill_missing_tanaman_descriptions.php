<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $descriptions = [
            'Daun Mint' => 'Tanaman beraroma segar dengan daun bergerigi yang tumbuh cepat dan cocok ditanam di pot.',
            'Daun Pandan' => 'Tanaman berdaun panjang dan harum yang tumbuh berumpun di lingkungan hangat dan lembap.',
            'Jahe Merah' => 'Tanaman rimpang beraroma kuat dengan batang semu dan daun memanjang yang tumbuh baik di tanah gembur.',
            'Kemangi' => 'Tanaman berdaun harum dengan batang tegak yang mudah tumbuh di tempat hangat dan mendapat cukup cahaya.',
            'Kencur' => 'Tanaman rimpang berdaun lebar yang tumbuh rendah dan sesuai ditanam pada media gembur dengan naungan ringan.',
            'Kumis Kucing' => 'Tanaman berbunga dengan benang sari panjang menyerupai kumis yang tumbuh baik di tempat terang.',
            'Kunyit' => 'Tanaman rimpang berdaun lebar yang tumbuh berumpun dan dapat dibudidayakan di kebun maupun pot besar.',
            'Lengkuas' => 'Tanaman rimpang berbatang tegak dengan daun panjang yang menyukai tanah subur dan cukup lembap.',
            'Lidah Buaya' => 'Tanaman sukulen berdaun tebal yang menyimpan air dan tumbuh baik pada media porous dengan banyak cahaya.',
            'Rosella' => 'Tanaman berbunga dengan kelopak merah mencolok yang dapat tumbuh di kebun pada area terbuka.',
            'Sambiloto' => 'Tanaman herba berbatang tegak dan berdaun hijau yang dapat ditanam pada tanah gembur di tempat terang.',
            'Seledri' => 'Tanaman berdaun majemuk dengan aroma khas yang menyukai media subur, lembap, dan tidak tergenang.',
            'Serai' => 'Rumput aromatik yang tumbuh berumpun dengan daun panjang dan cocok ditanam di halaman yang mendapat sinar matahari.',
            'Temulawak' => 'Tanaman rimpang tropis dengan daun lebar yang tumbuh berumpun pada tanah gembur dan kaya bahan organik.',
        ];

        foreach ($descriptions as $nama => $deskripsi) {
            DB::table('tanaman')
                ->where('nama', $nama)
                ->where(function ($query) {
                    $query->whereNull('deskripsi')->orWhere('deskripsi', '');
                })
                ->update(['deskripsi' => $deskripsi]);
        }
    }

    public function down(): void
    {
        // Existing descriptions are preserved to avoid removing user-edited data.
    }
};
