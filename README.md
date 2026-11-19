# KebunTOGA

Platform Laravel untuk katalog Tanaman Obat Keluarga, pencatatan kebun rumahan,
jadwal perawatan, dan estimasi panen.

## Stack

- PHP 8.3+
- Laravel 12
- MySQL 8 / MariaDB 11
- Blade, Livewire 3, Vite

## Menjalankan aplikasi

1. Siapkan database MySQL/MariaDB bernama `kebuntoga` dan akun database untuk aplikasi.
2. Salin `.env.example` ke `.env`, lalu sesuaikan `DB_HOST`, `DB_DATABASE`,
   `DB_USERNAME`, dan `DB_PASSWORD`.
3. Pasang dependensi dan buat application key:

   ```powershell
   composer install
   if (-not (Test-Path .env)) { Copy-Item .env.example .env }
   php artisan key:generate
   ```

4. Jalankan migrasi dan data contoh:

   ```powershell
   php artisan migrate:fresh --seed
   ```

   Seeder pengembangan membuat akun admin `admin@akarasa.com` dengan password
   `admin123`. Ganti password tersebut sebelum aplikasi dipakai di lingkungan
   bersama atau produksi.

5. Pasang dan build asset:

   ```powershell
   npm install
   npm run build
   ```

6. Jalankan server lokal:

   ```powershell
   php artisan serve
   ```

   Pada terminal lain, jalankan scheduler agar tanaman yang sudah dipanen
   lebih dari dua bulan dihapus otomatis:

   ```powershell
   php artisan schedule:work
   ```

## Fitur MVP

- Dashboard publik berisi rekap dan laporan perawatan, dengan unduhan PDF.
- Katalog tanaman aktif dengan pencarian, filter kategori, panduan, dan info umum.
- Kebunku: pilih identitas berdasarkan nama saja, tanpa akun atau PIN.
- Nama penanam baru ditambahkan saat mendaftarkan tanaman; nama kembar ditolak.
- Pengunjung dapat melihat kebun dan mencatat penyiraman atau panen.
- Tanaman didaftarkan dengan fase bibit, anakan, atau dewasa; estimasi panen
  mengikuti fase tersebut.
- Aktivitas dapat dibatalkan hanya pada minggu yang sama dengan pencatatannya.
- Setelah panen, pengingat berhenti dan tanaman dihapus setelah dua bulan.
- Jadwal dihasilkan dari aturan tiap tanaman dan dibuat tanpa duplikasi.
- Area admin terlindungi autentikasi untuk mengelola katalog dan aturan perawatan.
- API Resource di prefix `/api/v1`.

Estimasi panen adalah rentang perkiraan, bukan kepastian. Produk tidak memberikan
diagnosis penyakit atau rekomendasi medis. Skema database dan bentuk API tetap
mengikuti kontrak proyek; kode akses tetap digunakan oleh endpoint API, bukan
oleh alur website untuk penanam.

Sampai tersedia angka khusus per tanaman dan fase, rentang katalog dianggap
sebagai satu siklus penuh: bibit memakai seluruh rentang, anakan memakai sisa
dua pertiga, dan dewasa memakai sisa sepertiga.

## API

Endpoint publik utama: `GET /api/v1/tanaman`, `GET /api/v1/tanaman/{id}`,
`GET /api/v1/penanam`, `POST /api/v1/tanaman-tanaman`,
`GET /api/v1/penamans/{id}/garden`, dan
`GET /api/v1/penamans/{id}/perawatan`.

Permintaan API untuk melihat/mengubah kebun harus mengirim kode akses melalui
header `X-Kode-Akses`. Untuk menyelesaikan jadwal gunakan
`PATCH /api/v1/jadwal-perawatan/{id}/complete` dengan `access_code` pada body.
Endpoint `/api/v1/admin/*` memerlukan sesi admin yang aktif.
Permintaan mutasi admin API juga harus mengirim token CSRF sesi melalui
`X-CSRF-TOKEN`; endpoint publik pencatatan tanaman dan penyelesaian jadwal
memakai kode akses kebun.
