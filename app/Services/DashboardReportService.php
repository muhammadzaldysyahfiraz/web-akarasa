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
     *     penanam: ?Penanam,
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
     *     plantings: Collection,
     *     recentActivities: Collection
     * }
     */
    public function summary(?Penanam $penanam = null): array
    {
        $today = CarbonImmutable::today();
        $start = $today->subDays(6);

        $scheduledQuery = JadwalPerawatan::query()
            ->whereBetween('perawatan_at', [$start->toDateString(), $today->toDateString()])
            ->when($penanam, fn ($q) => $q->whereHas('tanamanDitanam', fn ($sub) => $sub->where('penanam_id', $penanam->id)));

        $scheduled = (clone $scheduledQuery)->count();
        $completed = (clone $scheduledQuery)->where('status', 'completed')->count();

        $activePlantingsQuery = TanamanDitanam::query()
            ->where('status', 'bertumbuh')
            ->when($penanam, fn ($q) => $q->where('penanam_id', $penanam->id));

        return [
            'penanam' => $penanam,
            'periodStart' => $start,
            'periodEnd' => $today,
            'growers' => $penanam ? 1 : Penanam::query()->count(),
            'activePlantings' => (clone $activePlantingsQuery)->count(),
            'activeGardens' => (clone $activePlantingsQuery)
                ->distinct()
                ->count('penanam_id'),
            'scheduledCare' => $scheduled,
            'completedCare' => $completed,
            'completionRate' => $scheduled > 0 ? (int) round(($completed / $scheduled) * 100) : null,
            'careActivities' => AktivitasPerawatan::query()
                ->whereBetween('performed_at', [$start->startOfDay(), $today->endOfDay()])
                ->when($penanam, fn ($q) => $q->whereHas('tanamanDitanam', fn ($sub) => $sub->where('penanam_id', $penanam->id)))
                ->count(),
            'harvests' => Panen::query()
                ->whereBetween('dipanen_at', [$start->toDateString(), $today->toDateString()])
                ->when($penanam, fn ($q) => $q->whereHas('tanamanDitanam', fn ($sub) => $sub->where('penanam_id', $penanam->id)))
                ->count(),
            'plantings' => $penanam
                ? TanamanDitanam::query()
                    ->with('tanaman')
                    ->where('penanam_id', $penanam->id)
                    ->latest('ditanam_at')
                    ->get()
                : new Collection(),
            'recentActivities' => AktivitasPerawatan::query()
                ->with([
                    'tanamanDitanam.tanaman',
                    'tanamanDitanam.penanam',
                    'jadwalPerawatan.aturanPerawatan',
                ])
                ->when($penanam, fn ($q) => $q->whereHas('tanamanDitanam', fn ($sub) => $sub->where('penanam_id', $penanam->id)))
                ->latest('performed_at')
                ->limit(15)
                ->get(),
        ];
    }
}