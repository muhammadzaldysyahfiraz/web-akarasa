@extends('layouts.app', ['title' => $tanaman->nama])

@section('content')

@php
    $imageFiles = [
        'daun mint' => 'daun mint.jpg',
        'daun pandan' => 'daun pandan.jpg',
        'jahe merah' => 'jahe merah.jpg',
        'kemangi' => 'kemangi.jpg',
        'kencur' => 'kencur.jpg',
        'kumis kucing' => 'kumis kucing.jpg',
        'kunyit' => 'kunyit.jpg',
        'lengkuas' => 'lengkuas.jpg',
        'lidah buaya' => 'lidah buaya.jpg',
        'rosella' => 'rosella.jpg',
        'sambiloto' => 'sambiloto.jpg',
        'seledri' => 'seledri.jpg',
        'serai' => 'serai.jpg',
        'temulawak' => 'temulawak.jpg',
    ];

    $namaTanaman = mb_strtolower(trim($tanaman->nama));
    $filename = $imageFiles[$namaTanaman] ?? null;
    $imageUrl = $tanaman->foto_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($tanaman->foto_path)
        : null;

    if (! $imageUrl && $filename) {
        foreach (['images/tanaman', 'tanaman'] as $folder) {
            if (file_exists(public_path($folder . '/' . $filename))) {
                $imageUrl = asset($folder . '/' . rawurlencode($filename));
                break;
            }
        }
    }

    $manfaatPerTanaman = [
        'daun mint' => ['Mengusir Bau Mulut', 'Menyegarkan Napas', 'Membantu Pencernaan', 'Aromaterapi Alami'],
        'daun pandan' => ['Pewangi Alami', 'Pewarna Makanan', 'Campuran Minuman', 'Bahan Masakan'],
        'jahe merah' => ['Minuman Herbal', 'Penghangat Tubuh', 'Bumbu Masakan', 'Penggunaan Tradisional untuk Pencernaan'],
        'kemangi' => ['Lalapan', 'Penyedap Masakan', 'Aroma Alami', 'Pelengkap Makanan'],
        'kencur' => ['Bahan Beras Kencur', 'Bumbu Masakan', 'Minuman Tradisional', 'Ramuan Tradisional'],
        'kumis kucing' => ['Teh Herbal Tradisional', 'Tanaman Obat Keluarga', 'Tanaman Pekarangan'],
        'kunyit' => ['Bumbu Masakan', 'Pewarna Alami', 'Minuman Kunyit', 'Bahan Jamu Tradisional'],
        'lengkuas' => ['Bumbu Masakan', 'Penambah Aroma', 'Bahan Ramuan Tradisional'],
        'lidah buaya' => ['Perawatan Rambut', 'Perawatan Kulit', 'Bahan Olahan Minuman', 'Tanaman Hias'],
        'rosella' => ['Teh Herbal', 'Minuman Segar', 'Pewarna Alami', 'Bahan Olahan Selai'],
        'sambiloto' => ['Ramuan Herbal Tradisional', 'Tanaman Obat Keluarga', 'Bahan Jamu Tradisional'],
        'seledri' => ['Bumbu Masakan', 'Pelengkap Makanan', 'Jus Sayuran', 'Tanaman Herbal'],
        'serai' => ['Mengusir Nyamuk', 'Meredakan Demam secara Tradisional', 'Membantu Pencernaan', 'Aromaterapi Alami'],
        'temulawak' => ['Minuman Herbal', 'Bahan Jamu', 'Bumbu Masakan', 'Penggunaan Tradisional untuk Pencernaan'],
    ];

    $manfaatList = $manfaatPerTanaman[$namaTanaman]
        ?? array_filter(array_map('trim', preg_split('/[,;\n]+/', $tanaman->manfaat ?? '')));

    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $plantings = $plantings ?? collect();
    $plantingSchedules = $plantings->flatMap(
        fn ($planting) => $planting->jadwalPerawatan
    );
    $dueSchedules = $plantingSchedules->filter(
        fn ($schedule) => $schedule->perawatan_at->lte(today())
    );
    $completedDueSchedules = $dueSchedules->where('status', 'completed')->count();
    $careCompletion = $dueSchedules->isNotEmpty()
        ? (int) round($completedDueSchedules / $dueSchedules->count() * 100)
        : null;
    $attentionSchedules = $plantingSchedules
        ->filter(fn ($schedule) => in_array($schedule->status, ['pending', 'missed'], true))
        ->sortBy(fn ($schedule) => [
            $schedule->status === 'missed' || $schedule->perawatan_at->lt(today()) ? 0 : 1,
            $schedule->perawatan_at->toDateString(),
        ])
        ->unique(fn ($schedule) => $schedule->tanaman_ditanam_id.':'.$schedule->aturan_perawatan_id)
        ->take(3);
    $activityHistory = $plantingSchedules
        ->filter(fn ($schedule) => $schedule->status === 'completed' && $schedule->aktivitasPerawatan)
        ->sortByDesc(fn ($schedule) => $schedule->aktivitasPerawatan->performed_at)
        ->take(8);
    $hasOverdueCare = $plantingSchedules->contains(
        fn ($schedule) => $schedule->status === 'missed'
            || ($schedule->status === 'pending' && $schedule->perawatan_at->lt(today()))
    );
    $needsAttentionToday = $plantingSchedules->contains(
        fn ($schedule) => $schedule->status === 'pending'
            && $schedule->perawatan_at->isToday()
    );
    $growerNames = $plantings->pluck('penanam.nama')->filter()->unique()->values();
    $plantingLocations = $plantings->pluck('location')->filter()->unique()->values();
