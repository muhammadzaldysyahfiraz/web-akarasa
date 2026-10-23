@extends('admin.layout', ['title' => 'Ringkasan'])

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="text-sm font-bold uppercase tracking-[.16em] text-emerald-700">Ringkasan kebun</p><h1 class="mt-2 font-display text-3xl font-extrabold text-emerald-950">Dashboard admin</h1></div>
        <p class="text-sm text-stone-500">{{ now()->translatedFormat('l, j F Y') }}</p>
    </div>
    <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([['Tanaman', $counts['tanaman'], 'tanaman'], ['Penanam', $counts['penanam'], 'penamans'], ['Tanaman ditanam', $counts['ditanam'], null], ['Jadwal pending', $counts['jadwal'], null]] as [$label, $value, $link])
            <div class="rounded-2xl border border-stone-200 bg-white p-5">
                <p class="text-sm font-semibold text-stone-500">{{ $label }}</p><p class="mt-2 font-display text-3xl font-extrabold text-emerald-950">{{ $value }}</p>
                @if ($link)<a href="{{ route('admin.resources.index', $link) }}" class="mt-3 inline-block text-sm font-bold text-emerald-800">Kelola →</a>@endif
            </div>
        @endforeach
    </div>
    <section class="mt-8 rounded-2xl border border-stone-200 bg-white p-5 sm:p-7">
        <div class="flex items-center justify-between"><div><p class="text-sm font-bold uppercase tracking-wider text-emerald-700">Monitoring</p><h2 class="mt-1 font-display text-xl font-extrabold text-emerald-950">Jadwal perawatan mendatang</h2></div><span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-800">{{ $counts['aturan'] }} aturan</span></div>
        @if ($upcoming->isEmpty())
            <p class="mt-5 rounded-xl bg-stone-50 p-4 text-sm text-stone-500">Belum ada jadwal perawatan.</p>
        @else
            <div class="mt-5 divide-y divide-stone-100">
                @foreach ($upcoming as $schedule)
                    <div class="flex flex-wrap justify-between gap-2 py-4">
                        <div><p class="font-bold">{{ $schedule->aturanPerawatan->nama }} · {{ $schedule->tanamanDitanam->tanaman->nama }}</p><p class="mt-1 text-sm text-stone-500">Penanam: {{ $schedule->tanamanDitanam->penanam->nama }}</p></div>
                        <p class="text-sm font-bold text-emerald-800">{{ $schedule->perawatan_at->translatedFormat('j M Y') }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
