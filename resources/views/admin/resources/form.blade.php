@extends('admin.layout', ['title' => ($item ? 'Edit ' : 'Tambah ').$config['title']])

@section('content')
@if ($resource === 'tanaman')
    @php
        $categoryOptions = ['Rimpang', 'Daun', 'Bunga', 'Batang & Akar'];
        $selectedCategory = old('kategori', $item?->kategori ?? 'Rimpang');

        if ($selectedCategory && ! in_array($selectedCategory, $categoryOptions, true)) {
            array_unshift($categoryOptions, $selectedCategory);
        }
    @endphp

    <style>
        .plant-form-page {
            width: 100%;
            max-width: 1040px;
            margin: 0 auto;
            padding: 0 20px 28px;
        }

        .plant-form-breadcrumb {
            margin: 0 -20px 18px;
        }
        .plant-form-grid {
            display: grid;
            grid-template-columns: minmax(210px, .75fr) minmax(0, 1.45fr);
            align-items: start;
            gap: 14px;
        }

        .plant-form-card {
            border: 1px solid #eef0eb;
            border-radius: 12px;
            background: #fff;
            padding: 15px;
            box-shadow: 0 5px 20px rgba(30, 60, 40, .05);
        }

        .plant-photo-drop {
            display: flex;
            min-height: 132px;
            cursor: pointer;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px dashed #cbd6c8;
            border-radius: 9px;
            background: #f6f8f4;
            text-align: center;
            transition: border-color .15s, background-color .15s;
        }

        .plant-photo-drop:hover {
            border-color: #39775b;
            background: #f1f6ed;
        }

        .plant-category-option input:checked + span {
            border-color: #28644e;
            background: #28644e;
            color: #fff;
        }

        .plant-form-label {
            display: block;
            color: #263f36;
            font-size: 10px;
            font-weight: 700;
        }

        .plant-form-input {
            display: block;
            width: 100%;
            margin-top: 5px;
            border: 1px solid #e1e6df;
            border-radius: 6px;
            background: #fff;
            padding: 8px 10px;
            color: #193b32;
            font-size: 10px;
            font-weight: 400;
            outline: none;
        }

        .plant-form-input:focus {
            border-color: #6b9b7a;
            box-shadow: 0 0 0 2px rgba(107, 155, 122, .14);
        }

        @media (max-width: 700px) {
            .plant-form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="plant-form-page">
        <div class="plant-form-breadcrumb flex items-center justify-between gap-3 border-b border-[#e2e8df] bg-white px-5 py-3">
            <p class="text-[9px] text-gray-500">
                <a href="{{ route('admin.resources.index', 'tanaman') }}" class="hover:text-emerald-800">Tanaman TOGA</a>
                <span class="mx-1.5">›</span>
                <span class="font-semibold text-[#193b32]">{{ $item ? 'Edit Tanaman' : 'Tambah Tanaman Baru' }}</span>
            </p>
            <a href="{{ route('admin.resources.index', 'tanaman') }}"
               class="shrink-0 rounded-lg bg-[#f3f6f0] px-3 py-2 text-[9px] font-semibold text-[#193b32] hover:bg-[#e8eee3]">
                ← Kembali
            </a>
        </div>

        <div class="mb-4 px-1">
            <h1 class="text-xl font-bold leading-tight text-[#193b32]">
                {{ $item ? 'Edit Tanaman' : 'Tambah Tanaman Baru' }}
            </h1>
            <p class="mt-1 text-[9px] text-gray-500">
                Masukkan informasi tanaman obat keluarga baru untuk didata dalam kebun TOGA warga.
            </p>
        </div>

        <form
            method="POST"
            action="{{ $item ? route('admin.resources.update', [$resource, $item->id]) : route('admin.resources.store', $resource) }}"
            enctype="multipart/form-data"
            class="plant-form-grid"
        >
            @csrf

            @if ($item)
                @method('PUT')
            @endif

            <div class="flex flex-col gap-3">
                <section class="plant-form-card">
                    <h2 class="text-[10px] font-bold text-[#193b32]">Foto Tanaman</h2>
                    <label class="plant-photo-drop mt-2.5 px-3 py-4">
                        <input
                            id="plant-photo"
                            type="file"
                            name="foto"
                            accept="image/png,image/jpeg,image/jpg,image/webp"
                            aria-label="Pilih foto tanaman"
                            class="sr-only"
                        >

                        <img
                            id="plant-photo-preview"
                            src="{{ $item?->foto_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->foto_path) : 'data:,' }}"
                            alt="Pratinjau foto tanaman"
                            class="{{ $item?->foto_path ? 'mb-2 h-16 w-full object-contain' : 'hidden mb-2 h-16 w-full object-contain' }}"
                        >

                        <span id="plant-photo-placeholder" class="{{ $item?->foto_path ? 'hidden' : 'flex flex-col items-center' }}">
                            <span class="mb-2 flex h-8 w-8 items-center justify-center rounded-full bg-[#edf4e7] text-base text-emerald-800" aria-hidden="true">◎</span>
                            <span class="text-[9px] font-semibold text-emerald-800">Unggah Foto Tanaman</span>
                        </span>
                        <span id="plant-photo-filename" class="max-w-full truncate text-[8px] text-gray-500">
                            {{ $item?->foto_path ? basename($item->foto_path) : 'Format PNG, JPG maks 5MB' }}
                        </span>
                    </label>

                    <p class="mt-2.5 text-[8px] leading-4 text-gray-500">
                        * Pastikan foto jelas, menggunakan pencahayaan yang cukup, dan memperlihatkan kondisi daun atau rimpang dengan baik.
                    </p>
                    @error('foto')
                        <p class="mt-2 text-[9px] font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </section>

                <aside class="rounded-lg border border-[#c6df87] bg-[#f0f7df] p-3">
                    <h2 class="text-[9px] font-bold text-emerald-800">ⓘ Petunjuk Pendataan</h2>
                    <p class="mt-1.5 text-[8px] leading-4 text-emerald-900">
                        Identifikasi kategori tanaman secara tepat agar memudahkan sistem dalam menjadwalkan rekomendasi penyiraman dan pemupukan yang disesuaikan oleh koordinator KKN.
                    </p>
                </aside>
            </div>

            <section class="plant-form-card">
                <h2 class="text-[11px] font-bold text-[#193b32]">Detail Informasi Tanaman</h2>

                <div class="mt-3 space-y-3">
                    <label class="plant-form-label">
                        Nama Tanaman
                        <input
                            name="nama"
                            value="{{ old('nama', $item?->nama) }}"
                            required
                            placeholder="Contoh: Jahe Merah, Serai, dll."
                            class="plant-form-input"
                        >
                    </label>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <fieldset>
                            <legend class="plant-form-label">Jenis / Kategori Tanaman</legend>
                            <div class="mt-1.5 flex flex-wrap gap-1">
                                @foreach ($categoryOptions as $category)
                                    <label class="plant-category-option cursor-pointer">
                                        <input
                                            type="radio"
                                            name="kategori"
                                            value="{{ $category }}"
                                            @checked($selectedCategory === $category)
                                            required
                                            class="sr-only"
                                        >
                                        <span class="inline-flex min-h-7 items-center rounded-md border border-[#e1e6df] bg-[#f5f7f3] px-2 text-[8px] font-semibold text-[#193b32]">
                                            {{ $category }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <label class="plant-form-label">
                            Nama Penanam / Pemilik
                            <input
                                name="nama_penanam"
                                value="{{ old('nama_penanam', $item?->nama_penanam) }}"
                                placeholder="Contoh: Bu Sari, Pak Rudi"
                                class="plant-form-input"
                            >
                        </label>
                    </div>

                    <label class="plant-form-label">
                        Catatan Tambahan
                        <textarea
                            name="deskripsi"
                            rows="4"
                            placeholder="Tulis instruksi khusus perawatan, kondisi awal bibit, atau manfaat tanaman di sini..."
                            class="plant-form-input resize-y"
                        >{{ old('deskripsi', $item?->deskripsi) }}</textarea>
                    </label>

                    <details class="rounded-lg border border-[#e8ece6] px-3 py-2">
                        <summary class="cursor-pointer text-[9px] font-semibold text-[#193b32]">
                            Informasi budidaya (opsional)
                        </summary>
                        <p class="mt-1 text-[8px] text-gray-500">
                            Isi jika sudah diketahui. Nilai dapat dilengkapi kembali nanti.
                        </p>

                        <div class="mt-2 grid gap-2 sm:grid-cols-2">
                            <label class="plant-form-label">
                                Kebutuhan Cahaya
                                <input
                                    name="kebutuhan_cahaya"
                                    value="{{ old('kebutuhan_cahaya', $item?->kebutuhan_cahaya) }}"
                                    placeholder="Contoh: Matahari pagi"
                                    class="plant-form-input"
                                >
                            </label>

                            <label class="plant-form-label">
                                Kebutuhan Air
                                <input
                                    name="kebutuhan_air"
                                    value="{{ old('kebutuhan_air', $item?->kebutuhan_air) }}"
                                    placeholder="Contoh: Siram saat media kering"
                                    class="plant-form-input"
                                >
                            </label>

                            <label class="plant-form-label">
                                Hari Panen Minimum
                                <input
                                    name="min_hari_panen"
                                    type="number"
                                    min="0"
                                    value="{{ old('min_hari_panen', $item?->min_hari_panen) }}"
                                    class="plant-form-input"
                                >
                            </label>

                            <label class="plant-form-label">
                                Hari Panen Maksimum
                                <input
                                    name="max_hari_panen"
                                    type="number"
                                    min="0"
                                    value="{{ old('max_hari_panen', $item?->max_hari_panen) }}"
                                    class="plant-form-input"
                                >
                            </label>
                        </div>

                        <div class="mt-2 grid gap-2 sm:grid-cols-2">
                            <label class="plant-form-label">
                                Nama Ilmiah
                                <input
                                    name="nama_ilmiah"
                                    value="{{ old('nama_ilmiah', $item?->nama_ilmiah) }}"
                                    class="plant-form-input"
                                >
                            </label>

                            <label class="plant-form-label">
                                Manfaat / Pemanfaatan Umum
                                <input
                                    name="manfaat"
                                    value="{{ old('manfaat', $item?->manfaat) }}"
                                    class="plant-form-input"
                                >
                            </label>
                        </div>

                        <div class="mt-2 grid gap-2 sm:grid-cols-2">
                            <label class="plant-form-label">
                                Panduan Penanaman
                                <textarea name="panduan_penanaman" rows="2" class="plant-form-input">{{ old('panduan_penanaman', $item?->panduan_penanaman) }}</textarea>
                            </label>

                            <label class="plant-form-label">
                                Panduan Perawatan
                                <textarea name="panduan_perawatan" rows="2" class="plant-form-input">{{ old('panduan_perawatan', $item?->panduan_perawatan) }}</textarea>
                            </label>
                        </div>
                    </details>

                    <label class="flex items-center gap-2 text-[9px] font-semibold text-[#193b32]">
                        <input type="hidden" name="is_active" value="0">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(old('is_active', $item?->is_active ?? true))
                            class="rounded border-stone-300 text-emerald-700"
                        >
                        Tampilkan di katalog tanaman
                    </label>

                    <div class="flex justify-end gap-2 border-t border-[#edf0eb] pt-3">
                        <a href="{{ route('admin.resources.index', $resource) }}"
                           class="rounded-lg border border-[#e1e6df] px-4 py-2 text-[9px] font-semibold text-[#193b32] hover:bg-[#f5f7f3]">
                            Batal
                        </a>
                        <button type="submit"
                                class="rounded-lg bg-[#174638] px-4 py-2 text-[9px] font-semibold text-white hover:bg-[#285e50]">
                            {{ $item ? 'Simpan Perubahan' : 'Simpan Tanaman' }}
                        </button>
                    </div>
                </div>
            </section>
        </form>
    </div>

    <script>
        document.getElementById('plant-photo')?.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (!file) return;

            const preview = document.getElementById('plant-photo-preview');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            document.getElementById('plant-photo-placeholder').classList.add('hidden');
            document.getElementById('plant-photo-filename').textContent = file.name;
        });
    </script>
