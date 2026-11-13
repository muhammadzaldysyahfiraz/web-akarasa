@extends('layouts.app', ['title' => $tanaman->nama])

@section('content')
@php
    $manfaat = collect(preg_split('/[,;\n]+/', $tanaman->manfaat ?? ''))
        ->map(fn ($item) => trim($item))
        ->filter();
@endphp

<section class="mx-auto max-w-4xl px-4 py-6 sm:px-7">
    <a href="{{ route('tanaman.index') }}" class="text-base font-semibold text-emerald-800 underline">← Kembali ke katalog</a>

    <article class="mt-5 rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-800">{{ $tanaman->kategori }}</span>
                <h1 class="mt-4 text-3xl font-bold text-emerald-950">{{ $tanaman->nama }}</h1>
                @if ($tanaman->nama_ilmiah)
                    <p class="mt-2 text-base italic text-stone-600">{{ $tanaman->nama_ilmiah }}</p>
                @endif
            </div>
            @if (auth()->user()?->role === 'admin')
                <a href="{{ route('admin.resources.edit', ['resource' => 'tanaman', 'id' => $tanaman->id]) }}"
                    class="min-h-12 rounded-xl border border-emerald-800 px-4 py-3 text-base font-semibold text-emerald-900">Edit informasi</a>
            @endif
        </div>

        @if ($tanaman->foto_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($tanaman->foto_path) }}"
                alt="{{ $tanaman->nama }}" class="mt-6 max-h-80 w-full rounded-xl object-cover">
        @endif

        @if ($tanaman->deskripsi)
            <section class="mt-7">
                <h2 class="text-xl font-bold text-[#193b32]">Tentang tanaman</h2>
                <p class="mt-2 text-base leading-7 text-stone-700">{{ $tanaman->deskripsi }}</p>
            </section>
        @endif

        @if ($manfaat->isNotEmpty())
            <section class="mt-7">
                <h2 class="text-xl font-bold text-[#193b32]">Manfaat dan kegunaan</h2>
                <ul class="mt-2 list-inside list-disc space-y-2 text-base leading-7 text-stone-700">
                    @foreach ($manfaat as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="mt-7 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-emerald-50 p-4">
                <h2 class="text-lg font-bold text-emerald-950">Cahaya</h2>
                <p class="mt-2 text-base text-stone-700">{{ $tanaman->kebutuhan_cahaya }}</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-4">
                <h2 class="text-lg font-bold text-emerald-950">Air</h2>
                <p class="mt-2 text-base text-stone-700">{{ $tanaman->kebutuhan_air }}</p>
            </div>
        </section>

        @if ($tanaman->panduan_penanaman)
            <section class="mt-7">
                <h2 class="text-xl font-bold text-[#193b32]">Panduan menanam</h2>
                <p class="mt-2 whitespace-pre-line text-base leading-7 text-stone-700">{{ $tanaman->panduan_penanaman }}</p>
            </section>
        @endif

        @if ($tanaman->panduan_perawatan)
            <section class="mt-7">
                <h2 class="text-xl font-bold text-[#193b32]">Panduan perawatan</h2>
                <p class="mt-2 whitespace-pre-line text-base leading-7 text-stone-700">{{ $tanaman->panduan_perawatan }}</p>
            </section>
        @endif

        <section class="mt-7 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <h2 class="text-lg font-bold text-amber-950">Perkiraan panen</h2>
            @if ($tanaman->min_hari_panen !== null && $tanaman->max_hari_panen !== null)
                <p class="mt-2 text-base text-amber-950">{{ $tanaman->min_hari_panen }}–{{ $tanaman->max_hari_panen }} hari setelah tanam.</p>
            @else
                <p class="mt-2 text-base text-amber-950">Belum tersedia.</p>
            @endif
            <p class="mt-1 text-sm text-amber-900">Rentang ini merupakan perkiraan, bukan tanggal pasti.</p>
        </section>

        @if ($tanaman->aturanPerawatan->isNotEmpty())
            <section class="mt-7">
                <h2 class="text-xl font-bold text-[#193b32]">Panduan aktivitas perawatan</h2>
                <ul class="mt-3 space-y-3">
                    @foreach ($tanaman->aturanPerawatan as $aturan)
                        <li class="rounded-xl border border-stone-200 p-4">
                            <p class="text-base font-bold text-stone-800">{{ $aturan->nama }}</p>
                            @if ($aturan->instruksi)
                                <p class="mt-1 text-base text-stone-700">{{ $aturan->instruksi }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <a href="{{ route('tanaman-ditanam.create', ['tanaman_id' => $tanaman->id]) }}"
            class="mt-8 inline-flex min-h-14 w-full items-center justify-center rounded-xl bg-[#174638] px-5 py-3 text-lg font-bold text-white hover:bg-[#285e50]">
            Tambahkan tanaman ini ke Kebunku
        </a>
    </article>
</section>
@endsection
