@extends('layouts.app')

@section('content')
    <section class="bg-emerald-950 px-5 py-12 text-white sm:px-8 sm:py-16">
        <div class="mx-auto max-w-7xl">
            <p class="text-sm font-bold uppercase tracking-[.16em] text-lime-300">Pantau kebun rumah</p>
            <h1 class="mt-3 font-display text-4xl font-extrabold sm:text-5xl">Cari kebunku</h1>
            <p class="mt-4 max-w-2xl leading-7 text-emerald-100">Pilih nama penanam, lalu masukkan kode akses privat untuk melihat tanaman dan jadwal perawatannya.</p>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8">
        <form method="GET" action="{{ route('penanam.index') }}" class="mb-7 flex max-w-xl gap-3">
            <label class="sr-only" for="q">Cari nama penanam</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Cari nama penanam..." class="min-w-0 flex-1 rounded-2xl border border-stone-300 bg-white px-5 py-3 outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
            <button class="rounded-2xl bg-emerald-800 px-5 py-3 font-bold text-white hover:bg-emerald-950">Cari</button>
        </form>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($penanams as $penanam)
                <a href="{{ route('kebun.show', $penanam) }}" class="flex items-center justify-between rounded-3xl border border-stone-200 bg-white p-5 transition hover:border-emerald-300 hover:shadow-md">
                    <div>
                        <p class="font-display text-lg font-extrabold text-emerald-950">{{ $penanam->nama }}</p>
                        <p class="mt-1 text-sm text-stone-500">{{ $penanam->tanaman_ditanam_count }} tanaman tercatat</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-emerald-50 font-bold text-emerald-800" aria-hidden="true">→</span>
                </a>
            @empty
                <div class="rounded-3xl border border-dashed border-stone-300 bg-white p-10 text-center text-stone-500 sm:col-span-2 lg:col-span-3">Penanam belum ditemukan. Kamu bisa mulai dengan menambahkan tanaman.</div>
            @endforelse
        </div>
        <div class="mt-8">{{ $penanams->links() }}</div>
    </section>
@endsection
