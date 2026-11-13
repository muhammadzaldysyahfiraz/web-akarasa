@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-7 sm:px-7">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-emerald-700">Kebunku</p>
                <h1 class="mt-2 text-3xl font-bold text-emerald-950">Pilih nama penanam</h1>
                <p class="mt-2 text-base text-stone-600">Semua orang dapat melihat kebun dan membantu mencatat perawatan tanaman.</p>
            </div>
            <a href="{{ route('tanaman-ditanam.create') }}" class="inline-flex min-h-14 items-center rounded-xl bg-emerald-800 px-5 py-3 text-base font-bold text-white">+ Tambah tanaman</a>
        </div>

        <form method="GET" action="{{ route('penanam.index') }}" class="mt-6 flex max-w-2xl gap-3">
            <label class="sr-only" for="q">Cari nama penanam</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Ketik nama penanam"
                class="min-h-14 min-w-0 flex-1 rounded-xl border border-stone-300 bg-white px-4 py-3 text-base">
            <button class="min-h-14 rounded-xl bg-emerald-800 px-5 py-3 text-base font-bold text-white">Cari</button>
        </form>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($penanams as $penanam)
                <a href="{{ route('kebun.show', $penanam) }}" class="flex min-h-24 items-center justify-between rounded-2xl border border-stone-200 bg-white p-5 transition hover:border-emerald-300 hover:shadow-md">
                    <div>
                        <p class="text-xl font-bold text-emerald-950">{{ $penanam->nama }}</p>
                        <p class="mt-1 text-base text-stone-600">{{ $penanam->tanaman_ditanam_count }} tanaman</p>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-emerald-50 text-2xl font-bold text-emerald-800" aria-hidden="true">→</span>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-10 text-center text-base text-stone-600 sm:col-span-2 lg:col-span-3">
                    Belum ada penanam. Mulai dengan menambahkan tanaman dan nama penanam.
                </div>
            @endforelse
        </div>
        <div class="mt-7">{{ $penanams->links() }}</div>
    </section>
@endsection
