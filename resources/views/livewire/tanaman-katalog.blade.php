<div>
    <div class="mb-7 grid gap-3 sm:grid-cols-[1fr_16rem]">
        <label class="sr-only" for="cari-tanaman">Cari tanaman</label>
        <input id="cari-tanaman" wire:model.live.debounce.300ms="search" type="search" placeholder="Cari nama tanaman..." class="rounded-2xl border border-stone-300 bg-white px-5 py-3.5 outline-none transition placeholder:text-stone-400 focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
        <label class="sr-only" for="filter-kategori">Filter kategori</label>
        <select id="filter-kategori" wire:model.live="kategori" class="rounded-2xl border border-stone-300 bg-white px-5 py-3.5 outline-none focus:border-emerald-700 focus:ring-4 focus:ring-emerald-100">
            <option value="">Semua kategori</option>
            @foreach ($kategoriList as $item)
                <option value="{{ $item }}">{{ $item }}</option>
            @endforeach
        </select>
    </div>
    @if ($tanamans->isEmpty())
        <div class="rounded-3xl border border-dashed border-stone-300 bg-white px-6 py-14 text-center">
            <p class="font-display text-xl font-bold text-stone-800">Belum ada tanaman yang cocok</p>
            <p class="mt-2 text-stone-500">Coba ubah kata pencarian atau pilihan kategori.</p>
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($tanamans as $tanaman)
                <article class="group flex h-full flex-col rounded-3xl border border-stone-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg">
                    <div class="flex items-start justify-between gap-3">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-100 font-display text-xl font-extrabold text-emerald-800" aria-hidden="true">{{ mb_substr($tanaman->nama, 0, 1) }}</span>
                        <span class="rounded-full bg-stone-100 px-3 py-1.5 text-xs font-bold text-stone-600">{{ $tanaman->kategori }}</span>
                    </div>
                    <h2 class="mt-5 font-display text-2xl font-extrabold text-emerald-950">{{ $tanaman->nama }}</h2>
                    @if ($tanaman->nama_ilmiah)<p class="mt-1 text-sm italic text-stone-500">{{ $tanaman->nama_ilmiah }}</p>@endif
                    <p class="mt-4 line-clamp-3 leading-6 text-stone-600">{{ $tanaman->deskripsi ?: 'Informasi tanaman akan segera dilengkapi.' }}</p>
                    <div class="mt-5 grid grid-cols-2 gap-3 rounded-2xl bg-stone-50 p-4 text-sm">
                        <div><p class="text-xs font-bold uppercase tracking-wide text-stone-400">Cahaya</p><p class="mt-1 font-semibold text-stone-700">{{ $tanaman->kebutuhan_cahaya }}</p></div>
                        <div><p class="text-xs font-bold uppercase tracking-wide text-stone-400">Air</p><p class="mt-1 font-semibold text-stone-700">{{ $tanaman->kebutuhan_air }}</p></div>
                    </div>
                    <div class="mt-auto flex items-center justify-between pt-6">
                        @if ($tanaman->min_hari_panen !== null && $tanaman->max_hari_panen !== null)
                            <p class="text-xs text-stone-500">Perkiraan panen <strong class="block text-sm text-stone-800">{{ $tanaman->min_hari_panen }}–{{ $tanaman->max_hari_panen }} hari</strong></p>
                        @else
                            <p class="text-xs text-stone-500">Estimasi panen belum tersedia</p>
                        @endif
                        <a class="rounded-full bg-emerald-50 px-4 py-2.5 text-sm font-bold text-emerald-800 group-hover:bg-emerald-700 group-hover:text-white" href="{{ route('tanaman.show', $tanaman) }}">Detail →</a>
                    </div>
                </article>
            @endforeach
        </div>
        @unless ($featured)
            <div class="mt-8">{{ $tanamans->links() }}</div>
        @endunless
    @endif
</div>
