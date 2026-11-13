<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, sans-serif; color: #193b32; font-size: 12px; }
        h1 { margin-bottom: 4px; font-size: 22px; }
        h2 { margin-top: 24px; font-size: 16px; }
        p { margin: 5px 0; }
        .muted { color: #64716c; }
        .stats { width: 100%; margin-top: 18px; border-collapse: collapse; }
        .stats td { width: 50%; padding: 10px; border: 1px solid #dce5d8; }
        .value { display: block; margin-top: 5px; font-size: 18px; font-weight: bold; }
        table.activities { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .activities th, .activities td { padding: 8px; border: 1px solid #dce5d8; text-align: left; }
        .activities th { background: #edf4e7; }
    </style>
</head>
<body>
    <h1>Laporan KebunTOGA</h1>
    <p class="muted">Rekap 7 hari terakhir: {{ $report['periodStart']->format('d-m-Y') }} - {{ $report['periodEnd']->format('d-m-Y') }}</p>

    <table class="stats">
        <tr>
            <td>Nama penanam <span class="value">{{ $report['growers'] }}</span></td>
            <td>Kebun aktif <span class="value">{{ $report['activeGardens'] }}</span></td>
        </tr>
        <tr>
            <td>Tanaman sedang tumbuh <span class="value">{{ $report['activePlantings'] }}</span></td>
            <td>Aktivitas perawatan <span class="value">{{ $report['careActivities'] }}</span></td>
        </tr>
        <tr>
            <td>Jadwal selesai <span class="value">{{ $report['completedCare'] }} dari {{ $report['scheduledCare'] }}</span></td>
            <td>Panen tercatat <span class="value">{{ $report['harvests'] }}</span></td>
        </tr>
    </table>

    <h2>Aktivitas perawatan terbaru</h2>
    <table class="activities">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tanaman</th>
                <th>Penanam</th>
                <th>Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report['recentActivities'] as $activity)
                <tr>
                    <td>{{ $activity->performed_at->format('d-m-Y') }}</td>
                    <td>{{ $activity->tanamanDitanam?->tanaman?->nama ?? 'Tanaman' }}</td>
                    <td>{{ $activity->tanamanDitanam?->penanam?->nama ?? 'Penanam' }}</td>
                    <td>{{ $activity->jadwalPerawatan?->aturanPerawatan?->nama ?? 'Perawatan' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada aktivitas perawatan.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
