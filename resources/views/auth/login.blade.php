<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#17483B">
    <title>Masuk Warga - Akarasa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#F4F6F0] font-sans text-[#173F35] lg:bg-[#1F1F1F] lg:p-3">

    <main class="mx-auto grid min-h-screen w-full max-w-[1400px] grid-cols-1 overflow-hidden bg-[#F4F6F0] lg:min-h-[calc(100vh-24px)] lg:grid-cols-[0.8fr_1.2fr]">

        <!-- PANEL KIRI: IDENTITAS AKARASA -->
        <section class="flex flex-col bg-[#17483B] px-5 py-5 text-white sm:px-8 sm:py-7 lg:px-8 lg:py-9 xl:px-12">

            <!-- Logo -->
            <a href="{{ route('home') }}"
               class="inline-flex w-fit items-center gap-2 text-base font-semibold text-white">

                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#B7D96B] text-[#17483B]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.5"
                         class="h-6 w-6">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 21v-8m0 0C5 13 4 8 4 5c5 0 8 3 8 8Zm0 0c0-5 3-8 8-8 0 4-2 8-8 8Z"/>
                    </svg>
                </span>

                Akarasa
            </a>

            <!-- Konten utama -->
            <div class="mt-6 w-full lg:my-auto lg:mt-0 lg:py-10">

                <!-- Foto kebun -->
                <div class="mb-4 overflow-hidden rounded-2xl sm:mb-6">
                    <img
                        src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=1200&q=85"
                        alt="Kegiatan berkebun dan merawat tanaman"
                        class="h-32 w-full object-cover sm:h-52 lg:h-48 xl:h-[210px]"
                    >
                </div>

                <!-- Judul -->
                <h1 class="max-w-md text-2xl font-semibold leading-tight tracking-tight sm:text-3xl lg:text-3xl">
                    Rawat tanaman,<br>
                    tumbuhkan manfaat.
                </h1>

                <!-- Deskripsi -->
                <p class="mt-2 max-w-sm text-xs leading-relaxed text-[#D0E0D6] sm:mt-4 sm:text-sm">
                    Kenali, tanam, dan rawat tanaman obat keluarga dengan mudah bersama Akarasa.
                </p>

            </div>

            <!-- Footer panel kiri -->
            <p class="mt-5 text-[10px] leading-relaxed text-[#C4D8CC] lg:mt-0">
                Program KKN · Bersama warga, untuk warga
            </p>

        </section>


        <!-- PANEL KANAN: FORM LOGIN -->
        <section class="flex items-center justify-center bg-[#F4F6F0] px-5 py-8 sm:px-10 sm:py-12 lg:px-12 xl:px-20">

            <div class="w-full max-w-[460px]">

                <!-- Pilihan jenis akun -->
                <div class="mb-7">

                    <p class="mb-3 text-xs font-medium text-[#7B8C80] sm:text-sm">
                        Pilih jenis akun untuk masuk
                    </p>

                    <nav aria-label="Pilih jenis login" class="grid grid-cols-2 gap-2">

                        <a href="{{ route('warga.login') }}"
                           aria-current="page"
                           class="flex min-h-[46px] items-center justify-center rounded-lg bg-[#17483B] px-2 py-3 text-center text-xs font-semibold text-white transition sm:text-sm">
                            Login sebagai warga
                        </a>

                        <a href="{{ route('admin.login') }}"
                           class="flex min-h-[46px] items-center justify-center rounded-lg border border-[#DCE3DA] bg-white px-2 py-3 text-center text-xs font-semibold text-[#476B58] transition hover:border-[#43836A] sm:text-sm">
                            Login sebagai admin
                        </a>

                    </nav>
                </div>


                <!-- Judul form -->
                <div class="mb-6">

                    <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-[#42775B] sm:text-xs">
                        Warga Akarasa
                    </p>

                    <h2 class="text-2xl font-semibold leading-tight tracking-tight text-[#25463B] sm:text-3xl">
                        Masuk ke Akarasa
                    </h2>

                    <p class="mt-2 text-xs leading-relaxed text-[#7B8C80] sm:text-sm">
                        Masukkan email dan kata sandi untuk masuk.
                        Pengguna baru dapat membuat akun dengan mengisi nama lengkap.
                    </p>

                </div>


                <!-- Pesan berhasil -->
                @if (session('success'))
                    <div role="status"
                         class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif


                <!-- Pesan error -->
                @if ($errors->any())
                    <div role="alert"
                         class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">

                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>
                @endif


                <!-- FORM LOGIN WARGA -->
                <form method="POST" action="{{ route('warga.login.store') }}">
                    @csrf

                    <!-- Nama -->
                    <div class="mb-5">

                        <label for="nama"
                               class="mb-2 block text-xs font-semibold text-[#30483D] sm:text-sm">
                            Nama lengkap
                            <span class="font-normal text-[#7B8C80]">
                                (khusus pengguna baru)
                            </span>
                        </label>

                        <input
                            id="nama"
                            type="text"
                            name="nama"
                            value="{{ old('nama') }}"
                            autocomplete="name"
                            placeholder="Contoh: Siti Aminah"
                            class="min-h-[48px] w-full rounded-lg border border-[#DCE3DA] bg-white px-4 py-3 text-sm text-[#30483D] outline-none transition placeholder:text-[#9AA69D] focus:border-[#43836A] focus:ring-2 focus:ring-[#43836A]/10"
                        >

                        <p class="mt-1.5 text-[11px] leading-relaxed text-[#89958D]">
                            Jika baru pertama kali masuk, isi nama lengkap Anda.
                        </p>

                    </div>


                    <!-- Email -->
                    <div class="mb-5">

                        <label for="email"
                               class="mb-2 block text-xs font-semibold text-[#30483D] sm:text-sm">
                            Email
                        </label>

                        <div class="relative">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.5"
                                 class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8C9B91]">

                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="m3 7 9 6 9-6"/>

                            </svg>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="nama@email.com"
                                class="min-h-[48px] w-full rounded-lg border border-[#DCE3DA] bg-white py-3 pl-11 pr-4 text-sm text-[#30483D] outline-none transition placeholder:text-[#9AA69D] focus:border-[#43836A] focus:ring-2 focus:ring-[#43836A]/10"
                            >

                        </div>

                    </div>


                    <!-- Kata sandi -->
                    <div class="mb-4">

                        <label for="password"
                               class="mb-2 block text-xs font-semibold text-[#30483D] sm:text-sm">
                            Kata sandi
                        </label>

                        <div class="relative">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.5"
                                 class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8C9B91]">

                                <rect x="4" y="10" width="16" height="11" rx="2"/>
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v3"/>

                            </svg>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi"
                                class="min-h-[48px] w-full rounded-lg border border-[#DCE3DA] bg-white py-3 pl-11 pr-4 text-sm text-[#30483D] outline-none transition placeholder:text-[#9AA69D] focus:border-[#43836A] focus:ring-2 focus:ring-[#43836A]/10"
                            >

                        </div>

                    </div>


                    <!-- Ingat saya -->
                    <div class="mb-6">

                        <label for="remember"
                               class="inline-flex min-h-[36px] cursor-pointer items-center gap-2 text-xs text-[#7B8C80] sm:text-sm">

                            <input
                                id="remember"
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="h-4 w-4 rounded border-[#C9D5CA] accent-[#17483B]"
                            >

                            Ingat saya

                        </label>

                    </div>


                    <!-- Tombol masuk -->
                    <button
                        type="submit"
                        class="flex min-h-[50px] w-full items-center justify-center rounded-lg bg-[#17483B] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#103A30] focus:outline-none focus:ring-4 focus:ring-[#17483B]/20 active:scale-[0.99]"
                    >
                        Masuk
                    </button>

                </form>


                <!-- Keterangan akun -->
                <div class="mt-5 rounded-lg bg-[#EAF0E7] px-4 py-3 text-center">

                    <p class="text-xs leading-relaxed text-[#64766C]">
                        <span class="font-semibold text-[#365D47]">
                            Pengguna baru?
                        </span>
                        Akun akan dibuat secara otomatis saat pertama kali masuk.
                    </p>

                </div>


                <!-- Footer mobile -->
                <p class="mt-7 text-center text-[11px] leading-relaxed text-[#89958D]">
                    © 2026 Akarasa
                    <br>
                    Institut Teknologi Kalimantan
                </p>

            </div>

        </section>

    </main>

</body>
</html>
