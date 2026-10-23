@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden bg-emerald-950 text-white">
        <div class="absolute -right-24 -top-32 h-96 w-96 rounded-full bg-emerald-800/50 blur-3xl"></div>
        <div class="relative mx-auto grid max-w-7xl gap-12 px-5 py-16 sm:px-8 sm:py-24 lg:grid-cols-[1.15fr_.85fr] lg:items-center">
            <div>
                <p class="mb-5 inline-flex rounded-full border border-emerald-700 bg-emerald-900/70 px-4 py-2 text-xs font-bold uppercase tracking-[.18em] text-emerald-200">Rawat dengan lebih teratur</p>
                <h1 class="max-w-3xl font-display text-4xl font-extrabold leading-tight tracking-tight sm:text-6xl">Kebun kecilmu, <span class="text-lime-300">tumbuh lebih terarah.</span></h1>
                <p class="mt-6 max-w-xl text-lg leading-8 text-emerald-100">Panduan sederhana, jadwal perawatan, dan perkiraan panen untuk tanaman obat yang kamu tanam di rumah.</p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('tanaman.index') }}" class="rounded-full bg-lime-300 px-6 py-3.5 font-bold text-emerald-950 hover:bg-lime-200">Jelajahi tanaman</a>
                    <a href="{{ route('tanaman-ditanam.create') }}" class="rounded-full border border-emerald-600 px-6 py-3.5 font-bold text-white hover:bg-emerald-900">Catat tanaman saya</a>
                </div>
            </div>
            <div class="rounded-[2rem] border border-emerald-800 bg-emerald-900/60 p-6 shadow-2xl sm:p-8">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm text-emerald-200">Mulai dari langkah kecil</p><p class="mt-1 font-display text-2xl font-bold">Hari ini di kebun</p></div>
                    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-lime-300 text-2xl text-emerald-950" aria-hidden="true">✦</span>
                </div>
                <div class="mt-7 space-y-3">
                    <div class="rounded-2xl bg-white p-4 text-stone-800">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">01 · Kenali</p>
                        <p class="mt-1 font-bold">Pilih TOGA yang cocok untuk rumahmu</p>
                    </div>
                    <div class="rounded-2xl bg-white p-4 text-stone-800">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">02 · Rawat</p>
                        <p class="mt-1 font-bold">Ikuti jadwal sesuai kebutuhan tanaman</p>
                    </div>
                    <div class="rounded-2xl bg-white p-4 text-stone-800">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">03 · Pantau</p>
                        <p class="mt-1 font-bold">Lihat perkembangan dan perkiraan panen</p>
                    </div>
                </div>
                <p class="mt-5 text-xs leading-5 text-emerald-200">Estimasi panen adalah perkiraan, bukan jaminan waktu panen.</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div><p class="text-sm font-bold uppercase tracking-[.16em] text-emerald-700">Mulai mengenal</p><h2 class="mt-2 font-display text-3xl font-extrabold text-emerald-950">Tanaman pilihan</h2></div>
            <a href="{{ route('tanaman.index') }}" class="font-bold text-emerald-800 hover:text-emerald-950">Lihat semua tanaman →</a>
        </div>
        <livewire:tanaman-katalog :featured="true" />
    </section>
@endsection
