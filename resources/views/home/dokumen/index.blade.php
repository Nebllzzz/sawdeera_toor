@extends('layouts.main')
@section('title','Dokumen')

@section('content')

<div class="content-wrapper" style="background:#E8C999; min-height:100vh; padding:30px;">
    <div class="card p-4">

        <h4 class="mb-4">Upload Persyaratan Dokumen</h4>

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
            @endphp

            <div class="col-md-6 mb-4">
                <div class="card p-3 h-100">

                    <h5>{{ $loop->iteration }}. {{ $label }}</h5>

                    <!-- STATUS -->
                    @php
                        $status = optional($doc)->status;

                        $badge = match($status){
                            'diverifikasi' => 'bg-success',
                            'ditolak' => 'bg-danger',
                            'diproses' => 'bg-warning',
                            default => 'bg-secondary'
                        };
                    @endphp

                    <span class="badge {{ $badge }} py-1">
                        {{ $status ?? 'belum upload' }}
                    </span>

                    <!-- PENOLAKAN -->
                    @if($doc && $doc->keterangan_penolakan)
                        <div class="mt-2">
                            <small class="text-danger">
                                <b>Alasan:</b> {{ $doc->keterangan_penolakan }}
                            </small>
                        </div>
                    @endif

                    <!-- PREVIEW -->
                    <div class="mt-3 mb-3"
                        style="height:200px; border:1px dashed #ccc; display:flex; align-items:center; justify-content:center;">

                        @if($doc && $doc->file_path)
                            <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank">
                                <img src="{{ asset('storage/'.$doc->file_path) }}"
                                     style="max-height:180px;">
                            </a>
                        @else
                            <span class="text-muted">Silahkan upload file</span>
                        @endif

                    </div>

                    <small class="text-muted">
                        Format: png, jpg, pdf. Maks 2MB
                    </small>

                    <!-- BUTTON -->
                    <button
                        class="btn btn-dark w-100 mt-2"
                        data-toggle="modal"
                        data-target="#modalUpload"
                        onclick="setJenis('{{ $key }}')"
                    >
                        {{ $doc && $doc->file_path ? 'Edit File' : 'Upload File' }}
                    </button>

                </div>
            </div>

        @endforeach

        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="modalUpload">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="/dokumen/upload" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="jenis_dokumen" id="jenis_dokumen">

                <div class="modal-header">
                    <h5>Upload Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="file" name="file" class="form-control" required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-dark w-100">Upload</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
function setJenis(jenis){
    document.getElementById('jenis_dokumen').value = jenis;
}
</script>

@endsection
