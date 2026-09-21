# SPESIFIKASI PROYEK — "KampusLMS"

**Mata Kuliah:** Inovasi Sosial (3 SKS, Semester 5)
**Lokasi:** Kecamatan Balikpapan Timur, Kelurahan Lamaru, RT 19
**Stack wajib:** Laravel 12, MySQL/MariaDB
**Dokumen ini adalah kontrak proyek.** Skema database dan kontrak API bersifat WAJIB dan tidak boleh diubah tanpa persetujuan bersama.

---

## 1. Latar Belakang & Sasaran

### 1.1 Nama Produk
> Platform digital yang membantu pengguna menanam, merawat, dan memantau Tanaman Obat Keluarga (TOGA) melalui panduan perawatan, jadwal penyiraman, pencatatan perkembangan tanaman, serta estimasi waktu tanaman dapat dipanen atau dimanfaatkan.

### 1.2 Latar Belakang
Tanaman Obat Keluarga (TOGA) dapat ditanam di lingkungan rumah dan dimanfaatkan sebagai salah satu sumber tanaman yang memiliki kegunaan kesehatan maupun kebutuhan rumah tangga. Namun, keberhasilan menanam TOGA tidak hanya bergantung pada proses penanaman awal, tetapi juga pada perawatan yang dilakukan secara konsisten.

Bagi pengguna yang belum memiliki banyak pengalaman bercocok tanam, beberapa hal dapat menjadi kendala, seperti:

- tidak mengetahui seberapa sering tanaman perlu disiram;
- tidak mengetahui kebutuhan perawatan setiap jenis tanaman;
- lupa melakukan penyiraman atau perawatan;
- tidak mengetahui tahapan pertumbuhan tanaman;
- kesulitan memperkirakan kapan tanaman dapat dipanen atau mulai dimanfaatkan;
- tidak memiliki catatan mengenai perkembangan tanaman yang sedang ditanam.

Masalah tersebut menjadi semakin relevan ketika setiap tanaman memiliki kebutuhan yang berbeda. Jadwal perawatan yang sama tidak selalu sesuai untuk semua tanaman.

Kebun TOGA dirancang untuk mengubah informasi perawatan tanaman menjadi panduan yang lebih praktis dan terpersonalisasi berdasarkan tanaman yang dimiliki pengguna.

### 1.3 Problem Statement
> Pengguna yang ingin menanam TOGA membutuhkan cara yang sederhana untuk mengetahui apa yang harus dilakukan terhadap tanaman mereka, kapan harus melakukannya, dan kapan tanaman tersebut diperkirakan dapat dimanfaatkan.

| No. | Problem | Impact |
| --- | ------- | ------ |
| 1 | Pengguna tidak mengetahui kebutuhan perawatan masing-masing tanaman | Perawatan dapat dilakukan terlalu sering atau terlalu jarang |
| 1 | Pengguna dapat lupa melakukan penyiraman | Tanaman berisiko tidak mendapatkan perawatan secara konsisten |
| 1 | Informasi perawatan tersebar di berbagai sumber | Pengguna harus mencari informasi sendiri |
| 1 | Pengguna sulit mengetahui perkembangan tanaman | Sulit mengetahui apakah tanaman berada pada tahap pertumbuhan yang sesuai |
| 1 | Pengguna tidak mengetahui estimasi waktu panen/pemanfaatan | Ekspektasi terhadap hasil tanaman menjadi tidak jelas |
| 1 | Tidak ada pencatatan tanaman yang dimiliki | Pengguna sulit memantau beberapa tanaman sekaligus |

### Sasaran Pengguna:
#### Primary Users
**Ibu rumah tangga atau pengguna rumahan yang ingin menanam TOGA tetapi membutuhkan bantuan dalam melakukan perawatan dan pemantauan tanaman.**

Karakteristik yang kita targetkan:
- memiliki atau ingin memiliki tanaman TOGA di rumah;
- tidak harus memiliki pengetahuan botani yang mendalam;
- membutuhkan instruksi yang sederhana;
- memiliki beberapa tanaman dengan kebutuhan berbeda;
- membutuhkan pengingat agar perawatan lebih konsisten;
- lebih membutuhkan informasi praktis daripada informasi teknis yang terlalu kompleks.

