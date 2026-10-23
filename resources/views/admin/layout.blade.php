<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin' }} · KebunTOGA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 font-sans text-stone-800">
    <div class="min-h-screen lg:grid lg:grid-cols-[16rem_1fr]">
        <aside class="bg-emerald-950 px-5 py-6 text-white lg:min-h-screen">
            <a href="{{ route('home') }}" class="font-display text-xl font-extrabold">Kebun<span class="text-lime-300">TOGA</span></a>
            <p class="mt-1 text-xs text-emerald-200">Panel administrator</p>
            <nav class="mt-8 flex gap-2 overflow-x-auto lg:flex-col">
                <a href="{{ route('admin.dashboard') }}" class="whitespace-nowrap rounded-xl px-4 py-3 text-sm font-bold hover:bg-emerald-900">Ringkasan</a>
                <a href="{{ route('admin.resources.index', 'tanaman') }}" class="whitespace-nowrap rounded-xl px-4 py-3 text-sm font-bold hover:bg-emerald-900">Tanaman</a>
                <a href="{{ route('admin.resources.index', 'penamans') }}" class="whitespace-nowrap rounded-xl px-4 py-3 text-sm font-bold hover:bg-emerald-900">Penanam</a>
                <a href="{{ route('admin.resources.index', 'aturan-perawatan') }}" class="whitespace-nowrap rounded-xl px-4 py-3 text-sm font-bold hover:bg-emerald-900">Aturan perawatan</a>
                <a href="{{ route('home') }}" class="whitespace-nowrap rounded-xl px-4 py-3 text-sm font-bold text-emerald-200 hover:bg-emerald-900">Lihat website ↗</a>
            </nav>
            <form method="POST" action="{{ route('admin.logout') }}" class="mt-7 lg:mt-12">
                @csrf
                <button class="rounded-xl border border-emerald-700 px-4 py-2.5 text-sm font-bold text-emerald-100 hover:bg-emerald-900">Keluar</button>
            </form>
        </aside>
        <main class="min-w-0 px-5 py-7 sm:px-8 lg:px-10">
            @if (session('status'))
                <div role="status" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
            @endif
            @if (session('new_access_code'))
                <div role="alert" class="mb-6 rounded-xl border border-amber-300 bg-amber-50 px-5 py-4 text-sm text-amber-950">
                    <strong>Kode akses kebun (ditampilkan sekali):</strong>
                    <code class="ml-2 select-all rounded bg-white px-2 py-1 font-mono">{{ session('new_access_code') }}</code>
                    <p class="mt-2">Sampaikan kode ini kepada penanam. Sistem hanya menyimpan bentuk hash.</p>
                </div>
            @endif
            @if ($errors->any())
                <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
