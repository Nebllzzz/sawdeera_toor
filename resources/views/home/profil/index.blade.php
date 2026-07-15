@extends('layouts.main')

@section('title', 'Pendaftaran Saya')

@section('content')

    @php
        $user = auth()->user();
        $j = $user->jemaah;

        $status = $j->status_data ?? 'belum_lengkap';

        $statusMap = [
            'belum_lengkap' => ['secondary', 'Belum Lengkap', 'Lengkapi seluruh data dan dokumen di bawah ini.'],
            'menunggu_verifikasi' => [
                'warning',
                'Menunggu Verifikasi',
                'Data Anda sudah diajukan dan sedang diperiksa admin.',
            ],
            'terverifikasi' => ['success', 'Terverifikasi', 'Data pendaftaran Anda telah disetujui admin.'],
            'perlu_perbaikan' => ['danger', 'Perlu Perbaikan', 'Silakan perbaiki data sesuai catatan admin.'],
        ];

        [$statusColor, $statusLabel, $statusText] = $statusMap[$status];
    @endphp

    <div class="content-wrapper registration-page px-3">

        <section class="content py-4">

            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="font-weight-bold mb-1">Pendaftaran Saya</h2>
                        <small class="text-muted">Dashboard &nbsp;›&nbsp; Pendaftaran Saya</small>
                    </div>
                    <a href="/dashboard" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-2"></i>Kembali ke
                        Dashboard</a>
                </div>

                @if (session('berhasil'))
                    <div class="alert alert-success">
                        {{ session('berhasil') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Data belum dapat disimpan.</strong>
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (!$hasDeparture)
                    <div class="empty-payment"><i class="fas fa-receipt"></i>
                        <h4>Belum ada rencana pembayaran</h4>
                        <p>Pilih paket dan ajukan keberangkatan terlebih dahulu.</p><a href="/paket-umrah-jemaah"
                            class="btn-gold">Pilih Paket Umrah</a>
                    </div>
                @else
                    <div class="status-card mb-4">

                        <div>
                            <small class="text-muted font-weight-bold">
                                STATUS VERIFIKASI DATA DIRI
                            </small>

                            <h4 class="text-{{ $statusColor }} font-weight-bold mb-1">
                                {{ $statusLabel }}
                            </h4>

                            <span class="text-muted">
                                {{ $statusText }}
                            </span>
                        </div>

                        @if ($status === 'perlu_perbaikan')
                            <div class="admin-note">

                                <strong>Catatan Admin</strong>

                                <p class="mb-1">
                                    {{ $j->catatan_admin }}
                                </p>

                                @if ($j->diverifikasi_pada)
                                    <small>
                                        Diperiksa {{ $j->diverifikasi_pada->format('d M Y H:i') }}
                                    </small>
                                @endif

                            </div>
                        @endif

                    </div>

                    <form method="POST" action="{{ route('registration.update') }}">
                        @csrf
                        <fieldset @disabled(!$hasDeparture)>

                            <div class="row">

                                <div class="col-xl-9">

                                    <div class="form-card">

                                        <h4 class="font-weight-bold mb-1">
                                            Form Pendaftaran
                                        </h4>

                                        <p class="text-muted mb-4">
                                            Pastikan seluruh data diisi sesuai dokumen resmi.
                                        </p>

                                        @include('home.profil.partials.section', [
                                            'title' => 'A. Data Pribadi',
                                            'section' => 'personal',
                                        ])

                                        @include('home.profil.partials.section', [
                                            'title' => 'B. Data Kontak',
                                            'section' => 'contact',
                                        ])

                                        @include('home.profil.partials.section', [
                                            'title' => 'C. Data Paspor',
                                            'section' => 'passport',
                                        ])

                                        @include('home.profil.partials.section', [
                                            'title' => 'D. Data Kesehatan',
                                            'section' => 'health',
                                        ])

                                        @if ($status !== 'terverifikasi')
                                            <div class="text-right pt-3 border-top">
                                                <button class="btn btn-save px-4" @disabled($status === 'menunggu_verifikasi' || !$hasDeparture)>
                                                    <i class="fas fa-save mr-2"></i>
                                                    {{ $status === 'menunggu_verifikasi' ? 'Menunggu Verifikasi' : 'Simpan Perubahan' }}
                                                </button>
                                            </div>
                                        @endif

                                    </div>

                                </div>

                                <div class="col-xl-3">

                                    <div class="help-card">

                                        <h6>
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Panduan Pengisian
                                        </h6>

                                        <ul>
                                            <li>Isi sesuai KTP dan paspor.</li>
                                            <li>Gunakan hasil scan yang jelas.</li>
                                            <li>Periksa kembali sebelum menyimpan.</li>
                                        </ul>

                                    </div>

                                    <div class="help-card">

                                        <h6>
                                            <i class="fas fa-headset mr-2"></i>
                                            Butuh Bantuan?
                                        </h6>

                                        <p class="text-muted mb-0">
                                            Hubungi tim Sawdeera Tour jika mengalami kendala.
                                        </p>

                                    </div>

                                </div>

                            </div>
                        </fieldset>

                    </form>
                @endif
            </div>

        </section>

    </div>

    <style>
        .registration-page {
            background: #fbf8f2 !important;
            padding: 0;
        }

        .registration-page .content {
            padding: 0;
        }

        .status-card,
        .form-card,
        .help-card,
        .empty-payment {
            background: #fff;
            border: 1px solid #eee7dc;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(78, 53, 28, .06);
        }

        .status-card {
            padding: 22px;
            display: flex;
            gap: 30px;
            justify-content: space-between;
            align-items: center;
        }

        .admin-note {
            background: #fff1f1;
            border: 1px solid #f2cece;
            border-radius: 8px;
            padding: 14px;
            max-width: 520px;
            color: #9d3030;
        }

        .form-card {
            padding: 26px;
        }

        .help-card {
            padding: 20px;
            margin-bottom: 18px;
        }

        .help-card h6 {
            font-weight: 700;
            color: #5c3a1a;
        }

        .help-card li {
            margin-bottom: 12px;
        }

        .section-heading {
            color: #5c3a1a;
            font-weight: 700;
            border-bottom: 1px solid #eee7dc;
            padding: 18px 0 10px;
            margin-bottom: 18px;
        }

        .form-control {
            border-radius: 7px;
            min-height: 42px;
            border-color: #dedbd5;
        }

        .required:after {
            content: " *";
            color: #dc3545;
        }

        .btn-save {
            min-height: 44px;
            border-radius: 8px;
        }

            background: #b9862c;
            color: #fff;
            border-radius: 7px;
        }

        .btn-save:hover {
            background: #966a20;
            color: #fff;
        }

        .upload-box {
            border: 1px dashed #d6c2a4;
            border-radius: 14px;
            background: #fffdf8;
            padding: 24px;
        }

        .preview-avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            overflow: hidden;
            background: #f1e7d8;
            border: 4px solid #fff;
            box-shadow: 0 4px 14px rgba(78, 53, 28, .15);
        }

        .preview-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8B5A2B;
            font-size: 38px;
        }

        .file-upload-card {
            border: 1px dashed #d6c2a4;
            background: #fffdf8;
            border-radius: 12px;
            padding: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .btn-outline-brown {
            border: 1px solid #b9862c;
            color: #8B5A2B;
            background: #fff;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-outline-brown:hover {
            background: #b9862c;
            color: #fff;
        }

        .empty-payment {
            text-align: center;
            padding: 70px
        }

        .empty-payment i {
            font-size: 45px;
            color: #bd8120;
            margin-bottom: 15px
        }

        .btn-outline-gold,
        .btn-gold {
            border: 1px solid #c68b2c;
            color: #a66d12;
            background: #fff;
            padding: 10px 15px;
            border-radius: 7px;
            font-weight: 600
        }

        .btn-gold {
            background: #bd8120;
            color: #fff
        }

        @media(max-width: 767px) {
            .status-card {
                display: block;
            }

            .admin-note {
                margin-top: 15px;
            }

            .form-card {
                padding: 18px;
            }

            .file-upload-card {
                display: block;
            }

            .file-upload-card .btn {
                margin-top: 14px;
                width: 100%;
            }
        }
    </style>

    <script>
        function previewImage(input, targetId) {
            const file = input.files[0];

            if (!file) {
                return;
            }

            const preview = document.getElementById(targetId);
            const fallback = document.getElementById(targetId + 'Fallback');

            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');

            if (fallback) {
                fallback.classList.add('d-none');
            }
        }
    </script>

@endsection
