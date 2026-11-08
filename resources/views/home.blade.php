@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-4">

    <div>
        <h1 class="font-display text-2xl font-medium tracking-wide text-[#193b32] sm:text-[27px]">
            TANAMAN OBAT KELUARGA
        </h1>

        <p class="mt-1 text-xs text-gray-500">
            Pantau kondisi tanaman TOGA warga secara menyeluruh.
        </p>
    </div>

    <a
        href="{{ route('tanaman.index') }}"
        class="rounded-md bg-[#174238] px-5 py-2.5 text-xs font-semibold text-white hover:bg-[#285e50]"
    >
        Lihat katalog
    </a>

</div>

<livewire:tanaman-katalog />

@endsection
