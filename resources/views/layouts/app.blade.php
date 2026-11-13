<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Akarasa' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            min-height: 100%;
        }

        .akarasa-layout {
            display: grid;
            grid-template-columns: 200px minmax(0, 1fr);
            min-height: 100vh;
            width: 100%;
        }

        .akarasa-sidebar {
            width: 200px;
            min-height: 100vh;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            background: #174238;
            color: white;
        }

        .akarasa-main {
            min-width: 0;
            background: #f3f5f0;
            display: flex;
            flex-direction: column;
        }

        .akarasa-content {
            width: 100%;
            min-width: 0;
            padding: 24px 28px;
            flex: 1;
        }

        .akarasa-content-detail {
            padding: 0;
            flex: 1;
        }

        @media (max-width: 768px) {
            .akarasa-layout {
                grid-template-columns: 1fr;
            }

            .akarasa-sidebar {
                width: 100%;
                min-height: auto;
                padding: 14px 16px;
            }

            .akarasa-content {
                padding: 16px;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-[#f3f5f0] font-sans text-[#193b32]">

@php
    $user = auth()->user();

    $isAdmin = $user?->role === 'admin';
    $isDetailTanaman = request()->routeIs('tanaman.show');

    $namaUser = $user?->nama ?? $user?->name ?? 'Pengguna';

    $dashboardUrl = $isAdmin
        ? route('admin.dashboard')
        : route('user.dashboard');

    $adminLoginUrl = route('admin.login');

    $hasLogoutRoute = \Illuminate\Support\Facades\Route::has('admin.logout');
    $logoutUrl = $hasLogoutRoute ? route('admin.logout') : url('/logout');

    $hasNotifications = session('status') or session('success') or $errors->any();
@endphp

<div class="akarasa-layout">

    {{-- SIDEBAR --}}
    <aside class="akarasa-sidebar">

        {{-- LOGO & TOGGLE MOBILE --}}
        <div class="flex items-center justify-between">
            <a href="{{ $dashboardUrl }}" class="flex items-center gap-3 px-1">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#b9dc68] text-xl text-[#174638]">
                    🌱
                </span>
                <span class="text-xl font-bold tracking-wide">
                    Akarasa
                </span>
            </a>

            <button type="button"
                    id="mobile-menu-toggle"
                    aria-label="Buka Menu"
                    class="rounded-lg border border-emerald-700 px-3 py-1.5 text-sm text-emerald-100 hover:bg-[#285447] md:hidden">
                ☰
            </button>
        </div>

        {{-- WRAPPER MENU & PROFIL (COLLAPSIBLE DI MOBILE) --}}
        <div id="sidebar-menu-wrapper" class="hidden flex-1 flex-col justify-between md:flex">

            {{-- MENU --}}
            <nav class="mt-6 flex flex-col gap-2 md:mt-9 md:gap-3">

                {{-- DASHBOARD --}}
                <a href="{{ $dashboardUrl }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition
                   {{ request()->routeIs('admin.dashboard') or (!$isAdmin and request()->routeIs('user.dashboard')) ? 'bg-[#315e50] font-semibold text-white' : 'text-emerald-100 hover:bg-[#285447]' }}">
                    <span>▦</span>
                    Dashboard
                </a>

                {{-- TANAMAN TOGA --}}
                <a href="{{ route('tanaman.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition
                   {{ request()->routeIs('tanaman.*') ? 'bg-[#315e50] font-semibold text-white' : 'text-emerald-100 hover:bg-[#285447]' }}">
                    <span>🌿</span>
                    Katalog Tanaman
                </a>

                <a href="{{ route('penanam.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition
                   {{ request()->routeIs('penanam.*', 'kebun.*', 'tanaman-ditanam.*') ? 'bg-[#315e50] font-semibold text-white' : 'text-emerald-100 hover:bg-[#285447]' }}">
                    <span>⌂</span>
                    Kebunku
                </a>

            </nav>

            {{-- PROFIL BAWAH SIDEBAR --}}
            <div class="mt-6 border-t border-emerald-800 pt-5 md:mt-8">

                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#b9dc68] text-xs font-bold text-[#174638]">
                        {{ mb_strtoupper(mb_substr($namaUser, 0, 2)) }}
                    </span>

                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold">
                            {{ $namaUser }}
                        </p>
                        <p class="text-[10px] text-emerald-200">
                            {{ $isAdmin ? 'Administrator' : 'Pengunjung' }}
                        </p>
                    </div>
                </div>

                {{-- TOMBOL LOGIN SEBAGAI ADMIN (HANYA DI BAWAH SINI) --}}
                <a href="{{ $adminLoginUrl }}"
                   class="mt-4 flex w-full items-center justify-center gap-1.5 rounded-lg bg-[#b9dc68] px-3 py-2 text-xs font-semibold text-[#174638] transition hover:bg-[#a9cf54]">
                    🔑 Login sebagai Admin
                </a>

                @if ($user and $hasLogoutRoute)
                    <form method="POST" action="{{ $logoutUrl }}" class="mt-2">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-lg px-3 py-2 text-left text-xs text-emerald-200 transition hover:bg-[#285447]">
                            ↪ Keluar
                        </button>
                    </form>
                @endif

            </div>

        </div>

    </aside>

    {{-- KONTEN KANAN --}}
    <main class="akarasa-main">

        {{-- HEADER (TANPA TOMBOL LOGIN ADMIN) --}}
        @unless ($isDetailTanaman)
            <header class="flex min-h-[58px] flex-wrap items-center justify-between gap-3 border-b border-[#e2e8df] bg-white px-5 py-3 sm:px-7">

                <div>
                    <p class="text-sm font-bold text-[#193b32]">
                        {{ $isAdmin and request()->routeIs('admin.resources.index') and request()->route('resource') === 'aturan-perawatan'
                            ? 'Jadwal Perawatan'
                            : 'Selamat datang, ' . $namaUser }}
                    </p>

                    <p class="mt-1 text-[10px] text-gray-500">
                        {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('tanaman.index') }}"
                       title="Daftar Tanaman"
                       class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#f3f5f0] text-[#193b32] transition hover:bg-[#e5ebe0]">
                        ♧
                    </a>
                </div>

            </header>
        @endunless

        {{-- NOTIFIKASI --}}
        @if ($hasNotifications)
            <div class="space-y-3 {{ $isDetailTanaman ? 'px-5 pt-3' : 'px-6 pt-4' }}">

                @if (session('status'))
                    <div role="status"
                         class="rounded-lg border border-green-200 bg-green-100 p-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('success'))
                    <div role="status"
                         class="rounded-lg border border-green-200 bg-green-100 p-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>
        @endif

        {{-- ISI HALAMAN --}}
        <div class="{{ $isDetailTanaman ? 'akarasa-content-detail' : 'akarasa-content' }}">
            @yield('content')
        </div>

    </main>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('mobile-menu-toggle');
        const menuWrapper = document.getElementById('sidebar-menu-wrapper');

        if (toggleBtn && menuWrapper) {
            toggleBtn.addEventListener('click', function () {
                menuWrapper.classList.toggle('hidden');
                menuWrapper.classList.toggle('flex');
            });
        }
    });
</script>

</body>
</html>