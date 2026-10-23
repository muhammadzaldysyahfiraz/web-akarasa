@extends('layouts.app')

@section('content')
    <section class="bg-emerald-950 px-5 py-12 text-white sm:px-8 sm:py-16">
        <div class="mx-auto max-w-5xl">
            <a href="{{ route('tanaman.index') }}" class="text-sm font-semibold text-emerald-200 hover:text-white">← Kembali ke katalog</a>
            <p class="mt-8 text-sm font-bold uppercase tracking-[.16em] text-lime-300">{{ $tanaman->kategori }}</p>
            <h1 class="mt-3 font-display text-4xl font-extrabold sm:text-5xl">{{ $tanaman->nama }}</h1>
            @if ($tanaman->nama_ilmiah)<p class="mt-2 text-lg italic text-emerald-200">{{ $tanaman->nama_ilmiah }}</p>@endif
            @if ($tanaman->deskripsi)<p class="mt-5 max-w-3xl text-lg leading-8 text-emerald-100">{{ $tanaman->deskripsi }}</p>@endif
            <a href="{{ route('tanaman-ditanam.create', ['tanaman_id' => $tanaman->id]) }}" class="mt-8 inline-flex rounded-full bg-lime-300 px-6 py-3.5 font-bold text-emerald-950 hover:bg-lime-200">Tanam di kebunku</a>
        </div>
    </section>
    <section class="mx-auto grid max-w-5xl gap-6 px-5 py-12 sm:px-8 lg:grid-cols-2">
        <article class="rounded-3xl border border-stone-200 bg-white p-6 sm:p-8">
            <h2 class="font-display text-2xl font-extrabold text-emerald-950">Kebutuhan tanaman</h2>
            <dl class="mt-6 space-y-5">
                <div><dt class="text-sm font-bold text-stone-500">Cahaya</dt><dd class="mt-1 leading-6">{{ $tanaman->kebutuhan_cahaya }}</dd></div>
                <div><dt class="text-sm font-bold text-stone-500">Air</dt><dd class="mt-1 leading-6">{{ $tanaman->kebutuhan_air }}</dd></div>
                @if ($tanaman->manfaat)<div><dt class="text-sm font-bold text-stone-500">Pemanfaatan umum</dt><dd class="mt-1 leading-6">{{ $tanaman->manfaat }}</dd></div>@endif
            </dl>
            @if ($tanaman->min_hari_panen !== null && $tanaman->max_hari_panen !== null)
                <div class="mt-7 rounded-2xl bg-amber-50 p-5 text-amber-950">
                    <p class="text-sm font-bold">Perkiraan waktu panen/pemanfaatan</p>
                    <p class="mt-1 text-lg font-extrabold">{{ $tanaman->min_hari_panen }}–{{ $tanaman->max_hari_panen }} hari sejak tanam</p>
                    <p class="mt-2 text-xs leading-5">Ini rentang perkiraan, bukan tanggal atau jaminan panen yang pasti.</p>
                </div>
            @endif
        </article>
        <div class="space-y-6">
            <article class="rounded-3xl border border-stone-200 bg-white p-6 sm:p-8">
                <h2 class="font-display text-2xl font-extrabold text-emerald-950">Cara menanam</h2>
                <p class="mt-4 leading-7 text-stone-600">{{ $tanaman->panduan_penanaman ?: 'Panduan penanaman belum tersedia.' }}</p>
            </article>
            <article class="rounded-3xl border border-stone-200 bg-white p-6 sm:p-8">
                <h2 class="font-display text-2xl font-extrabold text-emerald-950">Panduan perawatan</h2>
                <p class="mt-4 leading-7 text-stone-600">{{ $tanaman->panduan_perawatan ?: 'Panduan perawatan belum tersedia.' }}</p>
                @if ($tanaman->aturanPerawatan->isNotEmpty())
                    <ul class="mt-5 space-y-3">
                        @foreach ($tanaman->aturanPerawatan->where('is_active', true) as $aturan)
                            <li class="rounded-2xl bg-stone-50 p-4">
                                <p class="font-bold text-stone-800">{{ $aturan->nama }}</p>
                                @if ($aturan->interval_days)<p class="mt-1 text-sm text-emerald-800">Setiap {{ $aturan->interval_days }} hari</p>@endif
                                @if ($aturan->instruksi)<p class="mt-2 text-sm leading-6 text-stone-600">{{ $aturan->instruksi }}</p>@endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </article>
        </div>
    </section>
@endsection
