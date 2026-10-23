<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'KebunTOGA' }} · KebunTOGA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-stone-50 font-sans text-stone-800 antialiased">
    <header class="border-b border-stone-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                <img src="{{ asset('images/insos-akarasa-logo.png') }}" alt="Logo INSOS Akarasa" class="h-10 w-10 shrink-0 object-contain">
                <span class="min-w-0">
                    <span class="block whitespace-nowrap font-display text-lg font-extrabold tracking-tight text-emerald-950">Kebun<span class="text-emerald-700">TOGA</span></span>
                    <span class="block text-xs font-medium text-stone-500">oleh INSOS Akarasa</span>
                </span>
            </a>
            <div class="flex items-center gap-3 text-sm font-semibold">
                <a class="hidden rounded-full px-4 py-2 text-stone-600 hover:bg-stone-100 sm:block" href="{{ route('tanaman.index') }}">Katalog</a>
                <a class="hidden rounded-full px-4 py-2 text-stone-600 hover:bg-stone-100 sm:block" href="{{ route('penanam.index') }}">Kebunku</a>
                <a class="rounded-full bg-emerald-700 px-4 py-2.5 text-white transition hover:bg-emerald-800" href="{{ route('tanaman-ditanam.create') }}">+ Tambah tanaman</a>
            </div>
        </nav>
    </header>

    @if (session('status'))
        <div role="status" class="mx-auto mt-5 max-w-7xl rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="mt-20 border-t border-stone-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-5 py-8 text-sm text-stone-500 sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/insos-akarasa-logo.png') }}" alt="Logo INSOS Akarasa" class="h-12 w-12 shrink-0 rounded-full object-contain">
                <div class="space-y-1">
                    <p class="font-semibold text-stone-700">KebunTOGA <span class="font-normal text-stone-500">oleh INSOS Akarasa</span></p>
                    <p class="max-w-xl leading-5">Jalan Sosial Dalam RT 19, Kelurahan Lamaru, Kecamatan Balikpapan Timur, Kota Balikpapan.</p>
                </div>
            </div>
            <a class="shrink-0 hover:text-emerald-800" href="{{ route('admin.login') }}">Area admin</a>
        </div>
    </footer>
    @livewireScripts
</body>
</html>
