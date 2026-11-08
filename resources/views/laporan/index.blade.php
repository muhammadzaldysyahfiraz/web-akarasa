@extends('layouts.app', ['title' => 'Laporan'])

@section('content')
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-[#193b32] sm:text-[27px]">Laporan &amp; Analisis</h1>
        <p class="mt-1 text-xs text-gray-500">
            Ringkasan berdasarkan jadwal dan aktivitas perawatan yang tercatat.
        </p>
    </div>
    <a href="{{ route($report['scheduleRouteName'], $report['scheduleRouteParameters']) }}"
       class="rounded-lg border border-[#dce5d8] bg-white px-4 py-2.5 text-xs font-semibold text-[#174638] hover:bg-[#f3f6f0]">
        Lihat jadwal perawatan
    </a>
</div>

<div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
    <article class="rounded-xl border border-[#e2e8df] bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <p class="text-xs text-gray-500">Penyelesaian Perawatan</p>
            <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#edf4e7] text-emerald-800" aria-hidden="true">✓</span>
        </div>
        @if ($report['completionRate'] !== null)
            <p class="mt-3 text-2xl font-bold text-[#193b32]">{{ $report['completionRate'] }}%</p>
            <p class="mt-1 text-[10px] text-emerald-800">
                {{ $report['completedRecently'] }} dari {{ $report['scheduledRecently'] }} jadwal · 7 hari terakhir
            </p>
        @else
            <p class="mt-3 text-lg font-bold text-[#193b32]">Belum ada data</p>
            <p class="mt-1 text-[10px] text-gray-500">Belum ada jadwal dalam 7 hari terakhir.</p>
        @endif
    </article>

    <article class="rounded-xl border border-[#e2e8df] bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <p class="text-xs text-gray-500">Aktivitas Tercatat</p>
            <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#edf4e7] text-emerald-800" aria-hidden="true">▤</span>
        </div>
        <p class="mt-3 text-2xl font-bold text-[#193b32]">{{ $report['activitiesRecently'] }}</p>
        <p class="mt-1 text-[10px] text-emerald-800">Penyelesaian aktual · 7 hari terakhir</p>
    </article>

    <article class="rounded-xl border border-[#e2e8df] bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <p class="text-xs text-gray-500">Kebun Aktif</p>
            <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#edf4e7] text-emerald-800" aria-hidden="true">⌂</span>
        </div>
        <p class="mt-3 text-2xl font-bold text-[#193b32]">{{ $report['activeGardens'] }}</p>
        <p class="mt-1 text-[10px] text-emerald-800">Memiliki tanaman berstatus bertumbuh</p>
    </article>
</div>

<div class="mt-4 grid items-stretch gap-4 xl:grid-cols-[1.4fr_1fr]">
    <article class="rounded-xl border border-[#e2e8df] bg-white p-5 shadow-sm">
        <h2 class="text-xs font-bold text-[#193b32]">Aktivitas Perawatan Mingguan</h2>
        <p class="mt-1 text-[10px] text-gray-500">Jumlah aktivitas yang benar-benar dicentang selesai.</p>

        @php
            $maxWeeklyActivities = max(1, $report['activityTrend']->max('count'));
        @endphp
        <div class="mt-4 flex h-36 items-end justify-around gap-3 rounded-lg border border-[#edf0e9] bg-[#f7f9f5] px-4 pt-4">
            @foreach ($report['activityTrend'] as $week)
                @php
                    $barHeight = $week['count'] > 0
                        ? max(8, (int) round($week['count'] / $maxWeeklyActivities * 100))
                        : 3;
                @endphp
                <div class="flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2">
                    <span class="text-[9px] font-semibold text-[#47734b]">{{ $week['count'] }}</span>
                    <div class="w-full max-w-9 rounded-t bg-[#b9dc68]" style="height: {{ $barHeight }}%"></div>
                    <span class="whitespace-nowrap text-[9px] text-gray-500">{{ $week['label'] }}</span>
                </div>
            @endforeach
        </div>
    </article>

    <article class="rounded-xl border border-[#e2e8df] bg-white p-5 shadow-sm">
        <h2 class="text-xs font-bold text-[#193b32]">Riwayat Laporan Bulanan</h2>
        <p class="mt-1 text-[10px] text-gray-500">Rekap aktivitas dari jadwal yang tercatat.</p>

        <div class="mt-4 space-y-2">
            @forelse ($report['monthlyReports'] as $monthlyReport)
                <a href="{{ route($report['scheduleRouteName'], $report['scheduleRouteParameters'] + ['month' => $monthlyReport['month']->format('Y-m')]) }}"
                   class="flex items-center justify-between gap-3 rounded-lg border border-[#e5ebe2] bg-[#f5f7f2] px-3 py-3 transition hover:bg-[#edf4e7]">
                    <span class="min-w-0">
                        <span class="block truncate text-[10px] font-semibold text-[#193b32]">
                            {{ $monthlyReport['month']->translatedFormat('F Y') }}
                        </span>
                        <span class="mt-1 block text-[9px] text-gray-500">
                            {{ $monthlyReport['activities'] }} aktivitas ·
                            {{ $monthlyReport['completed'] }}/{{ $monthlyReport['scheduled'] }} jadwal selesai
                        </span>
                    </span>
                    <span class="shrink-0 text-[9px] font-semibold text-emerald-800">Lihat</span>
                </a>
            @empty
                <div class="rounded-lg border border-dashed border-[#dce5d9] px-4 py-8 text-center">
                    <p class="text-[10px] font-semibold text-[#193b32]">Belum ada aktivitas perawatan</p>
                    <p class="mt-1 text-[9px] text-gray-500">Rekap bulanan muncul setelah jadwal tercatat.</p>
                </div>
            @endforelse
        </div>
    </article>
</div>
@endsection
