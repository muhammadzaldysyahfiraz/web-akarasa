@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-xl px-5 py-16 sm:px-8">
        <div class="rounded-[2rem] border border-stone-200 bg-white p-7 shadow-sm sm:p-10">
            <div class="grid h-14 w-14 place-items-center rounded-2xl bg-emerald-100 text-2xl text-emerald-800" aria-hidden="true">⌑</div>
            <p class="mt-6 text-sm font-bold uppercase tracking-[.16em] text-emerald-700">Kebun pribadi</p>
            <h1 class="mt-2 font-display text-3xl font-extrabold text-emerald-950">Kebun {{ $penanam->nama }}</h1>
            <p class="mt-3 leading-6 text-stone-600">Masukkan kode akses yang dibuat saat kebun didaftarkan untuk melihat dan mengelola jadwal perawatan.</p>
            @if ($errors->any())
                <div role="alert" class="mt-5 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('kebun.access', $penanam) }}" class="mt-6 space-y-4">
                @csrf
                <label class="block text-sm font-bold text-stone-700">Kode akses
                    <input name="access_code" type="password" required autocomplete="current-password" class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                </label>
                <button class="w-full rounded-xl bg-emerald-800 px-5 py-3 font-bold text-white hover:bg-emerald-950">Buka kebun</button>
            </form>
        </div>
    </section>
@endsection
