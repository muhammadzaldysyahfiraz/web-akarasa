<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Warga - Akarasa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#1F1F1F] p-0 font-sans text-[#173F35] sm:p-3">

    <main class="mx-auto grid min-h-screen w-full max-w-[1400px] grid-cols-1 overflow-hidden bg-[#F4F6F0] lg:min-h-[calc(100vh-24px)] lg:grid-cols-[0.8fr_1.2fr]">

        <!-- PANEL KIRI: BRAND DAN FOTO KEBUN -->
        <section class="flex flex-col bg-[#17483B] px-7 py-7 text-white sm:px-10 sm:py-9 lg:px-8 xl:px-12">

            <!-- Logo -->
            <a href="{{ route('home') }}"
               class="inline-flex w-fit items-center gap-2 text-sm font-medium text-white">

                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#B7D96B] text-[#17483B]">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.5"
                         class="h-5 w-5">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 21v-8m0 0C5 13 4 8 4 5c5 0 8 3 8 8Zm0 0c0-5 3-8 8-8 0 4-2 8-8 8Z"/>
                    </svg>
                </span>

                <span>Akarasa</span>
            </a>

            <!-- Konten utama panel kiri -->
            <div class="my-auto w-full py-10">

                <!-- Foto kebun -->
                <div class="mb-5 overflow-hidden rounded-2xl">
                    <img
                        src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=1200&q=85"
                        alt="Tanaman hijau di kebun"
                        class="h-48 w-full object-cover sm:h-60 lg:h-48 xl:h-[198px]"
                    >
                </div>

                <!-- Judul -->
                <h1 class="max-w-md text-2xl font-medium leading-tight tracking-tight sm:text-3xl">
                    Rawat tanaman,<br>
                    tumbuhkan manfaat.
                </h1>

                <!-- Deskripsi -->
                <p class="mt-4 max-w-sm text-[11px] leading-[1.8] text-[#D0E0D6] sm:text-xs">
                    Pantau kebun TOGA warga dalam satu ruang kerja yang sederhana dan kolaboratif.
                </p>

            </div>

            <!-- Footer panel kiri -->
            <p class="text-[9px] leading-5 text-[#C4D8CC]">
                Program KKN · Bersama warga, untuk warga
            </p>

        </section>

        <!-- PANEL KANAN: FORM LOGIN WARGA -->
        <section class="flex items-center justify-center bg-[#F4F6F0] px-6 py-12 sm:px-12 lg:px-14 xl:px-20">

            <div class="w-full max-w-[420px]">

                <div class="mb-6">
                    <p class="mb-2 text-[10px] font-medium text-[#7B8C80]">
                        Pilih jenis akun untuk masuk
                    </p>
                    <nav aria-label="Pilih jenis login" class="grid grid-cols-2 gap-2">
                        <a href="{{ route('warga.login') }}"
                           aria-current="page"
                           class="rounded-md bg-[#17483B] px-3 py-2.5 text-center text-xs font-semibold text-white">
                            Login sebagai warga
                        </a>
                        <a href="{{ route('admin.login') }}"
                           class="rounded-md border border-[#DCE3DA] bg-white px-3 py-2.5 text-center text-xs font-semibold text-[#476B58] transition hover:border-[#43836A]">
                            Login sebagai admin
                        </a>
                    </nav>
                </div>

                <!-- Judul form -->
                <div class="mb-6">
                    <p class="mb-2 text-[9px] font-bold uppercase tracking-wide text-[#42775B]">
                        Warga Akarasa
                    </p>

                    <h2 class="text-[25px] font-medium leading-tight tracking-tight text-[#25463B] sm:text-[29px]">
                        Masuk ke Akarasa
                    </h2>

                    <p class="mt-2 text-[10px] leading-5 text-[#7B8C80] sm:text-xs">
                        Masuk dengan email Anda. Pengguna baru cukup mengisi nama dan membuat kata sandi.
                    </p>
                </div>

                <!-- Pesan berhasil -->
                @if (session('success'))
                    <div role="status"
                         class="mb-4 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-xs text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Pesan error -->
                @if ($errors->any())
                    <div role="alert"
                         class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700">
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

                    <!-- Nama untuk pengguna baru -->
                    <div class="mb-4">
                        <label for="nama"
                               class="mb-1.5 block text-[10px] font-medium text-[#30483D]">
                            Nama <span class="font-normal text-[#7B8C80]">(khusus pertama kali masuk)</span>
                        </label>

                        <input
                            id="nama"
                            type="text"
                            name="nama"
                            value="{{ old('nama') }}"
                            autocomplete="name"
                            placeholder="Nama lengkap"
                            class="w-full rounded-md border border-[#DCE3DA] bg-white px-3 py-2.5 text-xs text-[#30483D] outline-none transition placeholder:text-[#9AA69D] focus:border-[#43836A] focus:ring-2 focus:ring-[#43836A]/10"
                        >
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email"
                               class="mb-1.5 block text-[10px] font-medium text-[#30483D]">
                            Email
                        </label>

                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.5"
                                 class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#8C9B91]">
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
                                class="w-full rounded-md border border-[#DCE3DA] bg-white py-2.5 pl-9 pr-3 text-xs text-[#30483D] outline-none transition placeholder:text-[#9AA69D] focus:border-[#43836A] focus:ring-2 focus:ring-[#43836A]/10"
                            >
                        </div>
                    </div>

                    <!-- Kata sandi -->
                    <div class="mb-3">
                        <label for="password"
                               class="mb-1.5 block text-[10px] font-medium text-[#30483D]">
                            Kata sandi
                        </label>

                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.5"
                                 class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#8C9B91]">
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
                                placeholder="Buat atau masukkan kata sandi"
                                class="w-full rounded-md border border-[#DCE3DA] bg-white py-2.5 pl-9 pr-3 text-xs text-[#30483D] outline-none transition placeholder:text-[#9AA69D] focus:border-[#43836A] focus:ring-2 focus:ring-[#43836A]/10"
                            >
                        </div>
                    </div>

                    <!-- Ingat saya dan lupa password -->
                    <div class="mb-5 flex items-center justify-between gap-3 text-[9px] text-[#7B8C80] sm:text-[10px]">

                        <label for="remember"
                               class="inline-flex cursor-pointer items-center gap-1.5">
                            <input
                                id="remember"
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="h-3 w-3 rounded border-[#C9D5CA] accent-[#17483B]"
                            >
                            Ingat saya
                        </label>

                        <a href="#"
                           onclick="return false"
                           title="Fitur pemulihan kata sandi belum tersedia"
                           class="text-[#47755D] hover:underline">
                            Lupa kata sandi?
                        </a>

                    </div>

                    <!-- Tombol masuk -->
                    <button
                        type="submit"
                        class="w-full rounded-md bg-[#17483B] px-4 py-3 text-[11px] font-bold text-white transition hover:bg-[#103A30] focus:outline-none focus:ring-4 focus:ring-[#17483B]/20"
                    >
                        Masuk
                    </button>

                </form>

                <p class="mt-4 text-center text-[10px] text-[#7B8C80] sm:text-[11px]">
                    Akun baru dibuat otomatis saat pertama kali masuk.
                </p>

            </div>
        </section>

    </main>

</body>
</html>
