<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | Akarasa</title>

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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 173px;
            background: #174238;
            color: white;
            padding: 20px 16px;
        }

        .akarasa-main {
            min-width: 0;
            width: 100%;
            background: #f3f5f0;
        }

        .akarasa-content {
            min-width: 0;
            width: 100%;
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

            .akarasa-content-detail {
                padding: 0;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-[#f3f5f0] font-sans text-[#193b32]">

@php
    $isDetailTanaman = request()->routeIs('tanaman.show');
    $isPlantForm = request()->routeIs('admin.resources.create', 'admin.resources.edit')
        && request()->route('resource') === 'tanaman';
@endphp

<div class="akarasa-layout">

    {{-- SIDEBAR KIRI --}}
    <aside class="akarasa-sidebar">

        {{-- LOGO --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-2 px-1">

            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#b9dc68] text-xl text-[#174238]">
                🌱
            </span>

            <span class="text-lg font-semibold tracking-wide">
                Akarasa
            </span>
        </a>

        {{-- MENU SIDEBAR --}}
        <nav class="mt-6 flex flex-1 flex-col gap-2">

            <a href="{{ route('admin.dashboard') }}"
               @class([
                    'flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition',
                    'bg-[#315e50] text-white' => request()->routeIs('admin.dashboard'),
                    'text-emerald-100 hover:bg-[#285447]' => !request()->routeIs('admin.dashboard'),
               ])>
                <span>▦</span>
                Dashboard
            </a>

            <a href="{{ route('admin.resources.index', 'tanaman') }}"
               @class([
                    'flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition',
                    'bg-[#315e50] text-white' => request()->routeIs('admin.resources.*') && request()->route('resource') === 'tanaman',
                    'text-emerald-100 hover:bg-[#285447]' => !(request()->routeIs('admin.resources.*') && request()->route('resource') === 'tanaman'),
               ])>
                <span>🌿</span>
                Tanaman TOGA
            </a>

            <a href="{{ route('admin.resources.index', 'aturan-perawatan') }}"
               @class([
                    'flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition',
                    'bg-[#315e50] text-white' => request()->routeIs('admin.resources.*') && request()->route('resource') === 'aturan-perawatan',
                    'text-emerald-100 hover:bg-[#285447]' => !(request()->routeIs('admin.resources.*') && request()->route('resource') === 'aturan-perawatan'),
               ])>
                <span>▢</span>
                Jadwal Perawatan
            </a>

            <a href="{{ route('laporan.index') }}"
               @class([
                    'flex items-center gap-3 rounded-lg px-3 py-3 text-sm transition',
                    'bg-[#315e50] text-white' => request()->routeIs('laporan.index'),
                    'text-emerald-100 hover:bg-[#285447]' => !request()->routeIs('laporan.index'),
               ])>
                <span>▥</span>
                Laporan
            </a>

        </nav>

        {{-- PROFIL SIDEBAR --}}
        <div class="mt-8 border-t border-emerald-800 pt-4">

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3">

                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#b9dc68] text-xs font-bold text-[#174238]">
                    {{ mb_strtoupper(mb_substr(auth()->user()->nama, 0, 2)) }}
                </span>

                <span class="min-w-0">
                    <span class="block truncate text-xs font-semibold">
                        {{ auth()->user()->nama }}
                    </span>

                    <span class="block text-[10px] text-emerald-200">
                        Koordinator KKN
                    </span>
                </span>
            </a>

            <form method="POST" action="{{ route('admin.logout') }}" class="mt-4">
                @csrf

                <button class="w-full rounded-lg px-3 py-2 text-left text-xs text-emerald-200 hover:bg-[#285447]">
                    ↪ Keluar
                </button>
            </form>

        </div>

    </aside>

    {{-- KONTEN KANAN --}}
    <main class="akarasa-main">

        {{-- HEADER ADMIN: TIDAK DITAMPILKAN DI DETAIL TANAMAN --}}
        @unless ($isDetailTanaman || $isPlantForm)
            <header class="flex min-h-[58px] flex-wrap items-center justify-between gap-3 border-b border-[#e2e8df] bg-white px-5 py-3 sm:px-7">

                <div>
                    <p class="text-xs font-bold text-[#193b32]">
                        Selamat pagi, {{ auth()->user()->nama }}
                    </p>

                    <p class="mt-0.5 text-[10px] text-gray-500">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>

                <div class="flex items-center gap-3">

                    <form action="{{ route('admin.resources.index', 'tanaman') }}"
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
                            aria-label="Notifikasi"
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#f3f5f0] text-gray-600 hover:bg-gray-200">
                        ♧
                    </button>

                </div>
            </header>
        @endunless

        {{-- NOTIFIKASI SISTEM --}}
        <div class="{{ $isPlantForm ? 'px-5 pt-3 sm:px-7' : ($isDetailTanaman ? 'px-5 pt-2' : 'px-5 pt-4 sm:px-7') }}">

            @if (session('status'))
                <div role="status"
                     class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
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
        <div class="{{ $isPlantForm || $isDetailTanaman ? 'akarasa-content-detail' : 'akarasa-content' }}">
            @yield('content')
        </div>

    </main>

</div>

</body>
</html>
