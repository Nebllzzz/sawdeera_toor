@extends('layouts.main')
@section('title', 'Detail Data Jemaah')
@section('content')
    @php $j = $user->jemaah; @endphp
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <x-page-heading
                    title="Detail Data Jemaah"
                    :description="'Periksa data lengkap milik ' . $user->name . '.'"
                    section="Verifikasi Data Jemaah"
                    current="Detail"
                >
                    <x-slot:actions>
                        <a href="/jemaah/data-verifikasi" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                    </x-slot:actions>
                </x-page-heading>
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Informasi Pribadi</h3>
                            </div>
                            <div class="card-body detail-grid">
                                <div><small>Nama</small><b>{{ $user->name }}</b></div>
                                <div><small>Email</small><b>{{ $user->email }}</b></div>
                                <div><small>NIK</small><b>{{ $j->nik ?? '-' }}</b></div>
                                <div><small>No Telepon</small><b>{{ $j->no_telepon ?? '-' }}</b></div>
                                <div><small>Jenis Kelamin</small><b>{{ $j->jenis_kelamin ?? '-' }}</b></div>
                                <div><small>Tempat/Tanggal Lahir</small><b>{{ $j->tempat_lahir ?? '-' }},
                                        {{ $j->tanggal_lahir?->format('d M Y') ?? '-' }}</b></div>
                                <div><small>Status Pernikahan</small><b>{{ $j->status_pernikahan ?? '-' }}</b></div>
                                <div><small>Pekerjaan</small><b>{{ $j->pekerjaan ?? '-' }}</b></div>
                                <div class="grid-full"><small>Alamat</small><b>{{ $j->alamat ?? '-' }}</b></div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Paspor, Kontak & Kesehatan</h3>
                            </div>
                            <div class="card-body detail-grid">
                                <div><small>Nomor Paspor</small><b>{{ $j->nomor_paspor ?? '-' }}</b></div>
                                <div><small>Tempat Penerbitan</small><b>{{ $j->tempat_penerbitan_paspor ?? '-' }}</b></div>
                                <div><small>Tanggal
                                        Terbit</small><b>{{ $j->tanggal_terbit_paspor?->format('d M Y') ?? '-' }}</b></div>
                                <div>
                                    <small>Kedaluwarsa</small><b>{{ $j->tanggal_kedaluwarsa_paspor?->format('d M Y') ?? '-' }}</b>
                                </div>
                                <div><small>Kontak Darurat</small><b>{{ $j->kontak_darurat ?? '-' }}</b></div>
                                <div><small>Hubungan</small><b>{{ $j->hubungan_kontak_darurat ?? '-' }}</b></div>
                                <div><small>Golongan Darah</small><b>{{ $j->golongan_darah ?? '-' }}</b></div>
                                <div><small>Alergi</small><b>{{ $j->alergi ?? '-' }}</b></div>
                                <div class="grid-full"><small>Riwayat
                                        Penyakit</small><b>{{ $j->riwayat_penyakit ?? '-' }}</b></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Aksi Verifikasi</h3>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-success btn-block mb-2 status-data w-100"
                                    data-status="terverifikasi">Verifikasi Data
                                </button>
                                <button type="button" class="btn btn-danger btn-block w-100" id="btnRevisi">
                                    Minta Revisi
                                </button>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Riwayat Verifikasi</h3>
                            </div>
                            <div class="card-body timeline-box">
                                <div class="timeline-item"><b>Data
                                        dibuat</b><small>{{ $j->created_at?->translatedFormat('d M Y H:i') }}</small>
                                    <p>Data jemaah mulai dilengkapi.</p>
                                </div>
                                @forelse($logs as $log)
                                    <div class="timeline-item">
                                        <b>{{ ucfirst(str_replace('_', ' ', $log->status_after)) }}</b><small>{{ $log->created_at?->translatedFormat('d M Y H:i') }}
                                            oleh {{ $log->actor?->name ?? 'Sistem' }}</small>
                                        <p>{{ $log->note }}</p>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">Belum ada log verifikasi.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="modalRevisi">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formRevisi">@csrf
                    <div class="modal-header">
                        <h5>Catatan Revisi</h5><button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <textarea name="catatan_admin" class="form-control" rows="4" required
                            placeholder="Tulis informasi yang harus diperbaiki jemaah"></textarea>
                    </div>
                    <div class="modal-footer"><button class="btn btn-warning">Kirim Revisi</button></div>
                </form>
            </div>
        </div>
    </div>
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px
        }

        .detail-grid small {
            display: block;
            color: #777
        }

        .detail-grid b {
            display: block
        }

        .grid-full {
            grid-column: 1/-1
        }

        .timeline-item {
            border-left: 3px solid #ecd3ab;
            padding: 0 0 18px 16px
        }

        .timeline-item b,
        .timeline-item small {
            display: block
        }

        .timeline-item small {
            color: #777
        }

        .timeline-item p {
            margin: 0
        }

        @media(max-width:768px) {
            .detail-grid {
                grid-template-columns: 1fr
            }
        }
    </style>
    @push('scripts')
        <script>
            $('.status-data').click(function() {
                updateStatus('terverifikasi', null);
            });
            $('#formRevisi').submit(function(e) {
                e.preventDefault();
                updateStatus('perlu_perbaikan', $(this).find('[name=catatan_admin]').val());
            });

            function updateStatus(status, note) {
                $.post('/jemaah/toggle-data/{{ $user->id }}', {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        status_data: status,
                        catatan_admin: note
                    })
                    .done(r => Swal.fire('Berhasil', r.message, 'success').then(() => location.reload()))
                    .fail(x => Swal.fire('Gagal', x.responseJSON?.message || 'Status gagal diperbarui', 'error'));
            }

            $(document).ready(function () {

                $('#btnRevisi').on('click', function () {
                    $('#modalRevisi').modal('show');
                });

            });
        </script>
    @endpush
@endsection
