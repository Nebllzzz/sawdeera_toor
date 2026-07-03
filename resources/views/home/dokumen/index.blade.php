@extends('layouts.main')
@section('title', 'Dokumen')

@section('content')

<div class="content-wrapper"
    style="min-height:100vh; padding:30px;">

    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="card border-0 shadow-sm mb-4"
            style="border-radius:18px; overflow:hidden;">

            <div class="card-header text-white py-4"
                style="background:linear-gradient(135deg,#5c3317,#8c4d24); border:none;">

                <div class="d-flex justify-content-between align-items-center flex-wrap">

                    <div>
                        <h3 class="mb-1 fw-bold">
                            📄 Upload Persyaratan Dokumen
                        </h3>

                        <p class="mb-0 text-light">
                            Lengkapi seluruh dokumen persyaratan umrah untuk proses
                            verifikasi administrasi keberangkatan.
                        </p>
                    </div>

                    {{-- BUTTON INFO --}}
                    <div class="d-flex justify-content-end gap-2 mt-3">

                        <button type="button"
                            class="btn btn-sawdeera1 text-white"
                            id="btnShowDocInfo">

                            ℹ️ Informasi Lengkap Dokumen Yang Harus Diupload

                        </button>

                        <button type="button"
                            class="btn btn-sawdeera1 d-none text-white"
                            id="btnHideDocInfo">

                            ✖ Hide Informasi Dokumen Yang Harus Diupload

                        </button>

                    </div>
                </div>

            </div>

            <div class="card-body bg-white">

                <div class="row d-none" id="docInfoRow">

                    {{-- INFO --}}
                    <div class="col-lg-4 mb-3">

                        <div class="card border-0 shadow-sm h-100"
                            style="border-radius:16px; background:#f8f9fa;">

                            <div class="card-body p-4">

                                <h5 class="fw-bold mb-4">
                                    ℹ️ Informasi Dokumen
                                </h5>

                                <div class="mb-3">
                                    ✅ Upload file dengan data yang jelas dan valid.
                                </div>

                                <div class="mb-3">
                                    ✅ Format yang didukung JPG, PNG, PDF.
                                </div>

                                <div class="mb-3">
                                    ✅ Maksimal ukuran file 2MB.
                                </div>

                                <div class="mb-3">
                                    ✅ Dokumen akan diverifikasi oleh admin.
                                </div>

                                <div>
                                    ✅ Pastikan seluruh dokumen sudah lengkap sebelum keberangkatan.
                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- STATUS --}}
                    <div class="col-lg-4 mb-3">

                        <div class="card border-0 shadow-sm h-100"
                            style="border-radius:16px; background:#f8f9fa;">

                            <div class="card-body p-4">

                                <h5 class="fw-bold mb-4">
                                    📋 Status Verifikasi
                                </h5>

                                <div class="mb-3">
                                    <span class="badge bg-success w-100 py-2">
                                        Diverifikasi
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <span class="badge bg-warning w-100 py-2">
                                        Diproses
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <span class="badge bg-danger w-100 py-2">
                                        Ditolak
                                    </span>
                                </div>

                                <div>
                                    <span class="badge bg-secondary w-100 py-2">
                                        Belum Upload
                                    </span>
                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- PANDUAN --}}
                    <div class="col-lg-4 mb-3">

                        <div class="card border-0 shadow-sm h-100"
                            style="border-radius:16px; background:#fffaf2;">

                            <div class="card-body p-4">

                                <h5 class="fw-bold mb-4">
                                    🛂 Dokumen Wajib
                                </h5>

                                <ul class="ps-3 mb-0">

                                    <li class="mb-2">
                                        KTP
                                    </li>

                                    <li class="mb-2">
                                        Paspor
                                    </li>

                                    <li class="mb-2">
                                        Visa
                                    </li>

                                    <li>
                                        Sertifikat Vaksin
                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @php
            $docs = [
                'ktp' => 'KTP',
                'paspor' => 'Paspor',
                'visa' => 'Visa',
                'vaksin' => 'Vaksin'
            ];
        @endphp

        <div class="row">

            @foreach($docs as $key => $label)

                @php
                    $doc = $data[$key] ?? null;

                    $status = optional($doc)->status;

                    $badge = match($status){
                        'diverifikasi' => 'bg-success',
                        'ditolak' => 'bg-danger',
                        'diproses' => 'bg-warning',
                        default => 'bg-secondary'
                    };
                @endphp

                <div class="col-lg-6 mb-4 d-flex">

                    <div class="card border-0 shadow-sm w-100"
                        style="border-radius:18px; overflow:hidden;">

                        {{-- HEADER --}}
                        <div class="card-header text-white py-3"
                            style="background:linear-gradient(135deg,#6b3f1e,#8c4d24); border:none;">

                            <div class="d-flex justify-content-between align-items-center">

                                <h5 class="mb-0 fw-bold">
                                    📄 {{ $label }}
                                </h5>

                                <span class="badge {{ $badge }} px-3 py-2">
                                    {{ ucfirst($status) ?? 'belum upload' }}
                                </span>

                            </div>

                        </div>

                        {{-- BODY --}}
                        <div class="card-body p-4 d-flex flex-column">

                            {{-- PENOLAKAN --}}
                            @if($doc && $doc->keterangan_penolakan)

                                <div class="alert alert-danger">

                                    <b>Alasan Penolakan:</b><br>

                                    {{ $doc->keterangan_penolakan }}

                                </div>

                            @endif

                            {{-- PREVIEW --}}
                            <div class="border rounded p-3 d-flex align-items-center justify-content-center flex-grow-1"
                                style="min-height:280px; background:#f8f9fa;">

                                @if($doc && $doc->file_path)

                                    @if(Str::contains($doc->file_path, ['.pdf']))

                                        <a href="{{ asset('storage/'.$doc->file_path) }}"
                                            target="_blank"
                                            class="btn btn-outline-dark">

                                            📄 Lihat PDF

                                        </a>

                                    @else

                                        <a href="{{ asset('storage/'.$doc->file_path) }}"
                                            target="_blank">

                                            <img src="{{ asset('storage/'.$doc->file_path) }}"
                                                class="rounded shadow-sm"
                                                style="max-height:220px; max-width:100%; object-fit:contain;">

                                        </a>

                                    @endif

                                @else

                                    <div class="text-center text-muted">

                                        <div style="font-size:50px;">
                                            📁
                                        </div>

                                        <div>
                                            Belum ada file diupload
                                        </div>

                                    </div>

                                @endif

                            </div>

                            @if($doc && $doc->status == 'diverifikasi')
                                <small class="text-danger mt-3 d-block text-center  ">
                                    ⚠️ Dokumen ini sudah diverifikasi oleh admin.
                                </small>
                            @else
                                {{-- INFO --}}
                                <small class="text-muted mt-3">
                                    Format: JPG, PNG, PDF • Maksimal 2MB
                                </small>

                                <button
                                    class="btn btn-dark w-100 mt-3 py-2"
                                    data-toggle="modal"
                                    data-target="#modalUpload"
                                    onclick="setJenis('{{ $key }}')">

                                    {{ $doc && $doc->file_path ? '✏️ Edit Dokumen' : '🚀 Upload Dokumen' }}

                                </button>
                            @endif

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>