#### Secondary Users
Kita dapat mempertimbangkan pengguna lain seperti:
- pemula yang ingin belajar menanam TOGA;
- keluarga yang memiliki kebun rumah;
- komunitas tanaman;
- pengguna yang sudah memiliki pengalaman bercocok tanam tetapi ingin melakukan pencatatan digital.

Tapi tetap Primary Users untuk MVP nya

### 1.5 Tujuan Produk
#### Tujuan Utama
Membantu pengguna mengelola perawatan tanaman TOGA secara lebih teratur dan mudah dipahami.

#### Tujuan Spesifik
Kebun TOGA diharapkan dapat membantu pengguna:
1. menemukan informasi tanaman TOGA;
2. memahami kebutuhan dasar setiap tanaman;
3. mengetahui kapan tanaman perlu disiram;
4. mengetahui aktivitas perawatan yang perlu dilakukan;
5. mencatat tanaman yang sedang dimiliki;
6. memantau perkembangan tanaman;
7. memperoleh estimasi waktu panen atau pemanfaatan;
8. mengurangi ketergantungan pada pencarian informasi manual dari berbagai sumber.

### 1.6 Value Proposition
Nilai utama produk dapat dirumuskan menjadi:
> **"Satu tempat untuk mengetahui apa yang harus dilakukan terhadap tanaman TOGA, kapan harus melakukannya, dan kapan hasilnya dapat dimanfaatkan."**

Ada tiga konsep utama:
```
WHAT?
Apa yang harus dilakukan?
        │
        ▼
HOW?
Bagaimana cara melakukannya?
        │
        ▼
WHEN?
Kapan harus dilakukan / kapan hasilnya tersedia?
```

Katalog hanya menjawab:
> "Apa itu tanaman X?"

Sedangkan Kebun TOGA harus menjawab:
> "Tanaman saya sekarang perlu melakukan apa?"

### 1.7 Core User Job
Kalau kita menggunakan perspektif Jobs-to-be-Done, kebutuhan pengguna dapat diringkas menjadi:
> **"Ketika saya menanam tanaman obat di rumah, saya ingin mengetahui perawatan yang harus saya lakukan dan waktunya, sehingga saya dapat menjaga tanaman tetap terawat dan mengetahui kapan tanaman tersebut dapat dimanfaatkan."**

Dari sini muncul tiga kebutuhan inti:
1. **Know**
Pengguna perlu mengetahui:

- tanaman apa yang dimiliki;
- kebutuhan tanaman;
- kondisi/tahap pertumbuhan;
- estimasi waktu panen.

2. **Do**
Pengguna perlu melakukan:

- penyiraman;
- pemupukan;
- pemangkasan;
- aktivitas perawatan lainnya.

3. **Track**
Pengguna perlu dapat:

- mencatat aktivitas;
- melihat riwayat;
- memantau perkembangan;
- mengetahui jadwal berikutnya.

### 1.8 Konsep Produk
**Knowledge Base (Katalog Tanaman)** 

Berisi informasi umum yang dikelola sistem:

- jenis tanaman;
- karakteristik tanaman;
- kebutuhan air;
- kebutuhan cahaya;
- media tanam;
- panduan perawatan;
- tahapan pertumbuhan;
- estimasi waktu panen.

**Kebunku**

Berisi kondisi spesifik milik pengguna:

- tanaman yang sedang ditanam;
- tanggal mulai menanam;
- lokasi tanaman;
- jadwal penyiraman;
- aktivitas perawatan;
- perkembangan;
- estimasi panen.

Ini merupakan **pemisahan domain yang sangat penting**

### 1.9 Aturan Bisnis
1. Rule 01 — Tanaman memiliki informasi dasar
2. Rule 02 — Tanaman pengguna berbeda dari katalog tanaman
3. Rule 03 — Jenis tanaman sudah ditentukan dan ditunjukkan melalui katalog tanaman
4. Rule 04 — Jadwal bergantung pada tanaman
5. Rule 05 — Estimasi panen bukan tanggal absolut

### 1.10 Batasan Produk Awal
Untuk menjaga MVP tetap relistis nih, kita perlu membuat batasan.

