@extends('admin.layout', ['title' => $config['title']])

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="text-sm font-bold uppercase tracking-[.16em] text-emerald-700">Kelola data</p><h1 class="mt-2 font-display text-3xl font-extrabold text-emerald-950">{{ $config['title'] }}</h1></div>
        <a href="{{ route('admin.resources.create', $resource) }}" class="rounded-xl bg-emerald-800 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-950">+ Tambah {{ strtolower($config['title']) }}</a>
    </div>
    <div class="mt-7 overflow-hidden rounded-2xl border border-stone-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[42rem] text-left text-sm">
                <thead class="bg-stone-50 text-xs uppercase tracking-wider text-stone-500"><tr><th class="px-5 py-4">Data</th><th class="px-5 py-4">Informasi</th><th class="px-5 py-4">Status</th><th class="px-5 py-4 text-right">Aksi</th></tr></thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-5 py-4 font-bold text-stone-800">
                                @if ($resource === 'aturan-perawatan'){{ $item->nama }} @else{{ $item->nama }}@endif
                                @if ($resource === 'tanaman' && $item->nama_ilmiah)<span class="mt-1 block font-normal italic text-stone-500">{{ $item->nama_ilmiah }}</span>@endif
                            </td>
                            <td class="px-5 py-4 text-stone-600">
                                @if ($resource === 'tanaman'){{ $item->kategori }}
                                @elseif ($resource === 'penamans'){{ $item->phone ?: '—' }}
                                @else{{ $item->tanaman?->nama }} · {{ $item->type }}@endif
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $active = $resource === 'tanaman' || $resource === 'aturan-perawatan' ? $item->is_active : true;
                                @endphp
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $active ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-600' }}">{{ $active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.resources.edit', [$resource, $item->id]) }}" class="rounded-lg border border-stone-300 px-3 py-2 font-bold text-stone-700 hover:bg-stone-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.resources.destroy', [$resource, $item->id]) }}" onsubmit="return confirm('Hapus atau nonaktifkan data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-red-200 px-3 py-2 font-bold text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-stone-500">Belum ada data {{ strtolower($config['title']) }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-5 py-4">{{ $items->links() }}</div>
    </div>
@endsection
