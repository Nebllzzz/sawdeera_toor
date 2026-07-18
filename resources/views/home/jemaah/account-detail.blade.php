@extends('layouts.main')
@section('title', 'Detail Registrasi Akun')
@section('content')
    @php
        $labels = ['proses' => 'Menunggu Verifikasi', 'aktif' => 'Aktif', 'tidak_aktif' => 'Tidak Aktif'];
    @endphp
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <x-page-heading
                    title="Detail Registrasi Akun"
                    description="Periksa data registrasi akun calon jemaah sebelum melakukan verifikasi."
                    section="Verifikasi Registrasi Akun"
                    current="Detail Akun"
                >
                    <x-slot:actions>
                        <div class="status-card"><small>Status Akun</small>
                            <h5>{{ $labels[$user->status] ?? ucfirst($user->status) }}</h5>
                        </div>
                    </x-slot:actions>
                </x-page-heading>

                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Informasi Akun</h3>
                            </div>
                            <div class="card-body info-list">
                                <div><span>Nama Lengkap</span><b>{{ $user->name }}</b></div>
                                <div><span>Email</span><b>{{ $user->email }}</b></div>
                                <div><span>Nomor Telepon</span><b>{{ $user->jemaah?->no_telepon ?? '-' }}</b></div>
                                <div><span>Tanggal
                                        Registrasi</span><b>{{ $user->created_at?->translatedFormat('d M Y H:i') }} WIB</b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 mb-4">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Aksi</h3>
                            </div>
                            <div class="card-body">
                                <button class="btn {{ $user->status === 'aktif' ? 'btn-danger' : 'btn-success' }} btn-block"
                                    id="toggleAccount">
                                    {{ $user->status === 'aktif' ? 'Nonaktifkan Akun Jemaah' : 'Aktifkan Akun Jemaah' }}
                                </button>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Riwayat Registrasi</h3>
                            </div>
                            <div class="card-body timeline-box">
                                <div class="timeline-item"><b>Registrasi
                                        Akun</b><small>{{ $user->created_at?->translatedFormat('d M Y H:i') }} WIB</small>
                                    <p>Akun berhasil didaftarkan oleh calon jemaah.</p>
                                </div>
                                @forelse($logs as $log)
                                    <div class="timeline-item">
                                        <b>{{ ucfirst(str_replace('_', ' ', $log->status_after)) }}</b><small>{{ $log->created_at?->translatedFormat('d M Y H:i') }}
                                            WIB oleh {{ $log->actor?->name ?? 'Sistem' }}</small>
                                        <p>{{ $log->note }}</p>
                                    </div>
                                @empty
                                    <div class="timeline-item"><b>Status Saat
                                            Ini</b><small>{{ $labels[$user->status] ?? $user->status }}</small>
                                        <p>Akun menunggu tindakan admin.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <style>
        .status-card,
        .card {
            border-radius: 10px
        }

        .status-card {
            background: #fff;
            border: 1px solid #eee;
            padding: 14px 20px;
            min-width: 240px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .04)
        }

        .status-card small {
            display: block;
            color: #777
        }

        .status-card h5 {
            margin: 5px 0 0;
            color: #d58a16;
            font-weight: 800
        }

        .info-list>div {
            display: grid;
            grid-template-columns: 180px 1fr;
            padding: 14px 0;
            border-bottom: 1px solid #eee
        }

        .info-list span {
            color: #666
        }

        .info-list b {
            font-weight: 700
        }

        .timeline-box {
            position: relative
        }

        .timeline-item {
            border-left: 3px solid #ecd3ab;
            padding: 0 0 22px 18px
        }

        .timeline-item b,
        .timeline-item small {
            display: block
        }

        .timeline-item small {
            color: #777;
            margin: 4px 0
        }

        .timeline-item p {
            margin: 0;
            color: #555
        }
    </style>
    @push('scripts')
        <script>
            $('#toggleAccount').click(function() {
                Swal.fire({
                        title: 'Ubah status akun?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya'
                    })
                    .then(r => {
                        if (!r.isConfirmed) return;
                        $.post('/jemaah/toggle/{{ $user->id }}', {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            })
                            .done(x => Swal.fire('Berhasil', x.message, 'success').then(() => location.reload()))
                            .fail(x => Swal.fire('Gagal', x.responseJSON?.message || 'Status gagal diubah',
                                'error'));
                    });
            });
        </script>
    @endpush
@endsection
