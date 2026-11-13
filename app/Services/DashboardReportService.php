<?php

namespace App\Services;

use App\Models\AktivitasPerawatan;
use App\Models\JadwalPerawatan;
use App\Models\Panen;
use App\Models\Penanam;
use App\Models\TanamanDitanam;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class DashboardReportService
{
    /**
     * @return array{
     *     periodStart: CarbonImmutable,
     *     periodEnd: CarbonImmutable,
     *     growers: int,
     *     activePlantings: int,
     *     activeGardens: int,
     *     scheduledCare: int,
     *     completedCare: int,
     *     completionRate: int|null,
     *     careActivities: int,
     *     harvests: int,
     *     recentActivities: Collection
     * }
     */
    public function summary(): array
    {
        $today = CarbonImmutable::today();
        $start = $today->subDays(6);
        $scheduledQuery = JadwalPerawatan::query()
            ->whereBetween('perawatan_at', [$start->toDateString(), $today->toDateString()]);
        $scheduled = (clone $scheduledQuery)->count();
        $completed = (clone $scheduledQuery)->where('status', 'completed')->count();

        return [
            'periodStart' => $start,
            'periodEnd' => $today,
            'growers' => Penanam::query()->count(),
            'activePlantings' => TanamanDitanam::query()->where('status', 'bertumbuh')->count(),
            'activeGardens' => TanamanDitanam::query()
                ->where('status', 'bertumbuh')
                ->distinct()
                ->count('penanam_id'),
            'scheduledCare' => $scheduled,
            'completedCare' => $completed,
            'completionRate' => $scheduled > 0 ? (int) round($completed / $scheduled * 100) : null,
            'careActivities' => AktivitasPerawatan::query()
                ->whereBetween('performed_at', [$start->startOfDay(), $today->endOfDay()])
                ->count(),
            'harvests' => Panen::query()
                ->whereBetween('dipanen_at', [$start->toDateString(), $today->toDateString()])
                ->count(),
            'recentActivities' => AktivitasPerawatan::query()
                ->with([
                    'tanamanDitanam.tanaman',
                    'tanamanDitanam.penanam',
                    'jadwalPerawatan.aturanPerawatan',
                ])
                ->latest('performed_at')
                ->limit(10)
                ->get(),
        ];
    }
}
