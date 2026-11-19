@extends('layouts.app', ['title' => 'Jadwal Panen'])

@section('content')
<section class="mx-auto max-w-6xl space-y-6">
    {{-- BANNER PENGINGAT PANEN AKTIF --}}
    @if (isset($activeHarvestReminders) && $activeHarvestReminders->isNotEmpty())
        <div class="rounded-2xl border border-amber-300 bg-amber-50 p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="text-xl">🔔</span>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-amber-900">Pengingat Jadwal Panen</h3>
                    <ul class="mt-1.5 space-y-1.5 text-xs text-amber-900">
                        @foreach ($activeHarvestReminders as $rem)
                            <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-amber-200 bg-white/80 px-3 py-2">
                                <div>
                                    <span class="font-bold text-[#193b32]">
                                        {{ $rem->tanggal->locale('id')->translatedFormat('j M Y') }}
                                        @if($rem->tanggal->isToday())
                                            <span class="ml-1 rounded bg-amber-500 px-1.5 py-0.5 text-[10px] font-bold text-white">HARI INI</span>
                                        @elseif($rem->tanggal->isPast())
                                            <span class="ml-1 rounded bg-red-600 px-1.5 py-0.5 text-[10px] font-bold text-white">TERLEWAT</span>
                                        @endif
                                    </span>
                                    — <strong>{{ $rem->planting?->tanaman?->nama ?? 'Semua Tanaman' }}</strong>: "{{ $rem->catatan }}"
                                </div>
                                <form method="POST" action="{{ route('jadwal-perawatan.catatan.toggle', $rem->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-[#174638] px-2.5 py-1 text-[11px] font-bold text-white hover:bg-emerald-800">
                                        ✓ Tandai Sudah Panen
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Jadwal Panen</p>
            <h1 class="mt-1 text-2xl font-bold text-[#193b32] sm:text-3xl">Kalender Jadwal Panen</h1>
            <p class="mt-1 text-sm text-stone-600">Pilih tanggal pada kalender untuk menandai jadwal panen tanaman dan mengaktifkan pengingat.</p>
        </div>

        <a href="{{ route('user.dashboard') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-[#174638] bg-white px-4 py-2.5 text-sm font-semibold text-[#174638] hover:bg-emerald-50">
            Lihat Laporan di Dashboard →
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        {{-- KALENDER BULANAN --}}
        <div class="rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm lg:col-span-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('jadwal-perawatan.index', ['month' => $monthStart->copy()->subMonth()->format('Y-m'), 'tanggal' => $monthStart->copy()->subMonth()->toDateString()]) }}"
                   class="rounded-lg border border-stone-200 px-3 py-1.5 text-sm font-semibold text-stone-700 hover:bg-stone-100">
                    ←
                </a>
                <h2 class="text-base font-bold text-[#193b32]">
                    {{ $monthStart->locale('id')->translatedFormat('F Y') }}
                </h2>
                <a href="{{ route('jadwal-perawatan.index', ['month' => $monthStart->copy()->addMonth()->format('Y-m'), 'tanggal' => $monthStart->copy()->addMonth()->toDateString()]) }}"
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
                        $dayHarvests = $harvestByDate[$dateStr] ?? collect();
                    @endphp
                    <a href="{{ route('jadwal-perawatan.index', ['month' => $monthStart->format('Y-m'), 'tanggal' => $dateStr]) }}"
                       class="flex min-h-[64px] flex-col items-center justify-center rounded-xl border px-1 py-1 text-center text-sm transition
                       {{ $isSelected
                            ? 'border-[#174638] bg-[#174638] font-bold text-white shadow-sm'
                            : ($isCurrentMonth
                                ? 'border-stone-200 bg-white text-stone-800 hover:border-emerald-600 hover:bg-emerald-50'
                                : 'border-transparent bg-stone-50 text-stone-400') }}">
                        <span>{{ $day->day }}</span>

                        {{-- Label Nama Tanaman Panen Langsung di Kotak Tanggal --}}
                        @if ($dayHarvests->isNotEmpty())
                            @php
                                $firstPlant = $dayHarvests->first()->planting?->tanaman?->nama ?? 'Panen';
                                $extraCount = $dayHarvests->count() - 1;
                            @endphp
                            <span class="mt-0.5 max-w-full truncate rounded px-1 py-0.5 text-[9px] font-bold leading-tight
                                {{ $isSelected ? 'bg-amber-400 text-[#193b32]' : 'bg-amber-100 text-amber-900' }}">
                                🌾 Panen {{ $firstPlant }}{{ $extraCount > 0 ? ' +'.$extraCount : '' }}
                            </span>
                        @elseif ($isToday)
                            <span class="mt-1 h-1.5 w-1.5 rounded-full {{ $isSelected ? 'bg-[#b9dc68]' : 'bg-emerald-600' }}"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-stone-100 pt-3 text-xs text-stone-500">
                <span>🌾 = Jadwal Panen</span>
                <a href="{{ route('jadwal-perawatan.index') }}" class="font-semibold text-emerald-800 hover:underline">Ke Hari Ini</a>
            </div>
        </div>

        {{-- KOLOM KANAN: FORM JADWAL & PENGINGAT PANEN --}}
        <div class="lg:col-span-6">
            <div class="rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-stone-200 pb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-amber-700">Jadwal & Pengingat Panen</p>
                        <h2 class="mt-0.5 text-lg font-bold text-[#193b32]">
                            {{ $selectedDate->locale('id')->translatedFormat('l, j F Y') }}
                        </h2>
                    </div>
                </div>

                {{-- Form Simpan Jadwal Panen --}}
                <form method="POST" action="{{ route('jadwal-perawatan.catatan.store') }}" class="mt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $selectedDate->toDateString() }}">

                    <div class="space-y-2.5">
                        <select name="tanaman_ditanam_id"
                                id="selectTanamanPanen"
                                onchange="
                                    const nama = this.options[this.selectedIndex].dataset.nama;
                                    const input = document.getElementById('inputCatatanPanen');
                                    if (nama) input.value = 'Jadwal panen ' + nama;
                                "
                                class="w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-[#174638] focus:outline-none">
                            <option value="" data-nama="">-- Pilih Tanaman Panen --</option>
                            @foreach ($plantings as $planting)
                                @php $namaTanaman = $planting->tanaman?->nama ?? 'Tanaman'; @endphp
                                <option value="{{ $planting->id }}" data-nama="{{ $namaTanaman }}">
                                    🌱 {{ $namaTanaman }} ({{ $planting->penanam?->nama ?? '-' }})
                                </option>
                            @endforeach
                        </select>

                        <input type="text"
                               id="inputCatatanPanen"
                               name="catatan"
                               required
                               value="Jadwal panen"
                               placeholder="Keterangan (misal: Jadwal panen Kucai)..."
                               class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm text-stone-800 focus:border-[#174638] focus:outline-none">
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                        <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-semibold text-stone-700">
                            <input type="checkbox" name="is_reminder" value="1" checked
                                   class="h-4 w-4 rounded border-stone-300 text-[#174638] focus:ring-[#174638]">
                            <span>🔔 Tampilkan pengingat panen</span>
                        </label>

                        <button type="submit"
                                class="rounded-xl bg-[#174638] px-4 py-2 text-xs font-bold text-white transition hover:bg-emerald-800">
                            + Tandai Hari Panen
                        </button>
                    </div>
                </form>

                {{-- Daftar Jadwal Panen di Tanggal Terpilih --}}
                @if (isset($selectedHarvestNotes) && $selectedHarvestNotes->isNotEmpty())
                    <div class="mt-5 space-y-2 border-t border-stone-100 pt-4">
                        @foreach ($selectedHarvestNotes as $note)
                            <div class="flex items-center justify-between gap-3 rounded-xl border p-3 {{ $note->is_done ? 'border-stone-200 bg-stone-50 opacity-75' : 'border-amber-200 bg-amber-50/60' }}">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-[#193b32]">
                                            🌾 Panen {{ $note->planting?->tanaman?->nama ?? 'Semua Tanaman' }}
                                        </span>
                                        @if ($note->is_reminder && !$note->is_done)
                                            <span class="rounded-full bg-amber-200/80 px-2 py-0.5 text-[10px] font-bold text-amber-900">🔔 Pengingat Aktif</span>
                                        @elseif ($note->is_done)
                                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">✓ Sudah Dipanen</span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 text-sm text-stone-700 {{ $note->is_done ? 'line-through' : '' }}">
                                        {{ $note->catatan }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('jadwal-perawatan.catatan.toggle', $note->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="rounded-lg border border-stone-300 bg-white px-2.5 py-1 text-xs font-semibold text-stone-700 hover:bg-emerald-50">
                                            {{ $note->is_done ? 'Batal Selesai' : 'Selesai' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('jadwal-perawatan.catatan.destroy', $note->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-5 border-t border-stone-100 pt-4 text-center text-xs text-stone-500">
                        Belum ada jadwal panen pada tanggal ini.
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection