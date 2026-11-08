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

   Seeder membuat akun admin `admin@kebuntoga.local`. Bila `ADMIN_PASSWORD`
   tidak diisi, password acak sementara dan kode akses kebun contoh dicetak ke
   output seeder. Simpan secara aman; kode kebun disimpan di database sebagai
   hash. Untuk pengembangan, akun admin dapat diatur melalui `ADMIN_EMAIL` dan
   `ADMIN_PASSWORD` pada `.env`.

5. Pasang dan build asset:

   ```powershell
   npm install
   npm run build
   ```

6. Jalankan server lokal:

   ```powershell
   php artisan serve
   ```

## Fitur MVP

- Katalog tanaman aktif dengan pencarian dan filter kategori.
- Detail tanaman, panduan, kebutuhan cahaya/air, dan rentang perkiraan panen.
- Pencatatan tanaman oleh penanam tanpa akun; kode akses melindungi kebun.
- Dashboard kebun, jadwal tanggal, riwayat perawatan, dan aksi tandai selesai.
- Jadwal dihasilkan dari aturan tiap tanaman dan dibuat tanpa duplikasi.
- Jadwal dibuat pada jendela 90 hari ke depan, atau sampai estimasi panen maksimum bila lebih panjang.
- Area admin terlindungi autentikasi dengan CRUD tanaman, penanam, dan aturan.
- API Resource di prefix `/api/v1`.

Estimasi panen adalah rentang perkiraan, bukan kepastian. Produk tidak memberikan
diagnosis penyakit atau rekomendasi medis.

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
