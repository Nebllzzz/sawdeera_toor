@extends('layouts.main')
@section('title', 'Review Reschedule')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="font-weight-bold mb-1">Review Reschedule Keberangkatan</h2>
                        <small class="text-muted">Dashboard &nbsp;›&nbsp; Jadwal Keberangkatan &nbsp;›&nbsp; Review Reschedule</small>
                    </div>
                    <a href="/keberangkatan/detail/{{ $reschedule->keberangkatan_asal_id }}" class="btn btn-sawdeera1">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>

                @if(session('berhasil'))
                    <div class="alert alert-success">{{ session('berhasil') }}</div>
                @endif

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center"><h3 class="mb-0">Data Pengajuan</h3></div>
                            <div class="card-body review-grid">
                                <div><small>Jemaah</small><b>{{ $reschedule->jemaah?->user?->name ?? '-' }}</b></div>
                                <div><small>Paket</small><b>{{ $reschedule->keberangkatanJemaah?->paketUmrah?->nama_paket ?? '-' }}</b></div>
                                <div><small>Status</small><b>{{ ucfirst($reschedule->status) }}</b></div>
                                <div><small>Waktu Pengajuan</small><b>{{ $reschedule->diajukan_pada?->translatedFormat('d M Y H:i') ?? '-' }}</b></div>
                                <div class="grid-full"><small>Alasan Pengajuan</small><b>{{ $reschedule->alasan_pengajuan ?: '-' }}</b></div>
                                @if($reschedule->alasan_tolak_reschedule)
                                    <div class="grid-full"><small>Alasan Penolakan</small><b>{{ $reschedule->alasan_tolak_reschedule }}</b></div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center"><h3 class="mb-0">Keberangkatan Awal</h3></div>
                                    <div class="card-body">
                                        <h4>{{ $reschedule->keberangkatanAsal?->kode_keberangkatan }}</h4>
                                        <p class="mb-1">{{ $reschedule->keberangkatanAsal?->paket?->nama_paket ?? '-' }}</p>
                                        <b>{{ $reschedule->keberangkatanAsal?->tanggal_keberangkatan?->translatedFormat('d M Y') }} - {{ $reschedule->keberangkatanAsal?->tanggal_pulang?->translatedFormat('d M Y') }}</b>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center"><h3 class="mb-0">Keberangkatan Tujuan</h3></div>
                                    <div class="card-body">
                                        <h4>{{ $reschedule->keberangkatanTujuan?->kode_keberangkatan }}</h4>
                                        <p class="mb-1">{{ $reschedule->keberangkatanTujuan?->paket?->nama_paket ?? '-' }}</p>
                                        <b>{{ $reschedule->keberangkatanTujuan?->tanggal_keberangkatan?->translatedFormat('d M Y') }} - {{ $reschedule->keberangkatanTujuan?->tanggal_pulang?->translatedFormat('d M Y') }}</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center"><h3 class="mb-0">Aksi Admin</h3></div>
                            <div class="card-body">
                                @if($reschedule->status === \App\Models\KeberangkatanJemaahReschedule::STATUS_MENUNGGU)
                                    <form action="/keberangkatan/reschedule/{{ $reschedule->id }}/approve" method="POST" class="mb-2">
                                        @csrf
                                        <button class="btn btn-success btn-block w-100" onclick="return confirm('Setujui reschedule ini?')">
                                            <i class="fas fa-check mr-2"></i>Setujui Reschedule
                                        </button>
                                    </form>
                                    <button class="btn btn-danger btn-block w-100" data-toggle="modal" data-target="#modalTolak">
                                        <i class="fas fa-times mr-2"></i>Tolak Reschedule
                                    </button>
                                @else
                                    <p class="text-muted mb-0">Pengajuan ini sudah diproses oleh {{ $reschedule->pemroses?->name ?? '-' }} pada {{ $reschedule->diproses_pada?->translatedFormat('d M Y H:i') ?? '-' }}.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalTolak">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/keberangkatan/reschedule/{{ $reschedule->id }}/reject" method="POST">
                    @csrf
                    <div class="modal-header"><h5>Tolak Reschedule</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                    <div class="modal-body">
                        <label>Alasan Penolakan</label>
                        <textarea name="alasan_tolak_reschedule" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="modal-footer"><button class="btn btn-danger">Tolak Pengajuan</button></div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .review-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
        .review-grid small { display:block; color:#777; margin-bottom:4px; }
        .review-grid b { display:block; }
        .grid-full { grid-column:1/-1; }
        @media(max-width:768px) { .review-grid { grid-template-columns:1fr; } }
    </style>
@endsection
