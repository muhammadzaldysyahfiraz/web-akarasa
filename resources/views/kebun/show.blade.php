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
        <a href="{{ route('tanaman-ditanam.create') }}" class="inline-flex min-h-14 items-center rounded-xl bg-[#174638] px-5 py-3 text-base font-bold text-white hover:bg-[#285e50]">
            + Tambah tanaman
        </a>
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
                        <div class="p-5 sm:p-7">
                            <h3 class="text-xl font-bold text-emerald-950">Perawatan: penyiraman</h3>
                            <p class="mt-1 text-base text-stone-600">Pilih tombol setelah kegiatan benar-benar dilakukan.</p>

                            @if ($openWatering->isEmpty())
                                <p class="mt-4 rounded-xl bg-stone-50 p-4 text-base text-stone-600">Belum ada penyiraman yang perlu dicatat.</p>
                            @else
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
                            @endif

                            <details class="mt-4">
                                <summary class="cursor-pointer py-2 text-base font-bold text-emerald-800">Lihat riwayat penyiraman</summary>
                                <ul class="mt-2 divide-y divide-stone-200">
                                    @forelse ($wateringSchedules->where('status', 'completed')->sortByDesc('perawatan_at') as $schedule)
                                        @php
                                            $activity = $schedule->aktivitasPerawatan;
                                            $activityCanBeCancelled = $activity
                                                && $activity->performed_at->isSameWeek(today(), \Carbon\CarbonImmutable::MONDAY);
                                        @endphp
                                        <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <p class="text-base font-semibold text-stone-800">{{ $schedule->perawatan_at->translatedFormat('j F Y') }} · Sudah disiram</p>
                                                @if ($activity?->nama_pelaksana)
                                                    <p class="text-sm text-stone-600">Dicatat oleh {{ $activity->nama_pelaksana }}</p>
                                                @endif
                                            </div>
                                            @if ($activityCanBeCancelled)
                                                <form method="POST" action="{{ route('kebun.activity.cancel', [$penanam, $activity]) }}"
                                                    onsubmit="return confirm('Yakin membatalkan tanda sudah disiram?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="min-h-12 rounded-xl border-2 border-stone-400 px-4 py-2 text-base font-semibold text-stone-700">Batalkan</button>
                                                </form>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="py-3 text-base text-stone-600">Belum ada aktivitas penyiraman.</li>
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
