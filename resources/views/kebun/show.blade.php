@extends('layouts.app')

@section('content')
    <section class="bg-emerald-950 px-5 py-10 text-white sm:px-8 sm:py-14">
        <div class="mx-auto max-w-7xl">
            <a href="{{ route('penanam.index') }}" class="text-sm font-semibold text-emerald-200 hover:text-white">← Ganti kebun</a>
            <div class="mt-5 flex flex-wrap items-end justify-between gap-5">
                <div><p class="text-sm font-bold uppercase tracking-[.16em] text-lime-300">Dashboard kebun</p><h1 class="mt-2 font-display text-4xl font-extrabold">{{ $penanam->nama }}</h1><p class="mt-2 text-emerald-100">{{ $plantings->count() }} kelompok tanaman tercatat</p></div>
                <a href="{{ route('tanaman-ditanam.create') }}" class="rounded-full bg-lime-300 px-5 py-3 font-bold text-emerald-950 hover:bg-lime-200">+ Tambah tanaman</a>
            </div>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-5 py-10 sm:px-8">
        @php
            $nearHarvest = $plantings->filter(function ($planting) {
                if ($planting->tanaman->min_hari_panen === null || $planting->tanaman->max_hari_panen === null) {
                    return false;
                }
                $minimumDate = $planting->ditanam_at->copy()->addDays($planting->tanaman->min_hari_panen);
                $maximumDate = $planting->ditanam_at->copy()->addDays($planting->tanaman->max_hari_panen);

                return $minimumDate->lte(today()->addDays(30)) && $maximumDate->gte(today());
            });
        @endphp
        @if ($nearHarvest->isNotEmpty())
            <aside class="mb-7 rounded-3xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
                <p class="text-xs font-bold uppercase tracking-[.16em] text-amber-800">Perkiraan hasil mendekat</p>
                <h2 class="mt-1 font-display text-xl font-extrabold text-amber-950">Periksa tanaman yang memasuki rentang panen</h2>
                <div class="mt-4 flex flex-wrap gap-3">
                    @foreach ($nearHarvest as $planting)
                        @php
                            $from = $planting->ditanam_at->copy()->addDays($planting->tanaman->min_hari_panen);
                            $to = $planting->ditanam_at->copy()->addDays($planting->tanaman->max_hari_panen);
                        @endphp
                        <p class="rounded-xl bg-white px-4 py-3 text-sm text-amber-950"><strong>{{ $planting->tanaman->nama }}</strong><span class="mt-1 block">{{ $from->translatedFormat('j M Y') }} – {{ $to->translatedFormat('j M Y') }}</span></p>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-amber-800">Rentang ini perkiraan, bukan kepastian panen.</p>
            </aside>
        @endif
        <form method="GET" action="{{ route('kebun.show', $penanam) }}" class="mb-7 flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-4">
            <label class="text-sm font-bold text-stone-700">Filter jadwal berdasarkan tanggal
                <input type="date" name="tanggal" value="{{ $selectedDate }}" class="mt-1 block rounded-xl border border-stone-300 px-4 py-2 font-normal">
            </label>
            <button class="rounded-xl bg-emerald-800 px-4 py-2.5 text-sm font-bold text-white">Tampilkan</button>
            @if ($selectedDate)<a href="{{ route('kebun.show', $penanam) }}" class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-bold text-stone-600">Hapus filter</a>@endif
        </form>
        @if ($plantings->isEmpty())
            <div class="rounded-3xl border border-dashed border-stone-300 bg-white px-6 py-16 text-center">
                <h2 class="font-display text-2xl font-bold text-emerald-950">Kebunmu masih kosong</h2>
                <p class="mt-2 text-stone-500">Tambahkan tanaman pertama untuk mulai membuat jadwal perawatan.</p>
                <a href="{{ route('tanaman-ditanam.create') }}" class="mt-6 inline-flex rounded-full bg-emerald-800 px-5 py-3 font-bold text-white">Tambah tanaman</a>
            </div>
        @else
            <div class="space-y-7">
                @foreach ($plantings as $planting)
                    <article class="overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm">
                        <div class="grid gap-0 lg:grid-cols-[.8fr_1.2fr]">
                            <div class="bg-emerald-50 p-6 sm:p-8">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-emerald-800">{{ $planting->tanaman->kategori }}</span>
                                        <h2 class="mt-4 font-display text-2xl font-extrabold text-emerald-950">{{ $planting->tanaman->nama }}</h2>
                                        <p class="mt-1 text-sm text-stone-600">{{ $planting->quantity }} tanaman · ditanam {{ $planting->ditanam_at->format('d M Y') }}</p>
                                        @if ($planting->location)<p class="mt-1 text-sm text-stone-500">{{ $planting->location }}</p>@endif
                                    </div>
                                    <span class="rounded-full bg-emerald-800 px-3 py-1.5 text-xs font-bold text-white">{{ ucfirst($planting->status) }}</span>
                                </div>
                                @if ($planting->tanaman->min_hari_panen !== null && $planting->tanaman->max_hari_panen !== null)
                                    @php
                                        $harvestStart = $planting->ditanam_at->copy()->addDays($planting->tanaman->min_hari_panen);
                                        $harvestEnd = $planting->ditanam_at->copy()->addDays($planting->tanaman->max_hari_panen);
                                    @endphp
                                    <div class="mt-6 rounded-2xl bg-white p-4">
                                        <p class="text-xs font-bold uppercase tracking-wide text-stone-500">Perkiraan panen / pemanfaatan</p>
                                        <p class="mt-1 font-bold text-emerald-950">{{ $harvestStart->translatedFormat('j F Y') }} – {{ $harvestEnd->translatedFormat('j F Y') }}</p>
                                        <p class="mt-1 text-xs text-stone-500">Perkiraan, bukan tanggal pasti.</p>
                                    </div>
                                @else
                                    <p class="mt-6 rounded-2xl bg-white p-4 text-sm text-stone-500">Estimasi panen belum tersedia.</p>
                                @endif
                            </div>
                            <div class="p-6 sm:p-8">
                                <h3 class="font-display text-xl font-bold text-emerald-950">Jadwal perawatan</h3>
                                @php
                                    $todaySchedules = $planting->jadwalPerawatan
                                        ->where('status', 'pending')
                                        ->filter(fn ($schedule) => $schedule->perawatan_at->toDateString() === today()->toDateString());
                                    $upcoming = $planting->jadwalPerawatan
                                        ->where('status', 'pending')
                                        ->when($selectedDate, fn ($schedules) => $schedules->filter(fn ($schedule) => $schedule->perawatan_at->toDateString() === $selectedDate))
                                        ->sortBy('perawatan_at')
                                        ->take(6);
                                @endphp
                                @if ($upcoming->isEmpty())
                                    <p class="mt-4 rounded-2xl bg-stone-50 p-4 text-sm text-stone-500">{{ $selectedDate ? 'Tidak ada jadwal pending pada tanggal ini.' : 'Belum ada jadwal yang akan datang.' }}</p>
                                @else
                                    <ul class="mt-4 divide-y divide-stone-100">
                                        @foreach ($upcoming as $schedule)
                                            <li class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="font-bold text-stone-800">{{ $schedule->aturanPerawatan->nama }}</p>
                                                    <p class="mt-1 text-sm text-stone-500">{{ $schedule->aturanPerawatan->instruksi ?: 'Ikuti panduan perawatan tanaman.' }}</p>
                                                    <p class="mt-1 text-xs font-semibold text-emerald-800">{{ $schedule->perawatan_at->translatedFormat('l, j F Y') }}</p>
                                                </div>
                                                <form method="POST" action="{{ route('kebun.complete', [$penanam, $schedule]) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="whitespace-nowrap rounded-full border border-emerald-700 px-4 py-2 text-sm font-bold text-emerald-800 hover:bg-emerald-800 hover:text-white">Tandai selesai</button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if ($todaySchedules->isNotEmpty() && ! $selectedDate)
                                    <p class="mt-4 rounded-2xl border border-lime-300 bg-lime-50 p-4 text-sm font-bold text-emerald-950">Perlu dilakukan hari ini: {{ $todaySchedules->count() }} kegiatan perawatan.</p>
                                @endif
                                <details class="mt-3">
                                    <summary class="cursor-pointer text-sm font-bold text-emerald-800">Lihat riwayat perawatan</summary>
                                    <ul class="mt-3 space-y-2">
                                        @forelse ($planting->jadwalPerawatan->whereIn('status', ['completed', 'missed'])->sortByDesc('perawatan_at') as $schedule)
                                            <li class="flex justify-between gap-3 rounded-xl bg-stone-50 px-4 py-3 text-sm"><span>{{ $schedule->aturanPerawatan->nama }} · {{ $schedule->perawatan_at->format('d M Y') }}</span><span class="{{ $schedule->status === 'completed' ? 'text-emerald-700' : 'text-amber-700' }} font-bold">{{ $schedule->status === 'completed' ? 'Selesai' : 'Terlewat' }}</span></li>
                                        @empty
                                            <li class="mt-3 text-sm text-stone-500">Belum ada riwayat.</li>
                                        @endforelse
                                    </ul>
                                </details>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
