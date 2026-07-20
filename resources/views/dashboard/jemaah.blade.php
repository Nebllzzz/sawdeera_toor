@extends('layouts.main')

@section('title', 'Dashboard Jemaah')

@section('content')
@php
    $package = $kJ?->paketUmrah;
    $schedule = $kJ?->keberangkatan;
    $requiredTotal = count($docStatus);
    $paymentStatus = $paymentComplete
        ? 'diverifikasi'
        : ($latestPayment?->tahapan?->contains('status', 'ditolak')
            ? 'ditolak'
            : ($latestPayment?->tahapan?->whereIn('status', ['diproses', 'diverifikasi'])->isNotEmpty()
                ? 'diproses'
                : 'belum_upload'));

    $paymentLabel = [
        'belum_upload' => 'Belum Lunas',
        'diproses' => 'Sedang Diverifikasi',
        'diverifikasi' => 'Lunas',
        'ditolak' => 'Ditolak',
    ][$paymentStatus] ?? ucfirst(str_replace('_', ' ', $paymentStatus));

    $docLabel = $rejectedCount > 0
        ? 'Perlu Revisi'
        : ($completeCount === $requiredTotal ? 'Lengkap' : ($uploadedCount > 0 ? 'Sedang Diverifikasi' : 'Belum Lengkap'));

    $docStepLabel = $rejectedCount > 0
        ? 'Perlu Revisi'
        : ($completeCount === $requiredTotal ? 'Selesai' : ($uploadedCount > 0 ? 'Sedang Diverifikasi' : 'Belum Selesai'));

    $paymentStepLabel = match ($paymentStatus) {
        'diverifikasi' => 'Selesai',
        'diproses' => 'Sedang Diverifikasi',
        'ditolak' => 'Ditolak',
        default => 'Belum Selesai',
    };

    $steps = [
        [
            'Registrasi Akun',
            'Selesai',
            true,
            'fas fa-user-check',
        ],
        [
            'Pilih Paket Umrah',
            $package ? 'Selesai' : 'Belum Selesai',
            (bool) $package,
            'fas fa-kaaba',
        ],
        [
            'Lengkapi Data Diri',
            $jemaah->status_data === 'terverifikasi'
                ? 'Selesai'
                : ($jemaah->status_data === 'menunggu_verifikasi' ? 'Sedang Diverifikasi' : 'Belum Selesai'),
            $jemaah->status_data === 'terverifikasi',
            'fas fa-id-card',
        ],
        [
            'Upload Dokumen Pendukung',
            $docStepLabel,
            $completeCount === $requiredTotal,
            'fas fa-file-upload',
        ],
        [
            'Upload Bukti Pembayaran',
            $paymentStepLabel,
            $paymentComplete,
            'fas fa-wallet',
        ],
        [
            'Verifikasi Approval Admin',
            $paymentComplete && $jemaah->status_data === 'terverifikasi' && $completeCount === $requiredTotal ? 'Selesai' : 'Belum Selesai',
            $paymentComplete && $jemaah->status_data === 'terverifikasi' && $completeCount === $requiredTotal,
            'fas fa-shield-alt',
        ],
    ];
@endphp