@endphp

<style>
    .detail-page {
        width: 100%;
        min-height: 100vh;
        background: #f3f5f0;
        padding: 0 20px 28px;
        box-sizing: border-box;
    }

    .detail-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .detail-crumbs {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 0 -20px 18px;
        padding: 13px 20px;
        border-bottom: 1px solid #e2e8df;
        background: #fff;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.62fr) minmax(250px, 1fr);
        align-items: start;
        gap: 14px;
        width: 100%;
    }

    .detail-left,
    .detail-right {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .detail-main-card,
    .detail-benefit-card,
    .detail-card {
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 5px 20px rgba(30, 60, 40, .06);
        box-sizing: border-box;
    }

    .detail-photo {
        width: 100%;
        height: 170px;
        object-fit: cover;
        border-radius: 9px;
        display: block;
    }

    .detail-info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-top: 12px;
    }

    .detail-info {
        background: #f2f5ef;
        border-radius: 6px;
        padding: 9px;
        min-width: 0;
    }

    .detail-health-card {
        background: #174638;
        color: #fff;
        border-radius: 11px;
        padding: 14px;
        box-sizing: border-box;
    }

    .detail-health-content {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 10px;
    }

    .detail-health-ring {
        display: grid;
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        place-items: center;
        border-radius: 50%;
        background: conic-gradient(#f3aa32 calc(var(--progress) * 1%), rgba(255, 255, 255, .18) 0);
    }

    .detail-health-ring span {
        display: grid;
        width: 39px;
        height: 39px;
        place-items: center;
        border-radius: 50%;
        background: #174638;
        font-size: 11px;
        font-weight: 700;
    }

    .detail-schedule-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border: 1px solid #e8ece6;
        border-radius: 7px;
        padding: 9px;
        box-sizing: border-box;
    }

    .detail-schedule-row.is-urgent {
        border-color: #ef8f7e;
        background: #fff8f6;
    }

    .detail-timeline {
        position: relative;
        margin-top: 13px;
        padding-left: 3px;
    }

    .detail-history-card {
        min-height: 320px;
    }

    .detail-timeline-item {
        position: relative;
        padding: 0 0 16px 17px;
        border-left: 1px solid #dfe8df;
    }

    .detail-timeline-item:last-child {
        padding-bottom: 0;
        border-left-color: transparent;
    }

    .detail-timeline-dot {
        position: absolute;
        top: 3px;
        left: -4px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #357458;
        box-shadow: 0 0 0 3px #fff;
    }

    .detail-benefit-tag {
        display: inline-block;
        background: #f2f5ef;
        border: 1px solid #e5ebe2;
        border-radius: 6px;
        padding: 5px 9px;
        font-size: 9px;
        color: #193b32;
    }

    .detail-empty {
        border-radius: 8px;
        background: #f4f6f1;
        padding: 11px;
        color: #6b7280;
        font-size: 10px;
        line-height: 1.5;
    }

    .detail-schedule-icon {
        display: grid;
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        place-items: center;
        border: 1px solid currentColor;
        border-radius: 3px;
        font-size: 9px;
        line-height: 1;
    }

    @media (max-width: 700px) {
        .detail-page {
            padding: 0 16px 22px;
        }

        .detail-crumbs {
            margin-right: -16px;
            margin-left: -16px;
            padding-right: 16px;
            padding-left: 16px;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .detail-right {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: start;
        }

        .detail-history-card {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 420px) {
        .detail-page {
            padding-right: 12px;
            padding-left: 12px;
        }

        .detail-crumbs {
            margin-right: -12px;
            margin-left: -12px;
            padding-right: 12px;
            padding-left: 12px;
        }

        .detail-info-grid {
            gap: 6px;
        }

        .detail-info {
            padding: 7px;
        }

        .detail-photo {
            height: 155px;
        }

        .detail-right {
            grid-template-columns: 1fr;
        }

        .detail-history-card {
            grid-column: auto;
        }
    }
</style>

<div class="detail-page">
    <div class="detail-container">

        {{-- BREADCRUMB --}}
        <div class="detail-crumbs">
            <div class="flex flex-wrap items-center gap-2 text-[11px] text-gray-500">
                <a href="{{ route('tanaman.index') }}" class="hover:text-emerald-800">
                    Tanaman TOGA
                </a>
                <span>›</span>
                <span class="font-semibold text-[#193b32]">
                    {{ $tanaman->nama }}
                </span>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @if ($isAdmin)
                    <a href="{{ route('admin.resources.edit', [
                        'resource' => 'tanaman',
                        'id' => $tanaman->id
                    ]) }}"
                       class="rounded-lg border border-[#dce5d8] bg-white px-3 py-2 text-[10px] font-semibold text-[#174638] hover:bg-[#f3f6f0]">
                        Edit
                    </a>
                @endif

                <a href="{{ route('tanaman.index') }}"
                   class="rounded-lg bg-[#f3f6f0] px-3 py-2 text-[10px] font-semibold text-[#193b32] hover:bg-[#e8eee3]">
                    ← Kembali
                </a>
            </div>
        </div>

        <div class="detail-grid">

            {{-- KOLOM KIRI --}}
            <div class="detail-left">
                <article class="detail-main-card">

                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <h1 class="text-[22px] font-bold leading-tight text-[#193b32]">
                                {{ $tanaman->nama }}
                            </h1>

                            <p class="mt-1 text-[10px] italic text-emerald-700">
                                {{ $tanaman->nama_ilmiah ?: 'Nama ilmiah belum tersedia' }}
                            </p>
                        </div>

                        @if ($plantings->isEmpty())
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-[9px] font-semibold text-gray-600">
                                Belum Ditanam
                            </span>
                        @elseif ($hasOverdueCare || $needsAttentionToday)
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-[9px] font-semibold text-amber-700">
                                <span aria-hidden="true">●</span> Perlu Perhatian
                            </span>
                        @else
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-[#eff6e5] px-2.5 py-1 text-[9px] font-semibold text-emerald-800">
                                <span aria-hidden="true">●</span> Terpantau
                            </span>
                        @endif
                    </div>

                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}"
                             alt="{{ $tanaman->nama }}"
                             class="detail-photo">
                    @else
                        <div class="detail-photo flex items-center justify-center bg-[#e7eee3] text-5xl">
                            🌱
                        </div>
                    @endif

                    {{-- RINGKASAN TANAMAN --}}
                    <div class="detail-info-grid">
                        <div class="detail-info">
                            <p class="text-[8px] font-semibold uppercase tracking-wide text-gray-400">
                                KATEGORI
                            </p>
                            <p class="mt-1 truncate text-[9px] font-bold text-[#193b32]">
                                {{ $tanaman->kategori }}
                            </p>
                        </div>

                        <div class="detail-info">
                            <p class="text-[8px] font-semibold uppercase tracking-wide text-gray-400">
                                PEMILIK KEBUN
                            </p>
                            <p class="mt-1 truncate text-[9px] font-bold text-[#193b32]">
                                {{ $growerNames->isNotEmpty() ? $growerNames->join(', ') : ($tanaman->nama_penanam ?: 'Belum tersedia') }}
                            </p>
                        </div>

                        <div class="detail-info">
                            <p class="text-[8px] font-semibold uppercase tracking-wide text-gray-400">
                                LOKASI
                            </p>
                            <p class="mt-1 truncate text-[9px] font-bold text-[#193b32]">
                                {{ $plantingLocations->isNotEmpty() ? $plantingLocations->join(', ') : 'Belum tersedia' }}
                            </p>
                        </div>
                    </div>
                </article>

                <article class="detail-benefit-card">
                    <h2 class="text-[11px] font-bold text-[#193b32]">
                        Manfaat & Kegunaan
                    </h2>

                    @if (count($manfaatList))
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach ($manfaatList as $manfaat)
                                <span class="detail-benefit-tag">
                                    {{ $manfaat }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-3 text-[10px] text-gray-500">
                            Manfaat belum tersedia.
                        </p>
                    @endif
                </article>

                @if ($tanaman->deskripsi)
                    <article class="detail-card">
                        <h2 class="text-[11px] font-bold text-[#193b32]">Tentang Tanaman</h2>
                        <p class="mt-2 text-[10px] leading-5 text-gray-600">{{ $tanaman->deskripsi }}</p>
                    </article>
                @endif
            </div>

            <div class="detail-right">
                <article class="detail-health-card">
                    <h2 class="text-[11px] font-bold">Kondisi Kesehatan</h2>

                    <div class="detail-health-content">
                        <div
                            class="detail-health-ring"
                            style="--progress: {{ $careCompletion ?? 0 }}"
                            role="img"
                            aria-label="{{ $careCompletion === null ? 'Belum ada jadwal perawatan yang jatuh tempo' : $careCompletion.' persen jadwal perawatan selesai' }}"
                        >
                            <span>{{ $careCompletion === null ? '—' : $careCompletion.'%' }}</span>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[9px] font-semibold">
                                {{ $hasOverdueCare ? 'Perlu Perhatian' : ($needsAttentionToday ? 'Perlu Perawatan Hari Ini' : 'Jadwal Terpantau') }}
                            </p>
                            <p class="mt-1 text-[8px] leading-4 text-emerald-100">
                                @if ($careCompletion === null)
                                    Belum ada jadwal yang jatuh tempo untuk dinilai.
                                @else
                                    {{ $completedDueSchedules }} dari {{ $dueSchedules->count() }} jadwal jatuh tempo diselesaikan.
                                @endif
                            </p>
                        </div>
                    </div>
                </article>

                <article class="detail-card">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-[11px] font-bold text-[#193b32]">Jadwal Perawatan</h2>
                        <a href="{{ route('jadwal-perawatan.index') }}"
                           class="text-[8px] font-semibold text-emerald-800 hover:underline">
                            Lihat semua
                        </a>
                    </div>

                    <div class="mt-3 space-y-2">
                        @forelse ($attentionSchedules as $schedule)
                            @php
                                $isUrgent = $schedule->status === 'missed'
                                    || $schedule->perawatan_at->lte(today());
                            @endphp

                            <div class="detail-schedule-row {{ $isUrgent ? 'is-urgent' : '' }}">
                                <span class="detail-schedule-icon {{ $isUrgent ? 'text-red-600' : 'text-emerald-700' }}" aria-hidden="true">
                                    {{ $isUrgent ? '!' : '□' }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[9px] font-semibold text-[#193b32]">
                                        {{ $schedule->aturanPerawatan->nama ?? 'Perawatan tanaman' }}
                                    </p>
                                    <p class="mt-1 text-[8px] text-gray-500">
                                        {{ $schedule->perawatan_at->translatedFormat('j M Y') }}
                                    </p>
                                </div>

                                <span class="shrink-0 text-[8px] font-bold {{ $isUrgent ? 'text-red-600' : 'text-emerald-700' }}">
                                    {{ $schedule->status === 'missed' || $schedule->perawatan_at->lt(today()) ? 'Terlewat' : ($schedule->perawatan_at->isToday() ? 'Harus Segera' : 'Akan Datang') }}
                                </span>
                            </div>
                        @empty
                            <p class="detail-empty">
                                @if ($plantings->isEmpty())
                                    Tanaman ini belum tercatat ditanam.
                                @else
                                    Tidak ada jadwal perawatan berikutnya.
                                @endif
                            </p>
                        @endforelse
                    </div>
                    @if ($isAdmin)
                        <a href="{{ route('admin.resources.create', 'aturan-perawatan') }}"
                           class="mt-3 inline-flex text-[8px] font-semibold text-emerald-800 hover:underline">
                            + Tambah aturan perawatan
                        </a>
                    @endif
                </article>

                <article class="detail-card detail-history-card">
                    <h2 class="text-[11px] font-bold text-[#193b32]">Riwayat Aktivitas</h2>

                    @if ($activityHistory->isNotEmpty())
                        <div class="detail-timeline">
                            @foreach ($activityHistory as $schedule)
                                @php
                                    $activity = $schedule->aktivitasPerawatan;
                                    $activityDate = $activity->performed_at;
                                @endphp

                                <div class="detail-timeline-item">
                                    <span class="detail-timeline-dot" aria-hidden="true"></span>
                                    <p class="text-[8px] font-medium text-gray-500">
                                        {{ $activityDate->translatedFormat('l, j F Y') }}
                                    </p>
                                    <p class="mt-1 text-[9px] font-bold text-[#193b32]">
                                        {{ $schedule->aturanPerawatan->nama ?? 'Perawatan tanaman' }}
                                    </p>
                                    <p class="mt-0.5 text-[8px] leading-4 text-gray-500">
                                        {{ $activity->catatan ?: ($schedule->aturanPerawatan->instruksi ?? 'Aktivitas perawatan telah dicatat.') }}
                                    </p>
                                    @if ($activity->nama_pelaksana)
                                        <p class="mt-0.5 text-[8px] text-gray-400">
                                            Oleh {{ $activity->nama_pelaksana }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="detail-empty mt-3">
                            Belum ada aktivitas yang dicentang selesai pada jadwal perawatan.
                        </p>
                    @endif
                </article>
            </div>
        </div>
    </div>
</div>

@endsection