Versi awal (Yang Akarasa buat) tidak harus:
- mendiagnosis penyakit tanaman menggunakan AI;
- menentukan kondisi tanah secara otomatis;
- menggunakan sensor IoT;
- menjamin keberhasilan tanaman;
- memberikan diagnosis kesehatan manusia;
- memberikan rekomendasi medis berdasarkan tanaman.

### 1.11 Indikator keberhasilan produk
| Indikator | Tujuan |
| --------- | ------ |
| Pengguna berhasil menambahkan tanaman | Mengukur keberhasilan onboarding |
| Pengguna mengetahui jadwal perawatan berikutnya | Mengukur fungsi utama |
| Pengguna mencatat aktivitas perawatan | Mengukur engagement |
| Pengguna dapat melihat estimasi panen | Mengukur usefulness |
| Pengguna kembali untuk memeriksa dashboard | Mengukur penggunaan berkelanjutan |

### 1.12 Product Principles
4 prinsip desain:

1. **Simple**; Pengguna tidak perlu memahami istilah botani untuk menggunakan aplikasi.

2. **Action-oriented**; Informasi harus berujung pada tindakan.

3. **Personalized**; Informasi umum tanaman harus diterjemahkan menjadi informasi yang relevan dengan tanaman milik pengguna.

4. **Honest about uncertainty**; Estimasi seperti waktu panen harus disajikan sebagai perkiraan, bukan kepastian.

> **catatan pernting** Core problem yang kita selesaikan bukan "memberikan informasi tentang TOGA", melainkan "membantu pengguna mengubah informasi tanaman menjadi tindakan perawatan yang tepat waktu."

---

## 2. Ruang Lingkup

### 2.1 Fitur Wajib

| # | Modul | Aktor | Deskripsi minimum |
|---|-------|-------|-------------------|
| F1 | Katalog Tanaman Toga | Pengunjung | a |
| F2 | Detail & Panduan Tanaman | Pengunjung | Setiap tanaman memiliki halaman detail; Berisi informasi dasar, informasi penanaman, informasi perawatan, informasi hasil. |
| F3 | Manajemen Penanaman | Pengunjung | mengubah informasi umum tanaman menjadi tanaman konkret milik seorang penanam; Pengunjung dapat menambahkan tanaman. |
| F4 | Dashboard Kebun | Penanam | penanam dapat melihat kondisi kebunnya. |
| F5 | Jadwal Perawatan | Penanam | menampilkan aktivitas yang perlu dilakukan. |
| F6 | Estimasi Hasil/Panen | Penanam | menghitung estimasi berdasarkan data tanaman, jadi bahan growth tracking.. |
| F7 | Admin Management | Admin | satu-satunya aktor yang memiliki authentication. |

### 2.2 Fitur tambahan

1. Foto perkembangan (unutk membuat growth journal)
2. Catatan Perawatan (penanam dapat menulis sesuatu)
3. Notifikasi
4. Rekomendasi Tanaman
5. Integrasi Cuaca (hell nah)
6. Pencacatan Panen (isinya: Tanggal panen, Jumlah hasil, Satuan,Catatan)


### 2.3 Di Luar Lingkup (JANGAN dikerjakan)
| Modul | Aktor |
|-------|-------|
| Login penanam | ❌ Tidak diperlukan |
| Registrasi penanam | ❌ Tidak diperlukan |
| E-commerce tanaman/obat | ❌ |
| Pembayaran | ❌ |
| Diagnosis penyakit berbasis AI | ❌ |
| IoT sensor tanaman & tanah | ❌ |
| Integrasi hardware penyiraman otomatis | ❌ |
| Konsultasi dokter penyakit manusia | ❌ |
| Rekomendasi medis dengan bahan TOGA | ❌ |
| Marketplace | ❌ |
| Community | ❌ |
| Weather API | ❌ |

> Proyek tetap fokus pada **Information + Scheduling + Tracking.**

### 2.4 Prioritas Fitur
#### P0 -- core/wajib
Harus ada agar aplikasi memenuhi tujuan utamanya.
> 
Katalog tanaman
 Detail tanaman
 Tambah tanaman
 Nama penanam
 Tanggal tanam
 Dashboard kebun
 Jadwal perawatan
 Filter berdasarkan penanam
 Estimasi panen
 Admin authentication
 CRUD tanaman
 CRUD penanam

