@extends('layouts.app')

@section('content')
    <section class="bg-emerald-950 px-5 py-12 text-white sm:px-8 sm:py-16">
        <div class="mx-auto max-w-7xl">
            <p class="text-sm font-bold uppercase tracking-[.16em] text-lime-300">Kenali tanaman TOGA</p>
            <h1 class="mt-3 font-display text-4xl font-extrabold sm:text-5xl">Katalog tanaman</h1>
            <p class="mt-4 max-w-2xl leading-7 text-emerald-100">Temukan panduan dasar, kebutuhan perawatan, dan perkiraan waktu pemanfaatan untuk tanaman di kebun rumah.</p>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8">
        <livewire:tanaman-katalog />
    </section>
@endsection
