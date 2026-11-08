@extends('layouts.app', ['title' => 'Jadwal Perawatan'])

@section('content')
@php
    $previousMonth = $monthStart->subMonth()->format('Y-m');
    $nextMonth = $monthStart->addMonth()->format('Y-m');
    $weekdays = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
@endphp

@if (session('status'))
    <div role="status" class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">
        {{ session('status') }}
    </div>
@endif

<div class="mb-5 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-[#193b32] sm:text-[27px]">Jadwal Perawatan</h1>
        <p class="mt-1 text-xs text-gray-500">
            Pantau aktivitas penyiraman, pemupukan, dan perawatan tanaman TOGA.
        </p>
    </div>

    @if ($canManage)
        <a href="{{ route('admin.resources.create', 'aturan-perawatan') }}"
           class="rounded-lg bg-[#174638] px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-[#285e50]">
            + Tambah Jadwal
        </a>
    @endif
</div>

<div class="grid items-stretch gap-4 xl:grid-cols-[1.55fr_1fr]">
    <section class="rounded-xl border border-[#e2e8df] bg-white p-4 shadow-sm sm:p-5">
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-[#193b32]">
                {{ $monthStart->translatedFormat('F Y') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route($scheduleRouteName, $scheduleRouteParameters + ['month' => $previousMonth]) }}"
                   aria-label="Bulan sebelumnya"
                   class="grid h-8 w-8 place-items-center rounded-lg bg-[#f3f6f0] text-sm text-[#193b32] hover:bg-[#e8eee3]">‹</a>
                <a href="{{ route($scheduleRouteName, $scheduleRouteParameters + ['month' => $nextMonth]) }}"
                   aria-label="Bulan berikutnya"
                   class="grid h-8 w-8 place-items-center rounded-lg bg-[#f3f6f0] text-sm text-[#193b32] hover:bg-[#e8eee3]">›</a>
            </div>
        </div>

        <div class="grid grid-cols-7 gap-1 text-center">
            @foreach ($weekdays as $weekday)
                <div class="py-2 text-[10px] font-semibold text-gray-500">{{ $weekday }}</div>
            @endforeach

            @foreach ($calendarDays as $day)
                @php
                    $dayKey = $day->toDateString();
                    $daySchedules = $schedulesByDate->get($dayKey, collect());
                    $isSelected = $dayKey === $selectedDate->toDateString();
                    $isCurrentMonth = $day->month === $monthStart->month;
                @endphp
                <a href="{{ route($scheduleRouteName, $scheduleRouteParameters + [
                    'month' => $monthStart->format('Y-m'),
                    'tanggal' => $dayKey,
                ]) }}"
                   aria-label="{{ $day->translatedFormat('j F Y') }}: {{ $daySchedules->count() }} jadwal"
                   @class([
                       'flex min-h-11 flex-col items-center justify-center rounded-lg border text-xs transition sm:min-h-12',
                       'border-[#6a9a72] bg-[#f3f8ef] font-bold text-[#193b32]' => $isSelected,
                       'border-transparent text-[#193b32] hover:bg-[#f3f6f0]' => ! $isSelected && $isCurrentMonth,
                       'border-transparent text-gray-300 hover:bg-[#f8faf6]' => ! $isCurrentMonth,
                   ])>
                    <span>{{ $day->day }}</span>
                    @if ($daySchedules->isNotEmpty())
                        <span class="mt-1 flex gap-0.5" aria-hidden="true">
                            @foreach ($daySchedules->take(3) as $schedule)
                                <span @class([
                                    'h-1 w-1 rounded-full',
                                    'bg-emerald-700' => $schedule->status === 'completed',
                                    'bg-amber-500' => $schedule->status === 'pending',
                                    'bg-gray-400' => $schedule->status === 'missed',
                                ])></span>
                            @endforeach
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

    <section class="min-h-[390px] rounded-xl border border-[#e2e8df] bg-white p-4 shadow-sm sm:p-5">
        <h2 class="text-sm font-bold text-[#193b32]">
            Aktivitas {{ $selectedDate->translatedFormat('l, d M') }}
        </h2>

        <div class="mt-4 space-y-2">
            @forelse ($schedulesByDate->get($selectedDate->toDateString(), collect()) as $schedule)
                @php
                    $planting = $schedule->tanamanDitanam;
                    $statusLabel = match ($schedule->status) {
                        'completed' => 'Selesai',
                        'missed' => 'Terlewat',
                        default => 'Belum',
                    };
                @endphp
                <article class="rounded-lg border border-[#e5ebe2] bg-[#f5f7f2] p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-[#193b32]">
                                {{ $schedule->aturanPerawatan?->nama ?? 'Perawatan' }}
                            </p>
                            <p class="mt-1 text-[10px] text-gray-500">
                                {{ $planting?->tanaman?->nama ?? 'Tanaman' }}
                            </p>
                        </div>
                        <span @class([
                            'shrink-0 text-[10px] font-semibold',
                            'text-emerald-700' => $schedule->status === 'completed',
                            'text-amber-600' => $schedule->status === 'pending',
                            'text-gray-500' => $schedule->status === 'missed',
                        ])>
                            {{ $statusLabel }}
                        </span>
                    </div>
                    @if (! $canManage && $schedule->status === 'pending')
                        <form method="POST" action="{{ route('jadwal-perawatan.record', $schedule) }}" class="mt-3 border-t border-[#e2e8df] pt-3">
                                @csrf
                                <button type="submit"
                                        aria-label="Tandai {{ $schedule->aturanPerawatan?->nama ?? 'perawatan' }} selesai"
                                        class="inline-flex items-center gap-2 text-left text-[10px] font-semibold text-[#193b32] hover:text-emerald-800">
                                    <span class="grid h-4 w-4 place-items-center rounded border border-emerald-700 bg-white text-[10px]" aria-hidden="true"></span>
                                    <span>Klik untuk menandai sudah dilakukan</span>
                                </button>
                        </form>
                    @elseif (! $canManage && $schedule->status === 'completed')
                        <div class="mt-3 border-t border-[#e2e8df] pt-3">
                            <span class="inline-flex items-center gap-2 text-[10px] font-semibold text-emerald-700">
                                <span class="grid h-4 w-4 place-items-center rounded border border-emerald-700 bg-emerald-700 text-[10px] text-white" aria-hidden="true">✓</span>
                                Sudah dilakukan
                                @if ($schedule->aktivitasPerawatan?->user)
                                    oleh {{ $schedule->aktivitasPerawatan->user->nama }}
                                @endif
                            </span>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-[#dce5d9] bg-[#fafbf8] px-4 py-10 text-center">
                    <p class="text-xs font-semibold text-[#193b32]">Tidak ada aktivitas pada tanggal ini</p>
                    <p class="mt-1 text-[10px] text-gray-500">Pilih tanggal lain atau tambahkan aturan perawatan.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>

@if ($canManage)
<details class="mt-6 overflow-hidden rounded-xl border border-[#e2e8df] bg-white shadow-sm">
    <summary class="cursor-pointer px-5 py-4 text-sm font-bold text-[#193b32]">
        Kelola Aturan Perawatan
    </summary>
    <div class="border-t border-[#e2e8df]">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[36rem] text-left text-xs">
                <thead class="bg-[#f3f6f0] text-[10px] uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Aturan</th>
                        <th class="px-5 py-3">Tanaman</th>
                        <th class="px-5 py-3">Frekuensi</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#edf0e9]">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-5 py-3 font-semibold text-[#193b32]">{{ $item->nama }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $item->tanaman?->nama }}</td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $item->interval_days ? 'Setiap '.$item->interval_days.' hari' : 'Satu kali' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="{{ $item->is_active ? 'text-emerald-700' : 'text-gray-500' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.resources.edit', [$resource, $item->id]) }}"
                                       class="rounded-md border border-[#dce5d8] px-3 py-1.5 font-semibold text-[#174638] hover:bg-[#f3f6f0]">Edit</a>
                                    <form method="POST"
                                          action="{{ route('admin.resources.destroy', [$resource, $item->id]) }}"
                                          onsubmit="return confirm('Hapus atau nonaktifkan data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-3 py-1.5 font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">Belum ada aturan perawatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#e2e8df] px-5 py-4">{{ $items->links() }}</div>
    </div>
</details>
@endif
@endsection