#### P1 -- Enhancement
Sangat berguna tetapi aplikasi masih dapat berjalan tanpa fitur ini.
> 
 Care log
 Growth journal
 Foto perkembangan
 Pencatatan panen
 Notifikasi

### 2.5 Use Case Utama
#### US-01 — Melihat tanaman

> Sebagai pengunjung, saya ingin melihat daftar tanaman TOGA sehingga saya dapat mengetahui tanaman yang tersedia.

#### US-02 — Memahami tanaman

> Sebagai pengunjung, saya ingin melihat detail dan panduan suatu tanaman sehingga saya mengetahui cara menanam dan merawatnya.

#### US-03 — Mendaftarkan tanaman

> Sebagai penanam, saya ingin mencatat tanaman yang saya tanam beserta nama dan tanggal tanam sehingga sistem dapat membuat jadwal perawatan untuk tanaman tersebut.

#### US-04 — Melihat kebun

> Sebagai penanam, saya ingin melihat tanaman yang saya tanam berdasarkan nama saya sehingga saya dapat memantau kebun saya.

#### US-05 — Mengetahui jadwal

> Sebagai penanam, saya ingin melihat aktivitas perawatan yang harus dilakukan sehingga saya tidak lupa merawat tanaman.

#### US-06 — Menyelesaikan perawatan

> Sebagai penanam, saya ingin menandai aktivitas perawatan sebagai selesai sehingga sistem dapat mencatat aktivitas yang telah saya lakukan.

#### US-07 — Mengetahui estimasi panen

> Sebagai penanam, saya ingin melihat estimasi waktu tanaman dapat dipanen atau dimanfaatkan sehingga saya memiliki gambaran mengenai hasil tanaman saya.

#### US-08 — Administrasi tanaman

> Sebagai admin, saya ingin mengelola data tanaman sehingga informasi yang tersedia bagi penanam tetap dapat diperbarui.

#### US-09 — Administrasi penanam

> Sebagai admin, saya ingin mengelola data penanam sehingga data tanaman dapat dikaitkan dengan penanam yang tepat.

### 2.6 Kriteria keberhasilan MVP
Penambahan tanaman

#### Sistem harus:

- meminta nama penanam;
- meminta tanaman;
- meminta tanggal tanam;
- menyimpan data;
- menghasilkan jadwal berdasarkan data tanaman;
- menghitung estimasi berdasarkan tanggal tanam.

#### Jadwal
Sistem harus:

- menampilkan tanaman milik penanam;
- menampilkan aktivitas yang perlu dilakukan;
- membedakan jadwal yang sudah/belum selesai;
- memungkinkan penanam menandai aktivitas selesai.

#### Estimasi
Sistem harus:

- menggunakan tanggal tanam sebagai salah satu dasar perhitungan;
- menampilkan estimasi sebagai rentang/perkiraan;
- tidak menyatakan tanggal panen sebagai kepastian mutlak.

#### Admin
Sistem harus:

- mengharuskan authentication untuk area admin;
- mencegah guest mengakses halaman admin;
- menyediakan CRUD tanaman;
- menyediakan CRUD penanam.
---

## 3. Peran & Hak Akses

| Aktor | Authentication | Memiliki akun | Fungsi utama |
|------|:-----:|:-----:|:---------:|
| Pengunjung | No | No | Mengakses fitur publik dan mengelola data kebun melalui identitas penanam |
| Penanam | No | No | Mengelola/mengecek tanaman yang dikaitkan dengan namanya |
| Admin | Yes | Yes | Mengelola/mengecek tanaman yang dikaitkan dengan namanya |

**Catatan**
- Dalam implementasi, orang yang menggunakan website sebagai penanam tetap merupakan guest/non-authenticated user.

- Pengunjung dan Penanam bukan dua role database yang berbeda. "Penanam" adalah identitas data, bukan account.

---

## 4. Skema Database (WAJIB)

### 4.1 Diagram Relasi

