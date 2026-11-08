<?php

namespace App\Services;

use App\Models\JadwalPerawatan;
use App\Models\TanamanDitanam;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

class PerawatanScheduleService
{
    public function __construct(
        private readonly PerawatanScheduleCalculator $calculator,
    ) {}

    public function generateFor(TanamanDitanam $planting, DateTimeInterface $throughDate): int
    {
        if ($planting->status !== 'bertumbuh') {
            return $planting->jadwalPerawatan()
                ->where('status', 'pending')
                ->whereDate('perawatan_at', '>=', today())
                ->delete();
        }

        $planting->loadMissing('tanaman.aturanPerawatan');
        $rules = $planting->tanaman->aturanPerawatan->toArray();
        $occurrences = $this->calculator->calculate($planting->ditanam_at, $rules, $throughDate);

        if ($occurrences === []) {
            return 0;
        }

        $now = now();
        $rows = array_map(
            static fn (array $occurrence): array => [
                'tanaman_ditanam_id' => $planting->id,
                'aturan_perawatan_id' => $occurrence['aturan_perawatan_id'],
                'perawatan_at' => $occurrence['perawatan_at'],
                'status' => $occurrence['perawatan_at'] < $now->toDateString() ? 'missed' : 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $occurrences,
        );

        return DB::table('jadwal_perawatan')->insertOrIgnore($rows);
    }

    public function generateRollingHorizon(TanamanDitanam $planting, int $days = 90): int
    {
        $harvestDays = $planting->tanaman->max_hari_panen ?? 0;

        return $this->generateFor(
            $planting,
            $planting->ditanam_at->copy()->addDays(max($days, $harvestDays)),
        );
    }

    public function markMissed(): int
    {
        return JadwalPerawatan::query()
            ->where('status', 'pending')
            ->whereDate('perawatan_at', '<', today())
            ->update(['status' => 'missed', 'updated_at' => now()]);
    }
}
