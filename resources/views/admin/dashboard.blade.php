@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')

<div class="mb-5">
    <h1 class="mt-2 text-2xl font-bold text-[#193b32] sm:text-[27px]">
        Dashboard Akarasa
    </h1>

    <p class="mt-1 text-xs text-gray-500">
        Selamat datang di Kebun TOGA Digital. Pilih tanaman untuk melihat informasi dan perawatannya.
    </p>
</div>

<div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="rounded-2xl bg-white p-5 shadow-sm">
        <p class="text-xs text-gray-500">Tanaman TOGA</p>
        <p class="mt-2 text-2xl font-bold text-[#174638]">
            {{ \App\Models\Tanaman::where('is_active', true)->where('nama', '!=', 'Jahe')->count() }}
        </p>
        <p class="mt-1 text-xs text-gray-400">Tanaman aktif</p>
    </div>

    <div class="rounded-2xl bg-white p-5 shadow-sm">
        <p class="text-xs text-gray-500">Kategori Tanaman</p>
        <p class="mt-2 text-2xl font-bold text-[#174638]">
            {{ \App\Models\Tanaman::where('is_active', true)->where('nama', '!=', 'Jahe')->distinct()->count('kategori') }}
        </p>
        <p class="mt-1 text-xs text-gray-400">Kategori tersedia</p>
    </div>
</div>

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-[#193b32]">
            Pilihan Tanaman TOGA
        </h2>
        <p class="mt-1 text-xs text-gray-500">
            Klik tanaman untuk melihat detailnya.
        </p>
    </div>

    @if (auth()->user()->role === 'admin')
        <a href="{{ route('admin.resources.create', 'tanaman') }}"
           class="rounded-lg bg-[#174638] px-5 py-2.5 text-xs font-semibold text-white hover:bg-[#285e50]">
            + Tambah Tanaman
        </a>
    @endif
</div>

<livewire:tanaman-katalog />

@endsection