@else
<div class="max-w-5xl">

    <a href="{{ route('admin.resources.index', $resource) }}"
       class="text-xs font-semibold text-emerald-800 hover:text-emerald-950">
        ← Kembali
    </a>

    <h1 class="mt-3 font-display text-2xl font-bold text-[#193b32] sm:text-[27px]">
        {{ $item ? 'Edit' : 'Tambah' }} {{ $config['title'] }}
    </h1>
    <p class="mt-1 text-xs text-gray-500">
        Lengkapi informasi berikut untuk memperbarui data Akarasa.
    </p>

    <form
        method="POST"
        action="{{ $item ? route('admin.resources.update', [$resource, $item->id]) : route('admin.resources.store', $resource) }}"
        class="mt-5 space-y-5 rounded-xl border border-[#e2e8df] bg-white p-5 shadow-sm sm:p-6"
    >
        @csrf

        @if ($item)
            @method('PUT')
        @endif

        @if ($resource === 'penamans')

            <label class="block text-sm font-bold">
                Nama penanam
                <input name="nama" value="{{ old('nama', $item?->nama) }}" required maxlength="255" class="admin-input">
            </label>

        @else

            <div class="grid gap-5 sm:grid-cols-2">

                <label class="text-sm font-bold">
                    Tanaman

                    <select name="tanaman_id" required class="admin-input">
                        <option value="">Pilih tanaman</option>

                        @foreach ($tanamans as $tanaman)
                            <option
                                value="{{ $tanaman->id }}"
                                @selected(old('tanaman_id', $item?->tanaman_id) == $tanaman->id)
                            >
                                {{ $tanaman->nama }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="text-sm font-bold">
                    Nama aktivitas
                    <input
                        name="nama"
                        value="{{ old('nama', $item?->nama) }}"
                        required
                        class="admin-input"
                    >
                </label>

                <label class="text-sm font-bold">
                    Jenis

                    <select name="type" required class="admin-input">
                        @foreach (['penyiraman', 'pemupukan', 'pemangkasan', 'inspeksi', 'lainnya'] as $type)
                            <option
                                value="{{ $type }}"
                                @selected(old('type', $item?->type) === $type)
                            >
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="text-sm font-bold">
                    Ulang setiap (hari)
                    <input
                        name="interval_days"
                        type="number"
                        min="1"
                        value="{{ old('interval_days', $item?->interval_days) }}"
                        class="admin-input"
                    >
                    <span class="mt-1 block text-xs font-normal text-stone-500">
                        Kosongkan untuk aktivitas satu kali.
                    </span>
                </label>

                <label class="text-sm font-bold">
                    Mulai setelah (hari)
                    <input
                        name="mulai_setelah_hari"
                        type="number"
                        min="0"
                        value="{{ old('mulai_setelah_hari', $item?->mulai_setelah_hari ?? 0) }}"
                        class="admin-input"
                    >
                </label>

                <label class="text-sm font-bold">
                    Berakhir setelah (hari)
                    <input
                        name="berakhir_setelah_hari"
                        type="number"
                        min="0"
                        value="{{ old('berakhir_setelah_hari', $item?->berakhir_setelah_hari) }}"
                        class="admin-input"
                    >
                    <span class="mt-1 block text-xs font-normal text-stone-500">
                        Kosongkan untuk mengikuti jendela jadwal.
                    </span>
                </label>

            </div>

            <label class="block text-sm font-bold">
                Instruksi
                <textarea
                    name="instruksi"
                    rows="3"
                    class="admin-input"
                >{{ old('instruksi', $item?->instruksi) }}</textarea>
            </label>

            <label class="flex items-center gap-2 text-sm font-bold">
                <input type="hidden" name="is_active" value="0">

                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    @checked(old('is_active', $item?->is_active ?? true))
                    class="rounded border-stone-300 text-emerald-700"
                >

                Aturan aktif
            </label>

        @endif

        <div class="flex flex-wrap gap-3 pt-2">

            <button
                type="submit"
                class="rounded-xl bg-emerald-800 px-6 py-3 font-bold text-white hover:bg-emerald-950"
            >
                Simpan
            </button>

            <a
                href="{{ route('admin.resources.index', $resource) }}"
                class="rounded-xl border border-stone-300 px-6 py-3 font-bold text-stone-700 hover:bg-stone-50"
            >
                Batal
            </a>

        </div>

    </form>

</div>
@endif
@endsection
