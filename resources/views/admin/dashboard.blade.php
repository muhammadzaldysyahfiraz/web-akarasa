@extends('layouts.app', ['title' => 'Dashboard KebunTOGA'])

@section('content')
@php
    $completionRate = $report['completionRate'];
    $progressWidth = is_numeric($completionRate) ? max(0, min(100, (int) $completionRate)) : 0;
@endphp

<style>
    .dash-wrap {
        max-width: 1120px;
        margin: 0 auto;
        padding-bottom: 32px;
    }

    /* Hero Banner */
    .dash-hero {
        background: linear-gradient(135deg, #174638 0%, #235c4b 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #ffffff;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 10px 25px rgba(23, 70, 56, 0.14);
    }
    .dash-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #d9f29b;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 5px 12px;
        border-radius: 999px;
    }
    .dash-hero h1 {
        margin: 12px 0 6px;
        font-size: 28px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }
    .dash-hero p {
        margin: 0;
        font-size: 14px;
        color: #d1fae5;
        max-width: 560px;
        line-height: 1.5;
    }
    .dash-period {
        display: inline-block;
        margin-top: 14px;
        background: rgba(0, 0, 0, 0.22);
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        color: #ecfdf5;
    }
    .dash-btn-pdf {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #b9dc68;
        color: #174238;
        font-size: 14px;
        font-weight: 700;
        padding: 13px 22px;
        border-radius: 12px;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: background 0.2s, transform 0.15s;
    }
    .dash-btn-pdf:hover {
        background: #c8e87d;
        transform: translateY(-1px);
    }

    /* Grid 4 Kartu Statistik */
    .dash-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-top: 22px;
    }
    .dash-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8df;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(25, 59, 50, 0.04);
        transition: transform 0.2s, border-color 0.2s;
    }
    .dash-stat-card:hover {
        transform: translateY(-2px);
        border-color: #86efac;
    }
    .dash-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dash-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #eef6f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .dash-stat-tag {
        font-size: 11px;
        font-weight: 700;
        color: #174638;
        background: #edf7ed;
        padding: 3px 10px;
        border-radius: 999px;
    }
    .dash-stat-label {
        margin: 14px 0 4px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }
    .dash-stat-value {
        margin: 0;
        font-size: 32px;
        font-weight: 800;
        color: #193b32;
        line-height: 1.1;
    }

    /* Grid Utama Bawah (2 Kolom Proporsional) */
    .dash-main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-top: 22px;
        align-items: start;
    }
    .dash-panel {
        background: #ffffff;
        border: 1px solid #e2e8df;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(25, 59, 50, 0.04);
    }
    .dash-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 14px;
        margin-bottom: 18px;
    }
    .dash-panel-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #193b32;
    }
    .dash-panel-header p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #64748b;
    }

    /* 3 Sub-kartu Laporan Perawatan */
    .dash-sub-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .dash-sub-box {
        background: #f3f9f5;
        border: 1px solid #d9ece0;
        border-radius: 14px;
        padding: 16px;
    }
    .dash-sub-title {
        font-size: 12px;
        font-weight: 700;
        color: #174638;
        margin: 0 0 8px;
    }
    .dash-sub-num {
        font-size: 20px;
        font-weight: 800;
        color: #064e3b;
        margin: 0;
    }
    .dash-progress-bg {
        margin-top: 10px;
        height: 7px;
        width: 100%;
        background: #d1fae5;
        border-radius: 999px;
        overflow: hidden;
    }
    .dash-progress-fill {
        height: 100%;
        background: #174638;
        border-radius: 999px;
    }

    /* Aktivitas Terbaru */
    .dash-act-title {
        margin: 24px 0 12px;
        font-size: 16px;
        font-weight: 700;
        color: #193b32;
    }
    .dash-empty {
        background: #fafbf9;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        padding: 28px 16px;
        text-align: center;
        color: #64748b;
        font-size: 13px;
    }
    .dash-act-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .dash-act-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #eef2ec;
        background: #fafbf9;
    }

    /* Panel Kanan Admin */
    .dash-side-stack {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .dash-action-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s;
    }
    .dash-action-primary {
        background: #174638;
        color: #ffffff;
    }
    .dash-action-primary:hover {
        background: #235c4b;
    }
    .dash-action-secondary {
        background: #f8faf8;
        border: 1px solid #e2e8df;
        color: #193b32;
    }
    .dash-action-secondary:hover {
        border-color: #174638;
        background: #f0f7f2;
    }
    .dash-tip-box {
        background: #f2f8e4;
        border: 1px solid #cfe39d;
        border-radius: 16px;
        padding: 18px;
        color: #193b32;
    }

    @media (max-width: 960px) {
        .dash-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .dash-main-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 600px) {
        .dash-stats-grid,
        .dash-sub-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="dash-wrap">

    {{-- HERO BANNER --}}
    <div class="dash-hero">
        <div>
            <span class="dash-badge">🌿 KEBUNTOGA · MONITORING</span>
            <h1>Dashboard Kebun TOGA</h1>
            <p>Ringkasan kondisi kebun warga, statistik tanaman aktif, dan laporan perawatan mingguan.</p>
            <div class="dash-period">
                📅 Rekap 7 hari terakhir: <strong>{{ $report['periodStart']->translatedFormat('j F') }} – {{ $report['periodEnd']->translatedFormat('j F Y') }}</strong>
            </div>
        </div>

        <a href="{{ route('laporan.pdf') }}" class="dash-btn-pdf">
            <span>📄</span>
            <span>Unduh Laporan PDF</span>
        </a>
    </div>

    {{-- 4 KARTU STATISTIK --}}
    <div class="dash-stats-grid">
        <article class="dash-stat-card">
            <div class="dash-stat-top">
                <span class="dash-stat-icon">👩‍🌾</span>
                <span class="dash-stat-tag">Warga</span>
            </div>
            <p class="dash-stat-label">Nama penanam</p>
            <p class="dash-stat-value">{{ $report['growers'] }}</p>
        </article>

        <article class="dash-stat-card">
            <div class="dash-stat-top">
                <span class="dash-stat-icon">🏡</span>
                <span class="dash-stat-tag">Aktif</span>
            </div>
            <p class="dash-stat-label">Kebun aktif</p>
            <p class="dash-stat-value">{{ $report['activeGardens'] }}</p>
        </article>

        <article class="dash-stat-card">
            <div class="dash-stat-top">
                <span class="dash-stat-icon">🌱</span>
                <span class="dash-stat-tag">Budidaya</span>
            </div>
            <p class="dash-stat-label">Tanaman sedang tumbuh</p>
            <p class="dash-stat-value">{{ $report['activePlantings'] }}</p>
        </article>

        <article class="dash-stat-card">
            <div class="dash-stat-top">
                <span class="dash-stat-icon">💧</span>
                <span class="dash-stat-tag">7 Hari</span>
            </div>
            <p class="dash-stat-label">Aktivitas perawatan</p>
            <p class="dash-stat-value">{{ $report['careActivities'] }}</p>
        </article>
    </div>

    {{-- KONTEN BAWAH: LAPORAN PERAWATAN & MENU ADMIN --}}
    <div class="dash-main-grid">

        {{-- KIRI: LAPORAN PERAWATAN --}}
        <section class="dash-panel">
            <div class="dash-panel-header">
                <div>
                    <h2>Laporan Perawatan</h2>
                    <p>Jadwal yang selesai dalam 7 hari terakhir</p>
                </div>
                <span class="dash-stat-tag">Mingguan</span>
            </div>

            <div class="dash-sub-grid">
                <div class="dash-sub-box">
                    <p class="dash-sub-title">✅ Jadwal selesai</p>
                    <p class="dash-sub-num">{{ $report['completedCare'] }} dari {{ $report['scheduledCare'] }}</p>
                </div>

                <div class="dash-sub-box">
                    <p class="dash-sub-title">🧺 Panen tercatat</p>
                    <p class="dash-sub-num">{{ $report['harvests'] }}</p>
                </div>

                <div class="dash-sub-box">
                    <p class="dash-sub-title">📊 Penyelesaian jadwal</p>
                    <p class="dash-sub-num">
                        {{ $completionRate === null ? 'Belum ada data' : $completionRate.'%' }}
                    </p>
                    @if ($completionRate !== null)
                        <div class="dash-progress-bg">
                            <div class="dash-progress-fill" style="width: {{ $progressWidth }}%"></div>
                        </div>
                    @endif
                </div>
            </div>

            <h3 class="dash-act-title">Aktivitas Terbaru</h3>

            @if ($report['recentActivities']->isNotEmpty())
                <ul class="dash-act-list">
                    @foreach ($report['recentActivities'] as $activity)
                        <li class="dash-act-item">
                            <span class="dash-stat-icon" style="width:36px;height:36px;font-size:16px;">🌿</span>
                            <div>
                                <p style="margin:0;font-size:14px;font-weight:700;color:#1e293b;">
                                    {{ $activity->jadwalPerawatan?->aturanPerawatan?->nama ?? 'Perawatan' }} ·
                                    <span style="color:#174638;">{{ $activity->tanamanDitanam?->tanaman?->nama ?? 'Tanaman' }}</span>
                                </p>
                                <p style="margin:2px 0 0;font-size:12px;color:#64748b;">
                                    {{ $activity->tanamanDitanam?->penanam?->nama ?? 'Penanam' }} ·
                                    {{ $activity->performed_at->translatedFormat('j F Y') }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="dash-empty">
                    <div style="font-size:24px;margin-bottom:6px;">🍃</div>
                    <strong style="color:#334155;">Belum ada aktivitas yang tercatat.</strong>
                    <p style="margin:4px 0 0;">Catatan penyiraman, pemupukan, dan panen warga akan muncul di sini.</p>
                </div>
            @endif
        </section>

        {{-- KANAN: PENGELOLAAN ADMIN --}}
        <div class="dash-side-stack">
            @if (auth()->user()?->role === 'admin')
                <section class="dash-panel">
                    <div class="dash-panel-header" style="margin-bottom:14px;padding-bottom:10px;">
                        <div>
                            <h2 style="font-size:16px;">Pengelolaan Admin</h2>
                            <p style="font-size:12px;">Akses cepat manajemen TOGA</p>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <a href="{{ route('admin.resources.create', 'tanaman') }}" class="dash-action-btn dash-action-primary">
                            <span>➕ Tambah Tanaman Baru</span>
                            <span>→</span>
                        </a>

                        <a href="{{ route('admin.resources.index', 'tanaman') }}" class="dash-action-btn dash-action-secondary">
                            <span>🌿 Kelola Katalog Tanaman</span>
                            <span>→</span>
                        </a>

                        <a href="{{ route('admin.resources.index', 'aturan-perawatan') }}" class="dash-action-btn dash-action-secondary">
                            <span>🗓️ Kelola Aturan Perawatan</span>
                            <span>→</span>
                        </a>
                    </div>
                </section>
            @endif

            <aside class="dash-tip-box">
                <p style="margin:0;font-size:13px;font-weight:700;color:#174638;">💡 Info Koordinator KKN</p>
                <p style="margin:6px 0 0;font-size:12px;line-height:1.5;color:#264635;">
                    Pastikan setiap jenis tanaman memiliki aturan jadwal penyiraman dan pemupukan aktif agar warga mendapat pengingat tepat waktu.
                </p>
            </aside>
        </div>

    </div>
</section>
@endsection