```mermaid
erDiagram
    users ||--o{ course_user : "terdaftar"
    users ||--o{ courses : "mengajar"
    users ||--o{ submissions : "mengumpulkan"
    users ||--o{ materials : "mengunggah"
    users ||--o{ notifications : "menerima"

    courses ||--o{ course_user : "punya peserta"
    courses ||--o{ materials : "punya"
    courses ||--o{ assignments : "punya"

    assignments ||--o{ submissions : "dikumpulkan"
    submissions ||--o| grades : "dinilai"
    users ||--o{ grades : "memberi nilai"

    users {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin|dosen|mahasiswa"
        string nim_nip UK "nullable"
        timestamp email_verified_at
        timestamps created_updated
        softdeletes deleted_at
    }

    courses {
        bigint id PK
        string code UK "contoh SI2514024"
        string name
        text description
        tinyint sks
        bigint lecturer_id FK "users.id"
        enum status "draft|active|archived"
        timestamps created_updated
    }

    course_user {
        bigint id PK
        bigint course_id FK
        bigint user_id FK
        timestamp enrolled_at
        timestamps created_updated
    }

    materials {
        bigint id PK
        bigint course_id FK
        bigint uploaded_by FK "users.id"
        string title
        text description
        enum type "file|link"
        string file_path "nullable"
        string original_name "nullable"
        unsignedbigint file_size "nullable"
        string mime_type "nullable"
        string external_url "nullable"
        timestamps created_updated
    }

    assignments {
        bigint id PK
        bigint course_id FK
        bigint created_by FK "users.id"
        string title
        text instructions
        datetime due_at
        unsignedtinyint max_score "default 100"
        boolean allow_late "default true"
        enum status "draft|published"
        timestamps created_updated
    }

    submissions {
        bigint id PK
        bigint assignment_id FK
        bigint user_id FK
        string file_path
        string original_name
        unsignedbigint file_size
        text note "nullable"
        datetime submitted_at
        boolean is_late "default false"
        timestamps created_updated
    }

    grades {
        bigint id PK
        bigint submission_id FK "unique - 1 submission 1 nilai"
        bigint graded_by FK "users.id"
        decimal score
        text feedback "nullable"
        datetime graded_at
        timestamps created_updated
    }

    notifications {
        uuid id PK
        string type
        string notifiable_type
        bigint notifiable_id
        text data
        timestamp read_at
        timestamps created_updated
    }
```

### 4.2 Constraint & Index Wajib

| Tabel | Ketentuan |
|-------|-----------|
| `users` | `email` unique; `nim_nip` unique nullable; index pada `role` |
| `courses` | `code` unique; FK `lecturer_id` → `users.id` `restrictOnDelete` |
| `course_user` | **unique composite** (`course_id`, `user_id`) — cegah enroll ganda |
| `materials` | FK `course_id` `cascadeOnDelete`; index `course_id` |
| `assignments` | FK `course_id` `cascadeOnDelete`; index (`course_id`, `due_at`) |
| `submissions` | **unique composite** (`assignment_id`, `user_id`) — 1 mahasiswa 1 submission aktif |
| `grades` | `submission_id` **unique** — relasi one-to-one |

Semua migrasi **wajib reversible** (`down()` benar-benar mengembalikan keadaan). Ini diuji.

### 4.3 Relasi Eloquent yang Diharapkan

Nama method relasi ikut ditetapkan, karena dipakai di modul, di soal interview, dan di skrip pengujian.

```
User      : taughtCourses()  hasMany(Course, 'lecturer_id')
            courses()        belongsToMany(Course)->withPivot('enrolled_at')->withTimestamps()
            submissions()    hasMany(Submission)
            gradesGiven()    hasMany(Grade, 'graded_by')

Course    : lecturer()       belongsTo(User, 'lecturer_id')
            students()       belongsToMany(User)->withPivot('enrolled_at')->withTimestamps()
            materials()      hasMany(Material)
            assignments()    hasMany(Assignment)

Assignment: course()         belongsTo(Course)
            submissions()    hasMany(Submission)
            grades()         hasManyThrough(Grade, Submission)

Submission: assignment()     belongsTo(Assignment)
            student()        belongsTo(User, 'user_id')
            grade()          hasOne(Grade)

Grade     : submission()     belongsTo(Submission)
            grader()         belongsTo(User, 'graded_by')
```

⚠️ Perhatikan `->withPivot('enrolled_at')`. Tanpa itu kolom `enrolled_at` yang diwajibkan pada tabel `course_user` tidak akan pernah terbaca maupun terisi lewat relasi — dan ini termasuk yang diperiksa saat penilaian Tugas 1.

---