<style>
    .jdash {
        background: #fbfaf8;
        min-height: calc(100vh - 96px);
        padding: 22px;
    }

    .jcard {
        background: #fff;
        border: 1px solid #eee8dd;
        border-radius: 10px;
        box-shadow: 0 6px 22px rgba(44, 31, 17, .05);
    }

    .jhead {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: center;
        margin-bottom: 20px;
    }

    .jhead h2 {
        font-size: 22px;
        font-weight: 800;
        margin: 2px 0;
        color: #1f2937;
    }

    .jhead small {
        color: #6b7280;
    }

    .avatar-mini {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #7a4f13;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    /*
    |--------------------------------------------------------------------------
    | Kolom utama dashboard
    |--------------------------------------------------------------------------
    */

    .dashboard-main-column {
        display: flex;
        flex-direction: column;
        gap: 14px;
        height: 100%;
    }

    /*
    |--------------------------------------------------------------------------
    | Progress
    |--------------------------------------------------------------------------
    */

    .progress-panel {
        padding: 20px;
    }

    .progress-layout {
        display: grid;
        grid-template-columns: 150px minmax(0, 1fr);
        gap: 22px;
        align-items: center;
    }

    .ring {
        width: 128px;
        height: 128px;
        border-radius: 50%;
        background:
            conic-gradient(
                #a86d08 calc(var(--p) * 1%),
                #f1e3c9 0
            );
        display: grid;
        place-items: center;
    }

    .ring-inner {
        width: 94px;
        height: 94px;
        border-radius: 50%;
        background: #fff;
        display: grid;
        place-items: center;
        text-align: center;
    }

    .ring b {
        font-size: 30px;
    }

    .ring small {
        display: block;
        color: #6b7280;
    }

    .steps {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 8px;
        align-items: start;
    }

    .step {
        text-align: center;
        position: relative;
        min-width: 0;
    }

    .step::before {
        content: "";
        position: absolute;
        top: 20px;
        left: -50%;
        width: 100%;
        height: 2px;
        background: #dcc59c;
    }

    .step:first-child::before {
        display: none;
    }

    .step.done::before {
        background: #9c6508;
    }

    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f1f1;
        color: #a1a1aa;
        border: 2px solid #e5e7eb;
        position: relative;
        z-index: 1;
    }

    .step.done .step-icon {
        background: #9c6508;
        color: #fff;
        border-color: #9c6508;
    }

    .step b {
        display: block;
        font-size: 11px;
        margin-top: 8px;
        color: #1f2937;
        line-height: 1.2;
    }

    .step small {
        display: block;
        font-size: 10px;
        color: #9ca3af;
        line-height: 1.25;
        margin-top: 3px;
    }

    .step.done small {
        color: #2fa24c;
    }

    .step:not(.done):nth-child(4) small,
    .step:not(.done):nth-child(5) small {
        color: #c47a10;
    }

    .notice {
        margin-top: 18px;
        background: #fff6e6;
        border-radius: 7px;
        padding: 12px;
        text-align: center;
        color: #7a5a1b;
        font-size: 13px;
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi paket
    |--------------------------------------------------------------------------
    */

    .info-card {
        padding: 16px;
        height: 100%;
    }

    .info-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 14px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 26px minmax(0, 1fr);
        gap: 8px;
        margin-bottom: 9px;
    }

    .info-row i {
        color: #b17613;
    }

    .info-row small {
        display: block;
        color: #6b7280;
    }

    .info-row b {
        display: block;
        font-size: 12px;
    }

    /*
    |--------------------------------------------------------------------------
    | Card ringkasan
    |--------------------------------------------------------------------------
    */

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin: 0;
    }

    .stat-card {
        padding: 17px;
        min-height: 136px;
        display: flex;
        flex-direction: column;
    }

    .stat-card .icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 10px;
    }

    .stat-card h5 {
        font-size: 15px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .stat-card p {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 12px;
    }

    .outline-btn {
        display: block;
        text-align: center;
        border: 1px solid #b88735;
        border-radius: 6px;
        padding: 8px;
        color: #8a5b16;
        font-weight: 700;
        font-size: 12px;
        text-decoration: none !important;
        transition: .18s ease;
    }

    .stat-card .outline-btn {
        margin-top: auto;
    }

    .outline-btn:hover {
        background: #b17613;
        border-color: #b17613;
        color: #fff;
    }

    /*
    |--------------------------------------------------------------------------
    | Menu cepat
    |--------------------------------------------------------------------------
    */

    .quick-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
    }

    .quick-card {
        border-radius: 9px;
        padding: 15px 10px;
        text-align: center;
        color: #1f2937;
        font-weight: 700;
        font-size: 11px;
        text-decoration: none !important;
        min-height: 92px;
    }

    .quick-card i {
        display: block;
        font-size: 24px;
        margin-bottom: 10px;
    }

    /*
    |--------------------------------------------------------------------------
    | Notifikasi
    |--------------------------------------------------------------------------
    */

    .notif-item {
        display: flex;
        gap: 10px;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 8px;
        background: #f8fbff;
    }

    .notif-item i {
        margin-top: 3px;
    }

    .notif-item b {
        font-size: 12px;
    }

    .notif-item small {
        display: block;
        color: #6b7280;
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1199.98px) {
        .progress-layout {
            grid-template-columns: 1fr;
        }

        .steps {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            row-gap: 20px;
        }

        .step::before {
            display: none;
        }

        .stat-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .quick-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .stat-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .jdash {
            padding: 14px;
        }

        .jhead {
            align-items: flex-start;
        }

        .steps {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .quick-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="content-wrapper jdash">

    {{-- ============================================================
        PROGRESS, RINGKASAN, DAN INFORMASI PAKET
    ============================================================= --}}
    <div class="row align-items-stretch">

        {{-- Kolom kiri --}}
        <div class="col-xl-9 mb-3">
            <div class="dashboard-main-column">

                {{-- Progress pendaftaran --}}
                <div class="jcard progress-panel">
                    <h5 class="font-weight-bold mb-4">
                        Progress Pendaftaran Umrah
                    </h5>

                    <div class="progress-layout">

                        {{-- Persentase --}}
                        <div>
                            <div
                                class="ring mx-auto"
                                style="--p: {{ $percent }}"
                            >
                                <div class="ring-inner">
                                    <div>
                                        <b>{{ $percent }}%</b>
                                        <small>Selesai</small>
                                    </div>
                                </div>
                            </div>

                            <small class="d-block text-center mt-3">
                                {{ collect($steps)->filter(fn ($step) => $step[2])->count() }}
                                dari 6 tahap selesai
                            </small>
                        </div>

                        {{-- Tahapan --}}
                        <div>
                            <div class="steps">
                                @foreach($steps as $step)
                                    <div class="step {{ $step[2] ? 'done' : '' }}">
                                        <span class="step-icon">
                                            <i class="{{ $step[2] ? 'fas fa-check' : $step[3] }}"></i>
                                        </span>

                                        <b>{{ $step[0] }}</b>
                                        <small>{{ $step[1] }}</small>
                                    </div>
                                @endforeach
                            </div>

                            <div class="notice">
                                <i class="far fa-clock mx-2"></i>

                                {{ $missingCount > 0
                                    ? 'Silakan lengkapi dokumen pendukung. Dokumen yang sudah diunggah sedang diverifikasi admin.'
                                    : 'Dokumen pendukung Anda sudah lengkap.'
                                }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tiga card langsung berada di bawah progress --}}
                <div class="stat-grid">

                    {{-- Paket umrah --}}
                    <div class="jcard stat-card">
                        <span
                            class="icon"
                            style="background:#f0e9ff;color:#6f42c1"
                        >
                            <i class="fas fa-globe"></i>
                        </span>

                        <h5>
                            {{ $package->nama_paket ?? 'Belum Ada Paket' }}
                        </h5>

                        <p>
                            Keberangkatan
                            {{ $schedule?->tanggal_keberangkatan?->translatedFormat('d F Y') ?? '-' }}
                        </p>

                        <a
                            href="/paket-umrah-jemaah"
                            class="outline-btn"
                        >
                            Lihat Detail Paket
                        </a>
                    </div>

                    {{-- Pembayaran --}}
                    <div class="jcard stat-card">
                        <span
                            class="icon"
                            style="background:#e7f8ee;color:#159447"
                        >
                            <i class="fas fa-wallet"></i>
                        </span>

                        <h5>{{ $paymentLabel }}</h5>

                        <p>
                            Total Pembayaran Rp
                            {{ number_format(
                                $latestPayment->total_tagihan ?? 0,
                                0,
                                ',',
                                '.'
                            ) }}
                        </p>

                        <a
                            href="/pemabayan"
                            class="outline-btn"
                        >
                            Lihat Detail Pembayaran
                        </a>
                    </div>

                    {{-- Dokumen --}}
                    <div class="jcard stat-card">
                        <span
                            class="icon"
                            style="background:#e9f3ff;color:#226bd2"
                        >
                            <i class="fas fa-folder-open"></i>
                        </span>

                        <h5>{{ $docLabel }}</h5>

                        <p>
                            {{ $uploadedCount }} dari
                            {{ $requiredTotal }} dokumen terupload
                        </p>

                        <a
                            href="/dokumen"
                            class="outline-btn"
                        >
                            Lihat Dokumen
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- Kolom kanan --}}
        <div class="col-xl-3 mb-3">
            <div class="jcard info-card h-100">
                <h6 class="font-weight-bold">
                    Informasi Paket Anda
                </h6>

                <img
                    src="{{ asset('img/thumb1.jpg') }}"
                    alt="Paket Umrah"
                >

                <div class="info-row">
                    <i class="far fa-clipboard"></i>

                    <div>
                        <small>Nama Paket</small>
                        <b>
                            {{ $package->nama_paket ?? 'Belum memilih paket' }}
                        </b>
                    </div>
                </div>

                <div class="info-row">
                    <i class="fas fa-plane"></i>

                    <div>
                        <small>Maskapai</small>
                        <b>
                            {{ $schedule?->maskapaiBerangkat?->nama ?? '-' }}
                        </b>
                    </div>
                </div>

                <div class="info-row">
                    <i class="far fa-calendar"></i>

                    <div>
                        <small>Keberangkatan</small>
                        <b>
                            {{ $schedule?->tanggal_keberangkatan?->translatedFormat('d F Y') ?? 'Belum ditentukan' }}
                        </b>
                    </div>
                </div>

                <a
                    class="outline-btn mt-12"
                    href="/paket-umrah-jemaah"
                >
                    Lihat Detail
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================================
        MENU CEPAT DAN NOTIFIKASI
    ============================================================= --}}
    <div class="row">

        {{-- Menu cepat --}}
        <div class="col-xl-8 mb-3">
            <div class="jcard p-3">
                <h6 class="font-weight-bold mb-3">
                    Menu Cepat
                </h6>

                <div class="quick-grid">
                    <a class="quick-card" style="background:#f4edff;color:#7047bf" href="/pendaftaran-saya">
                        <i class="far fa-user" style="color:#8b5cf6"></i>
                        Lengkapi Data Diri
                    </a>

                    <a class="quick-card" style="background:#e9f4ff;color:#226bd2" href="/dokumen">
                        <i class="far fa-folder-open" style="color:#3b82f6"></i>
                        Upload Dokumen Pendukung
                    </a>

                    <a class="quick-card" style="background:#e9f8ef;color:#16924a" href="/pemabayan">
                        <i class="far fa-credit-card" style="color:#22c55e"></i>
                        Upload Bukti Pembayaran
                    </a>

                    <a class="quick-card" style="background:#fff7df;color:#b77900" href="/status-verifikasi">
                        <i class="fas fa-shield-alt" style="color:#eab308"></i>
                        Status Verifikasi
                    </a>

                    <a class="quick-card" style="background:#fff0f0;color:#d24b4b" href="/keberangkatan-jemaah">
                        <i class="far fa-calendar-alt" style="color:#ef4444"></i>
                        Jadwal Keberangkatan
                    </a>

                    <a class="quick-card" style="background:#f6efe6;color:#8a5b16" href="{{ route('profile') }}">
                        <i class="far fa-user-circle" style="color:#c0841a"></i>
                        Kelola Profil
                    </a>
                </div>
            </div>
        </div>

        {{-- Notifikasi --}}
        <div class="col-xl-4 mb-3">
            <div class="jcard p-3">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">
                        Notifikasi Terbaru
                    </h6>

                    <a
                        href="{{ route('notifications.index') }}"
                        class="small text-muted"
                    >
                        Lihat Semua
                    </a>
                </div>

                @forelse($recentDocs as $doc)
                    <div class="notif-item">
                        <i class="fas fa-info-circle text-primary"></i>

                        <div>
                            <b>
                                Dokumen
                                {{ strtoupper(str_replace('_', ' ', $doc->jenis_dokumen)) }}
                                {{ $doc->status === 'diproses'
                                    ? 'sedang diverifikasi'
                                    : $doc->status
                                }}.
                            </b>

                            <small>
                                {{ $doc->updated_at->translatedFormat('d M Y H:i') }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-muted small py-3">
                        Belum ada notifikasi terbaru.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
