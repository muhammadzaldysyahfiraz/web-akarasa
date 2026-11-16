@extends('layouts.app', ['title' => 'Tanaman TOGA'])

@section('content')

<main class="mx-auto max-w-[1400px] px-5 py-7 sm:px-8 lg:px-10 lg:py-8">
    <div class="mb-6">
        <p class="text-sm font-bold uppercase tracking-wide text-[#64866C]">
            Katalog Tanaman
        </p>

        <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#25463B]">
            Tanaman Obat Keluarga
        </h1>

        <p class="mt-2 text-base text-[#64716c]">
            Pilih tanaman untuk membaca informasi dan panduan dari pengelola.
        </p>
    </div>

    <section>
        <livewire:tanaman-katalog />
    </section>

</main>

@endsection