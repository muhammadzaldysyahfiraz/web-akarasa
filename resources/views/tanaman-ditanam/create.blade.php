@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-5xl px-5 py-7 sm:px-8">
    <a href="{{ route('tanaman.index') }}" class="text-xs font-semibold text-emerald-800 hover:text-emerald-950">← Kembali ke katalog</a>

    <div class="mt-4 rounded-xl border border-[#e2e8df] bg-white p-5 shadow-sm sm:p-7">
        <p class="text-[10px] font-bold uppercase tracking-[.16em] text-emerald-700">Catat kebunmu</p>
        <h1 class="mt-2 font-display text-2xl font-bold text-[#193b32] sm:text-[27px]">Tambah Tanaman Baru</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600">
            Gunakan kode akses yang sama untuk menambahkan tanaman ke kebun yang sudah ada. Kode ini dipakai untuk melindungi data kebunmu.
        </p>

        @if ($errors->any())
            <div role="alert" class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="font-bold">Periksa kembali data yang diisi.</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tanaman-ditanam.store') }}" class="mt-6 space-y-5">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <label class="block text-sm font-bold text-stone-700">
                    Nama penanam
                    <input name="penanam_nama" value="{{ old('penanam_nama') }}" required maxlength="255"
                        class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                </label>

                <label class="block text-sm font-bold text-stone-700">
                    Kode akses kebun
                    <input name="access_code" type="password" required minlength="8" autocomplete="new-password"
                        class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                    <span class="mt-1 block text-xs font-normal text-stone-500">Minimal 8 karakter. Simpan kode ini untuk akses berikutnya.</span>
                </label>
            </div>

            <label class="block text-sm font-bold text-stone-700">
                Tanaman
                <select name="tanaman_id" required
                    class="mt-2 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                    <option value="">Pilih tanaman</option>
                    @foreach ($tanamans as $tanaman)
                        <option value="{{ $tanaman->id }}" @selected(old('tanaman_id', request('tanaman_id')) == $tanaman->id)>
                            {{ $tanaman->nama }} · {{ $tanaman->kategori }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="grid gap-5 sm:grid-cols-2">
                <label class="block text-sm font-bold text-stone-700">
                    Tanggal tanam
                    <input name="ditanam_at" type="date" value="{{ old('ditanam_at', now()->toDateString()) }}"
                        max="{{ now()->toDateString() }}" required
                        class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                </label>

                <label class="block text-sm font-bold text-stone-700">
                    Jumlah tanaman
                    <input name="quantity" type="number" min="1" max="4294967295"
                        value="{{ old('quantity', 1) }}" required
                        class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
                </label>
            </div>

            <div class="rounded-xl border border-[#e2e8df] bg-[#f8faf6] p-4 sm:p-5">
                <p class="text-sm font-bold text-stone-700">Status penyiraman awal</p>
                <p class="mt-1 text-xs text-stone-500">Apakah tanaman sudah disiram setelah ditanam?</p>

                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:gap-6">
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-stone-700">
                        <input type="radio" name="sudah_disiram" value="1"
                            @checked(old('sudah_disiram', '0') == '1')
                            class="h-4 w-4 accent-emerald-700">
                        Sudah disiram
                    </label>

                    <label class="flex cursor-pointer items-center gap-2 text-sm text-stone-700">
                        <input type="radio" name="sudah_disiram" value="0"
                            @checked(old('sudah_disiram', '0') == '0')
                            class="h-4 w-4 accent-emerald-700">
                        Belum disiram
                    </label>
                </div>

                <div id="tanggal-penyiraman-wrapper" class="mt-4 hidden">
                    <label class="block text-sm font-bold text-stone-700">
                        Tanggal terakhir disiram
                        <input
                            id="terakhir_disiram_at"
                            name="terakhir_disiram_at"
                            type="date"
                            value="{{ old('terakhir_disiram_at') }}"
                            max="{{ now()->toDateString() }}"
                            class="mt-2 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100"
                        >
                    </label>
                    <p class="mt-1 text-xs font-normal text-stone-500">
                        Isi tanggal penyiraman terakhir setelah tanaman ditanam.
                    </p>
                </div>
            </div>

            <label class="block text-sm font-bold text-stone-700">
                Lokasi (opsional)
                <input name="location" value="{{ old('location') }}" maxlength="255"
                    placeholder="Contoh: teras atau halaman belakang"
                    class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
            </label>

            <label class="block text-sm font-bold text-stone-700">
                Catatan (opsional)
                <textarea name="catatan" rows="3"
                    class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">{{ old('catatan') }}</textarea>
            </label>

            <div class="flex justify-end">
                <button type="submit"
                    class="rounded-lg bg-[#174638] px-5 py-3 text-xs font-semibold text-white transition hover:bg-[#285e50]">
                    Simpan ke kebunku
                </button>
            </div>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[name="sudah_disiram"]');
        const wrapper = document.getElementById('tanggal-penyiraman-wrapper');
        const dateInput = document.getElementById('terakhir_disiram_at');

        function toggleTanggalPenyiraman() {
            const selected = document.querySelector('input[name="sudah_disiram"]:checked');
            const sudahDisiram = selected && selected.value === '1';

            wrapper.classList.toggle('hidden', !sudahDisiram);
            dateInput.required = !!sudahDisiram;

            if (!sudahDisiram) {
                dateInput.value = '';
            }
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', toggleTanggalPenyiraman);
        });

        toggleTanggalPenyiraman();
    });
</script>
@endsection