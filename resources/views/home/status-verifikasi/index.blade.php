@extends('layouts.main')
@section('title', 'Status Verifikasi')
@section('content')
    @php
        $stateIcons = [
            'verified' => 'fa-check',
            'processing' => 'fa-clock',
            'rejected' => 'fa-exclamation',
            'waiting' => 'fa-ellipsis-h',
        ];
        $applicationLabels = ['pendaftaran' => 'Pendaftaran', 'setuju' => 'Jadwal Berlaku', 'reschedule' => 'Penjadwalan Ulang'];
        $applicationStatus = $pengajuan?->status;
        $bottomLabel = $applicationStatus
            ? $applicationLabels[$applicationStatus] ?? ucwords(str_replace('_', ' ', $applicationStatus))
            : 'Belum Mengajukan';
    @endphp
    <div class="content-wrapper verification-page px-3">
        <section class="content py-4">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h2 class="font-weight-bold mb-1">Status Verifikasi Pendaftaran</h2>
                        <small class="text-muted">Dashboard &nbsp;›&nbsp; Status Verifikasi</small>
                    </div>
                        <a href="/dashboard" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mx-2"></i>Kembali ke Dashboard</a>
                </div>

                <div class="row mt-4">
                    <div class="col-xl-9">
                        <div class="success-banner {{ $complete ? 'complete' : '' }}"><i
                                class="fas {{ $complete ? 'fa-check-circle' : 'fa-info-circle' }}"></i>
                            <div>
                                <b>{{ $complete ? 'Seluruh verifikasi telah selesai!' : 'Pendaftaran Anda berhasil dikirim!' }}</b><small>{{ $complete ? 'Semua tahapan pendaftaran telah terpenuhi.' : 'Berikut status verifikasi data pendaftaran Anda. Perubahan status akan dikirim melalui notifikasi.' }}</small>
                            </div>
                        </div>

                        <div class="flow-card progress-flow-card">
                            <div class="progress-summary">
                                <div class="progress-ring" style="--progress: {{ $progressPercent }};">
                                    <span>{{ $progressPercent }}%</span>
                                    <small>Selesai</small>
                                </div>
                                <div>
                                    <h5>Progress Pendaftaran Umrah</h5>
                                    <p class="mb-0">{{ $completedCount }} dari 6 tahap selesai</p>
                                </div>
                            </div>
                            <div class="flow-linear six-steps">
                                @include('home.status-verifikasi.partials.node', [
                                    'number' => 1,
                                    'title' => 'Registrasi Akun',
                                    'step' => $steps['account'],
                                    'icon' => 'fa-user-plus',
                                ])
                                <span class="connector {{ $steps['account']['state'] === 'verified' ? 'done' : '' }}"></span>
                                @include('home.status-verifikasi.partials.node', [
                                    'number' => 2,
                                    'title' => 'Pilih Paket Umrah',
                                    'step' => $steps['package'],
                                    'icon' => 'fa-cube',
                                ])
                                <span class="connector {{ $steps['package']['state'] === 'verified' ? 'done' : '' }}"></span>
                                @include('home.status-verifikasi.partials.node', [
                                    'number' => 3,
                                    'title' => 'Lengkapi Data Diri',
                                    'step' => $steps['profile'],
                                    'icon' => 'fa-id-card',
                                ])
                                <span class="connector {{ $steps['profile']['state'] === 'verified' ? 'done' : '' }}"></span>
                                @include('home.status-verifikasi.partials.node', [
                                    'number' => 4,
                                    'title' => 'Upload Dokumen Pendukung',
                                    'step' => $steps['documents'],
                                    'icon' => 'fa-file-alt',
                                ])
                                <span class="connector {{ $steps['documents']['state'] === 'verified' ? 'done' : '' }}"></span>
                                @include('home.status-verifikasi.partials.node', [
                                    'number' => 5,
                                    'title' => 'Upload Bukti Pembayaran',
                                    'step' => $steps['payment'],
                                    'icon' => 'fa-receipt',
                                ])
                                <span class="connector {{ $steps['payment']['state'] === 'verified' ? 'done' : '' }}"></span>
                                @include('home.status-verifikasi.partials.node', [
                                    'number' => 6,
                                    'title' => 'Verifikasi/Approval Admin',
                                    'step' => $steps['approval'],
                                    'icon' => 'fa-shield-alt',
                                ])
                            </div>
                        </div>

                        <h6 class="section-label">Rincian Verifikasi</h6>
                        <div class="detail-list">
                            @include('home.status-verifikasi.partials.row', [
                                'number' => 1,
                                'title' => 'Registrasi Akun',
                                'subtitle' => 'Akun pengguna Sawdeera Toor.',
                                'step' => $steps['account'],
                                'icon' => 'fa-user-plus',
                                'url' => null,
                            ])
                            @include('home.status-verifikasi.partials.row', [
                                'number' => 2,
                                'title' => 'Pilih Paket Umrah',
                                'subtitle' => $pengajuan
                                    ? $pengajuan->paketUmrah->nama_paket
                                    : 'Paket umrah yang Anda pilih.',
                                'step' => $steps['package'],
                                'icon' => 'fa-cube',
                                'url' => '/paket-umrah-jemaah',
                            ])
                            @include('home.status-verifikasi.partials.row', [
                                'number' => 3,
                                'title' => 'Lengkapi Data Diri',
                                'subtitle' => 'Data pribadi dan identitas diri Anda.',
                                'step' => $steps['profile'],
                                'icon' => 'fa-id-card',
                                'url' => '/pendaftaran-saya',
                            ])
                            @include('home.status-verifikasi.partials.row', [
                                'number' => 4,
                                'title' => 'Upload Dokumen Pendukung',
                                'subtitle' => (($jemaah->status_pernikahan ?? null) === 'menikah' ? 'Enam' : 'Lima') . ' dokumen persyaratan keberangkatan.',
                                'step' => $steps['documents'],
                                'icon' => 'fa-file-alt',
                                'url' => '/dokumen',
                            ])
                            @include('home.status-verifikasi.partials.row', [
                                'number' => 5,
                                'title' => 'Upload Bukti Pembayaran',
                                'subtitle' => 'Bukti pembayaran paket umrah.',
                                'step' => $steps['payment'],
                                'icon' => 'fa-receipt',
                                'url' => '/pemabayan',
                            ])
                            @include('home.status-verifikasi.partials.row', [
                                'number' => 6,
                                'title' => 'Verifikasi/Approval Admin',
                                'subtitle' => 'Approval data, dokumen, dan pembayaran.',
                                'step' => $steps['approval'],
                                'icon' => 'fa-shield-alt',
                                'url' => null,
                            ])
                        </div>

                        <div class="bottom-status status-{{ $applicationStatus ?? 'none' }}"><span><i
                                    class="fas fa-clipboard-check"></i></span>
                            <div><small>Status Keberangkatan Jemaah</small>
                                <h4>{{ $bottomLabel }}</h4>
                                <p>{{ $pengajuan ? 'Status ini diambil langsung dari pengajuan keberangkatan Anda.' : 'Anda belum memiliki pengajuan pada tabel keberangkatan jemaah.' }}
                                </p>
                            </div><i class="fas fa-route decoration"></i>
                        </div>
                    </div>
                    <aside class="col-xl-3">
                        <div class="side-info">
                            <h6>Apa yang terjadi selanjutnya?</h6>
                            <div class="next-item"><i class="far fa-clock"></i><span>Tim kami akan memverifikasi data dan
                                    dokumen Anda.</span></div>
                            <div class="next-item"><i class="far fa-bell"></i><span>Anda mendapatkan notifikasi setiap ada
                                    perubahan status.</span></div>
                            <div class="next-item"><i class="far fa-check-circle"></i><span>Setelah dokumen dan pembayaran
                                    selesai, pendaftaran dinyatakan lengkap.</span></div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </div>
    <style>
        .verification-page {
            background: #faf9f7 !important
        }

        .success-banner,
        .flow-card,
        .detail-list,
        .side-info,
        .bottom-status {
            background: #fff;
            border: 1px solid #ebe8e2;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(60, 42, 20, .05)
        }

        .success-banner {
            display: flex;
            gap: 12px;
            align-items: center;
            background: #edf9f0;
            border-color: #cee8d5;
            color: #287a43;
            padding: 14px 18px
        }

        .success-banner>i {
            font-size: 20px
        }

        .success-banner b,
        .success-banner small {
            display: block
        }

        .flow-card {
            padding: 26px;
            margin: 14px 0 22px
        }

        .progress-summary {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 22px
        }

        .progress-ring {
            --progress: 0;
            width: 104px;
            height: 104px;
            border-radius: 50%;
            background: conic-gradient(#a66a0d calc(var(--progress) * 1%), #f3eadb 0);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            flex: 0 0 auto
        }

        .progress-ring:before {
            content: "";
            position: absolute;
            inset: 12px;
            background: #fff;
            border-radius: 50%
        }

        .progress-ring span,
        .progress-ring small {
            position: relative;
            z-index: 1;
            display: block
        }

        .progress-ring span {
            font-size: 28px;
            font-weight: 800;
            color: #1f1f1f
        }

        .progress-ring small {
            color: #666
        }

        .flow-linear {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            width: 100%
        }

        .flow-linear.six-steps {
            justify-content: flex-start;
            overflow-x: auto;
            padding-bottom: 8px
        }

        .connector {
            height: 2px;
            width: clamp(28px, 7vw, 110px);
            background: #ddd;
            margin-top: 25px
        }

        .connector.done {
            background: #32a85a
        }

        .verify-node {
            text-align: center;
            width: 125px;
            flex: 0 0 125px;
            position: relative
        }

        .node-icon {
            display: flex;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #f1f2f3;
            color: #69717b;
            align-items: center;
            justify-content: center;
            margin: auto;
            font-size: 19px;
            border: 1px solid #e3e3e3
        }

        .verify-node.verified .node-icon {
            background: #e2f5e8;
            color: #26904a
        }

        .verify-node.processing .node-icon {
            background: #fff0db;
            color: #c07b17
        }

        .verify-node.rejected .node-icon {
            background: #fde5e5;
            color: #bd3c3c
        }

        .verify-node b,
        .verify-node small {
            display: block
        }

        .node-number {
            color: #555;
            font-weight: 700;
            margin-top: 6px
        }

        .verify-node>b {
            font-size: 11px;
            margin: 7px 0 5px
        }

        .node-badge {
            display: inline-block !important;
            font-size: 9px;
            padding: 4px 8px;
            border-radius: 12px;
            background: #eee
        }

        .verified .node-badge {
            background: #e2f5e8;
            color: #267c43
        }

        .processing .node-badge {
            background: #fff0db;
            color: #9a6414
        }

        .rejected .node-badge {
            background: #fde5e5;
            color: #a83030
        }

        .branch-wrap {
            max-width: 500px;
            margin: 5px auto 0
        }

        .branch-line {
            width: 50%;
            height: 28px;
            border-left: 2px solid #ddd;
            border-right: 2px solid #ddd;
            border-top: 2px solid #ddd;
            margin: auto
        }

        .branch-nodes {
            display: flex;
            justify-content: space-between
        }

        .branch-merge {
            width: 50%;
            height: 28px;
            border-left: 2px solid #ddd;
            border-right: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
            margin: auto
        }

        .finish-node {
            display: flex;
            justify-content: center;
            margin-top: 0
        }

        .section-label {
            font-weight: 750
        }

        .detail-list {
            overflow: hidden
        }

        .verification-row {
            display: grid;
            grid-template-columns: 1.7fr .7fr .65fr auto;
            gap: 15px;
            align-items: center;
            padding: 14px 17px;
            border-bottom: 1px solid #eee
        }

        .row-title {
            display: flex;
            gap: 12px;
            align-items: center
        }

        .row-icon {
            display: flex;
            width: 38px;
            height: 38px;
            border-radius: 7px;
            background: #f2f0ed;
            color: #9b6818;
            align-items: center;
            justify-content: center
        }

        .row-title b,
        .row-title small {
            display: block
        }

        .row-title small {
            font-size: 10px;
            color: #777
        }

        .row-state {
            font-size: 10px;
            padding: 5px 9px;
            border-radius: 13px;
            background: #eee;
            justify-self: start
        }

        .row-state.verified {
            background: #e3f5e8;
            color: #267c43
        }

        .row-state.processing {
            background: #fff0d7;
            color: #9b6515
        }

        .row-state.rejected {
            background: #fde5e5;
            color: #aa3030
        }

        .row-date {
            font-size: 10px
        }

        .row-link {
            color: #9b6818
        }

        .row-note {
            grid-column: 1/-1;
            background: #fff0f0;
            color: #a63131;
            padding: 10px;
            border-radius: 7px;
            margin-left: 50px
        }

        .bottom-status {
            margin-top: 14px;
            padding: 17px;
            display: flex;
            align-items: center;
            gap: 13px;
            position: relative;
            overflow: hidden;
            background: #f1f6ff;
            border-color: #dbe6f7
        }

        .bottom-status>span {
            display: flex;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #4285e8;
            color: #fff;
            align-items: center;
            justify-content: center
        }

        .bottom-status small,
        .bottom-status h4 {
            display: block
        }

        .bottom-status h4 {
            color: #2872db;
            font-weight: 750;
            margin: 1px 0
        }

        .bottom-status p {
            margin: 0
        }

        .decoration {
            position: absolute;
            right: 24px;
            font-size: 45px;
            color: #9fc0ef
        }

        .status-cancel {
            background: #fff0f0
        }

        .status-cancel h4 {
            color: #b83232
        }

        .side-info {
            padding: 19px;
            margin-bottom: 17px
        }

        .side-info h6 {
            font-weight: 750;
            margin-bottom: 18px
        }

        .side-info h6 i,
        .next-item i {
            color: #bd7c18;
            margin-right: 8px
        }

        .side-info label,
        .side-info>b {
            display: block
        }

        .side-info label {
            font-size: 10px;
            color: #777;
            margin-top: 14px;
            margin-bottom: 2px
        }

        .current-status {
            display: inline-block;
            background: #fff0d7;
            color: #946013;
            padding: 5px 8px;
            border-radius: 5px;
            font-size: 10px
        }

        .next-item {
            display: flex;
            gap: 8px;
            margin: 15px 0;
            font-size: 11px
        }

        .next-item i {
            margin-top: 2px
        }

        @media(max-width:768px) {
            .connector {
                width: 45px
            }

            .branch-wrap {
                max-width: 330px
            }

            .verification-row {
                grid-template-columns: 1fr
            }

            .row-note {
                margin-left: 0
            }

            .row-date {
                display: none
            }

            .decoration {
                display: none
            }
        }
    </style>
@endsection
