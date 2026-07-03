@extends('layouts.main')
@section('title', 'Dashboard Admin')

@section('content')

    <div class="content-wrapper px-3 py-4">

        <style>
            :root {
                --brown: #6B3E20;
                --brown-dark: #4A2B17;
                --gold: #D6A25A;
                --cream: #FFF8EE;
            }

            .dashboard-card {
                background: #fff;
                border-radius: 20px;
                box-shadow: 0 4px 18px rgba(0, 0, 0, .05);
                border: none;
            }

            .summary-card {
                position: relative;
                overflow: hidden;
                padding: 22px;
                border-radius: 20px;
                background: linear-gradient(135deg, #ffffff, #fff8f1);
                height: 100%;
            }

            .summary-card .icon {
                width: 55px;
                height: 55px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 22px;
            }

            .summary-title {
                font-size: 14px;
                color: #8a8a8a;
                margin-bottom: 5px;
            }

            .summary-value {
                font-size: 26px;
                font-weight: 700;
                color: var(--brown-dark);
                line-height: 1.2;
            }

            .activity-item {
                border-bottom: 1px dashed #e7d9c5;
                padding-bottom: 12px;
                margin-bottom: 12px;
            }

            .activity-item:last-child {
                border: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }

            .mini-badge {
                font-size: 11px;
                padding: 5px 10px;
                border-radius: 30px;
            }

            .section-title {
                font-weight: 700;
                color: var(--brown-dark);
            }

            .empty-state {
                text-align: center;
                padding: 30px 15px;
                color: #999;
            }

            .quick-box {
                background: #fff8f0;
                border-radius: 14px;
                padding: 14px;
            }

            .departure-card {
                background: #fffaf5;
                border-radius: 16px;
                padding: 15px;
                height: 100%;
            }
        </style>

        {{-- HEADER --}}
        <div class="dashboard-card p-4 mb-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center">

                <div>

                    <h3 class="font-weight-bold mb-1">
                        Selamat Datang, {{ auth()->user()->name }}
                    </h3>

                    <div class="text-muted">
                        Ringkasan operasional aplikasi Sawdeera Toor
                    </div>

                </div>

            </div>

        </div>

        {{-- SUMMARY --}}
        <div class="row">

            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ url('/jemaah') }}" class="text-decoration-none">
                    <div class="summary-card dashboard-card h-100" style="cursor:pointer;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="summary-title">Total Jemaah</div>
                                <div class="summary-value">{{ $totalJemaah ?? 0 }}</div>
                            </div>

                            <div class="icon" style="background:linear-gradient(135deg,#4E73DF,#224ABE);">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ url('/admin/pemabayan-admin') }}" class="text-decoration-none">
                    <div class="summary-card dashboard-card h-100" style="cursor:pointer;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="summary-title">Total Pembayaran</div>
                                <div class="summary-value" style="font-size:20px;">
                                    Rp {{ number_format($pembayaranTotal ?? 0, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="icon" style="background:linear-gradient(135deg,#1CC88A,#13855C);">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ url('/admin/pemabayan-admin') }}" class="text-decoration-none">
                    <div class="summary-card dashboard-card h-100" style="cursor:pointer;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="summary-title">Pembayaran Pending</div>
                                <div class="summary-value">{{ $pembayaranPending ?? 0 }}</div>
                            </div>

                            <div class="icon" style="background:linear-gradient(135deg,#F6C23E,#D39E00);">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ url('/admin/dokumen') }}" class="text-decoration-none">
                    <div class="summary-card dashboard-card h-100" style="cursor:pointer;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="summary-title">Dokumen Pending</div>
                                <div class="summary-value">{{ $dokumenPending ?? 0 }}</div>
                            </div>

                            <div class="icon" style="background:linear-gradient(135deg,#E74A3B,#BE2617);">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        {{-- SECOND SUMMARY --}}
        <div class="row">

            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ url('/paket-umrah') }}" class="text-decoration-none">
                    <div class="summary-card dashboard-card h-100" style="cursor:pointer;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="summary-title">Paket Umrah</div>
                                <div class="summary-value">{{ $totalPaket ?? 0 }}</div>
                            </div>

                            <div class="icon" style="background:linear-gradient(135deg,#6F42C1,#4B2E83);">
                                <i class="fas fa-kaaba"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ url('/hotel') }}" class="text-decoration-none">
                    <div class="summary-card dashboard-card h-100" style="cursor:pointer;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="summary-title">Hotel</div>
                                <div class="summary-value">{{ $totalHotel ?? 0 }}</div>
                            </div>

                            <div class="icon" style="background:linear-gradient(135deg,#36B9CC,#258391);">
                                <i class="fas fa-hotel"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ url('/tour-leader') }}" class="text-decoration-none">
                    <div class="summary-card dashboard-card h-100" style="cursor:pointer;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="summary-title">Tour Leader</div>
                                <div class="summary-value">{{ $totalTourLeader ?? 0 }}</div>
                            </div>

                            <div class="icon" style="background:linear-gradient(135deg,#F39C12,#D68910);">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ url('/maskapai') }}" class="text-decoration-none">
                    <div class="summary-card dashboard-card h-100" style="cursor:pointer;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="summary-title">Maskapai</div>
                                <div class="summary-value">{{ $totalMaskapai ?? 0 }}</div>
                            </div>

                            <div class="icon" style="background:linear-gradient(135deg,#e315ba,#be179d);">
                                <i class="fas fa-plane"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        {{-- ANALYTICS CHART --}}
        <div class="row">

            {{-- STATUS DOKUMEN --}}
            <div class="col-lg-4 mb-4">

                <div class="dashboard-card p-4 h-100">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="section-title mb-1">
                                Statistik Dokumen
                            </h5>

                            <small class="text-muted">
                                Ringkasan status dokumen jemaah
                            </small>

                        </div>

                    </div>

                    <canvas id="dokumenChart" height="250"></canvas>

                </div>

            </div>

            {{-- STATISTIK AKUN JEMAAH --}}
            <div class="col-lg-8 mb-4">

                <div class="dashboard-card p-4 h-100">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="section-title mb-1">
                                Statistik Akun Jemaah
                            </h5>

                            <small class="text-muted">
                                Status aktivitas akun jemaah
                            </small>

                        </div>

                    </div>

                    <canvas id="akunJemaahChart" height="120"></canvas>

                </div>

            </div>

        </div>

        <div class="row">

            {{-- LEFT --}}
            <div class="col-lg-8">

                {{-- RECENT ACTIVITY --}}
                <div class="dashboard-card p-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <h5 class="section-title mb-0">
                            Aktivitas Terbaru
                        </h5>

                    </div>

                    @php
                        $recentDocs = \App\Models\DokumenJemaah::latest()->limit(6)->get();
                    @endphp

                    @forelse($recentDocs as $d)
                        <div class="activity-item">

                            <div class="font-weight-bold">

                                {{ optional($d->jemaah->user)->name ?? 'Jemaah' }}

                            </div>

                            <div class="text-muted small">

                                Upload dokumen
                                <b>{{ strtoupper($d->jenis_dokumen) }}</b>

                                •

                                {{ $d->updated_at->diffForHumans() }}

                            </div>

                        </div>

                    @empty

                        <div class="empty-state">

                            <i class="fas fa-folder-open fa-2x mb-3"></i>

                            <div>
                                Belum ada aktivitas terbaru.
                            </div>

                        </div>
                    @endforelse

                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-lg-4">

                {{-- QUICK VERIFICATION --}}
                <div class="dashboard-card p-4 mb-4">

                    <h5 class="section-title mb-4">
                        Verifikasi Cepat
                    </h5>

                    @php
                        $pendingPayments = \App\Models\Pembayaran::where('status', 'diproses')
                            ->latest()
                            ->limit(5)
                            ->get();
                    @endphp

                    @forelse($pendingPayments as $p)
                        <div class="quick-box mb-3">

                            <div class="d-flex justify-content-between align-items-center">

                                <div>

                                    <div class="font-weight-bold">

                                        Rp {{ number_format($p->jumlah, 0, ',', '.') }}

                                    </div>

                                    <div class="small text-muted">

                                        Calon Jemaah

                                    </div>

                                </div>

                                <a href="/admin/pemabayan-admin" class="btn btn-sm btn-outline-dark">

                                    Lihat

                                </a>

                            </div>

                        </div>

                    @empty

                        <div class="empty-state">

                            <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>

                            <div>
                                Tidak ada pembayaran pending.
                            </div>

                        </div>
                    @endforelse

                </div>

                {{-- TOP PAKET --}}
                <div class="dashboard-card p-4">

                    <h5 class="section-title mb-4">
                        Paket Umrah Terpopuler
                    </h5>

                    @forelse($topPaket as $pak)
                        <div class="activity-item">

                            <div class="font-weight-bold">

                                {{ $pak->nama_paket ?? 'Paket Umrah' }}

                            </div>

                            <div class="text-muted small">

                                {{ $pak->jemaah_count ?? 0 }}
                                jemaah terdaftar

                            </div>

                        </div>

                    @empty

                        <div class="empty-state">

                            <i class="fas fa-box-open fa-2x mb-3"></i>

                            <div>
                                Belum ada data paket umrah.
                            </div>

                        </div>
                    @endforelse

                </div>

            </div>

        </div>

        {{-- KEBERANGKATAN --}}
        <div class="dashboard-card p-4 mt-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h5 class="section-title mb-0">
                    Keberangkatan Terdekat
                </h5>

            </div>

            <div class="row">

                @forelse($nearKeberangkatan as $k)
                    <div class="col-lg-6 col-md-6 mb-3">

                        <div class="departure-card">

                            <div class="font-weight-bold mb-2">

                                {{ optional($k->tanggal_keberangkatan)->format('d F Y') ?? '-' }} -
                                {{ optional($k->tanggal_pulang)->format('d F Y') ?? '-' }}

                            </div>

                            <div class="text-muted small mb-1">

                                <i class="fas fa-user-tie mr-1"></i>

                                {{ optional($k->leader)->nama ?? 'Belum ada leader' }}

                            </div>

                            <div class="text-muted small">

                                <i class="fas fa-plane mr-1"></i>

                                {{ optional($k->maskapaiBerangkat)->nama ?? 'Belum ada maskapai' }} (Berangkat) -
                                {{ optional($k->maskapaiPulang)->nama ?? 'Belum ada maskapai' }} (Pulang)

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="col-12">

                        <div class="empty-state">

                            <i class="fas fa-plane-slash fa-2x mb-3"></i>

                            <div>
                                Belum ada jadwal keberangkatan terdekat.
                            </div>

                        </div>

                    </div>
                @endforelse

            </div>

        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <script>
                /*
                    |--------------------------------------------------------------------------
                    | DOKUMEN CHART
                    |--------------------------------------------------------------------------
                    */

                const dokumenCtx = document.getElementById('dokumenChart');

                new Chart(dokumenCtx, {

                    type: 'doughnut',

                    data: {

                        labels: [
                            'Diproses',
                            'Diverifikasi',
                            'Ditolak'
                        ],

                        datasets: [{

                            data: [
                                {{ $dokumenStatus['diproses'] ?? 0 }},
                                {{ $dokumenStatus['diverifikasi'] ?? 0 }},
                                {{ $dokumenStatus['ditolak'] ?? 0 }}
                            ],

                            backgroundColor: [
                                '#F6C23E',
                                '#1CC88A',
                                '#E74A3B'
                            ],

                            borderWidth: 0

                        }]

                    },

                    options: {

                        responsive: true,

                        plugins: {

                            legend: {
                                position: 'bottom'
                            }

                        }

                    }

                });
                /*
                |--------------------------------------------------------------------------
                | AKUN JEMAAH CHART
                |--------------------------------------------------------------------------
                */

                const akunCtx = document.getElementById('akunJemaahChart');

                new Chart(akunCtx, {

                    type: 'bar',

                    data: {

                        labels: [
                            'Aktif',
                            'Tidak Aktif',
                            'Proses Verifikasi',
                        ],

                        datasets: [{

                            data: [
                                {{ $akunAktif ?? 0 }},
                                {{ $akunTidakAktif ?? 0 }},
                                {{ $akunProsesVerifikasi ?? 0 }},
                            ],

                            backgroundColor: [
                                '#4E73DF',
                                '#D39E00',
                                '#8E5EA2',
                            ],

                            borderRadius: 10,

                            borderSkipped: false,

                            barThickness: 35

                        }]

                    },

                    options: {

                        responsive: true,

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