## 5. Kontrak API (WAJIB — dipakai mulai minggu 6)

Prefix `/api/v1`. Autentikasi: Laravel Sanctum (Bearer token). Semua response dibungkus API Resource.

| Method | Endpoint | Akses | Keterangan |
|--------|----------|-------|------------|
| POST | `/auth/login` | publik | mengembalikan token |
| POST | `/auth/logout` | auth | |
| GET | `/me` | auth | profil + role |
| GET | `/courses` | auth | dosen: MK yang diajar; mahasiswa: MK yang diikuti |
| GET | `/courses/{id}` | auth+scope | detail + jumlah materi/tugas |
| GET | `/courses/{id}/materials` | auth+scope | |
| GET | `/courses/{id}/assignments` | auth+scope | mendukung `?status=`, `?page=` |
| POST | `/assignments` | dosen | |
| PUT/PATCH | `/assignments/{id}` | dosen (pemilik) | |
| DELETE | `/assignments/{id}` | dosen (pemilik) | |
| GET | `/assignments/{id}/submissions` | dosen (pemilik) | |
| POST | `/assignments/{id}/submissions` | mahasiswa (terdaftar) | multipart, `file` |
| PUT | `/submissions/{id}/grade` | dosen (pemilik) | `score`, `feedback` — *upsert*, aman dipanggil berulang |
| GET | `/notifications` | auth | |
| POST | `/notifications/{id}/read` | auth | |

**Format response wajib:**

```json
// sukses (koleksi)
{ "data": [ ... ], "meta": { "current_page": 1, "last_page": 5, "total": 47 } }

// sukses (tunggal)
{ "data": { ... } }

// error validasi — 422
{ "message": "Data yang diberikan tidak valid.", "errors": { "score": ["..."] } }

// tidak berhak — 403
{ "message": "Anda tidak memiliki akses ke sumber daya ini." }
```


---

## 6. Jalur Frontend

| Ketentuan |
|-----------|
| Kelompok memilih **satu** jalur dan mendaftarkannya: (a) tetap Blade, (b) Blade + Livewire 3, (c) Inertia 2 + Vue/React/Svelte, (d) SPA terpisah yang mengonsumsi REST API di Bagian 5. |

---

## 9. Definition of Done per Fitur

Sebuah fitur baru dianggap selesai jika **seluruhnya** terpenuhi:

- [ ] Berjalan sesuai spesifikasi pada data seeder
- [ ] Validasi ada di **server-side** (bukan hanya di frontend)
- [ ] Otorisasi diterapkan — diuji dengan mencoba mengakses via URL/ID milik pengguna lain
- [ ] Tidak ada N+1 query (diverifikasi lewat Debugbar/Telescope)
- [ ] Ada minimal 1 feature test untuk jalur sukses dan 1 untuk jalur gagal/ditolak
- [ ] Sudah melalui Pull Request dan direview minimal 1 anggota lain
- [ ] Anggota yang menulisnya **mampu menjelaskan seluruh barisnya**

---

## 11. Lingkungan Pengembangan

| Komponen | Ketentuan |
|----------|-----------|
| PHP | 8.3+ |
| Laravel | 12.x |
| Database | MySQL 8 / MariaDB 11 |
| Environment lokal | Laravel Herd (disarankan) atau Laravel Sail (Docker). **Bukan XAMPP.** |
| Asset bundler | Vite |
| Queue | database driver (minimal), Redis jika mampu |
| Editor | bebas; disarankan VS Code + PHP Intelephense |
| Deployment | lihat Bagian 11.1 |

### 11.1 Deployment — dan biayanya

| Jalur | Biaya | Catatan |
|-------|-------|---------|
| **Server kelas** (disarankan) | Rp0 | Dosen menyediakan satu VPS dengan subdomain `kelompok-XX.<domain-kelas>`. Tiap kelompok mendapat akun SSH, satu basis data, dan satu virtual host. SSL wildcard dipasang sekali oleh dosen. |
| Tier gratis penyedia cloud | Rp0 | Misalnya Railway, Fly.io, Render, atau Oracle Cloud Always Free. Domain bawaan penyedia sudah ber-HTTPS dan diterima penuh. |
| VPS sendiri | berbayar | Hanya bila kelompok memang ingin. **Tidak menambah nilai.** |




