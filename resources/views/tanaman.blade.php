@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F3F5EF] lg:flex">

    {{-- SIDEBAR --}}
    <aside class="flex w-full shrink-0 flex-col bg-[#17483B] text-white lg:fixed lg:inset-y-0 lg:left-0 lg:w-[220px]">

        <a href="{{ route('home') }}" class="flex items-center gap-3 px-6 py-6">
            <img src="{{ asset('images/insos-akarasa-logo.png') }}"
                 alt="Logo Akarasa"
                 class="h-9 w-9 object-contain"
                 onerror="this.style.display='none'">

            <div>
                <h1 class="text-base font-bold">Akarasa</h1>
                <p class="text-[10px] text-[#C4D8CC]">Kebun TOGA Digital</p>
            </div>
        </a>

        <nav class="flex gap-2 overflow-x-auto px-4 pb-4 lg:flex-col lg:py-5">

            <a href="{{ route('tanaman.index') }}"
               class="flex shrink-0 items-center gap-3 rounded-lg bg-[#315E50] px-4 py-3 text-xs font-semibold text-white">
                <span>▦</span>
                Dashboard
            </a>

            <a href="{{ route('tanaman.index') }}"
               class="flex shrink-0 items-center gap-3 rounded-lg px-4 py-3 text-xs text-[#D0E0D6] hover:bg-[#315E50]">
                <span>🌿</span>
                Tanaman TOGA
            </a>

            @if(Route::has('jadwal-perawatan.index'))
                <a href="{{ route('jadwal-perawatan.index') }}"
                   class="flex shrink-0 items-center gap-3 rounded-lg px-4 py-3 text-xs text-[#D0E0D6] hover:bg-[#315E50]">
                    <span>▣</span>
                    Jadwal Perawatan
                </a>
            @endif

            @if(Route::has('laporan.index'))
                <a href="{{ route('laporan.index') }}"
                   class="flex shrink-0 items-center gap-3 rounded-lg px-4 py-3 text-xs text-[#D0E0D6] hover:bg-[#315E50]">
                    <span>▥</span>
                    Laporan
                </a>
            @endif

        </nav>

        <div class="mt-auto hidden border-t border-white/10 px-5 py-5 lg:block">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#B7D96B] text-xs font-bold text-[#17483B]">
                    {{ strtoupper(substr(auth()->user()->nama ?? 'W', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-[11px] font-semibold">
                        {{ auth()->user()->nama ?? 'Warga Akarasa' }}
                    </p>
                    <p class="text-[9px] text-[#C4D8CC]">Koordinator KKN</p>
                </div>
            </div>
        </div>

    </aside>

    {{-- KONTEN UTAMA --}}
    <div class="min-w-0 flex-1 lg:ml-[220px]">

        {{-- HEADER --}}
        <header class="flex items-center justify-between gap-4 border-b border-stone-100 bg-white px-5 py-4 sm:px-8 lg:px-10">
            <div>
                <p class="text-xs font-semibold text-[#25463B]">
                    Selamat pagi, {{ auth()->user()->nama ?? 'Warga' }}
                </p>
                <p class="mt-1 text-[9px] text-stone-400">
                    {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('tanaman.index') }}" method="GET" class="hidden sm:block">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari tanaman atau warga..."
                        class="w-52 rounded-lg border border-stone-200 bg-[#F8F9F6] px-3 py-2 text-[10px] outline-none focus:border-[#64866C]"
                    >
                </form>

                <a href="{{ route('penanam.index') }}"
                   class="text-xs font-semibold text-[#17483B]">
                    Kebunku
                </a>
            </div>
        </header>

        {{-- ISI DASHBOARD --}}
        <main class="mx-auto max-w-[1400px] px-5 py-7 sm:px-8 lg:px-10 lg:py-8">

            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#64866C]">
                        Dashboard Akarasa
                    </p>

                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-[#25463B] sm:text-[28px]">
                        TANAMAN OBAT KELUARGA
                    </h2>

                    <p class="mt-2 text-[11px] text-[#849187]">
                        Pantau kondisi tanaman TOGA warga secara menyeluruh.
                    </p>
                </div>

                @if(Route::has('tanaman-ditanam.create'))
                    <a href="{{ route('tanaman-ditanam.create') }}"
                       class="rounded-md bg-[#17483B] px-4 py-3 text-[10px] font-bold text-white hover:bg-[#10372D]">
                        + Tambah tanaman
                    </a>
                @endif
            </div>

            {{-- PENCARIAN MOBILE --}}
            <form action="{{ route('tanaman.index') }}" method="GET" class="mb-5 sm:hidden">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari tanaman atau warga..."
                    class="w-full rounded-lg border border-stone-200 bg-white px-4 py-3 text-xs outline-none focus:border-[#64866C]"
                >
            </form>

            {{-- KATALOG TANAMAN --}}
            <section>
                <livewire:tanaman-katalog />
            </section>

        </main>
    </div>
</div>
@endsection