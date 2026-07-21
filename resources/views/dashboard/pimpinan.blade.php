@extends('layouts.main')
@section('title', 'Dashboard Pimpinan')

@section('content')
    @php
        $summaryItems = [
            [
                'label' => 'Total Admin',
                'value' => $totalAdmin,
                'icon' => 'fas fa-user-shield',
                'tone' => 'gold',
                'url' => '/user',
            ],
            [
                'label' => 'Total Jemaah',
                'value' => $totalJemaah,
                'icon' => 'fas fa-users',
                'tone' => 'green',
                'url' => '/laporan/jemaah',
            ],
            [
                'label' => 'Jadwal Keberangkatan Aktif',
                'value' => $jadwalAktif,
                'icon' => 'fas fa-calendar-check',
                'tone' => 'blue',
                'url' => '/keberangkatan',
            ],
            [
                'label' => 'Menunggu Approval',
                'value' => $menungguApproval,
                'icon' => 'fas fa-hourglass-half',
                'tone' => 'orange',
                'url' => '/keberangkatan',
            ],
        ];

        $monitorItems = [
            ['label' => 'Terverifikasi', 'value' => $jemaahMonitoring['terverifikasi'], 'icon' => 'fas fa-check', 'class' => 'verified'],
            ['label' => 'Belum Verifikasi', 'value' => $jemaahMonitoring['belum_verifikasi'], 'icon' => 'fas fa-clock', 'class' => 'unverified'],
            ['label' => 'Ditolak', 'value' => $jemaahMonitoring['ditolak'], 'icon' => 'fas fa-times', 'class' => 'rejected'],
            ['label' => 'Dalam Proses', 'value' => $jemaahMonitoring['diproses'], 'icon' => 'fas fa-sync-alt', 'class' => 'processing'],
        ];
    @endphp

    <style>
        .leader-dashboard {
            min-height: calc(100vh - 130px);
            padding: 8px 4px 28px;
            color: #211b15;
        }

        .leader-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .leader-heading h1 {
            margin: 0 0 5px;
            font-size: 25px;
            font-weight: 800;
        }

        .leader-heading p {
            margin: 0;
            color: #81776d;
            font-size: 12px;
        }

        .owner-badge {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 9px 13px;
            color: #6e4814;
            background: #fff8e8;
            border: 1px solid #ead7af;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
        }

        .owner-badge i {
            color: #a66e1c !important;
            font-size: 16px;
        }

        .leader-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 14px;
        }

        .leader-card {
            background: #fff;
            border: 1px solid #e3d9ca;
            border-radius: 14px;
            box-shadow: 0 7px 22px rgba(62, 42, 19, .07);
        }

        .leader-summary-card {
            display: flex;
            align-items: center;
            gap: 18px;
            min-width: 0;
            min-height: 118px;
            padding: 20px 18px;
            overflow: hidden;
            position: relative;
            color: inherit !important;
            text-decoration: none !important;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .leader-summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 9px 23px rgba(62, 42, 19, .1);
        }

        .leader-summary-card .leader-summary-icon {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 62px;
            height: 62px;
            flex: 0 0 62px;
            color: #fff !important;
            background: var(--summary-color);
            border-radius: 16px;
            box-shadow: 0 9px 18px var(--summary-shadow);
        }

        .leader-summary-card .leader-summary-icon i {
            color: #fff !important;
            font-size: 25px;
            line-height: 1;
        }

        .leader-summary-card::after {
            content: "";
            width: 90px;
            height: 90px;
            position: absolute;
            top: -52px;
            right: -50px;
            background: var(--summary-soft);
            border-radius: 50%;
        }

        .summary-tone-gold {
            --summary-color: #9a681c;
            --summary-shadow: rgba(154, 104, 28, .25);
            --summary-soft: #f5e5c5;
        }

        .summary-tone-green {
            --summary-color: #14805f;
            --summary-shadow: rgba(20, 128, 95, .24);
            --summary-soft: #d8f1e8;
        }

        .summary-tone-blue {
            --summary-color: #2765a7;
            --summary-shadow: rgba(39, 101, 167, .24);
            --summary-soft: #dceafb;
        }

        .summary-tone-orange {
            --summary-color: #bd5b18;
            --summary-shadow: rgba(189, 91, 24, .24);
            --summary-soft: #f8e3d4;
        }

        .leader-summary-content {
            min-width: 0;
            position: relative;
            z-index: 1;
        }

        .leader-summary-content strong,
        .leader-summary-content span {
            display: block;
        }

        .leader-summary-content strong {
            margin-bottom: 7px;
            color: #17130f;
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
        }

        .leader-summary-content .leader-summary-label {
            color: #51483e;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.4;
        }

        .leader-insight-grid {
            display: grid;
            grid-template-columns: minmax(0, 2.25fr) minmax(270px, 1fr);
            gap: 14px;
            margin-bottom: 14px;
        }

        .leader-section {
            padding: 20px;
        }

        .leader-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 13px;
        }

        .leader-section-title h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
        }

        .leader-section-title small {
            color: #948a7e;
            font-size: 10px;
        }

        .leader-chart {
            height: 260px;
        }

        .monitor-list {
            display: grid;
            grid-template-rows: repeat(4, 1fr);
            min-height: 260px;
        }

        .monitor-row {
            display: grid;
            grid-template-columns: 38px minmax(0, 1fr) auto;
            gap: 11px;
            align-items: center;
            padding: 10px 4px;
            border-bottom: 1px solid #eee8df;
        }

        .monitor-row:last-child {
            border-bottom: 0;
        }

        .monitor-icon {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            color: #fff !important;
            border-radius: 9px;
        }

        .monitor-icon i {
            color: #fff !important;
            font-size: 13px;
            line-height: 1;
        }

        .monitor-label {
            color: #433b33;
            font-size: 12px;
            font-weight: 600;
        }

        .monitor-row strong {
            color: #1d1813;
            font-size: 14px;
            font-weight: 800;
        }

        .monitor-icon.verified {
            background: #43b96d;
        }

        .monitor-icon.unverified {
            background: #d99a1b;
        }

        .monitor-icon.rejected {
            background: #e6534c;
        }

        .monitor-icon.processing {
            background: #4289da;
        }

        .approval-head-link {
            padding: 8px 12px;
            color: #936119 !important;
            background: #fffaf0;
            border: 1px solid #d7b978;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 700;
            text-decoration: none !important;
        }

        .approval-table {
            width: 100%;
            min-width: 720px;
            margin: 0;
            table-layout: auto;
        }

        .leader-dashboard .table-responsive {
            overflow-x: auto !important;
        }

        .approval-table th {
            padding: 11px 12px;
            color: #4e3b26;
            background: #f7f0e6;
            border-bottom: 1px solid #e7dfd2;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .approval-table td {
            padding: 12px;
            border-bottom: 1px solid #eee8df;
            font-size: 11px;
            vertical-align: middle;
        }

        .approval-status {
            display: inline-block;
            padding: 5px 9px;
            color: #9a691f;
            background: #fff2d7;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            white-space: nowrap;
        }

        .approval-action {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 10px;
            color: #fff !important;
            background: #a96e18;
            border-radius: 7px;
            font-size: 9px;
            font-weight: 700;
            text-decoration: none !important;
            white-space: nowrap;
        }

        .approval-action i {
            color: #fff !important;
        }

        .empty-approval {
            padding: 25px !important;
            color: #948a7e;
            text-align: center;
        }

        @media (max-width: 1199px) {
            .leader-summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 850px) {
            .leader-insight-grid {
                grid-template-columns: 1fr;
            }

            .leader-chart,
            .monitor-list {
                min-height: 235px;
                height: 235px;
            }
        }

        @media (max-width: 600px) {
            .leader-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .leader-summary-grid {
                grid-template-columns: 1fr;
            }

            .leader-summary-card {
                min-height: 95px;
                padding: 16px;
            }

            .leader-summary-card .leader-summary-icon {
                width: 54px;
                height: 54px;
                flex-basis: 54px;
            }
        }
    </style>

    <div class="leader-dashboard">
        <div class="leader-heading">
            <div>
                <h1>Dashboard</h1>
                <p>Pantau data jemaah dan persetujuan jadwal keberangkatan dalam satu halaman.</p>
            </div>
            <span class="owner-badge"><i class="fas fa-user-circle"></i> Pimpinan / Owner</span>
        </div>

        <div class="leader-summary-grid">
            @foreach ($summaryItems as $item)
                <a href="{{ $item['url'] }}" class="leader-card leader-summary-card summary-tone-{{ $item['tone'] }}">
                    <div class="leader-summary-icon"><i class="{{ $item['icon'] }}"></i></div>
                    <div class="leader-summary-content">
                        <strong>{{ number_format($item['value'], 0, ',', '.') }}</strong>
                        <span class="leader-summary-label">{{ $item['label'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="leader-insight-grid">
            <section class="leader-card leader-section">
                <div class="leader-section-title">
                    <h2>Tren Pendaftaran Jemaah</h2>
                    <small>6 bulan terakhir</small>
                </div>
                <div class="leader-chart"><canvas id="leaderRegistrationChart"></canvas></div>
            </section>

            <section class="leader-card leader-section">
                <div class="leader-section-title">
                    <h2>Monitoring Status Jemaah</h2>
                </div>
                <div class="monitor-list">
                    @foreach ($monitorItems as $item)
                        <div class="monitor-row">
                            <span class="monitor-icon {{ $item['class'] }}"><i class="{{ $item['icon'] }}"></i></span>
                            <span class="monitor-label">{{ $item['label'] }}</span>
                            <strong>{{ number_format($item['value'], 0, ',', '.') }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="leader-card leader-section">
            <div class="leader-section-title">
                <h2>Approval Jadwal Keberangkatan Terbaru</h2>
                <a href="/keberangkatan" class="approval-head-link">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="approval-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Paket</th>
                            <th>Tanggal Keberangkatan</th>
                            <th>Jumlah Jemaah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($approvalSchedules as $schedule)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><b>{{ $schedule->paket?->nama_paket ?? 'Paket Umrah' }}</b></td>
                                <td>{{ $schedule->tanggal_keberangkatan?->translatedFormat('d F Y') ?? '-' }}</td>
                                <td>{{ number_format($schedule->jemaah_count, 0, ',', '.') }}</td>
                                <td><span class="approval-status">Menunggu Approval</span></td>
                                <td>
                                    <a href="/keberangkatan/detail/{{ $schedule->id }}" class="approval-action">
                                        <i class="far fa-eye"></i> Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-approval">Tidak ada jadwal yang menunggu approval.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (() => {
                const chart = document.getElementById('leaderRegistrationChart');
                if (!chart || typeof Chart === 'undefined') return;

                new Chart(chart, {
                    type: 'line',
                    data: {
                        labels: @json($registrationTrend->pluck('label')->values()),
                        datasets: [{
                            data: @json($registrationTrend->pluck('total')->values()),
                            borderColor: '#9d6a1d',
                            backgroundColor: 'rgba(196, 142, 56, .14)',
                            pointBackgroundColor: '#9d6a1d',
                            pointBorderColor: '#9d6a1d',
                            pointRadius: 4,
                            pointHoverRadius: 5,
                            borderWidth: 2,
                            fill: true,
                            tension: .35
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: context => `${context.parsed.y} jemaah`
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: '#eee8de' },
                                border: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, stepSize: 1 },
                                grid: { color: '#eee8de' },
                                border: { display: false }
                            }
                        }
                    }
                });
            })();
        </script>
    @endpush
@endsection
