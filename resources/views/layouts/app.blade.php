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
            grid-template-columns: 173px minmax(0, 1fr);
            min-height: 100vh;
            width: 100%;
        }

        .akarasa-sidebar {
            width: 173px;
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
        }

        .akarasa-content {
            width: 100%;
            min-width: 0;
            padding: 24px 28px;
        }

        .akarasa-content-detail {
            padding: 0;
        }

        @media (max-width: 700px) {
            .akarasa-layout {
                grid-template-columns: 1fr;
            }

            .akarasa-sidebar {
                width: 100%;
                min-height: auto;
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

    $isAdmin = $user && $user->role === 'admin';
    $isDetailTanaman = request()->routeIs('tanaman.show');

    $namaUser = $user->nama ?? $user->name ?? 'Pengguna';

    $dashboardUrl = $isAdmin
        ? route('admin.dashboard')
        : route('user.dashboard');

    $jadwalUrl = $isAdmin
        ? route('admin.resources.index', 'aturan-perawatan')
        : route('jadwal-perawatan.index');

    $logoutUrl = route('logout');
@endphp

<div class="akarasa-layout">

    {{-- SIDEBAR --}}
    <aside class="akarasa-sidebar">

        {{-- LOGO --}}
        <a href="{{ $dashboardUrl }}"
           class="flex items-center gap-3 px-1">

            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#b9dc68] text-xl text-[#174638]">
                🌱
            </span>

            <span class="text-xl font-bold tracking-wide">
                Akarasa
            </span>
        </a>

        {{-- MENU --}}
        <nav class="mt-9 flex flex-1 flex-col gap-3">

            {{-- DASHBOARD --}}
            <a href="{{ $dashboardUrl }}"
               class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition
               {{ request()->routeIs('admin.dashboard') || (!$isAdmin && request()->routeIs('user.dashboard')) ? 'bg-[#315e50] text-white' : 'text-emerald-100 hover:bg-[#285447]' }}">

                <span>▦</span>
                Dashboard
            </a>

            {{-- TANAMAN TOGA --}}
            {{-- Menampilkan tanaman terakhir yang dipilih --}}
            <a href="{{ route('tanaman.index') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition
               {{ request()->routeIs('tanaman.*') ? 'bg-[#315e50] text-white' : 'text-emerald-100 hover:bg-[#285447]' }}">

                <span>🌿</span>
                Tanaman TOGA
            </a>

            {{-- JADWAL PERAWATAN --}}
            <a href="{{ $jadwalUrl }}"
               class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition
               {{ ($isAdmin && request()->routeIs('admin.resources.index') && request()->route('resource') === 'aturan-perawatan')
                    || (!$isAdmin && request()->routeIs('jadwal-perawatan.index'))
                    ? 'bg-[#315e50] text-white'
                    : 'text-emerald-100 hover:bg-[#285447]' }}">

                <span>▢</span>
                Jadwal Perawatan
            </a>

            {{-- LAPORAN --}}
            <a href="{{ route('laporan.index') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition
               {{ request()->routeIs('laporan.index') ? 'bg-[#315e50] text-white' : 'text-emerald-100 hover:bg-[#285447]' }}">

                <span>▥</span>
                Laporan
            </a>

        </nav>

        {{-- PROFIL --}}
        <div class="mt-8 border-t border-emerald-800 pt-5">

            <div class="flex items-center gap-3">

                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#b9dc68] text-xs font-bold text-[#174638]">
                    {{ mb_strtoupper(mb_substr($namaUser, 0, 2)) }}
                </span>

                <div class="min-w-0">
                    <p class="truncate text-xs font-semibold">
                        {{ $namaUser }}
                    </p>

                    <p class="text-[10px] text-emerald-200">
                        {{ $isAdmin ? 'Administrator' : 'Pengguna' }}
                    </p>
                </div>

            </div>

            {{-- LOGOUT --}}
            <form method="POST" action="{{ $logoutUrl }}" class="mt-4">
                @csrf

                <button type="submit"
                        class="w-full rounded-lg px-3 py-2 text-left text-xs text-emerald-200 hover:bg-[#285447]">
                    ↪ Keluar
                </button>
            </form>

        </div>

    </aside>

    {{-- KONTEN KANAN --}}
    <main class="akarasa-main">

        {{-- HEADER --}}
        @unless ($isDetailTanaman)

            <header class="flex min-h-[58px] flex-wrap items-center justify-between gap-3 border-b border-[#e2e8df] bg-white px-5 py-3 sm:px-7">

                <div>
                    <p class="text-sm font-bold text-[#193b32]">
                        {{ $isAdmin && request()->routeIs('admin.resources.index') && request()->route('resource') === 'aturan-perawatan'
                            ? 'Jadwal Perawatan'
                            : 'Selamat datang, '.$namaUser }}
                    </p>

                    <p class="mt-1 text-[10px] text-gray-500">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>

                <div class="flex items-center gap-3">

                    <form action="{{ route('tanaman.index') }}"
                          method="GET"
                          class="hidden sm:block">

                        <input
                            type="search"
                            name="search"
                            placeholder="Cari tanaman atau warga..."
                            class="w-52 rounded-lg border-0 bg-[#f3f5f0] px-3 py-2 text-[11px] outline-none focus:ring-2 focus:ring-[#b9dc68]"
                        >
                    </form>

                    <button type="button"
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#f3f5f0]">
                        ♧
                    </button>

                </div>

            </header>

        @endunless

        {{-- NOTIFIKASI --}}
        <div class="{{ $isDetailTanaman ? 'px-5 pt-2' : 'px-6 pt-4' }}">

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

            @if (session('new_access_code'))
                <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <strong>Kode akses kebun:</strong>
                    <code class="ml-2 select-all">{{ session('new_access_code') }}</code>
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- ISI HALAMAN --}}
        <div class="{{ $isDetailTanaman ? 'akarasa-content-detail' : 'akarasa-content' }}">
            @yield('content')
        </div>

    </main>

</div>

</body>
</html>
