@extends('layouts.app', ['title' => 'Dashboard KebunTOGA'])

@section('content')
<section class="mx-auto max-w-6xl">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-bold uppercase tracking-wide text-emerald-700">KebunTOGA</p>
            <h1 class="mt-2 text-3xl font-bold text-[#193b32]">Dashboard</h1>
            <p class="mt-2 text-base text-stone-600">Ringkasan kebun dan laporan perawatan untuk semua pengunjung.</p>
        </div>
        <a href="{{ route('laporan.pdf') }}" class="inline-flex min-h-14 items-center rounded-xl bg-[#174638] px-5 py-3 text-base font-bold text-white hover:bg-[#285e50]">
            Unduh laporan PDF
        </a>
    </div>

    <p class="mt-6 text-sm font-semibold text-stone-600">
        Rekap 7 hari terakhir: {{ $report['periodStart']->translatedFormat('j F') }} – {{ $report['periodEnd']->translatedFormat('j F Y') }}
    </p>

    <div class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm">
            <p class="text-base text-stone-600">Nama penanam</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">{{ $report['growers'] }}</p>
        </article>
        <article class="rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm">
            <p class="text-base text-stone-600">Kebun aktif</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">{{ $report['activeGardens'] }}</p>
        </article>
        <article class="rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm">
            <p class="text-base text-stone-600">Tanaman sedang tumbuh</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">{{ $report['activePlantings'] }}</p>
        </article>
        <article class="rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm">
            <p class="text-base text-stone-600">Aktivitas perawatan</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">{{ $report['careActivities'] }}</p>
        </article>
    </div>

    <section class="mt-5 rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm sm:p-7">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-[#193b32]">Laporan perawatan</h2>
                <p class="mt-1 text-base text-stone-600">Jadwal yang selesai dalam 7 hari terakhir</p>
            </div>
            <a href="{{ route('laporan.pdf') }}" class="text-base font-bold text-emerald-800 underline">Unduh PDF</a>
        </div>
        <div class="mt-5 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl bg-emerald-50 p-4">
                <p class="text-sm text-stone-600">Jadwal selesai</p>
                <p class="mt-1 text-xl font-bold text-emerald-950">{{ $report['completedCare'] }} dari {{ $report['scheduledCare'] }}</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-4">
                <p class="text-sm text-stone-600">Panen tercatat</p>
                <p class="mt-1 text-xl font-bold text-emerald-950">{{ $report['harvests'] }}</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-4">
                <p class="text-sm text-stone-600">Penyelesaian jadwal</p>
                <p class="mt-1 text-xl font-bold text-emerald-950">
                    {{ $report['completionRate'] === null ? 'Belum ada data' : $report['completionRate'].'%' }}
                </p>
            </div>
        </div>

        <h3 class="mt-6 text-lg font-bold text-[#193b32]">Aktivitas terbaru</h3>
        <ul class="mt-2 divide-y divide-stone-200">
            @forelse ($report['recentActivities'] as $activity)
                <li class="py-3">
                    <p class="text-base font-semibold text-stone-800">
                        {{ $activity->jadwalPerawatan?->aturanPerawatan?->nama ?? 'Perawatan' }} ·
                        {{ $activity->tanamanDitanam?->tanaman?->nama ?? 'Tanaman' }}
                    </p>
                    <p class="mt-1 text-sm text-stone-600">
                        {{ $activity->tanamanDitanam?->penanam?->nama ?? 'Penanam' }} ·
                        {{ $activity->performed_at->translatedFormat('j F Y') }}
                    </p>
                </li>
            @empty
                <li class="py-4 text-base text-stone-600">Belum ada aktivitas yang tercatat.</li>
            @endforelse
        </ul>
    </section>

    @if (auth()->user()?->role === 'admin')
        <section class="mt-5 rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-[#193b32]">Pengelolaan admin</h2>
            <div class="mt-3 flex flex-wrap gap-3">
                <a href="{{ route('admin.resources.index', 'tanaman') }}" class="rounded-xl border border-emerald-800 px-4 py-3 font-semibold text-emerald-900">Kelola katalog tanaman</a>
                <a href="{{ route('admin.resources.index', 'aturan-perawatan') }}" class="rounded-xl border border-emerald-800 px-4 py-3 font-semibold text-emerald-900">Kelola aturan perawatan</a>
            </div>
        </section>
    @endif
</section>
@endsection