{{-- MODAL --}}
<div class="modal fade" id="modalUpload" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0"
            style="border-radius:18px; overflow:hidden;">

            <form method="POST"
                action="/dokumen/upload"
                enctype="multipart/form-data">

                @csrf

                <input type="hidden"
                    name="jenis_dokumen"
                    id="jenis_dokumen">

                <div class="modal-header text-white"
                    style="background:linear-gradient(135deg,#6b3f1e,#8c4d24); border:none;">

                    <h5 class="modal-title fw-bold">
                        📤 Upload Dokumen
                    </h5>

                    <button type="button"
                        class="close text-white"
                        data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body p-4">

                    <label class="fw-semibold mb-2">
                        Pilih File
                    </label>

                    <input type="file"
                        name="file"
                        class="form-control"
                        required>

                    <small class="text-muted">
                        Format: JPG, PNG, PDF • Maksimal 2MB
                    </small>

                </div>

                <div class="modal-footer border-0 px-4 pb-4">

                    <button class="btn btn-dark w-100 py-2">
                        🚀 Upload Sekarang
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

function setJenis(jenis) {

    document.getElementById('jenis_dokumen').value = jenis;

}

</script>

<script>

    const docInfoRow = document.getElementById('docInfoRow');
    const btnShowDocInfo = document.getElementById('btnShowDocInfo');
    const btnHideDocInfo = document.getElementById('btnHideDocInfo');

    btnShowDocInfo.addEventListener('click', function () {

        docInfoRow.classList.remove('d-none');

        btnShowDocInfo.classList.add('d-none');

        btnHideDocInfo.classList.remove('d-none');

    });

    btnHideDocInfo.addEventListener('click', function () {

        docInfoRow.classList.add('d-none');

        btnHideDocInfo.classList.add('d-none');

        btnShowDocInfo.classList.remove('d-none');

    });

</script>

@endsection
