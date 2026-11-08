@php
    $isAdminPage = auth()->check()
        && strtolower(trim((string) auth()->user()->role)) === 'admin';

    $gambarTanaman = [
        'Daun Mint' => 'daun mint.jpg',
        'Daun Pandan' => 'daun pandan.jpg',
        'Jahe Merah' => 'jahe merah.jpg',
        'Kemangi' => 'kemangi.jpg',
        'Kencur' => 'kencur.jpg',
        'Kumis Kucing' => 'kumis kucing.jpg',
        'Kunyit' => 'kunyit.jpg',
        'Lengkuas' => 'lengkuas.jpg',
        'Lidah Buaya' => 'lidah buaya.jpg',
        'Rosella' => 'rosella.jpg',
        'Sambiloto' => 'sambiloto.jpg',
        'Seledri' => 'seledri.jpg',
        'Serai' => 'serai.jpg',
        'Temulawak' => 'temulawak.jpg',
    ];
@endphp

<div class="space-y-5">

    {{-- PENCARIAN DAN FILTER --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div class="relative w-full sm:max-w-md">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama tanaman..."
                class="w-full rounded-xl border border-[#dce5d9] bg-white py-3 pl-11 pr-4 text-sm text-[#193b32] outline-none focus:border-[#174638] focus:ring-2 focus:ring-[#174638]/10"
            >

            <svg class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="7" stroke-width="2"/>
                <path d="m16 16 4 4" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>

        <select
            wire:model.live="kategori"
            class="w-full rounded-xl border border-[#dce5d9] bg-white px-4 py-3 text-sm text-[#193b32] outline-none focus:border-[#174638] sm:w-52"
        >
            <option value="">Semua Kategori</option>
            @foreach ($kategoriList as $kategoriItem)
                <option value="{{ $kategoriItem }}">{{ $kategoriItem }}</option>
            @endforeach
        </select>

    </div>

    {{-- JUMLAH TANAMAN --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Daftar tanaman obat keluarga
        </p>

        <p class="text-xs font-medium text-[#174638]">
            {{ $tanamans->total() }} tanaman
        </p>
    </div>

    {{-- KARTU TANAMAN --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

        @forelse ($tanamans as $tanaman)

            @php
                $namaFile = $gambarTanaman[$tanaman->nama] ?? null;

                $pathImages = $namaFile
                    ? public_path('images/tanaman/' . $namaFile)
                    : null;

                $pathTanaman = $namaFile
                    ? public_path('tanaman/' . $namaFile)
                    : null;

                $gambar = $pathImages && file_exists($pathImages)
                    ? asset('images/tanaman/' . rawurlencode($namaFile))
                    : (($pathTanaman && file_exists($pathTanaman))
                        ? asset('tanaman/' . rawurlencode($namaFile))
                        : null);
            @endphp

            <div class="group overflow-hidden rounded-2xl border border-[#e2e8df] bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">

                {{-- GAMBAR --}}
                <a href="{{ route('tanaman.show', $tanaman) }}"
                   class="block overflow-hidden bg-[#f3f6f0]">

                    @if ($gambar)
                        <img
                            src="{{ $gambar }}"
                            alt="{{ $tanaman->nama }}"
                            class="h-48 w-full object-cover transition duration-500 group-hover:scale-105"
                        >
                    @else
                        <div class="flex h-48 items-center justify-center">
                            <span class="text-sm text-gray-400">
                                Gambar belum tersedia
                            </span>
                        </div>
                    @endif

                </a>

                {{-- INFORMASI TANAMAN --}}
                <div class="p-4">

                    <div class="mb-3">
                        <span class="inline-flex rounded-full bg-[#edf4e7] px-3 py-1 text-[11px] font-semibold text-[#47734b]">
                            {{ $tanaman->kategori }}
                        </span>
                    </div>

                    <a href="{{ route('tanaman.show', $tanaman) }}"
                       class="block text-base font-bold text-[#193b32] transition hover:text-[#6b963e]">
                        {{ $tanaman->nama }}
                    </a>

                    <p class="mt-1 text-xs italic text-gray-400">
                        {{ $tanaman->nama_ilmiah ?: 'Nama ilmiah belum tersedia' }}
                    </p>

                    @if ($tanaman->deskripsi)
                        <p class="mt-3 line-clamp-2 text-xs leading-relaxed text-gray-500">
                            {{ $tanaman->deskripsi }}
                        </p>
                    @endif

                    {{-- TOMBOL --}}
                    <div class="mt-4 flex items-center justify-between border-t border-[#edf0eb] pt-3">

                        <a href="{{ route('tanaman.show', $tanaman) }}"
                           class="text-xs font-semibold text-[#174638] hover:text-[#6b963e]">
                            Lihat Detail →
                        </a>

                        @if ($isAdminPage)
                            <a href="{{ route('admin.resources.edit', ['resource' => 'tanaman', 'id' => $tanaman->id]) }}"
                               class="inline-flex cursor-pointer items-center rounded-lg bg-[#174638] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#285e50]">
                                Edit Tanaman
                            </a>
                        @endif

                    </div>

                </div>
            </div>

        @empty

            <div class="col-span-full rounded-xl border border-dashed border-[#dce5d9] bg-white px-5 py-12 text-center">
                <p class="font-semibold text-[#193b32]">
                    Tanaman tidak ditemukan
                </p>
                <p class="mt-1 text-sm text-gray-500">
                    Coba gunakan kata kunci atau kategori lainnya.
                </p>
            </div>

        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if ($tanamans->hasPages())
        <div class="pt-4">
            {{ $tanamans->links() }}
        </div>
    @endif

</div>