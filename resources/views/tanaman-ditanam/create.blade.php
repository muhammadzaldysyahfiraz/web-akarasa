@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-3xl px-5 py-12 sm:px-8">
        <a href="{{ route('tanaman.index') }}" class="text-sm font-bold text-emerald-800 hover:text-emerald-950">← Kembali ke katalog</a>
        <div class="mt-7 rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm sm:p-10">
            <p class="text-sm font-bold uppercase tracking-[.16em] text-emerald-700">Catat kebunmu</p>
            <h1 class="mt-2 font-display text-3xl font-extrabold text-emerald-950">Tambah tanaman</h1>
            <p class="mt-3 leading-6 text-stone-600">Gunakan kode akses yang sama untuk menambahkan tanaman ke kebun yang sudah ada. Kode ini dipakai untuk melindungi data kebunmu.</p>
            @if ($errors->any())
                <div role="alert" class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-bold">Periksa kembali data yang diisi.</p>
                    <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <form method="POST" action="{{ route('tanaman-ditanam.store') }}" class="mt-7 space-y-5">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="block text-sm font-bold text-stone-700">Nama penanam
                        <input name="penanam_nama" value="{{ old('penanam_nama') }}" required maxlength="255" class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                    </label>
                    <label class="block text-sm font-bold text-stone-700">Kode akses kebun
                        <input name="access_code" type="password" required minlength="8" autocomplete="new-password" class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                        <span class="mt-1 block text-xs font-normal text-stone-500">Minimal 8 karakter. Simpan kode ini untuk akses berikutnya.</span>
                    </label>
                </div>
                <label class="block text-sm font-bold text-stone-700">Tanaman
                    <select name="tanaman_id" required class="mt-2 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                        <option value="">Pilih tanaman</option>
                        @foreach ($tanamans as $tanaman)
                            <option value="{{ $tanaman->id }}" @selected(old('tanaman_id', request('tanaman_id')) == $tanaman->id)>{{ $tanaman->nama }} · {{ $tanaman->kategori }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="block text-sm font-bold text-stone-700">Tanggal tanam
                        <input name="ditanam_at" type="date" value="{{ old('ditanam_at', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                    </label>
                    <label class="block text-sm font-bold text-stone-700">Jumlah tanaman
                        <input name="quantity" type="number" min="1" max="4294967295" value="{{ old('quantity', 1) }}" required class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                    </label>
                </div>
                <label class="block text-sm font-bold text-stone-700">Lokasi (opsional)
                    <input name="location" value="{{ old('location') }}" maxlength="255" placeholder="Contoh: teras atau halaman belakang" class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                </label>
                <label class="block text-sm font-bold text-stone-700">Catatan (opsional)
                    <textarea name="catatan" rows="3" class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">{{ old('catatan') }}</textarea>
                </label>
                <button class="w-full rounded-xl bg-emerald-800 px-5 py-3.5 font-bold text-white hover:bg-emerald-950">Simpan ke kebunku</button>
            </form>
        </div>
    </section>
@endsection
