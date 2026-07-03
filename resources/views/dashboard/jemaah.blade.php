@extends('layouts.main')

@section('title', 'Dashboard Jemaah')

@section('content')

    <style>
        :root {
            --primary-brown: #6B3E20;
            --secondary-brown: #8B5A2B;
            --soft-cream: #FFF8EE;
            --card-bg: #FFFFFF;
            --gold: #D6A25A;
            --text-dark: #2F2F2F;
        }

        .content-wrapper {
            padding: 20px;
        }

        .dashboard-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            border: none;
            overflow: hidden;
        }

        .hero-section {
            background: linear-gradient(135deg, #FFF6E8, #FFFFFF);
            border-radius: 22px;
            padding: 30px;
            position: relative;
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .welcome-subtitle {
            color: #777;
            font-size: 15px;
        }

        .payment-highlight {
            background: linear-gradient(135deg, var(--primary-brown), #4D2B12);
            color: white;
            border-radius: 18px;
            padding: 20px;
            min-width: 260px;
        }

        .payment-highlight .label {
            font-size: 13px;
            opacity: .85;
        }

        .payment-highlight .status {
            font-size: 24px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .summary-card {
            padding: 20px;
            height: 100%;
        }

        .summary-icon {
            width: 55px;
            height: 55px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }

        .summary-title {
            font-size: 14px;
            color: #777;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.4;
        }

        .summary-desc {
            font-size: 13px;
            color: #888;
        }

        .progress-custom {
            width: 100%;
            height: 22px;
            background: #F1E3CF;
            border-radius: 30px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #D6A25A, #6B3E20);
            border-radius: 30px;
            min-width: 45px;
            font-size: 12px;
        }

        .quick-action {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: .2s ease;
            text-decoration: none !important;
            color: inherit;
            height: 100%;
        }

        .quick-action:hover {
            transform: translateY(-2px);
            color: inherit;
        }

        .quick-action-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .timeline {
            position: relative;
            padding-left: 20px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 5px;
            width: 2px;
            height: 100%;
            background: #E5D6C3;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -19px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-brown);
        }

        .timeline-title {
            font-weight: 600;
            color: var(--text-dark);
        }

        .timeline-subtitle {
            font-size: 13px;
            color: #888;
        }

        @media(max-width:768px) {

            .hero-section {
                padding: 22px;
            }

            .payment-highlight {
                margin-top: 20px;
                width: 100%;
                min-width: 100%;
            }

            .welcome-title {
                font-size: 1.6rem;
            }
        }
    </style>

    <div class="content-wrapper">

        {{-- HERO --}}
        <div class="dashboard-card hero-section mb-4">

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start">

                <div>

                    <h1 class="welcome-title">
                        Selamat Datang, {{ $user->name }}
                    </h1>

                    <div class="welcome-subtitle">
                        {{ now()->translatedFormat('d F Y') }}
                        •
                        {{ optional(optional($kJ)->paketUmrah)->nama_paket ?? 'Belum memilih paket' }}
                    </div>

                    @if (!is_null($countdown))

                        @if ($countdown > 0)

                            <div class="mt-3 font-weight-bold">
                                ✈️ Keberangkatan dalam {{ $countdown }} hari lagi
                            </div>

                        @elseif ($countdown == 0)

                            <div class="mt-3 font-weight-bold text-success">
                                ✈️ Hari ini adalah jadwal keberangkatan Anda
                            </div>

                        @else

                            <div class="mt-3 font-weight-bold text-muted">
                                ✈️ Jadwal keberangkatan telah berlalu
                            </div>

                        @endif

                    @endif

                </div>

                <div class="payment-highlight mt-4 mt-lg-0">

                    <div class="label">
                        Status Pembayaran
                    </div>

                    <div class="status">
                        {{ $latestPayment->status ?? 'Belum Upload' }}
                    </div>

                    <div class="small mt-2">
                        {{ $latestPayment->jenis_pembayaran ?? '-' }}
                        •
                        {{ $latestPayment->metode_pembayaran ?? '-' }}
                    </div>

                </div>

            </div>

        </div>

        {{-- SUMMARY --}}
        <div class="row">

            {{-- PROFILE --}}
            <div class="col-lg-3 col-md-6 mb-4">

                <div class="dashboard-card summary-card">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <div class="summary-title">
                                Profile
                            </div>

                            <div class="summary-value">
                                Data Diri Lengkap
                            </div>

                            <div class="summary-desc">
                                Registrasi berhasil dilakukan
                            </div>
                        </div>

                        <div class="summary-icon" style="background:linear-gradient(135deg,#4CAF50,#2E7D32);">
                            <i class="fas fa-user-check"></i>
                        </div>

                    </div>

                </div>

            </div>

            {{-- DOKUMEN --}}
            <div class="col-lg-3 col-md-6 mb-4">

                <div class="dashboard-card summary-card">

                    @php
                        $pending = $missingCount;
                        $rejected = $rejectedCount;
                    @endphp

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="summary-title">
                                Status Dokumen
                            </div>

                            <div class="summary-value">

                                @if ($rejected > 0)
                                    Dokumen perlu revisi
                                @elseif($pending > 0)
                                    {{ $pending }} dokumen perlu dilengkapi
                                @else
                                    Semua dokumen lengkap
                                @endif

                            </div>

                            <div class="summary-desc">
                                KTP • Paspor • Visa • Vaksin
                            </div>

                        </div>

                        <div class="summary-icon" style="background:linear-gradient(135deg,#F4A62A,#D97B00);">
                            <i class="fas fa-file-alt"></i>
                        </div>

                    </div>

                </div>

            </div>

            {{-- PEMBAYARAN --}}
            <div class="col-lg-3 col-md-6 mb-4">

                <div class="dashboard-card summary-card">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="summary-title">
                                Pembayaran
                            </div>

                            <div class="summary-value">
                                {{ ucfirst($latestPayment->status ?? 'Belum Upload') }}
                            </div>

                            <div class="summary-desc">
                                Rp {{ number_format($latestPayment->jumlah ?? 0, 0, ',', '.') }}
                            </div>

                        </div>

                        <div class="summary-icon" style="background:linear-gradient(135deg,#4B7BEC,#1E4DB7);">
                            <i class="fas fa-wallet"></i>
                        </div>

                    </div>

                </div>

            </div>

            {{-- KEBERANGKATAN --}}
            <div class="col-lg-3 col-md-6 mb-4">

                <div class="dashboard-card summary-card">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="summary-title">
                                Jadwal Keberangkatan
                            </div>

                            <div class="summary-value">
                                {{ $kJ?->keberangkatan?->tanggal_keberangkatan?->format('d M Y') ?? '-' }}
                            </div>

                            <div class="summary-desc">
                                {{ $kJ?->paketUmrah?->nama_paket ?? '-' }}
                            </div>

                        </div>

                        <div class="summary-icon" style="background:linear-gradient(135deg,#B65B2A,#7A3618);">
                            <i class="fas fa-plane-departure"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- PROGRESS --}}
        <div class="dashboard-card p-4 mb-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <div>
                    <h5 class="mb-1">
                        Progress Kelengkapan Data
                    </h5>

                    <small class="text-muted">
                        Data Diri, Dokumen yang Teferivikasi, dan Pembayaran yang Teferivikasi
                    </small>
                </div>

                <div class="font-weight-bold">
                    {{ $percent }}%
                </div>

            </div>

            <div class="progress-custom position-relative">

                <div class="progress-bar d-flex align-items-center justify-content-center text-white font-weight-bold"
                    style="width: {{ $percent }}%; transition: .5s ease;">

                    {{ $percent }}%

                </div>

            </div>

        </div>

        <div class="row">

            {{-- LEFT --}}
            <div class="col-lg-8">

                {{-- QUICK ACTION --}}
                <div class="dashboard-card p-4 mb-4">

                    <h5 class="mb-4">
                        Akses Cepat
                    </h5>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <a href="/dokumen" class="quick-action">

                                <div class="quick-action-icon" style="background:#F4A62A;">
                                    <i class="fas fa-upload"></i>
                                </div>

                                <div>
                                    <div class="font-weight-bold">
                                        Upload Dokumen
                                    </div>

                                    <small class="text-muted">
                                        Lengkapi dokumen persyaratan
                                    </small>
                                </div>

                            </a>
                        </div>

                        <div class="col-md-6 mb-3">
                            <a href="/pemabayan" class="quick-action">

                                <div class="quick-action-icon" style="background:#4CAF50;">
                                    <i class="fas fa-money-check-alt"></i>
                                </div>

                                <div>
                                    <div class="font-weight-bold">
                                        Upload Pembayaran
                                    </div>

                                    <small class="text-muted">
                                        Upload bukti pembayaran
                                    </small>
                                </div>

                            </a>
                        </div>

                        <div class="col-md-6 mb-3">
                            <a href="/paket" class="quick-action">

                                <div class="quick-action-icon" style="background:#4B7BEC;">
                                    <i class="fas fa-box-open"></i>
                                </div>

                                <div>
                                    <div class="font-weight-bold">
                                        Paket Umrah
                                    </div>

                                    <small class="text-muted">
                                        Lihat detail paket perjalanan
                                    </small>
                                </div>

                            </a>
                        </div>

                        <div class="col-md-6 mb-3">
                            <a href="/keberangkatan-jemaah" class="quick-action">

                                <div class="quick-action-icon" style="background:#B65B2A;">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>

                                <div>
                                    <div class="font-weight-bold">
                                        Jadwal Saya
                                    </div>

                                    <small class="text-muted">
                                        Informasi jadwal keberangkatan
                                    </small>
                                </div>

                            </a>
                        </div>

                    </div>

                </div>

                {{-- RECENT ACTIVITY --}}
                <div class="dashboard-card p-4 mb-4">

                    <h5 class="mb-4">
                        Aktivitas Terbaru
                    </h5>

                    <div class="timeline">

                        @if ($recentDocs->count() > 0 || $recentPayments->count() > 0)

                            {{-- DOKUMEN --}}
                            @foreach ($recentDocs as $d)

                                <div class="timeline-item">

                                    <div class="timeline-title">
                                        Upload {{ strtoupper($d->jenis_dokumen) }}
                                    </div>

                                    <div class="timeline-subtitle">

                                        Status:
                                        {{ ucfirst($d->status) }}

                                        •

                                        {{ $d->updated_at->diffForHumans() }}

                                    </div>

                                </div>

                            @endforeach


                            {{-- PEMBAYARAN --}}
                            @foreach ($recentPayments as $p)

                                <div class="timeline-item">

                                    <div class="timeline-title">

                                        Pembayaran
                                        {{ $p->jenis_pembayaran }}

                                        •

                                        Rp {{ number_format($p->jumlah, 0, ',', '.') }}

                                    </div>

                                    <div class="timeline-subtitle">

                                        Status:
                                        {{ ucfirst($p->status) }}

                                        •

                                        {{ $p->updated_at->diffForHumans() }}

                                    </div>

                                </div>

                            @endforeach

                        @else

                            <div class="text-center py-4">

                                <i class="fas fa-inbox fa-2x text-muted mb-3"></i>

                                <div class="text-muted">
                                    Belum ada aktivitas terbaru.
                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-lg-4">

                {{-- CHART --}}
                <div class="dashboard-card p-4 mb-4">

                    <h5 class="mb-4">
                        Statistik Dokumen Yang Sudah Teferivikasi
                    </h5>

                    <canvas id="docChart" height="220"></canvas>

                </div>

                {{-- ALERT --}}
                <div class="dashboard-card p-4">

                    <h5 class="mb-4">
                        Notifikasi Pintar
                    </h5>

                    {{-- DOKUMEN --}}
                    @if ($missingCount > 0)
                        <div class="alert alert-warning">
                            Anda masih memiliki
                            <b>{{ $missingCount }} dokumen</b>
                            yang belum dilengkapi.
                        </div>
                    @elseif($rejectedCount > 0)
                        <div class="alert alert-danger">
                            Ada dokumen yang ditolak dan perlu diperbaiki kembali.
                        </div>
                    @else
                        <div class="alert alert-success">
                            Semua dokumen berhasil dilengkapi oleh Anda.
                        </div>
                    @endif


                    {{-- PEMBAYARAN --}}
                    @if ($latestPayment == null)
                        <div class="alert alert-info">
                            Anda belum mengupload pembayaran.
                        </div>
                    @elseif($latestPayment->status === 'ditolak')
                        <div class="alert alert-danger">
                            Pembayaran ditolak:
                            {{ $latestPayment->keterangan_penolakan }}
                        </div>
                    @elseif($latestPayment->status === 'diproses')
                        <div class="alert alert-warning">
                            Pembayaran sedang menunggu proses verifikasi admin.
                        </div>
                    @elseif($latestPayment->status === 'diverifikasi')
                        <div class="alert alert-success">
                            Pembayaran berhasil diverifikasi.
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const ctx = document.getElementById('docChart');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['KTP', 'Paspor', 'Visa', 'Vaksin'],
                    datasets: [{
                        data: [
                            {{ ($docStatus['ktp'] ?? '') === 'diverifikasi' ? 1 : 0 }},
                            {{ ($docStatus['paspor'] ?? '') === 'diverifikasi' ? 1 : 0 }},
                            {{ ($docStatus['visa'] ?? '') === 'diverifikasi' ? 1 : 0 }},
                            {{ ($docStatus['vaksin'] ?? '') === 'diverifikasi' ? 1 : 0 }}
                        ],
                        backgroundColor: [
                            '#E8B56A',
                            '#D89B42',
                            '#C8743C',
                            '#9C5227'
                        ],
                        borderRadius: 8
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
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    @endpush

@endsection
