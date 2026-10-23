<?php

namespace Database\Seeders;

use App\Models\AturanPerawatan;
use App\Models\Penanam;
use App\Models\Tanaman;
use App\Models\TanamanDitanam;
use App\Models\User;
use App\Services\PerawatanScheduleService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TanamanSeeder extends Seeder
{
    public function run(): void
    {
        $plants = [
            [
                'nama' => 'Jahe',
                'nama_ilmiah' => 'Zingiber officinale',
                'kategori' => 'Rimpang',
                'deskripsi' => 'Tanaman rimpang tropis yang mudah ditanam di pot atau kebun rumah.',
                'manfaat' => 'Rimpangnya umum dimanfaatkan sebagai bumbu dan minuman tradisional.',
                'panduan_penanaman' => 'Tanam rimpang bertunas sedalam 3-5 cm pada media gembur dengan drainase baik.',
                'panduan_perawatan' => 'Jaga media tetap lembap, hindari genangan, dan tambahkan kompos secara berkala.',
                'kebutuhan_cahaya' => 'Cahaya terang tidak langsung atau matahari pagi.',
                'kebutuhan_air' => 'Siram saat permukaan media mulai mengering.',
                'min_hari_panen' => 240,
                'max_hari_panen' => 300,
            ],
            [
                'nama' => 'Kunyit',
                'nama_ilmiah' => 'Curcuma longa',
                'kategori' => 'Rimpang',
                'deskripsi' => 'Tanaman rimpang berdaun lebar yang dapat tumbuh baik di pot besar.',
                'manfaat' => 'Rimpangnya dimanfaatkan sebagai bumbu dan bahan minuman tradisional.',
                'panduan_penanaman' => 'Tanam potongan rimpang bertunas pada media subur dan gembur.',
                'panduan_perawatan' => 'Letakkan di tempat terang, siram secukupnya, dan buang gulma di sekitar tanaman.',
                'kebutuhan_cahaya' => 'Cahaya terang, toleran terhadap naungan ringan.',
                'kebutuhan_air' => 'Jaga kelembapan media tanpa membuatnya tergenang.',
                'min_hari_panen' => 210,
                'max_hari_panen' => 300,
            ],
            [
                'nama' => 'Serai',
                'nama_ilmiah' => 'Cymbopogon citratus',
                'kategori' => 'Herba',
                'deskripsi' => 'Rumput aromatik yang tumbuh berumpun dan cocok ditanam di halaman.',
                'manfaat' => 'Daunnya umum digunakan sebagai bumbu masak dan minuman.',
                'panduan_penanaman' => 'Tanam anakan pada media subur dengan ruang tumbuh dan drainase yang cukup.',
                'panduan_perawatan' => 'Siram teratur saat masa awal tumbuh dan rapikan daun kering.',
                'kebutuhan_cahaya' => 'Matahari langsung setidaknya 6 jam sehari.',
                'kebutuhan_air' => 'Siram saat lapisan atas media mulai kering.',
                'min_hari_panen' => 90,
                'max_hari_panen' => 120,
            ],
            [
                'nama' => 'Kencur',
                'nama_ilmiah' => 'Kaempferia galanga',
                'kategori' => 'Rimpang',
                'deskripsi' => 'Tanaman rimpang rendah yang dapat ditanam pada pot dengan naungan ringan.',
                'manfaat' => 'Rimpangnya dikenal sebagai bumbu dan bahan minuman tradisional.',
                'panduan_penanaman' => 'Tanam rimpang bertunas dangkal pada media gembur.',
                'panduan_perawatan' => 'Jaga kelembapan secukupnya dan kurangi penyiraman saat daun menguning.',
                'kebutuhan_cahaya' => 'Cahaya terang dengan naungan ringan.',
                'kebutuhan_air' => 'Siram secukupnya setelah media mulai mengering.',
                'min_hari_panen' => 180,
                'max_hari_panen' => 240,
            ],
        ];

        foreach ($plants as $attributes) {
            $plant = Tanaman::updateOrCreate(['nama' => $attributes['nama']], [
                ...$attributes,
                'is_active' => true,
            ]);

            $rules = match ($plant->nama) {
                'Jahe' => [
                    ['Penyiraman', 'penyiraman', 2, 0, null, 'Siram secukupnya dan pastikan air tidak menggenang.'],
                    ['Pemupukan', 'pemupukan', 30, 30, 210, 'Tambahkan kompos atau pupuk organik.'],
                ],
                'Kunyit' => [
                    ['Penyiraman', 'penyiraman', 2, 0, null, 'Periksa kelembapan media sebelum menyiram.'],
                    ['Pemupukan', 'pemupukan', 30, 30, 180, 'Berikan pupuk organik sesuai petunjuk produk.'],
                ],
                'Serai' => [
                    ['Penyiraman', 'penyiraman', 3, 0, null, 'Siram pangkal rumpun, sesuaikan dengan kondisi cuaca.'],
                    ['Pemupukan', 'pemupukan', 45, 30, 90, 'Tambahkan kompos di sekitar rumpun.'],
                    ['Pemangkasan', 'pemangkasan', 60, 60, null, 'Potong daun kering atau rusak.'],
                ],
                default => [
                    ['Penyiraman', 'penyiraman', 3, 0, null, 'Jaga media lembap tanpa genangan.'],
                    ['Pemupukan', 'pemupukan', 45, 30, 180, 'Tambahkan kompos secukupnya.'],
                ],
            };

            foreach ($rules as [$name, $type, $interval, $start, $end, $instruction]) {
                AturanPerawatan::updateOrCreate(
                    ['tanaman_id' => $plant->id, 'nama' => $name],
                    [
                        'type' => $type,
                        'instruksi' => $instruction,
                        'interval_days' => $interval,
                        'mulai_setelah_hari' => $start,
                        'berakhir_setelah_hari' => $end,
                        'is_active' => true,
                    ],
                );
            }
        }

        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@kebuntoga.local'))->firstOrFail();
        foreach ([
            ['nama' => 'Siti Aminah', 'phone' => '081200000001'],
            ['nama' => 'Rina Wulandari', 'phone' => '081200000002'],
            ['nama' => 'Dewi Kartika', 'phone' => '081200000003'],
        ] as $growerData) {
            $accessCode = Str::password(12);
            $grower = Penanam::updateOrCreate(
                ['nama' => $growerData['nama']],
                [
                    'phone' => $growerData['phone'],
                    'created_by' => $admin->id,
                    'access_code_hash' => Hash::make($accessCode),
                ],
            );

            if ($this->command) {
                $this->command->line("Kode akses kebun {$grower->nama}: {$accessCode}");
            }

            if ($grower->tanamanDitanam()->exists()) {
                continue;
            }

            $plantNames = match ($grower->nama) {
                'Siti Aminah' => ['Jahe', 'Serai'],
                'Rina Wulandari' => ['Kunyit'],
                default => ['Kencur'],
            };

            foreach ($plantNames as $index => $plantName) {
                $plant = Tanaman::where('nama', $plantName)->firstOrFail();
                $planting = TanamanDitanam::create([
                    'penanam_id' => $grower->id,
                    'tanaman_id' => $plant->id,
                    'ditanam_at' => now()->subDays(40 + ($index * 20))->toDateString(),
                    'quantity' => $index + 1,
                    'location' => $index === 0 ? 'Halaman depan' : 'Teras',
                    'status' => 'bertumbuh',
                ]);
                app(PerawatanScheduleService::class)->generateRollingHorizon($planting);
            }
        }
    }
}
