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
    $imageUrl = null;

    if ($filename) {
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

    $aturanAktif = $tanaman->aturanPerawatan->where('is_active', true);

    $minPanen = $tanaman->min_hari_panen;
    $maxPanen = $tanaman->max_hari_panen;

    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
@endphp

<style>
    .detail-page {
        width: 100%;
        min-height: 100vh;
        background: #f3f5f0;
        padding: 0 22px 24px;
        box-sizing: border-box;
    }

    .detail-container {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
    }

    .detail-crumbs {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 0 -22px 20px;
        padding: 14px 22px;
        border-bottom: 1px solid #e2e8df;
        background: #fff;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.63fr) minmax(270px, 1fr);
        align-items: start;
        gap: 16px;
        width: 100%;
    }

    .detail-left,
    .detail-right {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .detail-main-card,
    .detail-benefit-card,
    .detail-schedule-card {
        background: #fff;
        border-radius: 11px;
        padding: 16px;
        box-shadow: 0 5px 20px rgba(30,60,40,.06);
        box-sizing: border-box;
    }

    .detail-main-card {
        min-height: 414px;
    }

    .detail-photo {
        width: 100%;
        height: 188px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
    }

    .detail-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 14px;
    }

    .detail-info {
        background: #f2f5ef;
        border-radius: 7px;
        padding: 10px;
        min-width: 0;
    }

    .detail-harvest-card {
        background: #174638;
        color: white;
        border-radius: 10px;
        padding: 16px;
        min-height: 110px;
        box-sizing: border-box;
    }

    .detail-harvest-content {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-top: 9px;
    }

    .detail-harvest-circle {
        width: 54px;
        height: 54px;
        flex-shrink: 0;
        border: 5px solid #b5d75d;
        border-radius: 50%;
        box-sizing: border-box;
    }

    .detail-care-row {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 8px;
        background: #f4f6f1;
        border: 1px solid #e5ebe2;
        border-radius: 8px;
        padding: 9px 10px;
        min-height: 36px;
        box-sizing: border-box;
    }

    .detail-care-check {
        display: grid;
        width: 13px;
        height: 13px;
        flex: 0 0 13px;
        place-items: center;
        border: 1px solid #357458;
        border-radius: 3px;
        color: #357458;
        font-size: 9px;
        line-height: 1;
    }

    .detail-benefit-tag {
        display: inline-block;
        background: #f2f5ef;
        border: 1px solid #e5ebe2;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 9px;
        color: #193b32;
    }

    @media (max-width: 700px) {
        .detail-page {
            padding: 0 16px 20px;
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

        .detail-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
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
            grid-template-columns: 1fr;
        }

        .detail-photo {
            height: 165px;
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

                {{-- DETAIL TANAMAN --}}
                <article class="detail-main-card">

                    <div class="mb-4 flex items-start justify-between gap-3">
                        <div>
                            <h1 class="text-2xl font-bold leading-tight text-[#193b32]">
                                {{ $tanaman->nama }}
                            </h1>

                            <p class="mt-1 text-[11px] italic text-emerald-700">
                                {{ $tanaman->nama_ilmiah ?: 'Nama ilmiah belum tersedia' }}
                            </p>
                        </div>

                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-[#eff6e5] px-2.5 py-1 text-[9px] font-semibold text-emerald-800">
                            <span aria-hidden="true">●</span> TOGA Aktif
                        </span>
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
                            <p class="text-[8px] uppercase tracking-wide text-gray-500">
                                KATEGORI
                            </p>
                            <p class="mt-1 text-[10px] font-bold text-[#193b32]">
                                {{ $tanaman->kategori }}
                            </p>
                        </div>

                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="mt-4">
                        <h2 class="text-[10px] font-bold text-[#193b32]">
                            Deskripsi Tanaman
                        </h2>

                        @if ($tanaman->deskripsi)
                            <p class="mt-2 text-[10px] leading-5 text-gray-600">
                                {{ $tanaman->deskripsi }}
                            </p>
                        @endif
                    </div>

                </article>

                {{-- MANFAAT --}}
                <article class="detail-benefit-card">
                    <h2 class="text-xs font-bold text-[#193b32]">
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

            </div>

            {{-- KOLOM KANAN --}}
            <div class="detail-right">

                {{-- ESTIMASI PANEN --}}
                <article class="detail-harvest-card">
                    <h2 class="text-[11px] font-bold">Estimasi Panen</h2>

                    <div class="detail-harvest-content">
                        <div class="detail-harvest-circle" aria-hidden="true"></div>

                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold">{{ $tanaman->nama }}</p>
                            @if ($minPanen !== null && $maxPanen !== null)
                                <p class="mt-1 text-[9px] leading-[1.4] text-emerald-100">
                                    Panen pertama sekitar {{ $minPanen }}–{{ $maxPanen }} hari sejak tanam.
                                </p>
                            @elseif ($minPanen !== null)
                                <p class="mt-1 text-[9px] leading-[1.4] text-emerald-100">
                                    Perkiraan panen mulai {{ $minPanen }} hari sejak tanam.
                                </p>
                            @else
                                <p class="mt-1 text-[9px] leading-[1.4] text-emerald-100">
                                    Estimasi panen belum tersedia.
                                </p>
                            @endif
                        </div>
                    </div>
                </article>

                {{-- JADWAL PERAWATAN --}}
                <article class="detail-schedule-card">
                    <h2 class="text-xs font-bold text-[#193b32]">Jadwal Perawatan</h2>

                    <div class="mt-3 space-y-2">
                        @forelse ($aturanAktif as $aturan)
                            <div class="detail-care-row">
                                <span class="detail-care-check" aria-hidden="true"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[9px] font-semibold text-[#193b32]">{{ $aturan->nama }}</p>
                                    @if ($aturan->instruksi)
                                        <p class="mt-1 text-[8px] leading-4 text-gray-500">{{ $aturan->instruksi }}</p>
                                    @endif
                                </div>
                                <div class="flex shrink-0 flex-col items-end gap-1">
                                    <span class="text-[8px] font-semibold text-gray-500">
                                        @if ($aturan->interval_days)
                                            Setiap {{ $aturan->interval_days }} hari
                                        @else
                                            Satu kali
                                        @endif
                                    </span>
                                    @if ($isAdmin)
                                        <a href="{{ route('admin.resources.edit', [
                                            'resource' => 'aturan-perawatan',
                                            'id' => $aturan->id
                                        ]) }}"
                                           class="rounded bg-[#174638] px-2.5 py-1 text-[8px] font-semibold text-white hover:bg-[#285e50]">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="rounded-lg bg-[#f4f6f1] p-3 text-[10px] leading-5 text-gray-500">
                                Jadwal perawatan belum tersedia.
                            </p>
                        @endforelse
                    </div>

                    @if ($isAdmin)
                        <a href="{{ route('admin.resources.create', 'aturan-perawatan') }}"
                           class="mt-3 inline-flex text-[9px] font-semibold text-emerald-800 hover:text-emerald-950">
                            + Tambah aturan perawatan
                        </a>
                    @endif
                </article>

            </div>
        </div>
    </div>
</div>

@endsection