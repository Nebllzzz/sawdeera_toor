@extends('layouts.main')
@section('title', 'Dokumen Pendukung')
@section('content')
    @php
        $colors = [
            'ditolak' => 'danger',
            'diverifikasi' => 'success',
            'diproses' => 'warning',
            'belum_lengkap' => 'secondary',
        ];
        $statusLabels = [
            'ditolak' => 'Perlu Perbaikan',
            'diverifikasi' => 'Terverifikasi',
            'diproses' => 'Dalam Proses',
        ];
    @endphp
    <div class="content-wrapper document-page px-3">
        <section class="content py-4">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="font-weight-bold mb-1">Dokumen Pendukung</h2>
                        <small class="text-muted">Dashboard &nbsp;›&nbsp; Dokumen Pendukung</small>
                    </div>
                        <a href="/dashboard" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mx-2"></i>Kembali ke Dashboard</a>
                </div>
                @if (session('success'))
                    <div class="alert alert-success mt-3">{{ session('success') }}</div>
                @endif
                @if (session('berhasil') && !session('success'))
                    <div class="alert alert-success mt-3">{{ session('berhasil') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
                @endif
                @if(!$hasDeparture || !$hasRegistration)
                    <div class="empty-payment"><i class="fas fa-receipt"></i>
                        <h4>Dokumen belum bisa diunggah</h4>
                        @if(!$hasDeparture)
                            <p>Pilih paket dan ajukan keberangkatan terlebih dahulu.</p>
                            <a href="/paket-umrah-jemaah" class="btn-gold">Pilih Paket Umrah</a>
                        @else
                            <p>Isi data diri pada menu Pendaftaran Saya terlebih dahulu.</p>
                            <a href="/pendaftaran-saya" class="btn-gold">Isi Data Diri</a>
                        @endif
                    </div>
                @else
                    <div class="verification-summary my-4 status-{{ $summary['status'] }}">
                        <div class="summary-status"><span class="summary-icon"><i
                                    class="fas {{ $summary['status'] === 'diverifikasi' ? 'fa-check' : 'fa-exclamation' }}"></i></span>
                            <div><small>Status Verifikasi Dokumen</small>
                                <h4>{{ $summary['label'] }}</h4>
                                <p>{{ $summary['text'] }}</p>
                            </div>
                        </div>
                        @if ($summary['latest'])
                            <div class="admin-note"><b><i class="fas fa-exclamation-circle mx-1"></i> Catatan Admin</b>
                                <p>{{ $summary['latest']->keterangan_penolakan }}</p><small>Diperiksa
                                    {{ $summary['latest']->verified_at?->translatedFormat('d M Y H:i') }} oleh
                                    {{ $summary['latest']->verifier->name ?? 'Admin' }}</small>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-xl-9">
                            <div class="document-list-card">
                                <h5>Daftar Dokumen Pendukung</h5>
                                <p>Pastikan semua dokumen diunggah sesuai ketentuan yang berlaku.</p>
                                <div class="document-table-head"><span>Jenis Dokumen</span><span>Status
                                        Verifikasi</span><span>Catatan Admin</span><span>Aksi</span></div>
                                @foreach ($documentTypes as $type => $meta)
                                    @php
                                        $doc = $data->get($type);
                                        $status = $doc?->status;
                                    @endphp
                                    <div class="document-row">
                                        <div class="document-identity"><span class="doc-icon {{ $meta['color'] }}"><i
                                                    class="fas {{ $meta['icon'] }}"></i></span>
                                            <div><b>{{ $meta['label'] }}</b>
                                                @if ($doc)
                                                    <small>Diunggah:
                                                        {{ $doc->updated_at->translatedFormat('d M Y H:i') }}
                                                    </small>
                                                @else
                                                    <small>
                                                        Belum ada file diunggah
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div><span class="doc-status status-{{ $status ?? 'missing' }}"><i
                                                    class="fas {{ $status === 'diverifikasi' ? 'fa-check' : ($status === 'ditolak' ? 'fa-exclamation-circle' : 'fa-clock') }}"></i>
                                                {{ $statusLabels[$status] ?? 'Belum Upload' }}</span></div>
                                        <div class="admin-comment">{{ $doc?->keterangan_penolakan ?? '-' }}</div>
                                        <div class="doc-actions">
                                            @if ($doc)
                                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                                    class="btn-view"><i class="far fa-eye"></i> Lihat Dokumen</a>
                                            @endif
                                            @if ($hasDeparture && $hasRegistration && (!$doc || $doc->status !== 'diverifikasi'))
                                                <button class="btn-upload open-upload" data-type="{{ $type }}"
                                                    data-label="{{ $meta['label'] }}"><i class="fas fa-upload"></i>
                                                    {{ $doc ? 'Upload Ulang' : 'Upload' }}</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <aside class="col-xl-3">
                            <div class="guide-card">
                                <h6><i class="fas fa-book-open"></i> Ketentuan Dokumen</h6>
                                <p>Pastikan dokumen masih berlaku.</p>
                                <ul>
                                    <li>File jelas, tidak buram, dan seluruh teks terbaca.</li>
                                    <li>Format PDF, JPG, JPEG, atau PNG.</li>
                                    <li>Maksimal ukuran file 5MB.</li>
                                    <li>Foto jemaah wajib berupa JPG/PNG ukuran 4×6.</li>
                                </ul>
                            </div>
                            <div class="guide-card">
                                <h6><i class="fas fa-headset"></i> Butuh Bantuan?</h6>
                                <p>Jika ada kendala dalam pengunggahan dokumen, hubungi tim kami.</p>
                                <b><i class="fab fa-whatsapp mx-2"></i>0895-6007-91616</b>
                                <br>
                                <b><i class="fas fa-envelope mx-2 mt-2"></i>info@sawdeeratour.com</b>
                            </div>
                        </aside>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <div class="modal fade" id="uploadModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content upload-modal">
                <form method="POST" action="/dokumen/upload" enctype="multipart/form-data">@csrf<input type="hidden"
                        name="jenis_dokumen" id="documentType">
                    <div class="modal-header">
                        <div><small>UPLOAD DOKUMEN</small>
                            <h5 id="uploadTitle"></h5>
                        </div><button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body"><label class="file-drop" id="documentDrop">
                            <div id="documentPlaceholder"><i class="fas fa-cloud-upload-alt"></i><b>Klik untuk memilih
                                    file</b><small>PDF, JPG, JPEG, PNG · Maks. 5MB</small></div>
                            <div id="documentPreview" class="d-none"></div><input id="documentFile" type="file"
                                name="file" accept=".pdf,.jpg,.jpeg,.png" required>
                        </label></div>
                    <div class="modal-footer"><button type="button" class="btn btn-light"
                            data-dismiss="modal">Batal</button><button class="btn-upload">Kirim Dokumen</button></div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .document-page {
            background: #faf9f7 !important
        }

        .verification-summary,
        .document-list-card,
        .guide-card,
        .empty-payment {
            background: #fff;
            border: 1px solid #ece9e4;
            border-radius: 10px;
            box-shadow: 0 3px 16px rgba(60, 42, 20, .05)
        }

        .verification-summary {
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1.25fr auto;
            gap: 25px;
            align-items: center
        }

        .summary-status {
            display: flex;
            gap: 14px;
            align-items: center
        }

        .summary-icon {
            display: flex;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #fde7e7;
            color: #d84b4b;
            align-items: center;
            justify-content: center
        }

        .summary-status small,
        .summary-status h4 {
            display: block
        }

        .summary-status h4 {
            font-weight: 750;
            color: #bd3434;
            margin: 2px 0
        }

        .summary-status p,
        .admin-note p {
            margin: 0
        }

        .status-diverifikasi .summary-icon {
            background: #e4f6e9;
            color: #278044
        }

        .status-diverifikasi .summary-status h4 {
            color: #278044
        }

        .status-diproses .summary-icon {
            background: #fff1d6;
            color: #a66d12
        }

        .status-diproses .summary-status h4 {
            color: #a66d12
        }

        .admin-note {
            background: #fff0f0;
            border: 1px solid #f3d3d3;
            border-radius: 7px;
            padding: 13px;
            color: #9b3434
        }

        .history-btn,
        .btn-view {
            border: 1px solid #c88d2d;
            color: #946115;
            padding: 9px 12px;
            border-radius: 6px;
            background: #fff;
            white-space: nowrap
        }

        .document-list-card {
            padding: 20px
        }

        .document-list-card>h5 {
            font-weight: 750
        }

        .document-list-card>p {
            font-size: 12px;
            color: #777
        }

        .document-table-head,
        .document-row {
            display: grid;
            grid-template-columns: 2fr 1.1fr 1.6fr 1.1fr;
            gap: 15px;
            align-items: center
        }

        .document-table-head {
            background: #f7f6f4;
            padding: 9px 12px;
            font-size: 10px;
            text-transform: uppercase;
            color: #777
        }

        .document-row {
            padding: 16px 5px;
            border-bottom: 1px solid #eee
        }

        .document-identity {
            display: flex;
            gap: 10px;
            align-items: center
        }

        .document-identity small,
        .document-identity b {
            display: block
        }

        .document-identity small {
            font-size: 10px;
            color: #777
        }

        .doc-icon {
            display: flex;
            flex: none;
            width: 42px;
            height: 42px;
            border-radius: 7px;
            align-items: center;
            justify-content: center
        }

        .doc-icon.blue {
            background: #e1f1ff;
            color: #3184c5
        }

        .doc-icon.green {
            background: #e2f6ea;
            color: #39a15f
        }

        .doc-icon.purple {
            background: #eee5ff;
            color: #8457c5
        }

        .doc-icon.brown {
            background: #f4eadf;
            color: #8b5a2b
        }

        .doc-icon.indigo {
            background: #e8eaff;
            color: #5969c5
        }

        .doc-icon.teal {
            background: #e2f6f3;
            color: #319583
        }

        .doc-icon.orange {
            background: #fff0d8;
            color: #d18a21
        }

        .doc-status {
            font-size: 10px;
            padding: 5px 9px;
            border-radius: 15px;
            white-space: nowrap
        }

        .doc-status.status-diverifikasi {
            background: #e4f6e9;
            color: #278044
        }

        .doc-status.status-diproses {
            background: #fff1d6;
            color: #9d6814
        }

        .doc-status.status-ditolak {
            background: #fbe4e4;
            color: #b03434
        }

        .doc-status.status-missing {
            background: #eee;
            color: #666
        }

        .admin-comment {
            font-size: 11px;
            color: #555
        }

        .doc-actions {
            display: flex;
            flex-direction: column;
            gap: 7px
        }

        .btn-view,
        .btn-upload {
            font-size: 11px;
            text-align: center
        }

        .btn-upload {
            border: 0;
            background: #bd8120;
            color: #fff;
            padding: 9px 12px;
            border-radius: 6px
        }

        .guide-card {
            padding: 20px;
            margin-bottom: 16px;
            font-size: 11px
        }

        .guide-card h6 {
            font-weight: 750
        }

        .guide-card h6 i {
            color: #bd8120;
            margin-right: 7px
        }

        .guide-card li {
            margin-bottom: 9px
        }

        .upload-modal {
            border: 0;
            border-radius: 10px
        }

        .file-drop {
            width: 100%;
            height: 240px;
            border: 1px dashed #bd8120;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            cursor: pointer;
            overflow: hidden
        }

        .file-drop i,
        .file-drop b,
        .file-drop small {
            display: block
        }

        .file-drop i {
            font-size: 30px;
            color: #bd8120
        }

        .file-drop input {
            display: none
        }

        #documentPreview {
            width: 100%;
            height: 100%;
            position: relative
        }

        #documentPreview img,
        #documentPreview object {
            width: 100%;
            height: 240px;
            object-fit: contain
        }

        .preview-name {
            position: absolute;
            left: 8px;
            right: 8px;
            bottom: 8px;
            background: rgba(30, 25, 20, .82);
            color: #fff;
            padding: 7px;
            border-radius: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
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

        @media(max-width:900px) {
            .verification-summary {
                grid-template-columns: 1fr
            }

            .document-table-head {
                display: none
            }

            .document-row {
                grid-template-columns: 1fr
            }

            .selected-detail-grid {
                grid-template-columns: 1fr
            }
        }
    </style>
    @push('scripts')
        <script>
            let docUrl = null;
            $('.open-upload').click(function() {
                $('#documentType').val($(this).data('type'));
                $('#uploadTitle').text($(this).data('label'));
                $('#documentFile').attr('accept', $(this).data('type') === 'foto_4x6' ? '.jpg,.jpeg,.png' :
                    '.pdf,.jpg,.jpeg,.png');
                $('#documentFile').val('');
                $('#documentPreview').addClass('d-none').empty();
                $('#documentPlaceholder').removeClass('d-none');
                $('#uploadModal').modal('show');
            });
            $('#documentFile').change(function() {
                const f = this.files?.[0];
                if (!f) return;
                if (docUrl) URL.revokeObjectURL(docUrl);
                docUrl = URL.createObjectURL(f);
                const name = $('<div>').text(f.name).html(),
                    pdf = f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf');
                $('#documentPreview').html((pdf ? `<object data="${docUrl}" type="application/pdf"></object>` :
                        `<img src="${docUrl}">`) +
                    `<div class="preview-name">${pdf?'<i class="fas fa-file-pdf mx-1"></i>':'<i class="fas fa-image mx-1"></i>'}${name} · Klik untuk mengganti</div>`
                ).removeClass('d-none');
                $('#documentPlaceholder').addClass('d-none');
            });
        </script>
    @endpush
@endsection
