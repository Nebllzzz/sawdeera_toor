@extends('layouts.main')
@section('title', 'Keberangkatan')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">

                <x-page-heading
                    title="Jadwal Keberangkatan"
                    description="Kelola jadwal, kuota, dan proses persetujuan keberangkatan jemaah."
                    section="Keberangkatan"
                    current="Daftar Jadwal"
                />

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Daftar Jadwal Keberangkatan</h3>

                        @if(auth()->user()->role === 'operator')
                            <button class="btn btn-sawdeera1" onclick="createKeberangkatan()">
                                <i class="fas fa-plus mr-2 text-white"></i>Tambah Jadwal
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="dtKeberangkatan">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Keberangkatan</th>
                                        <th>Paket</th>
                                        <th>Tanggal Berangkat</th>
                                        <th>Tanggal Pulang</th>
                                        <th>Kuota</th>
                                        <th>Terisi</th>
                                        <th>Status</th>
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

    <div class="modal fade" id="modalKeberangkatan">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formKeberangkatan">
                    @csrf
                    <div class="modal-header">
                        <h4 id="modalTitle">Tambah Jadwal</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editId">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label>Paket</label>
                                <select name="paket_id" id="paket_id" class="form-control" required></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Kuota</label>
                                <input type="number" name="kuota" id="kuota" class="form-control" min="1" value="40" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Maskapai Berangkat</label>
                                <select name="maskapai_berangkat_id" id="maskapai_berangkat_id" class="form-control" required></select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Maskapai Pulang</label>
                                <select name="maskapai_pulang_id" id="maskapai_pulang_id" class="form-control" required></select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Pembimbing / Guide</label>
                                <select name="tour_leader_id" id="tour_leader_id" class="form-control"></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Tanggal Berangkat</label>
                                <input type="date" name="tanggal_keberangkatan" id="tanggal_keberangkatan" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Jam Berangkat</label>
                                <input type="time" name="jam_berangkat" id="jam_berangkat" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Jam Tiba</label>
                                <input type="time" name="jam_tiba" id="jam_tiba" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Tanggal Pulang</label>
                                <input type="date" name="tanggal_pulang" id="tanggal_pulang" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Jam Pulang</label>
                                <input type="time" name="jam_pulang" id="jam_pulang" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Jam Tiba Pulang</label>
                                <input type="time" name="jam_tiba_pulang" id="jam_tiba_pulang" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label>Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sawdeera1">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let formDataCache = null;

            const dt = $("#dtKeberangkatan").DataTable({
                dom: "<'row'<'col-sm-6 d-flex align-items-center justify-content-start'l><'col-sm-6 d-flex align-items-center justify-content-end'f>>" +
                    "<'table-responsive'tr>" +
                    "<'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
                processing: true,
                serverSide: false,
                ajax: "/keberangkatan/list",
                columns: [
                    { data: "DT_RowIndex", orderable: false, searchable: false },
                    { data: "kode", name: "id" },
                    { data: "paket", name: "paket.nama_paket" },
                    { data: "tanggal_berangkat", name: "tanggal_keberangkatan" },
                    { data: "tanggal_pulang", name: "tanggal_pulang" },
                    { data: "kuota", name: "kuota" },
                    { data: "terisi", orderable: false, searchable: false },
                    { data: "status_badge", name: "status" },
                    { data: "action", orderable: false, searchable: false },
                ]
            });

            function fillOptions(res) {
                $("#paket_id").html(res.paket.map(row => `<option value="${row.id}">${row.nama_paket}</option>`).join(''));
                const maskapai = res.maskapai.map(row => `<option value="${row.id}">${row.nama}</option>`).join('');
                $("#maskapai_berangkat_id").html(maskapai);
                $("#maskapai_pulang_id").html(maskapai);
                $("#tour_leader_id").html('<option value="">-</option>' + res.leader.map(row => `<option value="${row.id}">${row.nama}</option>`).join(''));
            }

            function loadFormData(callback) {
                if (formDataCache) {
                    fillOptions(formDataCache);
                    callback();
                    return;
                }
                $.get('/keberangkatan/form-data', function(res) {
                    formDataCache = res;
                    fillOptions(res);
                    callback();
                });
            }

            function createKeberangkatan() {
                loadFormData(function() {
                    $("#modalTitle").text("Tambah Jadwal");
                    $("#formKeberangkatan")[0].reset();
                    $("#editId").val('');
                    $("#kuota").val(40);
                    $("#modalKeberangkatan").modal("show");
                });
            }

            $("#formKeberangkatan").submit(function(e) {
                e.preventDefault();
                const id = $("#editId").val();
                $.ajax({
                    url: id ? `/keberangkatan/update/${id}` : '/keberangkatan/store',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        Swal.fire("Berhasil", res.message, "success");
                        $("#modalKeberangkatan").modal("hide");
                        dt.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire("Gagal", xhr.responseJSON?.message || "Periksa kembali data jadwal.", "error");
                    }
                });
            });
        </script>
    @endpush
@endsection
