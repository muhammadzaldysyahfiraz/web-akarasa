@extends('layouts.app', ['title' => 'Kebunku'])

@section('content')
<section class="mx-auto max-w-5xl px-4 py-6 sm:px-7">
    <a href="{{ route('penanam.index') }}" class="text-base font-semibold text-emerald-800 underline">← Daftar penanam</a>

    <div class="mt-5 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-bold uppercase tracking-wide text-emerald-700">Kebunku</p>
            <h1 class="mt-1 text-3xl font-bold text-[#193b32]">{{ $penanam->nama }}</h1>
            <p class="mt-2 text-base text-stone-600">{{ $plantings->count() }} tanaman tercatat</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('laporan.pdf', ['penanam_id' => $penanam->id]) }}"
               class="inline-flex min-h-14 items-center rounded-xl border-2 border-[#174638] bg-white px-5 py-3 text-base font-bold text-[#174638] hover:bg-emerald-50">
                Unduh Laporan PDF
            </a>
            <a href="{{ route('tanaman-ditanam.create') }}"
               class="inline-flex min-h-14 items-center rounded-xl bg-[#174638] px-5 py-3 text-base font-bold text-white hover:bg-[#285e50]">
                + Tambah tanaman
            </a>
        </div>
    </div>

    @if ($plantings->isEmpty())
        <div class="mt-7 rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-14 text-center">
            <h2 class="text-2xl font-bold text-emerald-950">Kebun ini masih kosong</h2>
            <p class="mt-3 text-base text-stone-600">Tambahkan tanaman pertama untuk mulai mencatat perawatannya.</p>
            <a href="{{ route('tanaman-ditanam.create') }}" class="mt-6 inline-flex min-h-14 items-center rounded-xl bg-emerald-800 px-5 py-3 text-base font-bold text-white">Tambah tanaman</a>
        </div>
    @else
        <div class="mt-7 space-y-6">
            @foreach ($plantings as $planting)
                @php
                    $estimate = $planting->estimasiPanen();
                    $wateringSchedules = $planting->jadwalPerawatan
                        ->filter(fn ($schedule) => $schedule->aturanPerawatan?->type === 'penyiraman')
                        ->sortByDesc('perawatan_at');
                    $openWatering = $wateringSchedules
                        ->whereIn('status', ['pending', 'missed'])
                        ->sortBy('perawatan_at')
                        ->take(4);
                    $recentActivities = $planting->aktivitasPerawatan
                        ->sortByDesc('performed_at');
                    $harvest = $planting->panen->sortByDesc('dipanen_at')->first();
                    $harvestCanBeCancelled = $harvest
                        && $harvest->dipanen_at->isSameWeek(today(), \Carbon\CarbonImmutable::MONDAY);
                @endphp

                <article class="overflow-hidden rounded-2xl border border-[#e2e8df] bg-white shadow-sm">
                    <div class="bg-emerald-50 p-5 sm:p-7">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <span class="rounded-full bg-white px-3 py-1 text-sm font-bold text-emerald-800">{{ $planting->tanaman->kategori }}</span>
                                <h2 class="mt-3 text-2xl font-bold text-emerald-950">{{ $planting->tanaman->nama }}</h2>
                                <p class="mt-2 text-base text-stone-700">{{ $planting->quantity }} tanaman · ditanam {{ $planting->ditanam_at->translatedFormat('j F Y') }}</p>
                                <p class="mt-1 text-base text-stone-700">Tahap: {{ ucfirst($planting->fase_pertumbuhan) }}</p>
                                @if ($planting->location)
                                    <p class="mt-1 text-base text-stone-600">Lokasi: {{ $planting->location }}</p>
                                @endif
                                @if ($planting->catatan)
                                    <p class="mt-2 text-base text-stone-600">{{ $planting->catatan }}</p>
                                @endif
                            </div>
                            <span class="rounded-full bg-emerald-800 px-4 py-2 text-sm font-bold text-white">{{ $planting->status === 'dipanen' ? 'Sudah dipanen' : 'Sedang tumbuh' }}</span>
                        </div>

                        <div class="mt-5 rounded-xl bg-white p-4 sm:p-5">
                            <h3 class="text-lg font-bold text-emerald-950">Perkiraan panen</h3>
                            @if ($estimate)
                                <p class="mt-2 text-base text-stone-700">
                                    {{ $estimate['mulai']->translatedFormat('j F Y') }} – {{ $estimate['sampai']->translatedFormat('j F Y') }}
                                </p>
                                <p class="mt-1 text-sm text-stone-600">Ini perkiraan, bukan tanggal yang pasti.</p>
                            @else
                                <p class="mt-2 text-base text-stone-600">Perkiraan panen belum tersedia untuk tanaman ini.</p>
                            @endif

                            @if ($planting->status === 'bertumbuh')
                                @if ($estimate && $estimate['mulai']->lte(today()))
                                    <form method="POST" action="{{ route('kebun.harvest', [$penanam, $planting]) }}" class="mt-4"
                                        onsubmit="return confirm('Yakin tanaman ini sudah dipanen?')">
                                        @csrf
                                        <button class="min-h-14 w-full rounded-xl bg-amber-700 px-5 py-3 text-base font-bold text-white hover:bg-amber-800 sm:w-auto">
                                            Sudah dipanen
                                        </button>
                                    </form>
                                @else
                                    <p class="mt-3 text-base font-semibold text-stone-600">Tombol panen akan aktif saat perkiraan masa panen dimulai.</p>
                                @endif
                            @elseif ($planting->status === 'dipanen')
                                <p class="mt-3 text-base font-semibold text-emerald-800">Pengingat perawatan sudah berhenti.</p>
                                @if ($harvestCanBeCancelled)
                                    <form method="POST" action="{{ route('kebun.harvest.cancel', [$penanam, $planting]) }}" class="mt-4"
                                        onsubmit="return confirm('Yakin membatalkan catatan panen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="min-h-12 rounded-xl border-2 border-emerald-800 px-4 py-2 text-base font-bold text-emerald-900">Batalkan panen</button>
                                    </form>
                                @else
                                    <p class="mt-1 text-sm text-stone-600">Riwayat tanaman akan dihapus otomatis setelah 2 bulan.</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if ($planting->status === 'bertumbuh')
                        <div class="space-y-6 p-5 sm:p-7">
                            {{-- Catat Perawatan Hari Ini (Simpel) --}}
                            <div class="rounded-2xl border border-[#dce5d8] bg-[#f8faf6] p-4 sm:p-5">
                                <h3 class="text-lg font-bold text-emerald-950">Catat perawatan hari ini</h3>
                                <p class="mt-1 text-sm text-stone-600">Pilih perawatan yang baru saja dilakukan pada tanaman ini.</p>

                                <div class="mt-4 flex flex-wrap gap-2.5">
                                    @foreach (['Disiram', 'Diberi pupuk', 'Dipangkas / bersihkan gulma', 'Cek hama & daun'] as $aksi)
                                        <form method="POST" action="{{ route('kebun.quick-log', [$penanam, $planting]) }}">
                                            @csrf
                                            <input type="hidden" name="aktivitas" value="{{ $aksi }}">
                                            <button type="submit"
                                                class="min-h-12 rounded-xl border-2 border-[#174638] bg-white px-4 py-2.5 text-sm font-bold text-[#174638] transition hover:bg-[#174638] hover:text-white">
                                                + {{ $aksi }}
                                            </button>
                                        </form>
                                    @endforeach
                                </div>

                                <form method="POST" action="{{ route('kebun.quick-log', [$penanam, $planting]) }}" class="mt-3 flex flex-col gap-2.5 sm:flex-row">
                                    @csrf
                                    <input type="text" name="aktivitas" required maxlength="255"
                                        placeholder="Atau tulis perawatan lain, misal: Ganti media tanam..."
                                        class="min-h-12 flex-1 rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                                    <button type="submit"
                                        class="min-h-12 rounded-xl bg-[#174638] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#285e50]">
                                        Simpan
                                    </button>
                                </form>
                            </div>

                            {{-- Jadwal Penyiraman Terjadwal --}}
                            @if ($openWatering->isNotEmpty())
                                <div>
                                    <h3 class="text-lg font-bold text-emerald-950">Pengingat jadwal perawatan</h3>
                                    <ul class="mt-3 divide-y divide-stone-200">
                                        @foreach ($openWatering as $schedule)
                                            <li class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="text-base font-bold text-stone-800">{{ $schedule->aturanPerawatan->nama }}</p>
                                                    <p class="mt-1 text-base text-stone-600">{{ $schedule->aturanPerawatan->instruksi ?: 'Siram tanaman secukupnya.' }}</p>
                                                    <p class="mt-1 text-sm font-semibold text-emerald-800">{{ $schedule->perawatan_at->translatedFormat('l, j F Y') }}</p>
                                                </div>
                                                <form method="POST" action="{{ route('kebun.complete', [$penanam, $schedule]) }}"
                                                    onsubmit="return confirm('Yakin tanaman ini sudah disiram?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="min-h-14 w-full rounded-xl border-2 border-emerald-800 px-5 py-3 text-base font-bold text-emerald-900 hover:bg-emerald-800 hover:text-white sm:w-auto">
                                                        Sudah disiram
                                                    </button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Riwayat Aktivitas Perawatan --}}
                            <details open>
                                <summary class="cursor-pointer py-1 text-base font-bold text-emerald-800">Riwayat perawatan tanaman ({{ $recentActivities->count() }})</summary>
                                <ul class="mt-2 divide-y divide-stone-200">
                                    @forelse ($recentActivities as $activity)
                                        @php
                                            $activityCanBeCancelled = $activity->performed_at->isSameWeek(today(), \Carbon\CarbonImmutable::MONDAY);
                                        @endphp
                                        <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <p class="text-base font-semibold text-stone-800">
                                                    {{ $activity->performed_at->translatedFormat('j F Y') }} · {{ $activity->catatan ?: ($activity->jadwalPerawatan?->aturanPerawatan?->nama ?? 'Perawatan') }}
                                                </p>
                                                @if ($activity->nama_pelaksana)
                                                    <p class="text-sm text-stone-600">Dicatat oleh {{ $activity->nama_pelaksana }}</p>
                                                @endif
                                            </div>
                                            @if ($activityCanBeCancelled)
                                                <form method="POST" action="{{ route('kebun.activity.cancel', [$penanam, $activity]) }}"
                                                    onsubmit="return confirm('Yakin membatalkan catatan perawatan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="min-h-11 rounded-xl border border-stone-300 px-3.5 py-1.5 text-sm font-semibold text-stone-700 hover:bg-stone-100">Batalkan</button>
                                                </form>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="py-3 text-base text-stone-600">Belum ada aktivitas perawatan. Klik salah satu tombol di atas untuk mencatat.</li>
                                    @endforelse
                                </ul>
                            </details>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</section>
@endsection