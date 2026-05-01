@extends('layouts.main')
@section('title', 'Keberangkatan Jemaah')
@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mt-3">
                            <div class="card-header">
                                <h2>Jadwal Keberangkatan</h2>
                                @if ($keberangkatanJemaah->isEmpty())
                                    <button class="btn btn-sawdeera1" data-toggle="modal" data-target="#modalTambah"
                                        id="btnTambah">
                                        <i class="fas fa-plus"></i> Tambah Jadwal Keberangkatan
                                    </button>
                                @endif
                                <a href="/paket" target="_blank" class="btn btn-info ml-2">
                                    <i class="fas fa-eye"></i> Lihat Paket
                                </a>
                            </div>
                            <div class="card-body">
                                @if ($keberangkatanJemaah->isNotEmpty())
                                    @foreach ($keberangkatanJemaah as $item)

                                        <div class="card shadow-sm mb-4 p-4"
                                            style="background:linear-gradient(135deg,#6b3f1e,#8c4d24);color:white;border-radius:12px">

                                            {{-- HEADER --}}
                                            <h4 class="fw-bold mb-2">
                                                🛫 {{ $item->paketUmrah->nama_paket }}
                                            </h4>

                                            <hr style="border-color:rgba(255,255,255,0.2)">

                                            {{-- INFO UMUM --}}
                                            <p>📅 <b>Durasi :</b> {{ $item->paketUmrah->durasi }} Hari</p>

                                            <p>
                                                🗓 <b>Tanggal Berangkat :</b>
                                                {{ $item->keberangkatan->tanggal_keberangkatan->format('d F Y') }}
                                            </p>

                                            <p>
                                                🗓 <b>Tanggal Pulang :</b>
                                                {{ $item->keberangkatan->tanggal_pulang->format('d F Y') }}
                                            </p>

                                            <p>
                                                ✈️ <b>Maskapai Berangkat :</b>
                                                {{ $item->keberangkatan->maskapaiBerangkat->nama ?? '-' }}
                                            </p>

                                            <p>
                                                🛬 <b>Maskapai Pulang :</b>
                                                {{ $item->keberangkatan->maskapaiPulang->nama ?? '-' }}
                                            </p>

                                            <hr style="border-color:rgba(255,255,255,0.2)">

                                            {{-- JAM --}}
                                            <p>
                                                ⏰ <b>Jam Berangkat :</b><br>
                                                {{ $item->keberangkatan->jam_berangkat }}
                                            </p>

                                            <p>
                                                ⏳ <b>Jam Tiba :</b><br>
                                                {{ $item->keberangkatan->jam_tiba }}
                                            </p>

                                            <p>
                                                ⏰ <b>Jam Pulang :</b><br>
                                                {{ $item->keberangkatan->jam_pulang }}
                                            </p>

                                            <p>
                                                ⏳ <b>Jam Tiba Pulang :</b><br>
                                                {{ $item->keberangkatan->jam_tiba_pulang }}
                                            </p>

                                            {{-- TOUR LEADER --}}
                                            <p>
                                                👤 <b>Tour Leader :</b>
                                                {{ $item->keberangkatan->leader->nama ?? 'Belum ditentukan' }}
                                            </p>

                                            {{-- STATUS + ACTION --}}
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <p class="mb-0">
                                                    📌 <b>Status :</b>
                                                    <span class="badge bg-light text-dark">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                </p>

                                                @if ($item->keberangkatan->status == 'pendaftaran')
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-warning edit-jadwal"
                                                            data-id="{{ $item->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button class="btn btn-sm btn-danger delete-jadwal"
                                                            data-id="{{ $item->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>

                                    @endforeach
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-plane-departure fa-4x text-muted mb-4"></i>
                                        <h4>Belum ada jadwal keberangkatan</h4>
                                        <p class="text-muted">Mulai daftar jadwal keberangkatan Anda</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Alerts --}}
    @if (session('berhasil'))
        <script>
            Swal.fire('Sukses!', '{{ session('berhasil') }}', 'success');
        </script>
    @endif
    @if (session('gagal'))
        <script>
            Swal.fire('Error!', '{{ session('gagal') }}', 'error');
        </script>
    @endif

    {{-- Modal Tambah --}}
    <div class="modal fade" id="modalTambah">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formTambah" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tambah Jadwal Keberangkatan</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Paket Umrah <span class="text-danger">*</span></label>
                            <select name="paket_umrah_id" id="paketSelect" class="form-control" required>
                                <option value="">Pilih Paket</option>
                                @foreach (App\Models\PaketUmrah::all() as $paket)
                                    <option value="{{ $paket->id }}" data-durasi="{{ $paket->durasi }}">
                                        {{ $paket->nama_paket }} ({{ $paket->durasi }} Hari)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jadwal Keberangkatan <span class="text-danger">*</span></label>
                            <select name="keberangkatan_id" id="jadwalSelect" class="form-control" disabled required>
                                <option value="">Pilih paket dulu untuk melihat jadwal</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitTambah" disabled>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEdit">
                    @csrf
                    <div class="modal-header">
                        <h5>Edit Jadwal</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body" id="editBody">
                        -- AJAX Content --
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {

                // Load jadwal based on paket durasi
                $('#paketSelect').on('change', function() {
                    let paketId = $(this).val();
                    let durasi = $(this).find('option:selected').data('durasi');

                    if (paketId && durasi) {
                        $.ajax({
                            url: `/keberangkatan-jemaah/jadwal-paket/${paketId}/${durasi}`,
                            type: 'GET',
                            success: function(jadwals) {
                                let options = '<option value="">Pilih Jadwal</option>';
                                jadwals.forEach(function(jadwal) {
                                    let berangkat = new Date(jadwal.tanggal_keberangkatan)
                                        .toLocaleDateString('id-ID');
                                    let pulang = new Date(jadwal.tanggal_pulang)
                                        .toLocaleDateString('id-ID');
                                    let maskapaiBer = jadwal.maskapai_berangkat ? jadwal
                                        .maskapai_berangkat.nama : 'N/A';
                                    let maskapaiPul = jadwal.maskapai_pulang ? jadwal
                                        .maskapai_pulang.nama : 'N/A';

                                    options += `<option value="${jadwal.id}">
                            (${berangkat} - ${maskapaiBer}) - (${pulang} - ${maskapaiPul})
                        </option>`;
                                });
                                $('#jadwalSelect').html(options).prop('disabled', false);
                                $('#btnSubmitTambah').prop('disabled', false);
                            },
                            error: function() {
                                Swal.fire('Error', 'Gagal memuat jadwal', 'error');
                            }
                        });
                    } else {
                        $('#jadwalSelect').html('<option value="">Pilih paket dulu</option>').prop('disabled',
                            true);
                        $('#btnSubmitTambah').prop('disabled', true);
                    }
                });

                // Tambah form submit
                $('#formTambah').submit(function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);
                    $.ajax({
                        url: '/keberangkatan-jemaah/store',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Sukses!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let error = xhr.responseJSON;
                            Swal.fire('Error!', error.message || 'Terjadi kesalahan', 'error');
                        }
                    });
                });

                // Edit button
                $(document).on('click', '.edit-jadwal', function() {
                    let id = $(this).data('id');
                    $.get(`/keberangkatan-jemaah/${id}/edit`, function(response) {
                        $('#editBody').html(response.html);
                        $('#formEdit').attr('action', `/keberangkatan-jemaah/${id}`);
                        $('#modalEdit').modal('show');
                    });
                });

                // Delete button
                $(document).on('click', '.delete-jadwal', function() {
                    let id = $(this).data('id');
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Hapus jadwal keberangkatan ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/keberangkatan-jemaah/${id}`,
                                method: 'DELETE',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire('Terhapus!', response.message, 'success');
                                    location.reload();
                                }
                            });
                        }
                    });
                });

                // Edit form submit
                $('#formEdit').submit(function(e) {
                    e.preventDefault();
                    let action = $(this).attr('action');

                    let formData = new FormData(this);
                    $.ajax({
                        url: action,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire('Sukses!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Gagal update jadwal', 'error');
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection
