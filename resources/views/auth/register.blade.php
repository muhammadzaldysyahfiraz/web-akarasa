<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun - Akarasa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#1F1F1F] p-0 font-sans text-[#173F35] sm:p-3">

    <main class="mx-auto grid min-h-screen w-full max-w-[1400px] grid-cols-1 overflow-hidden bg-[#F4F6F0] lg:min-h-[calc(100vh-24px)] lg:grid-cols-[1.6fr_1fr]">

        <!-- KOLOM KIRI: FORM REGISTER -->
        <section class="flex items-center justify-center px-6 py-10 sm:px-12 lg:px-16 xl:px-20">

            <div class="w-full max-w-[460px]">

                <!-- Logo -->
                <a href="{{ route('home') }}"
                   class="mb-7 inline-flex items-center gap-2 text-sm font-medium text-[#24483C]">

                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#174C3D] text-white">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.5"
                             class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 21v-8m0 0C5 13 4 8 4 5c5 0 8 3 8 8Zm0 0c0-5 3-8 8-8 0 4-2 8-8 8Z"/>
                        </svg>
                    </span>

                    <span class="text-sm font-medium">Akarasa</span>
                </a>

                <!-- Judul -->
                <div class="mb-5">
                    <h1 class="text-[25px] font-medium tracking-tight text-[#25463B] sm:text-[29px]">
                        Buat akun baru
                    </h1>

                    <p class="mt-1 text-[11px] leading-5 text-[#7B8C80] sm:text-xs">
                        Bergabung untuk mencatat dan memantau tanaman TOGA.
                    </p>
                </div>

                <!-- Pesan error -->
                @if ($errors->any())
                    <div role="alert"
                         class="mb-4 rounded-lg border border-red-100 bg-red-50 px-3 py-2 text-xs text-red-700">

                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>
                @endif

                <!-- Pesan berhasil -->
                @if (session('success'))
                    <div role="status"
                         class="mb-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-xs text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- FORM REGISTER -->
                <form method="POST"
                      action="{{ route('register.store') }}"
                      class="space-y-3">

                    @csrf

                    <!-- Nama lengkap -->
                    <div>
                        <label for="nama"
                               class="mb-1.5 block text-[11px] font-medium text-[#30483D]">
                            Nama lengkap
                        </label>

                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#8C9B91]"
                                 xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 19a6 6 0 0 0-12 0m6-8a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-3h5m-2.5-2.5v5"/>
                            </svg>

                            <input
                                id="nama"
                                type="text"
                                name="nama"
                                value="{{ old('nama') }}"
                                required
                                autocomplete="name"
                                placeholder="Masukkan nama lengkap"
                                class="w-full rounded-md border border-[#DCE3DA] bg-white py-2.5 pl-9 pr-3 text-xs text-[#30483D] outline-none transition placeholder:text-[#9AA69D] focus:border-[#43836A] focus:ring-2 focus:ring-[#43836A]/10"
                            >
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email"
                               class="mb-1.5 block text-[11px] font-medium text-[#30483D]">
                            Email
                        </label>

                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#8C9B91]"
                                 xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.5">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m3 7 9 6 9-6"/>
                            </svg>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                placeholder="nama@email.com"
                                class="w-full rounded-md border border-[#DCE3DA] bg-white py-2.5 pl-9 pr-3 text-xs text-[#30483D] outline-none transition placeholder:text-[#9AA69D] focus:border-[#43836A] focus:ring-2 focus:ring-[#43836A]/10"
                            >
                        </div>
                    </div>

                    <!-- Kata sandi -->
                    <div>
                        <label for="password"
                               class="mb-1.5 block text-[11px] font-medium text-[#30483D]">
                            Kata sandi
                        </label>

                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#8C9B91]"
                                 xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.5">
                                <rect x="4" y="10" width="16" height="11" rx="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v3"/>
                            </svg>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                placeholder="Minimal 8 karakter"
                                class="w-full rounded-md border border-[#DCE3DA] bg-white py-2.5 pl-9 pr-3 text-xs text-[#30483D] outline-none transition placeholder:text-[#9AA69D] focus:border-[#43836A] focus:ring-2 focus:ring-[#43836A]/10"
                            >
                        </div>
                    </div>

                    <!-- KONFIRMASI KATA SANDI -->
                    <div>
                        <label for="password_confirmation"
                               class="mb-1.5 block text-[11px] font-medium text-[#30483D]">
                            Konfirmasi kata sandi
                        </label>

                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#8C9B91]"
                                 xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.5">
                                <rect x="4" y="10" width="16" height="11" rx="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v3"/>
                            </svg>

                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                placeholder="Masukkan ulang kata sandi"
                                class="w-full rounded-md border border-[#DCE3DA] bg-white py-2.5 pl-9 pr-3 text-xs text-[#30483D] outline-none transition placeholder:text-[#9AA69D] focus:border-[#43836A] focus:ring-2 focus:ring-[#43836A]/10"
                            >
                        </div>
                    </div>

                    <!-- PILIHAN PERAN -->
                    <fieldset>
                        <legend class="mb-1.5 block text-[11px] font-medium text-[#30483D]">
                            Peran
                        </legend>

                        <div class="grid grid-cols-2 gap-2">

                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="jenis_pengguna"
                                    value="warga"
                                    class="peer sr-only"
                                    {{ old('jenis_pengguna', 'warga') === 'warga' ? 'checked' : '' }}
                                >

                                <span class="flex min-h-[36px] items-center justify-center rounded-md border border-[#DCE3DA] bg-white px-3 py-2 text-[11px] font-semibold text-[#78877D] transition peer-checked:border-[#43836A] peer-checked:bg-[#E6EFE5] peer-checked:text-[#286047] peer-focus-visible:ring-2 peer-focus-visible:ring-[#43836A]">
                                    Warga
                                </span>
                            </label>

                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="jenis_pengguna"
                                    value="tim_kkn"
                                    class="peer sr-only"
                                    {{ old('jenis_pengguna') === 'tim_kkn' ? 'checked' : '' }}
                                >

                                <span class="flex min-h-[36px] items-center justify-center rounded-md border border-[#DCE3DA] bg-white px-3 py-2 text-[11px] font-semibold text-[#78877D] transition peer-checked:border-[#43836A] peer-checked:bg-[#E6EFE5] peer-checked:text-[#286047] peer-focus-visible:ring-2 peer-focus-visible:ring-[#43836A]">
                                    Tim KKN
                                </span>
                            </label>

                        </div>
                    </fieldset>

                    <!-- TOMBOL DAFTAR -->
                    <button
                        type="submit"
                        class="mt-1 w-full rounded-md bg-[#17483B] px-4 py-3 text-xs font-bold text-white transition hover:bg-[#103A30] focus:outline-none focus:ring-4 focus:ring-[#17483B]/20"
                    >
                        Daftar akun
                    </button>

                </form>

                <!-- LINK LOGIN -->
                <p class="mt-3 text-center text-[11px] text-[#7B8C80]">
                    Sudah punya akun?
                    <a href="{{ route('admin.login') }}"
                       class="ml-1 font-medium text-[#476B58] hover:underline">
                        Masuk
                    </a>
                </p>

            </div>
        </section>

        <!-- KOLOM KANAN: INFORMASI TOGA -->
        <aside class="relative hidden flex-col justify-between bg-[#E7F0E5] px-8 py-10 sm:px-10 lg:flex xl:px-12 xl:py-12">

            <div class="mx-auto my-auto w-full max-w-[440px]">

                <p class="mb-3 text-[9px] font-bold uppercase tracking-wide text-[#42775B]">
                    Kebun sehat, warga kuat
                </p>

                <h2 class="max-w-[400px] text-[29px] font-medium leading-[1.1] tracking-tight text-[#254A3D] xl:text-[36px]">
                    Setiap catatan membantu kebun tumbuh lebih baik.
                </h2>

                <div class="mt-5 overflow-hidden rounded-xl">
                    <img
                        src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=1200&q=85"
                        alt="Aktivitas merawat tanaman di kebun"
                        class="h-[220px] w-full object-cover xl:h-[270px]"
                    >
                </div>

            </div>

            <p class="mt-8 text-[9px] leading-5 text-[#819385]">
                Data kebun tersimpan aman dan hanya digunakan untuk program Akarasa.
            </p>

        </aside>

    </main>

</body>
</html>
