@extends('layouts.main')
@section('title', 'Detail Dokumen Jemaah')
@section('content')
    @php
        $statusLabels = [
            'ditolak' => 'Perlu Perbaikan',
            'diverifikasi' => 'Terverifikasi',
            'diproses' => 'Dalam Proses',
        ];
    @endphp
    <div class="content-wrapper admin-doc-page px-3">
        <section class="content py-4">
            <div class="container-fluid">
                <x-page-heading
                    title="Detail Dokumen Jemaah"
                    :description="$jemaah->user->name . ' · ' . $jemaah->user->email"
                    section="Verifikasi Dokumen"
                    current="Detail"
                >
                    <x-slot:actions>
                        <a href="/admin/dokumen" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                        <span class="summary-badge status-{{ $summary['status'] }}">{{ $summary['label'] }}</span>
                    </x-slot:actions>
                </x-page-heading>

                <div class="jemaah-summary mb-4">
                    <div><small>Nama Lengkap</small><b>{{ $jemaah->user->name }}</b></div>
                    <div><small>Email</small><b>{{ $jemaah->user->email }}</b></div>
                    <div><small>NIK</small><b>{{ $jemaah->nik ?: '-' }}</b></div>
                    <div><small>Kelengkapan</small><b>{{ $data->count() }}/{{ count($documentTypes) }} dokumen</b></div>
                </div>

                <div class="row">
                    @foreach ($documentTypes as $type => $meta)
                        @php
                            $doc = $data->get($type);
                            $isPdf = $doc && str_ends_with(strtolower($doc->file_path), '.pdf');
                        @endphp
                        <div class="col-xl-6 mb-4">
                            <article class="admin-doc-card" id="dokumen-{{ $doc?->id }}">
                                <header>
                                    <div><span class="doc-number"><i
                                                class="fas {{ $meta['icon'] }}"></i></span><span><small>DOKUMEN</small>
                                            <h5>{{ $meta['label'] }}</h5>
                                        </span></div><span
                                        class="doc-status status-{{ $doc?->status ?? 'missing' }}">{{ $statusLabels[$doc?->status] ?? 'Belum Upload' }}</span>
                                </header>
                                @if ($doc)
                                    <div class="doc-meta"><span><small>Terakhir
                                                Diunggah</small><b>{{ $doc->updated_at->translatedFormat('d M Y H:i') }}</b></span>
                                    </div>
                                    <div class="document-preview">
                                        @if ($isPdf)
                                            <object data="{{ asset('storage/' . $doc->file_path) }}"
                                                type="application/pdf"><a href="{{ asset('storage/' . $doc->file_path) }}"
                                                    target="_blank">Buka PDF</a></object>
                                        @else
                                            <img src="{{ asset('storage/' . $doc->file_path) }}"
                                                alt="{{ $meta['label'] }}">
                                        @endif
                                    </div>
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                        class="open-file"><i class="fas fa-external-link-alt mr-1"></i>Buka dokumen ukuran
                                        penuh</a>
                                    @if ($doc->keterangan_penolakan)
                                        <div class="rejection"><b>Catatan Penolakan</b>
                                            <p>{{ $doc->keterangan_penolakan }}</p>
                                        </div>
                                    @endif
                                    @if ($doc->status === 'diproses')
                                        <div class="verify-actions"><button class="btn btn-danger reject-doc"
                                                data-id="{{ $doc->id }}">Tolak</button><button
                                                class="btn btn-success approve-doc mx-2"
                                                data-id="{{ $doc->id }}">Verifikasi</button></div>
                                    @elseif($doc->status === 'ditolak')
                                        <div class="verify-actions"><button class="btn btn-success approve-doc"
                                                data-id="{{ $doc->id }}">Verifikasi Sekarang</button></div>
                                    @else
                                        <div class="verified-info"><i class="fas fa-check-circle"></i> Diverifikasi
                                            {{ $doc->verified_at?->translatedFormat('d M Y H:i') }} oleh
                                            {{ $doc->verifier->name ?? 'Admin' }}</div>
                                    @endif
                                @else
                                    <div class="empty-doc"><i class="fas fa-folder-open"></i>
                                        <p>Jemaah belum mengunggah {{ $meta['label'] }}.</p>
                                    </div>
                                @endif
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
    <style>
        .admin-doc-page {
            background: #faf9f7 !important
        }

        .jemaah-summary,
        .admin-doc-card {
            background: #fff;
            border: 1px solid #ebe7e0;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(60, 42, 20, .05)
        }

        .jemaah-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 18px
        }

        .jemaah-summary small,
        .jemaah-summary b {
            display: block
        }

        .summary-badge,
        .doc-status {
            padding: 7px 11px;
            border-radius: 18px;
            font-size: 11px
        }

        .status-diverifikasi {
            background: #e3f5e8;
            color: #267c40
        }

        .status-diproses {
            background: #fff0d2;
            color: #966112
        }

        .status-ditolak {
            background: #fbe3e3;
            color: #a82e2e
        }

        .status-belum_lengkap,
        .status-missing {
            background: #eee;
            color: #666
        }

        .admin-doc-card {
            padding: 18px;
            height: 100%
        }

        .admin-doc-card header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 12px
        }

        .admin-doc-card header>div {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .admin-doc-card h5,
        .admin-doc-card small {
            margin: 0;
            display: block
        }

        .doc-number {
            display: flex;
            width: 40px;
            height: 40px;
            border-radius: 7px;
            background: #f3eadb;
            color: #aa7119;
            align-items: center;
            justify-content: center
        }

        .doc-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            padding: 13px 0
        }

        .doc-meta small,
        .doc-meta b {
            display: block
        }

        .doc-meta b {
            font-size: 11px
        }

        .document-preview {
            height: 310px;
            background: #f5f5f5;
            border-radius: 7px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .document-preview img,
        .document-preview object {
            width: 100%;
            height: 100%;
            object-fit: contain
        }

        .open-file {
            display: block;
            text-align: center;
            padding: 9px;
            color: #9b6819
        }

        .rejection {
            background: #fff0f0;
            color: #a42e2e;
            padding: 11px;
            border-radius: 7px
        }

        .rejection p {
            margin: 3px 0 0
        }

        .verify-actions {
            text-align: right;
            border-top: 1px solid #eee;
            margin-top: 12px;
            padding-top: 12px
        }

        .verified-info {
            background: #eaf7ed;
            color: #267c40;
            padding: 10px;
            margin-top: 12px;
            border-radius: 7px
        }

        .empty-doc {
            text-align: center;
            padding: 100px 20px;
            color: #888
        }

        .empty-doc i {
            font-size: 38px;
            margin-bottom: 12px
        }

        @media(max-width:700px) {
            .jemaah-summary {
                grid-template-columns: 1fr 1fr
            }

            .doc-meta {
                grid-template-columns: 1fr
            }
        }
    </style>
    @push('scripts')
        <script>
            $('.approve-doc').click(function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Verifikasi dokumen ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, verifikasi'
                }).then(r => {
                    if (r.isConfirmed) $.post(`/admin/dokumen/${id}/approve`, {
                            _token: $('meta[name=csrf-token]').attr('content')
                        }).done(x => Swal.fire('Berhasil', x.message, 'success').then(() => location.reload()))
                        .fail(x => Swal.fire('Gagal', x.responseJSON?.message || 'Terjadi kesalahan', 'error'));
                });
            });
            $('.reject-doc').click(function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Tolak dokumen',
                    input: 'textarea',
                    inputLabel: 'Jelaskan bagian yang harus diperbaiki',
                    inputValidator: v => !v && 'Catatan penolakan wajib diisi',
                    showCancelButton: true,
                    confirmButtonText: 'Tolak Dokumen',
                    confirmButtonColor: '#dc3545'
                }).then(r => {
                    if (r.isConfirmed) $.post(`/admin/dokumen/${id}/reject`, {
                            _token: $('meta[name=csrf-token]').attr('content'),
                            alasan: r.value
                        }).done(x => Swal.fire('Terkirim', x.message, 'success').then(() => location.reload()))
                        .fail(x => Swal.fire('Gagal', x.responseJSON?.message || 'Terjadi kesalahan', 'error'));
                });
            });
        </script>
    @endpush
@endsection
