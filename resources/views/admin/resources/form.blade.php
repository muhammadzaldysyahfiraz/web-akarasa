@extends('admin.layout', ['title' => ($item ? 'Edit ' : 'Tambah ').$config['title']])

@section('content')
    <div class="max-w-4xl">
        <a href="{{ route('admin.resources.index', $resource) }}" class="text-sm font-bold text-emerald-800">← Kembali</a>
        <h1 class="mt-4 font-display text-3xl font-extrabold text-emerald-950">{{ $item ? 'Edit' : 'Tambah' }} {{ $config['title'] }}</h1>
        <form method="POST" action="{{ $item ? route('admin.resources.update', [$resource, $item->id]) : route('admin.resources.store', $resource) }}" class="mt-6 space-y-5 rounded-2xl border border-stone-200 bg-white p-6 sm:p-8">
            @csrf
            @if ($item)@method('PUT')@endif
            @if ($resource === 'tanaman')
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="text-sm font-bold">Nama tanaman<input name="nama" value="{{ old('nama', $item?->nama) }}" required class="admin-input"></label>
                    <label class="text-sm font-bold">Nama ilmiah<input name="nama_ilmiah" value="{{ old('nama_ilmiah', $item?->nama_ilmiah) }}" class="admin-input"></label>
                    <label class="text-sm font-bold">Kategori<input name="kategori" value="{{ old('kategori', $item?->kategori) }}" required class="admin-input"></label>
                    <label class="text-sm font-bold">Kebutuhan cahaya<input name="kebutuhan_cahaya" value="{{ old('kebutuhan_cahaya', $item?->kebutuhan_cahaya) }}" required class="admin-input"></label>
                    <label class="text-sm font-bold">Kebutuhan air<input name="kebutuhan_air" value="{{ old('kebutuhan_air', $item?->kebutuhan_air) }}" required class="admin-input"></label>
                    <label class="text-sm font-bold">Hari panen minimum<input name="min_hari_panen" type="number" min="0" value="{{ old('min_hari_panen', $item?->min_hari_panen) }}" class="admin-input"></label>
                    <label class="text-sm font-bold">Hari panen maksimum<input name="max_hari_panen" type="number" min="0" value="{{ old('max_hari_panen', $item?->max_hari_panen) }}" class="admin-input"></label>
                </div>
                @foreach (['deskripsi' => 'Deskripsi', 'manfaat' => 'Manfaat / pemanfaatan umum', 'panduan_penanaman' => 'Panduan penanaman', 'panduan_perawatan' => 'Panduan perawatan'] as $field => $label)
                    <label class="block text-sm font-bold">{{ $label }}<textarea name="{{ $field }}" rows="3" class="admin-input">{{ old($field, $item?->$field) }}</textarea></label>
                @endforeach
                <label class="flex items-center gap-2 text-sm font-bold"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item?->is_active ?? true)) class="rounded border-stone-300 text-emerald-700"> Aktif di katalog publik</label>
            @elseif ($resource === 'penamans')
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="text-sm font-bold">Nama penanam<input name="nama" value="{{ old('nama', $item?->nama) }}" required class="admin-input"></label>
                    <label class="text-sm font-bold">Nomor telepon (opsional)<input name="phone" value="{{ old('phone', $item?->phone) }}" class="admin-input"></label>
                </div>
                <label class="block text-sm font-bold">Catatan<textarea name="catatan" rows="3" class="admin-input">{{ old('catatan', $item?->catatan) }}</textarea></label>
                <label class="block text-sm font-bold">{{ $item ? 'Kode akses baru (opsional)' : 'Kode akses kebun' }}<input name="access_code" type="password" {{ $item ? '' : 'required' }} minlength="8" autocomplete="new-password" class="admin-input"><span class="mt-1 block text-xs font-normal text-stone-500">Minimal 8 karakter. Kode hanya ditampilkan saat penanam pertama kali dibuat.</span></label>
            @else
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="text-sm font-bold">Tanaman
                        <select name="tanaman_id" required class="admin-input"><option value="">Pilih tanaman</option>@foreach ($tanamans as $tanaman)<option value="{{ $tanaman->id }}" @selected(old('tanaman_id', $item?->tanaman_id) == $tanaman->id)>{{ $tanaman->nama }}</option>@endforeach</select>
                    </label>
                    <label class="text-sm font-bold">Nama aktivitas<input name="nama" value="{{ old('nama', $item?->nama) }}" required class="admin-input"></label>
                    <label class="text-sm font-bold">Jenis
                        <select name="type" required class="admin-input">@foreach (['penyiraman', 'pemupukan', 'pemangkasan', 'inspeksi', 'lainnya'] as $type)<option value="{{ $type }}" @selected(old('type', $item?->type) === $type)>{{ ucfirst($type) }}</option>@endforeach</select>
                    </label>
                    <label class="text-sm font-bold">Ulang setiap (hari)<input name="interval_days" type="number" min="1" value="{{ old('interval_days', $item?->interval_days) }}" class="admin-input"><span class="mt-1 block text-xs font-normal text-stone-500">Kosongkan untuk aktivitas satu kali.</span></label>
                    <label class="text-sm font-bold">Mulai setelah (hari)<input name="mulai_setelah_hari" type="number" min="0" value="{{ old('mulai_setelah_hari', $item?->mulai_setelah_hari ?? 0) }}" class="admin-input"></label>
                    <label class="text-sm font-bold">Berakhir setelah (hari)<input name="berakhir_setelah_hari" type="number" min="0" value="{{ old('berakhir_setelah_hari', $item?->berakhir_setelah_hari) }}" class="admin-input"><span class="mt-1 block text-xs font-normal text-stone-500">Kosongkan untuk mengikuti jendela jadwal.</span></label>
                </div>
                <label class="block text-sm font-bold">Instruksi<textarea name="instruksi" rows="3" class="admin-input">{{ old('instruksi', $item?->instruksi) }}</textarea></label>
                <label class="flex items-center gap-2 text-sm font-bold"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item?->is_active ?? true)) class="rounded border-stone-300 text-emerald-700"> Aturan aktif</label>
            @endif
            <div class="flex flex-wrap gap-3 pt-2">
                <button class="rounded-xl bg-emerald-800 px-6 py-3 font-bold text-white hover:bg-emerald-950">Simpan</button>
                <a href="{{ route('admin.resources.index', $resource) }}" class="rounded-xl border border-stone-300 px-6 py-3 font-bold text-stone-700 hover:bg-stone-50">Batal</a>
            </div>
        </form>
    </div>
@endsection
