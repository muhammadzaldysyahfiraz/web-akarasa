@extends('layouts.app', ['title' => 'Jadwal Perawatan'])

@section('content')
<section class="mx-auto max-w-6xl space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Perawatan Harian</p>
            <h1 class="mt-1 text-2xl font-bold text-[#193b32] sm:text-3xl">Kalender Jadwal Perawatan</h1>
            <p class="mt-1 text-sm text-stone-600">Pilih tanggal pada kalender, lalu centang perawatan yang sudah dilakukan. Otomatis masuk ke laporan Dashboard.</p>
        </div>

        <a href="{{ route('user.dashboard') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-[#174638] bg-white px-4 py-2.5 text-sm font-semibold text-[#174638] hover:bg-emerald-50">
            Lihat Laporan di Dashboard →
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        {{-- KALENDER BULANAN --}}
        <div class="rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm lg:col-span-5">
            <div class="flex items-center justify-between">
                <a href="{{ route('jadwal-perawatan.index', ['month' => $monthStart->subMonth()->format('Y-m'), 'tanggal' => $monthStart->subMonth()->toDateString()]) }}"
                   class="rounded-lg border border-stone-200 px-3 py-1.5 text-sm font-semibold text-stone-700 hover:bg-stone-100">
                    ←
                </a>
                <h2 class="text-base font-bold text-[#193b32]">
                    {{ $monthStart->locale('id')->translatedFormat('F Y') }}
                </h2>
                <a href="{{ route('jadwal-perawatan.index', ['month' => $monthStart->addMonth()->format('Y-m'), 'tanggal' => $monthStart->addMonth()->toDateString()]) }}"
                   class="rounded-lg border border-stone-200 px-3 py-1.5 text-sm font-semibold text-stone-700 hover:bg-stone-100">
                    →
                </a>
            </div>

            <div class="mt-4 grid grid-cols-7 text-center text-xs font-bold text-stone-500">
                <div>Sen</div>
                <div>Sel</div>
                <div>Rab</div>
                <div>Kam</div>
                <div>Jum</div>
                <div>Sab</div>
                <div>Min</div>
            </div>

            <div class="mt-2 grid grid-cols-7 gap-1.5">
                @foreach ($calendarDays as $day)
                    @php
                        $dateStr = $day->toDateString();
                        $isCurrentMonth = $day->month === $monthStart->month;
                        $isSelected = $dateStr === $selectedDate->toDateString();
                        $isToday = $day->isToday();
                        $doneCount = $activityCountByDate[$dateStr] ?? 0;
                    @endphp
                    <a href="{{ route('jadwal-perawatan.index', ['month' => $monthStart->format('Y-m'), 'tanggal' => $dateStr]) }}"
                       class="flex h-12 flex-col items-center justify-center rounded-xl border text-sm transition
                       {{ $isSelected
                            ? 'border-[#174638] bg-[#174638] font-bold text-white shadow-sm'
                            : ($isCurrentMonth
                                ? 'border-stone-200 bg-white text-stone-800 hover:border-emerald-600 hover:bg-emerald-50'
                                : 'border-transparent bg-stone-50 text-stone-400') }}">
                        <span>{{ $day->day }}</span>
                        @if ($doneCount > 0)
                            <span class="mt-0.5 text-[10px] font-bold {{ $isSelected ? 'text-[#b9dc68]' : 'text-emerald-700' }}">
                                ✓ {{ $doneCount }}
                            </span>
                        @elseif ($isToday)
                            <span class="mt-0.5 h-1.5 w-1.5 rounded-full {{ $isSelected ? 'bg-[#b9dc68]' : 'bg-emerald-600' }}"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-3 text-xs text-stone-500">
                <span>✓ = Ada perawatan dicentang</span>
                <a href="{{ route('jadwal-perawatan.index') }}" class="font-semibold text-emerald-800 hover:underline">Ke Hari Ini</a>
            </div>
        </div>

        {{-- DAFTAR CENTANG PERAWATAN PADA TANGGAL TERPILIH --}}
        <div class="rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm sm:p-6 lg:col-span-7">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-stone-200 pb-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Checklist Perawatan</p>
                    <h2 class="mt-0.5 text-xl font-bold text-[#193b32]">
                        {{ $selectedDate->locale('id')->translatedFormat('l, j F Y') }}
                    </h2>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800">
                    {{ collect($checkedMap)->flatten()->count() }} aktivitas dicentang
                </span>
            </div>

            @if ($plantings->isEmpty())
                <div class="py-12 text-center">
                    <p class="text-base font-semibold text-stone-700">Belum ada tanaman aktif di kebun warga.</p>
                    <a href="{{ route('tanaman-ditanam.create') }}"
                       class="mt-4 inline-flex items-center rounded-xl bg-[#174638] px-4 py-2.5 text-sm font-bold text-white">
                        + Tambah Tanaman Baru
                    </a>
                </div>
            @else
                <div class="mt-4 space-y-4">
                    @foreach ($plantings as $planting)
                        <div class="rounded-xl border border-stone-200 bg-[#f9fbf8] p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <h3 class="text-base font-bold text-[#193b32]">
                                        {{ $planting->tanaman?->nama ?? 'Tanaman' }}
                                        <span class="ml-1.5 rounded-md bg-white px-2 py-0.5 text-xs font-semibold text-emerald-800 border border-emerald-200">
                                            Kebun {{ $planting->penanam?->nama ?? '-' }}
                                        </span>
                                    </h3>
                                    <p class="mt-0.5 text-xs text-stone-500">
                                        {{ $planting->quantity }} tanaman · ditanam {{ $planting->ditanam_at->translatedFormat('j M Y') }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                @foreach ($careTypes as $care)
                                    @php
                                        $isChecked = isset($checkedMap[$planting->id][$care]);
                                    @endphp
                                    <form method="POST" action="{{ route('jadwal-perawatan.toggle') }}">
                                        @csrf
                                        <input type="hidden" name="tanaman_ditanam_id" value="{{ $planting->id }}">
                                        <input type="hidden" name="tanggal" value="{{ $selectedDate->toDateString() }}">
                                        <input type="hidden" name="aktivitas" value="{{ $care }}">

                                        <button type="submit"
                                            class="flex w-full items-center gap-3 rounded-xl border px-3.5 py-2.5 text-left text-sm font-semibold transition
                                            {{ $isChecked
                                                ? 'border-[#174638] bg-[#174638] text-white'
                                                : 'border-stone-300 bg-white text-stone-700 hover:border-emerald-700 hover:bg-emerald-50' }}">
                                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded border text-xs font-bold
                                                {{ $isChecked
                                                    ? 'border-[#b9dc68] bg-[#b9dc68] text-[#174638]'
                                                    : 'border-stone-400 bg-white text-transparent' }}">
                                                ✓
                                            </span>
                                            <span>{{ $care }}</span>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endsection