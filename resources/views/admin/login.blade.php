<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin · KebunTOGA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-emerald-950 px-5 py-10 font-sans text-stone-800">
    <main class="w-full max-w-md rounded-[2rem] bg-white p-7 shadow-2xl sm:p-10">
        <a href="{{ route('home') }}" class="text-sm font-bold text-emerald-800">← KebunTOGA</a>
        <p class="mt-8 text-sm font-bold uppercase tracking-[.16em] text-emerald-700">Area pengelola</p>
        <h1 class="mt-2 font-display text-3xl font-extrabold text-emerald-950">Masuk admin</h1>
        <p class="mt-3 leading-6 text-stone-600">Gunakan email dan kata sandi akun admin yang terdaftar.</p>
        @if ($errors->any())
            <div role="alert" class="mt-5 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('admin.login.store') }}" class="mt-7 space-y-5">
            @csrf
            <label class="block text-sm font-bold">Email
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
            </label>
            <label class="block text-sm font-bold">Kata sandi
                <input type="password" name="password" required autocomplete="current-password" class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
            </label>
            <label class="flex items-center gap-2 text-sm text-stone-600"><input type="checkbox" name="remember" value="1" class="rounded border-stone-300 text-emerald-700"> Ingat saya</label>
            <button class="w-full rounded-xl bg-emerald-800 px-5 py-3.5 font-bold text-white hover:bg-emerald-950">Masuk</button>
        </form>
    </main>
</body>
</html>
