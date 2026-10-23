<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AturanPerawatan;
use App\Models\Penanam;
use App\Models\Tanaman;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResourceController extends Controller
{
    private const RESOURCES = [
        'tanaman' => ['model' => Tanaman::class, 'title' => 'Tanaman'],
        'penamans' => ['model' => Penanam::class, 'title' => 'Penanam'],
        'aturan-perawatan' => ['model' => AturanPerawatan::class, 'title' => 'Aturan Perawatan'],
    ];

    public function index(string $resource): View
    {
        $config = $this->resource($resource);
        $items = $config['model']::query()->latest()->paginate(15);

        return view('admin.resources.index', compact('resource', 'config', 'items'));
    }

    public function create(string $resource): View
    {
        $config = $this->resource($resource);

        return view('admin.resources.form', [
            'resource' => $resource,
            'config' => $config,
            'item' => null,
            'tanamans' => Tanaman::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $config = $this->resource($resource);
        $data = $request->validate($this->rules($resource));

        if ($resource === 'penamans') {
            $code = $data['access_code'];
            unset($data['access_code']);
            $data['access_code_hash'] = Hash::make($code);
            $data['created_by'] = $request->user()->id;
        }

        $config['model']::create($data);

        $redirect = redirect()
            ->route('admin.resources.index', $resource)
            ->with('status', 'Data berhasil ditambahkan.');

        return $resource === 'penamans'
            ? $redirect->with('new_access_code', $code)
            : $redirect;
    }

    public function edit(string $resource, string $id): View
    {
        $config = $this->resource($resource);
        $item = $config['model']::findOrFail($id);

        return view('admin.resources.form', [
            'resource' => $resource,
            'config' => $config,
            'item' => $item,
            'tanamans' => Tanaman::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function update(Request $request, string $resource, string $id): RedirectResponse
    {
        $config = $this->resource($resource);
        $item = $config['model']::findOrFail($id);
        $data = $request->validate($this->rules($resource, $item));

        if ($resource === 'penamans') {
            $code = $data['access_code'] ?? null;
            unset($data['access_code']);
            if ($code) {
                $data['access_code_hash'] = Hash::make($code);
            }
        }

        $item->update($data);

        return redirect()->route('admin.resources.index', $resource)->with('status', 'Data berhasil diperbarui.');
    }

    public function destroy(string $resource, string $id): RedirectResponse
    {
        $config = $this->resource($resource);
        $item = $config['model']::findOrFail($id);

        if ($item instanceof Penanam && $item->tanamanDitanam()->exists()) {
            return back()->withErrors(['delete' => 'Penanam masih memiliki tanaman di kebunnya. Hapus tanaman tersebut terlebih dahulu.']);
        }

        if ($item instanceof Tanaman && ($item->tanamanDitanam()->exists() || $item->aturanPerawatan()->exists())) {
            $item->update(['is_active' => false]);
            return back()->with('status', 'Tanaman masih memiliki riwayat; tanaman dinonaktifkan, bukan dihapus.');
        }

        if ($item instanceof AturanPerawatan && $item->jadwalPerawatan()->exists()) {
            return back()->withErrors(['delete' => 'Aturan masih digunakan oleh jadwal perawatan.']);
        }

        $item->delete();

        return back()->with('status', 'Data berhasil dihapus.');
    }

    private function resource(string $resource): array
    {
        abort_unless(isset(self::RESOURCES[$resource]), 404);

        return self::RESOURCES[$resource];
    }

    private function rules(string $resource, ?Model $item = null): array
    {
        return match ($resource) {
            'tanaman' => [
                'nama' => ['required', 'string', 'max:255', Rule::unique('tanaman', 'nama')->ignore($item?->getKey())],
                'nama_ilmiah' => ['nullable', 'string', 'max:255'],
                'kategori' => ['required', 'string', 'max:255'],
                'deskripsi' => ['nullable', 'string'],
                'manfaat' => ['nullable', 'string'],
                'panduan_penanaman' => ['nullable', 'string'],
                'panduan_perawatan' => ['nullable', 'string'],
                'kebutuhan_cahaya' => ['required', 'string', 'max:255'],
                'kebutuhan_air' => ['required', 'string', 'max:255'],
                'min_hari_panen' => ['nullable', 'integer', 'min:0', 'max:65535'],
                'max_hari_panen' => ['nullable', 'integer', 'gte:min_hari_panen', 'max:65535'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'penamans' => [
                'nama' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:255'],
                'catatan' => ['nullable', 'string'],
                'access_code' => [$item ? 'nullable' : 'required', 'string', 'min:8', 'max:128'],
            ],
            default => [
                'tanaman_id' => ['required', 'exists:tanaman,id'],
                'nama' => ['required', 'string', 'max:255'],
                'type' => ['required', Rule::in(['penyiraman', 'pemupukan', 'pemangkasan', 'inspeksi', 'lainnya'])],
                'instruksi' => ['nullable', 'string'],
                'interval_days' => ['nullable', 'integer', 'min:1', 'max:65535'],
                'mulai_setelah_hari' => ['nullable', 'integer', 'min:0', 'max:65535'],
                'berakhir_setelah_hari' => ['nullable', 'integer', 'gte:mulai_setelah_hari', 'max:65535'],
                'is_active' => ['sometimes', 'boolean'],
            ],
        };
    }
}
