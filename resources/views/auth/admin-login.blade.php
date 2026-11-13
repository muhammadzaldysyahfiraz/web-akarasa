<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - Akarasa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#f3f5f0] px-4 font-sans text-[#193b32]">

    <div class="w-full max-w-sm rounded-2xl bg-white p-7 shadow-sm">
        <div class="mb-6 flex flex-col items-center text-center">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#b9dc68] text-2xl text-[#174638]">
                🌱
            </span>
            <h1 class="mt-3 text-xl font-bold text-[#174238]">Login sebagai Admin</h1>
            <p class="mt-1 text-xs text-gray-500">Masuk untuk mengelola data Kebun TOGA Akarasa</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-[#193b32]">Email Admin</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', 'admin@akarasa.com') }}"
                    required
                    autofocus
                    class="mt-1.5 w-full rounded-lg border border-gray-200 bg-[#f3f5f0] px-3.5 py-2.5 text-xs outline-none focus:border-[#174238] focus:bg-white focus:ring-2 focus:ring-[#b9dc68]"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-[#193b32]">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Masukkan password admin"
                    required
                    class="mt-1.5 w-full rounded-lg border border-gray-200 bg-[#f3f5f0] px-3.5 py-2.5 text-xs outline-none focus:border-[#174238] focus:bg-white focus:ring-2 focus:ring-[#b9dc68]"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-[#174238] px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-[#285447]">
                Masuk sebagai Admin
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="{{ url('/') }}" class="text-xs text-gray-500 hover:text-[#174238]">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

</body>
</html>