<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AturanPerawatanResource;
use App\Http\Resources\JadwalPerawatanResource;
use App\Http\Resources\PenanamResource;
use App\Http\Resources\TanamanDitanamResource;
use App\Http\Resources\TanamanResource;
use App\Models\AturanPerawatan;
use App\Models\JadwalPerawatan;
use App\Models\Penanam;
use App\Models\Tanaman;
use App\Models\TanamanDitanam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminApiController extends Controller
{
    public function index(Request $request, string $resource): AnonymousResourceCollection
    {
        $model = $this->model($resource);
        $query = $model::query()->latest();

        if ($resource === 'tanaman-tanaman') {
            $query = TanamanDitanam::with(['penanam', 'tanaman'])->latest();
        } elseif ($resource === 'jadwal-perawatan') {
            $query = JadwalPerawatan::with(['tanamanDitanam.tanaman', 'aturanPerawatan'])
                ->when($request->filled('tanggal'), fn ($builder) => $builder->whereDate('perawatan_at', $request->input('tanggal')))
                ->when($request->filled('status'), fn ($builder) => $builder->where('status', $request->input('status')))
                ->latest('perawatan_at');
        }

        return $this->resourceCollection($resource, $query->get());
    }

    public function store(Request $request, string $resource)
    {
        abort_unless(in_array($resource, ['tanaman', 'penamans', 'aturan-perawatan'], true), 404);
        $data = $request->validate($this->validationRules($resource));

        if ($resource === 'penamans') {
            $code = $data['access_code'];
            unset($data['access_code']);
            $data['access_code_hash'] = Hash::make($code);
            $data['created_by'] = $request->user()->id;
        }

        $item = $this->model($resource)::create($data);

        return response()->json(['data' => $this->resourceItem($resource, $item)], 201);
    }

    public function show(string $id, string $resource)
    {
        $item = $this->model($resource)::findOrFail($id);

        return response()->json(['data' => $this->resourceItem($resource, $item)]);
    }

    public function update(Request $request, string $id, string $resource)
    {
        abort_unless(in_array($resource, ['tanaman', 'penamans', 'aturan-perawatan'], true), 404);
        $item = $this->model($resource)::findOrFail($id);
        $rules = $this->validationRules($resource, $item);
        if ($request->isMethod('PATCH')) {
            foreach ($rules as &$fieldRules) {
                $fieldRules = array_values(array_filter(
                    $fieldRules,
                    static fn ($rule): bool => $rule !== 'required',
                ));
                array_unshift($fieldRules, 'sometimes');
            }
            unset($fieldRules);
        }
        $data = $request->validate($rules);

        if ($resource === 'penamans') {
            $code = $data['access_code'] ?? null;
            unset($data['access_code']);
            if ($code) {
                $data['access_code_hash'] = Hash::make($code);
            }
        }

        $item->update($data);

        return response()->json(['data' => $this->resourceItem($resource, $item->refresh())]);
    }

    public function destroy(string $id, string $resource): Response
    {
        $item = $this->model($resource)::findOrFail($id);

        if ($item instanceof Penanam && $item->tanamanDitanam()->exists()) {
            abort(409, 'Penanam masih memiliki tanaman di kebunnya.');
        }

        if ($item instanceof Tanaman && ($item->tanamanDitanam()->exists() || $item->aturanPerawatan()->exists())) {
            $item->update(['is_active' => false]);
            return response()->noContent();
        }

        if ($item instanceof AturanPerawatan && $item->jadwalPerawatan()->exists()) {
            abort(409, 'Aturan masih digunakan oleh jadwal perawatan.');
        }

        $item->delete();

        return response()->noContent();
    }

    private function model(string $resource): string
    {
        return match ($resource) {
            'tanaman' => Tanaman::class,
            'penamans' => Penanam::class,
            'aturan-perawatan' => AturanPerawatan::class,
            'tanaman-tanaman' => TanamanDitanam::class,
            'jadwal-perawatan' => JadwalPerawatan::class,
            default => abort(404),
        };
    }

    private function resourceItem(string $resource, Model $item)
    {
        return match ($resource) {
            'tanaman' => new TanamanResource($item),
            'penamans' => new PenanamResource($item),
            'aturan-perawatan' => new AturanPerawatanResource($item),
            'tanaman-tanaman' => new TanamanDitanamResource($item->load(['tanaman', 'jadwalPerawatan', 'panen'])),
            'jadwal-perawatan' => new JadwalPerawatanResource($item->load(['aturanPerawatan', 'tanamanDitanam'])),
            default => abort(404),
        };
    }

    private function resourceCollection(string $resource, $items): AnonymousResourceCollection
    {
        return match ($resource) {
            'tanaman' => TanamanResource::collection($items),
            'penamans' => PenanamResource::collection($items),
            'aturan-perawatan' => AturanPerawatanResource::collection($items),
            'tanaman-tanaman' => TanamanDitanamResource::collection($items),
            'jadwal-perawatan' => JadwalPerawatanResource::collection($items),
            default => abort(404),
        };
    }

    private function validationRules(string $resource, ?Model $item = null): array
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
            'aturan-perawatan' => [
                'tanaman_id' => ['required', 'exists:tanaman,id'],
                'nama' => ['required', 'string', 'max:255'],
                'type' => ['required', Rule::in(['penyiraman', 'pemupukan', 'pemangkasan', 'inspeksi', 'lainnya'])],
                'instruksi' => ['nullable', 'string'],
                'interval_days' => ['nullable', 'integer', 'min:1', 'max:65535'],
                'mulai_setelah_hari' => ['nullable', 'integer', 'min:0', 'max:65535'],
                'berakhir_setelah_hari' => ['nullable', 'integer', 'gte:mulai_setelah_hari', 'max:65535'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            default => [],
        };
    }
}
