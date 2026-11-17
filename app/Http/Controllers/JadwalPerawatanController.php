<?php

namespace App\Http\Controllers;

use App\Models\AktivitasPerawatan;
use App\Models\JadwalPerawatan;
use App\Models\TanamanDitanam;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalPerawatanController extends Controller
{
    public const CARE_TYPES = [
        'Disiram',
        'Diberi pupuk',
        'Dipangkas / bersihkan gulma',
        'Cek hama & daun',
    ];

    public function index(Request $request): View
    {
        $selectedDate = $request->filled('tanggal')
            ? CarbonImmutable::parse($request->string('tanggal')->toString())
            : CarbonImmutable::today();

        $monthStart = $request->filled('month')
            ? CarbonImmutable::parse($request->string('month')->toString() . '-01')->startOfMonth()
            : $selectedDate->startOfMonth();

        $monthEnd = $monthStart->endOfMonth();

        $plantings = TanamanDitanam::query()
            ->with(['tanaman', 'penanam'])
            ->where('status', 'bertumbuh')
            ->orderBy('penanam_id')
            ->orderByDesc('ditanam_at')
            ->get();

        // Aktivitas sepanjang bulan ini untuk penanda kalender
        $monthActivities = AktivitasPerawatan::query()
            ->whereBetween('performed_at', [$monthStart->startOfDay(), $monthEnd->endOfDay()])
            ->get();

        $activityCountByDate = $monthActivities
            ->groupBy(fn ($item) => $item->performed_at->toDateString())
            ->map->count();

        // Aktivitas khusus di tanggal yang sedang dipilih
        $dayActivities = AktivitasPerawatan::query()
            ->whereDate('performed_at', $selectedDate->toDateString())
            ->get();

        $checkedMap = [];
        foreach ($dayActivities as $act) {
            $label = $act->catatan ?: ($act->jadwalPerawatan?->aturanPerawatan?->nama ?? 'Disiram');
            $checkedMap[$act->tanaman_ditanam_id][$label] = $act->id;
        }

        // Bangun grid kalender (Senin - Minggu)
        $gridStart = $monthStart->startOfWeek(CarbonImmutable::MONDAY);
        $gridEnd = $monthEnd->endOfWeek(CarbonImmutable::SUNDAY);

        $calendarDays = [];
        for ($cursor = $gridStart; $cursor->lte($gridEnd); $cursor = $cursor->addDay()) {
            $calendarDays[] = $cursor;
        }

        return view('jadwal-perawatan.index', [
            'monthStart' => $monthStart,
            'selectedDate' => $selectedDate,
            'calendarDays' => $calendarDays,
            'plantings' => $plantings,
            'careTypes' => self::CARE_TYPES,
            'checkedMap' => $checkedMap,
            'activityCountByDate' => $activityCountByDate,
        ]);
    }

    public function toggle(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanaman_ditanam_id' => ['required', 'exists:tanaman_ditanam,id'],
            'tanggal' => ['required', 'date'],
            'aktivitas' => ['required', 'string', 'max:255'],
        ]);

        $planting = TanamanDitanam::with('penanam')->findOrFail($data['tanaman_ditanam_id']);
        $date = CarbonImmutable::parse($data['tanggal']);

        $existing = AktivitasPerawatan::query()
            ->where('tanaman_ditanam_id', $planting->id)
            ->whereDate('performed_at', $date->toDateString())
            ->where('catatan', $data['aktivitas'])
            ->first();

        if ($existing) {
            if ($existing->jadwal_perawatan_id) {
                JadwalPerawatan::query()
                    ->whereKey($existing->jadwal_perawatan_id)
                    ->update(['status' => 'pending']);
            }
            $existing->delete();
            $message = 'Centang "' . $data['aktivitas'] . '" dibatalkan.';
        } else {
            // Hubungkan ke jadwal pending di tanggal tersebut jika ada agar "Jadwal selesai" di Dashboard ikut naik
            $matchingSchedule = JadwalPerawatan::query()
                ->where('tanaman_ditanam_id', $planting->id)
                ->whereDate('perawatan_at', $date->toDateString())
                ->where('status', 'pending')
                ->first();

            if ($matchingSchedule) {
                $matchingSchedule->update(['status' => 'completed']);
            }

            AktivitasPerawatan::create([
                'tanaman_ditanam_id' => $planting->id,
                'jadwal_perawatan_id' => $matchingSchedule?->id,
                'performed_at' => $date->setTimeFromTimeString(now()->toTimeString()),
                'nama_pelaksana' => $planting->penanam?->nama,
                'catatan' => $data['aktivitas'],
            ]);

            if ($data['aktivitas'] === 'Disiram') {
                $planting->update([
                    'sudah_disiram' => true,
                    'terakhir_disiram_at' => $date->toDateString(),
                ]);
            }

            $message = 'Tercatat: ' . $data['aktivitas'] . ' (' . ($planting->tanaman?->nama ?? 'Tanaman') . ').';
        }

        return redirect()
            ->route('jadwal-perawatan.index', [
                'month' => $date->format('Y-m'),
                'tanggal' => $date->toDateString(),
            ])
            ->with('status', $message);
    }
}