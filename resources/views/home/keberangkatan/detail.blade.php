@extends('layouts.main')
@section('title', 'Detail Keberangkatan')

@section('content')
    @php
        use App\Models\Keberangkatan;
        $statusClass = Keberangkatan::statusBadgeClass($keberangkatan->status);
        $canEdit = auth()->user()->role === 'operator' && in_array($keberangkatan->status, [Keberangkatan::STATUS_DRAFT, Keberangkatan::STATUS_DIREVISI], true);
        $canDelete = auth()->user()->role === 'operator' && $keberangkatan->status === Keberangkatan::STATUS_DRAFT && $keberangkatan->jemaah_count === 0;
    @endphp
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <x-page-heading
                    title="Detail Jadwal Keberangkatan"
                    :description="$keberangkatan->kode_keberangkatan . ' · ' . ($keberangkatan->paket?->nama_paket ?? 'Paket belum dipilih')"
                    section="Jadwal Keberangkatan"
                    current="Detail"
                >
                    <x-slot:actions>
                        <a href="/keberangkatan" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                    </x-slot:actions>
                </x-page-heading>

                @if(session('berhasil'))
                    <div class="alert alert-success">{{ session('berhasil') }}</div>
                @endif

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card mb-4">
                            <div class="card-body departure-summary">
                                <div class="summary-icon"><i class="far fa-calendar-alt"></i></div>
                                <div class="summary-main">
                                    <span class="badge badge-{{ $statusClass }}">{{ Keberangkatan::statusLabel($keberangkatan->status) }}</span>
                                    <h3>{{ $keberangkatan->kode_keberangkatan }}</h3>
                                    <p>{{ $keberangkatan->paket?->nama_paket ?? 'Paket belum dipilih' }}</p>
                                </div>
                                <div class="summary-grid">
                                    <div><small>Tanggal Berangkat</small><b>{{ $keberangkatan->tanggal_keberangkatan?->translatedFormat('d M Y') }}</b></div>
                                    <div><small>Tanggal Pulang</small><b>{{ $keberangkatan->tanggal_pulang?->translatedFormat('d M Y') }}</b></div>
                                    <div><small>Kuota</small><b>{{ $keberangkatan->kuota }} Jemaah</b></div>
                                    <div><small>Terisi</small><b>{{ $keberangkatan->terisi }} Jemaah</b></div>
                                </div>
                            </div>
                        </div>

                        @if($keberangkatan->status === Keberangkatan::STATUS_DIREVISI && $keberangkatan->alasan_revisi)
                            <div class="alert alert-warning">
                                <b>Alasan revisi admin:</b><br>{{ $keberangkatan->alasan_revisi }}
                            </div>
                        @endif

                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center"><h3 class="mb-0">Informasi Detail</h3></div>
                            <div class="card-body detail-grid">
                                <div><small>Hotel Makkah</small><b>{{ $keberangkatan->paket?->hotelMakkah?->nama ?? '-' }}</b></div>
                                <div><small>Hotel Madinah</small><b>{{ $keberangkatan->paket?->hotelMadinah?->nama ?? '-' }}</b></div>
                                <div><small>Maskapai Berangkat</small><b>{{ $keberangkatan->maskapaiBerangkat?->nama ?? '-' }}</b></div>
                                <div><small>Maskapai Pulang</small><b>{{ $keberangkatan->maskapaiPulang?->nama ?? '-' }}</b></div>
                                <div><small>Pembimbing / Guide</small><b>{{ $keberangkatan->leader?->nama ?? '-' }}</b></div>
                                <div><small>Harga Paket</small><b>Rp {{ number_format($keberangkatan->paket?->harga ?? 0, 0, ',', '.') }}</b></div>
                                <div><small>Dibuat Oleh</small><b>{{ $keberangkatan->pembuat?->name ?? '-' }}</b></div>
                                <div><small>Tanggal Dibuat</small><b>{{ $keberangkatan->created_at?->translatedFormat('d M Y H:i') ?? '-' }}</b></div>
                                <div><small>Terakhir Diubah</small><b>{{ $keberangkatan->updated_at?->translatedFormat('d M Y H:i') ?? '-' }}</b></div>
                                <div class="grid-full"><small>Keterangan</small><b>{{ $keberangkatan->keterangan ?: '-' }}</b></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card action-panel">
                            <div class="card-header d-flex justify-content-between align-items-center"><h3 class="mb-0">Aksi</h3></div>
                            <div class="card-body">
                                @if($canEdit)
                                    <button class="btn btn-primary btn-block mb-2 w-100" onclick="openEdit()"><i class="fas fa-pen mr-2"></i>Edit Jadwal</button>
                                @endif
                                @if($canDelete)
                                    <button class="btn btn-danger btn-block mb-2 w-100" onclick="deleteSchedule()"><i class="fas fa-trash mr-2"></i>Hapus Jadwal</button>
                                @endif
                                @forelse($actions as $action)
                                    @if($action === 'revise')
                                        <button class="btn btn-warning btn-block mb-2 w-100" data-toggle="modal" data-target="#modalRevisi"><i class="fas fa-comment-dots mr-2"></i>Minta Revisi</button>
                                    @else
                                        <button class="btn btn-success btn-block mb-2 w-100 status-action" data-action="{{ $action }}">{{ app(\App\Services\KeberangkatanStatusService::class)->actionLabel($action) }}</button>
                                    @endif
                                @empty
                                    <div class="text-muted">Tidak ada aksi status lanjutan untuk role Anda.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center"><h3 class="mb-0">List Jemaah</h3></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="dtJemaah">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Jemaah</th>
                                        <th>Paket</th>
                                        <th>Jadwal Keberangkatan</th>
                                        <th>Status Keberangkatan Jemaah</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            </table>
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
                    <div class="modal-header"><h5>Minta Revisi Jadwal</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                    <div class="modal-body">
                        <label>Alasan Revisi</label>
                        <textarea name="alasan_revisi" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="modal-footer"><button class="btn btn-warning">Kirim Revisi</button></div>
                </form>
            </div>
        </div>
    </div>

    @if($canEdit)
        <div class="modal fade" id="modalEdit">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="formEdit">@csrf
                        <div class="modal-header"><h5>Edit Jadwal</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label>Paket</label>
                                    <select name="paket_id" class="form-control" required>
                                        @foreach($formData['pakets'] as $paket)
                                            <option value="{{ $paket->id }}" @selected($keberangkatan->paket_id == $paket->id)>{{ $paket->nama_paket }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3"><label>Kuota</label><input type="number" name="kuota" class="form-control" value="{{ $keberangkatan->kuota }}" min="1" required></div>
                                <div class="col-md-6 mb-3">
                                    <label>Maskapai Berangkat</label>
                                    <select name="maskapai_berangkat_id" class="form-control" required>
                                        @foreach($formData['maskapais'] as $m)
                                            <option value="{{ $m->id }}" @selected($keberangkatan->maskapai_berangkat_id == $m->id)>{{ $m->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Maskapai Pulang</label>
                                    <select name="maskapai_pulang_id" class="form-control" required>
                                        @foreach($formData['maskapais'] as $m)
                                            <option value="{{ $m->id }}" @selected($keberangkatan->maskapai_pulang_id == $m->id)>{{ $m->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>Pembimbing / Guide</label>
                                    <select name="tour_leader_id" class="form-control">
                                        <option value="">-</option>
                                        @foreach($formData['leaders'] as $leader)
                                            <option value="{{ $leader->id }}" @selected($keberangkatan->tour_leader_id == $leader->id)>{{ $leader->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3"><label>Tanggal Berangkat</label><input type="date" name="tanggal_keberangkatan" class="form-control" value="{{ $keberangkatan->tanggal_keberangkatan?->format('Y-m-d') }}" required></div>
                                <div class="col-md-4 mb-3"><label>Jam Berangkat</label><input type="time" name="jam_berangkat" class="form-control" value="{{ substr((string) $keberangkatan->jam_berangkat, 0, 5) }}" required></div>
                                <div class="col-md-4 mb-3"><label>Jam Tiba</label><input type="time" name="jam_tiba" class="form-control" value="{{ substr((string) $keberangkatan->jam_tiba, 0, 5) }}" required></div>
                                <div class="col-md-4 mb-3"><label>Tanggal Pulang</label><input type="date" name="tanggal_pulang" class="form-control" value="{{ $keberangkatan->tanggal_pulang?->format('Y-m-d') }}" required></div>
                                <div class="col-md-4 mb-3"><label>Jam Pulang</label><input type="time" name="jam_pulang" class="form-control" value="{{ substr((string) $keberangkatan->jam_pulang, 0, 5) }}" required></div>
                                <div class="col-md-4 mb-3"><label>Jam Tiba Pulang</label><input type="time" name="jam_tiba_pulang" class="form-control" value="{{ substr((string) $keberangkatan->jam_tiba_pulang, 0, 5) }}" required></div>
                                <div class="col-12 mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="3">{{ $keberangkatan->keterangan }}</textarea></div>
                            </div>
                        </div>
                        <div class="modal-footer"><button class="btn btn-primary">Simpan Perubahan</button></div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .departure-summary { display:flex; gap:18px; align-items:center; flex-wrap:wrap; }
        .summary-icon { width:72px; height:72px; border-radius:50%; background:#f3f4f7; display:flex; align-items:center; justify-content:center; font-size:30px; color:#6B3E20; }
        .summary-main { min-width:220px; flex:1; }
        .summary-main h3 { margin:8px 0 2px; font-weight:800; }
        .summary-main p { margin:0; color:#555; font-weight:600; }
        .summary-grid, .detail-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px 24px; }
        .summary-grid small, .detail-grid small { display:block; color:#777; font-size:11px; margin-bottom:4px; }
        .summary-grid b, .detail-grid b { display:block; color:#1f2937; }
        .grid-full { grid-column:1/-1; }
        .action-panel .btn { min-height:42px; }
        .jemaah-action-list { display:flex; flex-wrap:wrap; gap:6px; }
        .jemaah-action-list .btn { white-space:nowrap; }
        @media(max-width:768px) { .summary-grid, .detail-grid { grid-template-columns:1fr; } }
    </style>

    @push('scripts')
        <script>
            const scheduleId = "{{ $keberangkatan->id }}";

            $("#dtJemaah").DataTable({
                dom: "<'row'<'col-sm-6 d-flex align-items-center justify-content-start'l><'col-sm-6 d-flex align-items-center justify-content-end'f>>" +
                    "<'table-responsive'tr>" +
                    "<'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/keberangkatan/jemaah/data",
                    type: "POST",
                    data: function(d) {
                        d.keberangkatan_id = scheduleId;
                    },
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
                },
                columns: [
                    { data: "DT_RowIndex", orderable: false, searchable: false },
                    { data: "nama" },
                    { data: "paket" },
                    { data: "jadwal" },
                    { data: "status" },
                    { data: "tanggal_pengajuan" },
                    { data: "action", orderable: false, searchable: false },
                ]
            });

            $(document).on('click', '.status-action', function() {
                const action = $(this).data('action');
                Swal.fire({ title: 'Lanjutkan aksi ini?', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya' })
                    .then(result => {
                        if (!result.isConfirmed) return;
                        $.post('/keberangkatan/update-status', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: scheduleId,
                            action
                        }).done(res => Swal.fire('Berhasil', res.message, 'success').then(() => location.reload()))
                            .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Aksi tidak dapat diproses.', 'error'));
                    });
            });

            $('#formRevisi').submit(function(e) {
                e.preventDefault();
                $.post('/keberangkatan/update-status', $(this).serialize() + '&id=' + scheduleId + '&action=revise')
                    .done(res => Swal.fire('Berhasil', res.message, 'success').then(() => location.reload()))
                    .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Alasan revisi wajib diisi.', 'error'));
            });

            function openEdit() {
                $('#modalEdit').modal('show');
            }

            $('#formEdit').submit(function(e) {
                e.preventDefault();
                $.post('/keberangkatan/update/' + scheduleId, $(this).serialize())
                    .done(res => Swal.fire('Berhasil', res.message, 'success').then(() => location.reload()))
                    .fail(xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Jadwal tidak dapat diperbarui.', 'error'));
            });

            function deleteSchedule() {
                Swal.fire({ title: 'Hapus jadwal draft ini?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus' })
                    .then(result => {
                        if (!result.isConfirmed) return;
                        $.ajax({
                            url: '/keberangkatan/delete/' + scheduleId,
                            type: 'DELETE',
                            data: { _token: $('meta[name="csrf-token"]').attr('content') },
                            success: () => Swal.fire('Berhasil', 'Jadwal dihapus.', 'success').then(() => location.href = '/keberangkatan'),
                            error: xhr => Swal.fire('Gagal', xhr.responseJSON?.message || 'Jadwal tidak dapat dihapus.', 'error')
                        });
                    });
            }
        </script>
    @endpush
@endsection
