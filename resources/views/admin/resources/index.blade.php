@extends('layouts.app', ['title' => $config['title']])

@section('content')

<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">
            Kelola Data
        </p>

        <h1 class="mt-2 text-2xl font-bold text-[#193b32]">
            {{ $config['title'] }}
        </h1>

        <p class="mt-1 text-xs text-gray-500">
            Kelola informasi {{ strtolower($config['title']) }} Akarasa.
        </p>
    </div>

    <a href="{{ route('admin.resources.create', $resource) }}"
       class="rounded-lg bg-[#174638] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#285e50]">
        + Tambah {{ strtolower($config['title']) }}
    </a>
</div>

<div class="overflow-hidden rounded-xl border border-[#e2e8df] bg-white shadow-sm">

    <div class="overflow-x-auto">
        <table class="w-full min-w-[42rem] text-left text-sm">

            <thead class="bg-[#f3f6f0] text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-4">Data</th>
                    <th class="px-5 py-4">Informasi</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-[#edf0e9]">

                @forelse ($items as $item)
                    <tr class="transition hover:bg-[#f8faf6]">

                        <td class="px-5 py-4 font-semibold text-[#193b32]">
                            {{ $item->nama }}

                            @if ($resource === 'tanaman' && $item->nama_ilmiah)
                                <span class="mt-1 block text-xs font-normal italic text-gray-500">
                                    {{ $item->nama_ilmiah }}
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-gray-600">
                            @if ($resource === 'tanaman')
                                {{ $item->kategori }}
                            @elseif ($resource === 'penamans')
                                {{ $item->phone ?: '—' }}
                            @else
                                {{ $item->tanaman?->nama }} · {{ $item->type }}
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            @php
                                $active = in_array($resource, ['tanaman', 'aturan-perawatan'])
                                    ? $item->is_active
                                    : true;
                            @endphp

                            <span class="rounded-full px-3 py-1 text-xs font-semibold
                                {{ $active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">

                                <a href="{{ route('admin.resources.edit', [$resource, $item->id]) }}"
                                   class="rounded-lg border border-[#dce5d8] px-3 py-2 text-xs font-semibold text-[#174638] hover:bg-[#f3f6f0]">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.resources.destroy', [$resource, $item->id]) }}"
                                      onsubmit="return confirm('Hapus atau nonaktifkan data ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-sm text-gray-500">
                            Belum ada data {{ strtolower($config['title']) }}.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <div class="border-t border-[#e2e8df] px-5 py-4">
        {{ $items->links() }}
    </div>

</div>

@endsection