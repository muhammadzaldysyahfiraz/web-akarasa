@extends('layouts.app')

@section('content')
    <section style="background-color: #193b32; color: #ffffff;" class="rounded-3xl px-6 py-10 shadow-sm sm:px-10 sm:py-12">
        <div class="mx-auto max-w-7xl">
            <p class="text-sm font-bold uppercase tracking-[.16em] text-[#bef264]" style="color: #bef264;">Pantau kebun rumah</p>
            <h1 class="mt-3 font-display text-4xl font-extrabold sm:text-5xl">Cari kebunku</h1>
            <p class="mt-4 max-w-2xl leading-7 text-[#d1fae5]" style="color: #d1fae5;">Pilih nama penanam untuk melihat daftar tanaman dan jadwal perawatannya.</p>
        </div>

        <form method="GET" action="{{ route('penanam.index') }}" class="mt-6 flex max-w-2xl gap-3">
            <label class="sr-only" for="q">Cari nama penanam</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Cari nama penanam..." class="min-w-0 flex-1 rounded-2xl border border-stone-300 bg-white px-5 py-3 outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
            <button class="rounded-2xl bg-[#17483B] px-5 py-3 font-bold text-white hover:bg-[#10372D]">Cari</button>
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
