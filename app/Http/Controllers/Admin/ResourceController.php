<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AturanPerawatan;
use App\Models\JadwalPerawatan;
use App\Models\Penanam;
use App\Models\Tanaman;
use App\Models\TanamanDitanam;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
        $items = $config['model']::query()
            ->when($resource === 'aturan-perawatan', fn ($query) => $query->with('tanaman'))
            ->latest()
            ->paginate(15);

        if ($resource === 'aturan-perawatan') {
            return $this->scheduleCalendar($resource, $config, $items);
        }

        return view('admin.resources.index', compact('resource', 'config', 'items'));
    }

    public function schedule(): View
    {
        return $this->index('aturan-perawatan');
    }

    private function scheduleCalendar(string $resource, array $config, $items): View
    {
        $filters = request()->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $selectedDate = isset($filters['tanggal'])
            ? CarbonImmutable::parse($filters['tanggal'])
            : (isset($filters['month'])
                ? CarbonImmutable::parse($filters['month'].'-01')
                : CarbonImmutable::today());
        $monthStart = $selectedDate->startOfMonth();
        $monthEnd = $monthStart->endOfMonth();
        $calendarStart = $monthStart->startOfWeek(CarbonImmutable::MONDAY);
        $calendarDays = collect(range(0, 41))
            ->map(fn (int $offset) => $calendarStart->addDays($offset));

        $scheduleQuery = JadwalPerawatan::query()
            ->with([
                'tanamanDitanam.tanaman',
                'tanamanDitanam.penanam',
                'aturanPerawatan',
                'aktivitasPerawatan',
            ])
            ->whereBetween('perawatan_at', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ]);

        if (! empty($filters['location'])) {
            $scheduleQuery->whereHas('tanamanDitanam', fn ($query) => $query
                ->where('location', $filters['location']));
        }

        $monthSchedules = $scheduleQuery
            ->orderBy('perawatan_at')
            ->get();
        $scheduleRouteName = request()->routeIs('admin.resources.index')
            ? 'admin.resources.index'
            : 'jadwal-perawatan.index';

        return view('admin.resources.schedule', [
            'resource' => $resource,
            'config' => $config,
            'items' => $items,
            'selectedDate' => $selectedDate,
            'monthStart' => $monthStart,
            'calendarDays' => $calendarDays,
            'schedulesByDate' => $monthSchedules->groupBy(
                fn (JadwalPerawatan $schedule) => $schedule->perawatan_at->toDateString()
            ),
            'locations' => TanamanDitanam::query()
                ->whereNotNull('location')
                ->where('location', '!=', '')
                ->distinct()
                ->orderBy('location')
                ->pluck('location'),
            'selectedLocation' => $filters['location'] ?? '',
            'canManage' => request()->user()?->role === 'admin',
            'scheduleRouteName' => $scheduleRouteName,
            'scheduleRouteParameters' => $scheduleRouteName === 'admin.resources.index'
                ? ['resource' => $resource]
                : [],
        ]);
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
        $photo = $data['foto'] ?? null;
        unset($data['foto']);

        if ($resource === 'tanaman') {
            $data['kebutuhan_cahaya'] = $data['kebutuhan_cahaya'] ?? 'Belum ditentukan';
            $data['kebutuhan_air'] = $data['kebutuhan_air'] ?? 'Belum ditentukan';
        }

        if ($resource === 'penamans') {
            $this->ensureGrowerNameIsUnique($data['nama']);
            $data['access_code_hash'] = Hash::make(Str::random(64));
            $data['created_by'] = $request->user()->id;
        }

        $item = $config['model']::create($data);

        if ($photo) {
            $item->update(['foto_path' => $this->storePlantPhoto($photo)]);
        }

        $redirect = redirect()
            ->route('admin.resources.index', $resource)
            ->with('status', 'Data berhasil ditambahkan.');

        return $redirect;
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
        $photo = $data['foto'] ?? null;
        unset($data['foto']);

        if ($resource === 'tanaman') {
            foreach (['kebutuhan_cahaya', 'kebutuhan_air'] as $field) {
                if (array_key_exists($field, $data) && $data[$field] === null) {
                    $data[$field] = $item->{$field};
                }
            }
        }

        if ($resource === 'penamans') {
            $this->ensureGrowerNameIsUnique($data['nama'], (int) $item->getKey());
        }

        $item->update($data);

        if ($photo) {
            $newPhotoPath = $this->storePlantPhoto($photo);
            $oldPhotoPath = $resource === 'tanaman' ? $item->foto_path : null;
            $item->update(['foto_path' => $newPhotoPath]);

            if ($oldPhotoPath) {
                Storage::disk('public')->delete($oldPhotoPath);
            }
        }

        return redirect()
            ->route('admin.resources.index', $resource)
            ->with('status', 'Data berhasil diperbarui.');
    }

    public function destroy(string $resource, string $id): RedirectResponse
    {
        $config = $this->resource($resource);
        $item = $config['model']::findOrFail($id);

        if ($item instanceof Penanam && $item->tanamanDitanam()->exists()) {
            return back()->withErrors([
                'delete' => 'Penanam masih memiliki tanaman di kebunnya. Hapus tanaman tersebut terlebih dahulu.',
            ]);
        }

        if ($item instanceof Tanaman && ($item->tanamanDitanam()->exists() || $item->aturanPerawatan()->exists())) {
            $item->update(['is_active' => false]);

            return back()->with('status', 'Tanaman masih memiliki riwayat; tanaman dinonaktifkan, bukan dihapus.');
        }

        if ($item instanceof AturanPerawatan && $item->jadwalPerawatan()->exists()) {
            return back()->withErrors([
                'delete' => 'Aturan masih digunakan oleh jadwal perawatan.',
            ]);
        }

        $item->delete();

        return back()->with('status', 'Data berhasil dihapus.');
    }

    private function resource(string $resource): array
    {
        abort_unless(isset(self::RESOURCES[$resource]), 404);

        return self::RESOURCES[$resource];
    }

    private function ensureGrowerNameIsUnique(string $name, ?int $ignoreId = null): void
    {
        $duplicate = Penanam::query()
            ->whereRaw('LOWER(TRIM(nama)) = ?', [mb_strtolower(trim($name))])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'nama' => 'Nama penanam tersebut sudah terdaftar.',
            ]);
        }
    }

    private function rules(string $resource, ?Model $item = null): array
    {
        return match ($resource) {
            'tanaman' => [
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tanaman', 'nama')->ignore($item?->getKey()),
                ],
                'nama_ilmiah' => ['nullable', 'string', 'max:255'],
                'kategori' => ['required', 'string', 'max:255'],
                'nama_penanam' => ['nullable', 'string', 'max:255'],
                'foto' => ['nullable', 'image', 'max:5120'],
                'deskripsi' => ['nullable', 'string'],
                'manfaat' => ['nullable', 'string'],
                'panduan_penanaman' => ['nullable', 'string'],
                'panduan_perawatan' => ['nullable', 'string'],
                'kebutuhan_cahaya' => ['nullable', 'string', 'max:255'],
                'kebutuhan_air' => ['nullable', 'string', 'max:255'],
                'min_hari_panen' => ['nullable', 'integer', 'min:0', 'max:65535'],
                'max_hari_panen' => [
                    'nullable',
                    'integer',
                    'gte:min_hari_panen',
                    'max:65535',
                ],
                'is_active' => ['sometimes', 'boolean'],
            ],

            'penamans' => [
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('penanam', 'nama')->ignore($item?->getKey()),
                ],
            ],

            default => [
                'tanaman_id' => ['required', 'exists:tanaman,id'],
                'nama' => ['required', 'string', 'max:255'],
                'type' => [
                    'required',
                    Rule::in(['penyiraman', 'pemupukan', 'pemangkasan', 'inspeksi', 'lainnya']),
                ],
                'instruksi' => ['nullable', 'string'],
                'interval_days' => ['nullable', 'integer', 'min:1', 'max:65535'],
                'mulai_setelah_hari' => ['nullable', 'integer', 'min:0', 'max:65535'],
                'berakhir_setelah_hari' => [
                    'nullable',
                    'integer',
                    'gte:mulai_setelah_hari',
                    'max:65535',
                ],
                'is_active' => ['sometimes', 'boolean'],
            ],
        };
    }

    private function storePlantPhoto(UploadedFile $photo): string
    {
        $path = $photo->store('tanaman', 'public');

        if ($path === false) {
            throw new \RuntimeException('Foto tanaman gagal disimpan.');
        }

        return $path;
    }
}
