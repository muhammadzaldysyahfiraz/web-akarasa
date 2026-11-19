<?php

namespace App\Http\Controllers;

use App\Models\CatatanPanen;
use App\Models\TanamanDitanam;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalPerawatanController extends Controller
{
    public function index(Request $request)
    {
        // Tentukan bulan & tanggal yang dipilih
        $monthParam =$request->query('month');
        $tanggalParam =$request->query('tanggal');

        $selectedDate =$tanggalParam
            ? Carbon::parse($tanggalParam)->startOfDay()
            : now()->startOfDay();

        $monthStart =$monthParam
            ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth()
            : $selectedDate->copy()->startOfMonth();

        // Susun hari-hari kalender (Senin s/d Minggu)
        $gridStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);$gridEnd = $monthStart->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);$calendarDays = collect(CarbonPeriod::create($gridStart,$gridEnd));

        // Daftar jenis perawatan harian
        $careTypes = [
            'Penyiraman',
            'Pemupukan',
            'Penyiangan Gulma',
            'Cek Hama & Penyakit',
        ];

        // Ambil tanaman yang sedang ditanam
        $plantings = TanamanDitanam::with(['tanaman', 'penanam'])
            ->latest('ditanam_at')
            ->get();

        // Hitung jumlah aktivitas perawatan per tanggal di bulan aktif
        $activityCountByDate = [];$checkedMap = [];

        if (DB::getSchemaBuilder()->hasTable('aktivitas_perawatans')) {
            $activityCountByDate = DB::table('aktivitas_perawatans')
                ->whereBetween('tanggal', [
                    $monthStart->copy()->startOfMonth()->toDateString(),$monthStart->copy()->endOfMonth()->toDateString(),
                ])
                ->selectRaw('DATE(tanggal) as tgl, count(*) as total')
                ->groupBy('tgl')
                ->pluck('total', 'tgl')
                ->toArray();

            $checkedRows = DB::table('aktivitas_perawatans')
                ->whereDate('tanggal', $selectedDate->toDateString())
                ->get();

            foreach ($checkedRows as $row) {$checkedMap[$row->tanaman_ditanam_id][$row->aktivitas] = true;
            }
        }

        // DATA CATATAN & PENGINGAT PANEN (Dikelompokkan per tanggal beserta nama tanamannya)
        $harvestByDate = CatatanPanen::with('planting.tanaman')
            ->whereBetween('tanggal', [
                $monthStart->copy()->startOfMonth()->toDateString(),$monthStart->copy()->endOfMonth()->toDateString(),
            ])
            ->get()
            ->groupBy(fn ($item) =>$item->tanggal->toDateString());

        $selectedHarvestNotes = CatatanPanen::with('planting.tanaman')
            ->whereDate('tanggal', $selectedDate->toDateString())
            ->latest()
            ->get();

        $activeHarvestReminders = CatatanPanen::with('planting.tanaman')
            ->where('is_reminder', true)
            ->where('is_done', false)
            ->whereDate('tanggal', '<=', now()->addDays(3)->toDateString())
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('jadwal-perawatan.index', compact(
            'monthStart',
            'selectedDate',
            'calendarDays',
            'activityCountByDate',
            'plantings',
            'checkedMap',
            'careTypes',
            'harvestByDate',
            'selectedHarvestNotes',
            'activeHarvestReminders'
        ));
    }

    public function toggle(Request $request)
    {
        $validated =$request->validate([
            'tanaman_ditanam_id' => 'required|integer',
            'tanggal' => 'required|date',
            'aktivitas' => 'required|string|max:100',
        ]);

        if (DB::getSchemaBuilder()->hasTable('aktivitas_perawatans')) {
            $existing = DB::table('aktivitas_perawatans')
                ->where('tanaman_ditanam_id', $validated['tanaman_ditanam_id'])
                ->whereDate('tanggal', $validated['tanggal'])
                ->where('aktivitas', $validated['aktivitas'])
                ->first();

            if ($existing) {
                DB::table('aktivitas_perawatans')->where('id', $existing->id)->delete();
            } else {
                DB::table('aktivitas_perawatans')->insert([
                    'tanaman_ditanam_id' => $validated['tanaman_ditanam_id'],
                    'tanggal' => $validated['tanggal'],
                    'aktivitas' => $validated['aktivitas'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return back();
    }

    public function storeCatatanPanen(Request $request)
    {
        $validated =$request->validate([
            'tanaman_ditanam_id' => 'nullable|integer',
            'tanggal' => 'required|date',
            'catatan' => 'required|string|max:255',
            'is_reminder' => 'nullable|boolean',
        ]);

        CatatanPanen::create([
            'tanaman_ditanam_id' => $validated['tanaman_ditanam_id'] ?: null,
            'tanggal' => $validated['tanggal'],
            'catatan' => $validated['catatan'],
            'is_reminder' => $request->boolean('is_reminder'),
        ]);

        return back()->with('success', 'Jadwal & pengingat panen berhasil disimpan!');
    }

    public function toggleSelesaiPanen(CatatanPanen $catatan)
    {
        $catatan->update(['is_done' => !$catatan->is_done]);

        return back()->with('success', 'Status panen diperbarui.');
    }

    public function destroyCatatanPanen(CatatanPanen $catatan)
    {
        $catatan->delete();

        return back()->with('success', 'Catatan panen dihapus.');
    }
}