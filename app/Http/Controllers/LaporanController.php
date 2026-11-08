<?php

namespace App\Http\Controllers;

use App\Models\AktivitasPerawatan;
use App\Models\JadwalPerawatan;
use App\Models\TanamanDitanam;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function __invoke(): View
    {
        $today = CarbonImmutable::today();
        $weekStart = $today->startOfWeek(CarbonImmutable::MONDAY);
        $recentStart = $today->subDays(6)->startOfDay();
        $recentEnd = $today->endOfDay();

        $recentSchedules = JadwalPerawatan::query()
            ->whereBetween('perawatan_at', [$recentStart->toDateString(), $today->toDateString()])
            ->get(['status']);
        $completedRecently = $recentSchedules->where('status', 'completed')->count();
        $scheduledRecently = $recentSchedules->count();

        $weeklyActivities = AktivitasPerawatan::query()
            ->whereBetween('performed_at', [
                $weekStart->subWeeks(5)->startOfDay(),
                $today->endOfDay(),
            ])
            ->get(['performed_at'])
            ->groupBy(fn (AktivitasPerawatan $activity) => CarbonImmutable::parse($activity->performed_at)
                ->startOfWeek(CarbonImmutable::MONDAY)
                ->toDateString());

        $activityTrend = collect(range(0, 5))
            ->map(function (int $offset) use ($weekStart, $weeklyActivities): array {
                $start = $weekStart->subWeeks(5 - $offset);

                return [
                    'label' => $start->translatedFormat('d M'),
                    'count' => $weeklyActivities->get($start->toDateString(), collect())->count(),
                ];
            });

        $monthlyReports = collect(range(0, 5))
            ->map(function (int $offset) use ($today): array {
                $monthStart = $today->startOfMonth()->subMonths($offset);
                $monthEnd = $monthStart->endOfMonth();
                $schedules = JadwalPerawatan::query()
                    ->whereBetween('perawatan_at', [
                        $monthStart->toDateString(),
                        $monthEnd->toDateString(),
                    ])
                    ->get(['status']);
                $activities = AktivitasPerawatan::query()
                    ->whereBetween('performed_at', [
                        $monthStart->startOfDay(),
                        $monthEnd->endOfDay(),
                    ])
                    ->count();

                return [
                    'month' => $monthStart,
                    'scheduled' => $schedules->count(),
                    'completed' => $schedules->where('status', 'completed')->count(),
                    'activities' => $activities,
                ];
            })
            ->filter(fn (array $report) => $report['scheduled'] > 0 || $report['activities'] > 0)
            ->values();

        $isAdmin = auth()->user()?->role === 'admin';

        return view('laporan.index', [
            'report' => [
                'scheduledRecently' => $scheduledRecently,
                'completedRecently' => $completedRecently,
                'completionRate' => $scheduledRecently > 0
                    ? (int) round($completedRecently / $scheduledRecently * 100)
                    : null,
                'activitiesRecently' => AktivitasPerawatan::query()
                    ->whereBetween('performed_at', [$recentStart, $recentEnd])
                    ->count(),
                'activeGardens' => TanamanDitanam::query()
                    ->where('status', 'bertumbuh')
                    ->distinct()
                    ->count('penanam_id'),
                'activityTrend' => $activityTrend,
                'monthlyReports' => $monthlyReports,
                'scheduleRouteName' => $isAdmin ? 'admin.resources.index' : 'jadwal-perawatan.index',
                'scheduleRouteParameters' => $isAdmin ? ['resource' => 'aturan-perawatan'] : [],
            ],
        ]);
    }
}
