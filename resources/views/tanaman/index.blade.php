@extends('layouts.app', ['title' => 'Tanaman TOGA'])

@section('content')

<main class="mx-auto max-w-[1400px] px-5 py-7 sm:px-8 lg:px-10 lg:py-8">

    {{-- JUDUL HALAMAN --}}
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#64866C]">
                Katalog Akarasa
            </p>

            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-[#25463B] sm:text-[28px]">
                TANAMAN OBAT KELUARGA
            </h1>

            <p class="mt-2 text-[11px] text-[#849187]">
                Kenali berbagai tanaman TOGA beserta manfaat dan cara perawatannya.
            </p>
        </div>

        {{-- TAMBAH TANAMAN KE KEBUN WARGA --}}
        @if (auth()->user()->role === 'user' && Route::has('tanaman-ditanam.create'))
            <a
                href="{{ route('tanaman-ditanam.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-[#17483B] px-4 py-3 text-[10px] font-bold text-white transition hover:bg-[#10372D]"
            >
                <span class="text-base leading-none">+</span>
                Tanam di Kebun Saya
            </a>
        @endif
    </div>

    {{-- KATALOG TANAMAN --}}
    <section>
        <livewire:tanaman-katalog />
    </section>

</main>

@endsection