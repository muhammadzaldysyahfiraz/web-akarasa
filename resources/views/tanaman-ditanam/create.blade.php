@extends('layouts.app', ['title' => 'Tambah Tanaman'])

@section('content')
<section class="mx-auto max-w-3xl px-4 py-6 sm:px-7">
    <a href="{{ route('penanam.index') }}" class="text-base font-semibold text-emerald-800 underline">Kembali ke daftar kebun</a>

    <div class="mt-4 rounded-2xl border border-[#e2e8df] bg-white p-5 shadow-sm sm:p-8">
        <p class="text-sm font-bold uppercase tracking-wide text-emerald-700">Tambah tanaman</p>
        <h1 class="mt-2 text-3xl font-bold text-[#193b32]">Catat tanaman di kebun</h1>
        <p class="mt-3 text-base leading-7 text-stone-600">Pilih nama penanam yang sudah ada, atau tulis nama baru. Tidak perlu membuat akun atau PIN.</p>

        @if ($errors->any())
            <div role="alert" class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-base text-red-800">
                <p class="font-bold">Mohon periksa isian berikut:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tanaman-ditanam.store') }}" class="mt-6 space-y-6">
            @csrf

            <div>
                <label for="penanam_id" class="block text-base font-bold text-stone-800">Nama penanam yang sudah terdaftar</label>
                <select id="penanam_id" name="penanam_id"
                    class="mt-2 min-h-14 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-base"
                    aria-describedby="penanam-help">
                    <option value="">Pilih nama jika sudah pernah ditambahkan</option>
                    @foreach ($penanams as $penanam)
                        <option value="{{ $penanam->id }}" @selected(old('penanam_id') == $penanam->id)>{{ $penanam->nama }}</option>
                    @endforeach
                </select>
                <p id="penanam-help" class="mt-2 text-sm text-stone-600">Nama penanam dapat dilihat pengunjung lain.</p>
            </div>

            <div>
                <label for="penanam_nama_baru" class="block text-base font-bold text-stone-800">Atau tulis nama penanam baru</label>
                <input id="penanam_nama_baru" name="penanam_nama_baru" value="{{ old('penanam_nama_baru') }}"
                    maxlength="255" autocomplete="name" placeholder="Contoh: Ibu Siti"
                    class="mt-2 min-h-14 w-full rounded-xl border border-stone-300 px-4 py-3 text-base">
            </div>

            <label class="block text-base font-bold text-stone-800">
                Pilih tanaman
                <select name="tanaman_id" required class="mt-2 min-h-14 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-base">
                    <option value="">Pilih dari katalog</option>
                    @foreach ($tanamans as $tanaman)
                        <option value="{{ $tanaman->id }}" @selected(old('tanaman_id', request('tanaman_id')) == $tanaman->id)>
                            {{ $tanaman->nama }} — {{ $tanaman->kategori }}
                        </option>
                    @endforeach
                </select>
            </label>

            <fieldset>
                <legend class="text-base font-bold text-stone-800">Tahap pertumbuhan tanaman</legend>
                <p class="mt-1 text-sm text-stone-600">Pilih kondisi tanaman saat ini. Perkiraan panen akan menyesuaikan.</p>
                <div class="mt-3 grid gap-3 sm:grid-cols-3">
                    @foreach (['bibit' => 'Bibit', 'anakan' => 'Anakan', 'dewasa' => 'Dewasa'] as $value => $label)
                        <label class="flex min-h-14 cursor-pointer items-center gap-3 rounded-xl border border-stone-300 px-4 py-3 text-base">
                            <input type="radio" name="fase_pertumbuhan" value="{{ $value }}" required
                                @checked(old('fase_pertumbuhan', 'bibit') === $value) class="h-5 w-5 accent-emerald-800">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="grid gap-5 sm:grid-cols-2">
                <label class="block text-base font-bold text-stone-800">
                    Tanggal ditanam
                    <input name="ditanam_at" type="date" value="{{ old('ditanam_at', now()->toDateString()) }}"
                        max="{{ now()->toDateString() }}" required
                        class="mt-2 min-h-14 w-full rounded-xl border border-stone-300 px-4 py-3 text-base">
                </label>
                <label class="block text-base font-bold text-stone-800">
                    Jumlah tanaman
                    <input name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}"
                        class="mt-2 min-h-14 w-full rounded-xl border border-stone-300 px-4 py-3 text-base">
                </label>
            </div>

            <details class="rounded-xl border border-stone-200 p-4">
                <summary class="cursor-pointer text-base font-semibold text-emerald-900">Tambahkan keterangan (opsional)</summary>
                <label class="mt-4 block text-base font-bold text-stone-800">
                    Lokasi tanaman
                    <input name="location" value="{{ old('location') }}" maxlength="255" placeholder="Contoh: teras"
                        class="mt-2 min-h-14 w-full rounded-xl border border-stone-300 px-4 py-3 text-base">
                </label>
                <label class="mt-4 block text-base font-bold text-stone-800">
                    Catatan
                    <textarea name="catatan" rows="3" class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-3 text-base">{{ old('catatan') }}</textarea>
                </label>
            </details>

            <button type="submit" class="min-h-14 w-full rounded-xl bg-[#174638] px-6 py-3 text-lg font-bold text-white hover:bg-[#285e50]">
                Simpan tanaman ke Kebunku
            </button>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const growerSelect = document.getElementById('penanam_id');
        const newGrowerInput = document.getElementById('penanam_nama_baru');

        growerSelect.addEventListener('change', function () {
            if (growerSelect.value) {
                newGrowerInput.value = '';
            }
        });

        newGrowerInput.addEventListener('input', function () {
            if (newGrowerInput.value.trim()) {
                growerSelect.value = '';
            }
        });
    });
</script>
@endsection
