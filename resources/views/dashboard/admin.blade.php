@extends('layouts.main')
@section('title', 'Dashboard Admin')

@section('content')
    @php
        $roleTitle = ucfirst(auth()->user()->role);
        $recentJemaah = \App\Models\DataJemaah::with(['user'])
            ->latest()
            ->limit(5)
            ->get();
        $recentDocs = \App\Models\DokumenJemaah::with('jemaah.user')->latest()->limit(5)->get();
        $verifiedPercent = $totalJemaah > 0 ? round(($akunAktif / $totalJemaah) * 100, 1) : 0;
        $pendingPercent = $totalJemaah > 0 ? round(($akunProsesVerifikasi / $totalJemaah) * 100, 1) : 0;
        $summaryItems = [
            [
                'label' => 'Total Jemaah',
                'value' => number_format($totalJemaah ?? 0, 0, ',', '.'),
                'icon' => 'fas fa-users',
                'background' => '#efe8ff',
                'color' => '#8b5cf6',
                'url' => '/jemaah/data-verifikasi',
            ],
            [
                'label' => 'Menunggu Verifikasi',
                'value' => number_format($akunProsesVerifikasi ?? 0, 0, ',', '.'),
                'icon' => 'far fa-clock',
                'background' => '#fff2df',
                'color' => '#f59e0b',
                'url' => '/jemaah/registrasi',
            ],
            [
                'label' => 'Jemaah Terverifikasi',
                'value' => number_format($akunAktif ?? 0, 0, ',', '.'),
                'icon' => 'fas fa-shield-alt',
                'background' => '#e8f7ea',
                'color' => '#22c55e',
                'url' => '/jemaah/data-verifikasi',
            ],
            [
                'label' => 'Total Pembayaran',
                'value' => 'Rp ' . number_format($pembayaranTotal ?? 0, 0, ',', '.'),
                'icon' => 'fas fa-wallet',
                'background' => '#eaf3ff',
                'color' => '#3b82f6',
                'url' => '/admin/pemabayan-admin',
            ],
            [
                'label' => 'Pembayaran Pending',
                'value' => number_format($pembayaranPending ?? 0, 0, ',', '.'),
                'icon' => 'fas fa-receipt',
                'background' => '#fff0ef',
                'color' => '#ef4444',
                'url' => '/admin/pemabayan-admin',
            ],
            [
                'label' => 'Dokumen Pending',
                'value' => number_format($dokumenPending ?? 0, 0, ',', '.'),
                'icon' => 'far fa-file-alt',
                'background' => '#fff8df',
                'color' => '#eab308',
                'url' => '/admin/dokumen',
            ],
            [
                'label' => 'Paket Umrah',
                'value' => number_format($totalPaket ?? 0, 0, ',', '.'),
                'icon' => 'fas fa-kaaba',
                'background' => '#edf7ff',
                'color' => '#0ea5e9',
                'url' => '/paket-umrah',
            ],
            [
                'label' => 'Jadwal Terdekat',
                'value' => number_format($nearKeberangkatan?->count() ?? 0, 0, ',', '.'),
                'icon' => 'far fa-calendar-check',
                'background' => '#edf8f3',
                'color' => '#10b981',
                'url' => '/keberangkatan',
            ],
        ];
    @endphp

    <style>
        .adash {
            background: #fbfaf8;
            min-height: calc(100vh - 72px);
            padding: 22px
        }

        .acard {
            background: #fff;
            border: 1px solid #eee8dd;
            border-radius: 10px;
            box-shadow: 0 6px 22px rgba(44, 31, 17, .05)
        }

        .ahead {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: center;
            margin-bottom: 20px
        }

        .ahead h2 {
            font-size: 23px;
            font-weight: 800;
            margin: 2px 0;
            color: #1f2937
        }

        .ahead small {
            color: #6b7280
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px
        }

        .summary-box {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px;
            text-decoration: none !important;
            color: inherit;
            min-height: 118px
        }

        .summary-icon {
            width: 64px;
            height: 64px;
            flex: 0 0 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: var(--icon-background);
            color: var(--icon-color);
        }

        .summary-icon i {
            color: inherit;
            font-size: 16px;
        }

        .summary-content {
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .summary-label {
            color: #171717;
            font-size: 16px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 6px;
        }

        .summary-value {
            color: #171717;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
        }

        .summary-value-sm {
            font-size: 18px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px
        }

        .section-head h5 {
            font-size: 16px;
            font-weight: 800;
            margin: 0
        }

        .section-head a {
            font-size: 12px;
            color: #9b6819;
            font-weight: 700
        }

        .chart-wrap {
            height: 260px
        }

        .departure-item {
            display: grid;
            grid-template-columns: 64px 1fr auto;
            gap: 12px;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0ece5
        }

        .departure-item:last-child {
            border-bottom: 0
        }

        .departure-item img {
            width: 64px;
            height: 48px;
            border-radius: 6px;
            object-fit: cover
        }

        .pill {
            border-radius: 20px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800
        }

        .pill.green {
            background: #e6f6e9;
            color: #229543
        }

        .pill.blue {
            background: #e8f1ff;
            color: #276bd1
        }

        .pill.orange {
            background: #fff1df;
            color: #bf7414
        }

        .mini-table {
            width: 100%
        }

        .mini-table th {
            font-size: 11px;
            color: #6b7280;
            padding: 10px;
            border-bottom: 1px solid #eee
        }

        .mini-table td {
            font-size: 12px;
            padding: 11px 10px;
            border-bottom: 1px solid #f1eee8;
            vertical-align: middle
        }

        .status-chip {
            border-radius: 6px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 800
        }

        .chip-warn {
            background: #fff0d8;
            color: #c47a10
        }

        .chip-ok {
            background: #e6f6e9;
            color: #229543
        }

        .chip-bad {
            background: #ffe4e4;
            color: #d04444
        }

        .notif {
            display: flex;
            gap: 11px;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f0ece5
        }

        .notif:last-child {
            border-bottom: 0
        }

        .notif i {
            margin-top: 2px
        }

        .notif b {
            font-size: 12px
        }

        .notif small {
            display: block;
            color: #6b7280
        }

        .muted-box {
            text-align: center;
            color: #9ca3af;
            padding: 28px
        }

        @media(max-width:1200px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media(max-width:700px) {
            .adash {
                padding: 14px
            }

            .summary-grid {
                grid-template-columns: 1fr
            }

            .departure-item {
                grid-template-columns: 52px 1fr
            }

            .departure-item .pill {
                grid-column: 2
            }

            .ahead {
                align-items: flex-start
            }

            .mini-table {
                min-width: 680px
            }
        }
    </style>

    <div class="content-wrapper adash">
        <x-page-heading
            :title="'Dashboard ' . $roleTitle"
            description="Pantau ringkasan operasional dan aktivitas terbaru Sawdeera Tour."
            current="Ringkasan"
        />

        <div class="summary-grid">
            @foreach ($summaryItems as $item)
                <a class="acard summary-box" href="{{ $item['url'] }}">
                    <span
                        class="summary-icon"
                        style="
                            --icon-background: {{ $item['background'] }};
                            --icon-color: {{ $item['color'] }};
                        "
                    >
                        <i class="{{ $item['icon'] }}"></i>
                    </span>

                    <div class="summary-content">
                        <span class="summary-label">
                            {{ $item['label'] }}
                        </span>

                        <strong class="summary-value {{ strlen($item['value']) > 11 ? 'summary-value-sm' : '' }}">
                            {{ $item['value'] }}
                        </strong>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="row">
            <div class="col-xl-6 mb-3">
                <div class="acard p-4 h-100">
                    <div class="section-head">
                        <h5>Grafik Pendaftaran Jemaah</h5><small>6 Bulan Terakhir</small>
                    </div>
                    <div class="chart-wrap"><canvas id="registrationChart"></canvas></div>
                </div>
            </div>
            <div class="col-xl-6 mb-3">
                <div class="acard p-4 h-100">
                    <div class="section-head">
                        <h5>Jadwal Keberangkatan Terdekat</h5><a href="/keberangkatan">Lihat Semua <i
                                class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                    @forelse($nearKeberangkatan as $k)
                        <div class="departure-item">
                            <img src="{{ asset('img/thumb1.jpg') }}" alt="Keberangkatan">
                            <div>
                                <b>{{ $k->paket?->nama_paket ?? 'Paket Umrah' }}</b><br><small>{{ $k->tanggal_keberangkatan?->translatedFormat('d F Y') ?? '-' }}</small>
                            </div>
                            <span
                                class="pill {{ $k->status === 'aktif' ? 'green' : ($k->status === 'draft' ? 'blue' : 'orange') }}">{{ ucfirst(str_replace('_', ' ', $k->status ?? 'Persiapan')) }}</span>
                        </div>
                    @empty
                        <div class="muted-box">Belum ada jadwal keberangkatan terdekat.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6 mb-3">
                <div class="acard p-4 h-100">
                    <div class="section-head">
                        <h5>Jemaah Terbaru</h5><a href="/jemaah/data-verifikasi">Lihat Semua <i
                                class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Jemaah</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Status Verifikasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentJemaah as $j)
                                    @php $status = $j->status_data ?? 'belum_lengkap'; @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><b>{{ $j->user?->name ?? '-' }}</b></td>
                                        <td>{{ $j->created_at?->translatedFormat('d M Y') }}</td>
                                        <td><span
                                                class="status-chip {{ $status === 'terverifikasi' ? 'chip-ok' : ($status === 'perlu_perbaikan' ? 'chip-bad' : 'chip-warn') }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td><a class="btn btn-sm btn-light"
                                                href="/jemaah/data-verifikasi/{{ $j->user_id }}"><i
                                                    class="far fa-eye"></i></a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada data jemaah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 mb-3">
                <div class="acard p-4 h-100">
                    <div class="section-head">
                        <h5>Notifikasi Sistem</h5><a href="{{ route('notifications.index') }}">Lihat Semua <i
                                class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                    @forelse($recentDocs as $doc)
                        <div class="notif">
                            <i class="fas fa-info-circle text-primary"></i>
                            <div><b>Dokumen {{ strtoupper(str_replace('_', ' ', $doc->jenis_dokumen)) }} milik
                                    {{ $doc->jemaah?->user?->name ?? 'jemaah' }}
                                    {{ $doc->status === 'diproses' ? 'menunggu verifikasi' : $doc->status }}.</b><small>{{ $doc->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="muted-box">Belum ada notifikasi sistem.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const registrationCtx = document.getElementById('registrationChart');
            new Chart(registrationCtx, {
                type: 'line',
                data: {
                    labels: ['Aktif', 'Tidak Aktif', 'Proses'],
                    datasets: [{
                        label: 'Jemaah',
                        data: [{{ $akunAktif ?? 0 }}, {{ $akunTidakAktif ?? 0 }},
                            {{ $akunProsesVerifikasi ?? 0 }}
                        ],
                        borderColor: '#a96f13',
                        backgroundColor: 'rgba(169,111,19,.12)',
                        fill: true,
                        tension: .35,
                        pointBackgroundColor: '#a96f13'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
