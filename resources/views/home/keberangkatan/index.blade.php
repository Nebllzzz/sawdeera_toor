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
                                <i class="fas fa-plus mx-2 text-white"></i>Tambah Jadwal
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
                                        <th>Jumlah Kuota/Terisi</th>
                                        <th>Pengajuan Reschedule</th>
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

    <style>
        /* =========================
        TABEL KEBERANGKATAN
        ========================= */
        #dtKeberangkatan {
            width: 100% !important;
            margin-bottom: 0 !important;
            border: 1px solid #E8DED5;
            border-collapse: separate !important;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        #dtKeberangkatan thead {
            background-color: #FBF6F1;
        }

        #dtKeberangkatan thead th {
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #1F2937;
            white-space: nowrap;
            vertical-align: middle;
            border-top: 0 !important;
            border-bottom: 1px solid #E8DED5 !important;
        }

        #dtKeberangkatan tbody td {
            padding: 14px 16px;
            font-size: 13px;
            color: #1F2937;
            vertical-align: middle;
            border-top: 0 !important;
            border-bottom: 1px solid #E8DED5 !important;
        }

        #dtKeberangkatan tbody tr:last-child td {
            border-bottom: 0 !important;
        }

        #dtKeberangkatan tbody tr:hover {
            background-color: #FFFCF9;
        }

        table.dataTable#dtKeberangkatan,
        table.dataTable#dtKeberangkatan.no-footer {
            border-bottom: 1px solid #E8DED5 !important;
        }

        /* Jarak kontrol DataTables dengan tabel */
        #dtKeberangkatan_wrapper .row:first-child {
            margin-bottom: 16px;
            align-items: flex-end;
        }

        #dtKeberangkatan_wrapper .row:last-child {
            margin-top: 16px;
            align-items: center;
        }

        /* Dropdown jumlah data */
        #dtKeberangkatan_wrapper .dataTables_length label {
            margin-bottom: 0;
            font-size: 13px;
            font-weight: 400;
            color: #475467;
        }

        #dtKeberangkatan_wrapper .dataTables_length select {
            min-width: 76px;
            height: 40px;
            margin: 6px 6px 0;
            padding: 6px 28px 6px 12px;
            border: 1px solid #E4E7EC;
            border-radius: 8px;
            background-color: #F8F9FB;
            outline: none;
        }

        /* Search */
        #dtKeberangkatan_wrapper .dataTables_filter {
            text-align: right;
        }

        #dtKeberangkatan_wrapper .dataTables_filter label {
            margin-bottom: 0;
            font-size: 13px;
            font-weight: 400;
            color: #475467;
        }

        #dtKeberangkatan_wrapper .dataTables_filter input {
            width: 220px;
            height: 40px;
            margin-top: 6px;
            margin-left: 8px;
            padding: 8px 12px;
            border: 1px solid #E4E7EC;
            border-radius: 8px;
            background-color: #F8F9FB;
            outline: none;
        }

        #dtKeberangkatan_wrapper .dataTables_filter input:focus,
        #dtKeberangkatan_wrapper .dataTables_length select:focus {
            border-color: #E39A1B;
            box-shadow: 0 0 0 3px rgba(227, 154, 27, 0.12);
        }

        /* Informasi tabel */
        #dtKeberangkatan_wrapper .dataTables_info {
            padding-top: 0;
            font-size: 13px;
            color: #667085;
        }

        /* Pagination */
        #dtKeberangkatan_wrapper .dataTables_paginate {
            padding-top: 0;
        }

        #dtKeberangkatan_wrapper .dataTables_paginate .paginate_button {
            min-width: 36px;
            height: 36px;
            margin-left: 4px;
            padding: 7px 11px !important;
            border: 0 !important;
            border-radius: 8px !important;
            background: transparent !important;
            color: #667085 !important;
            box-shadow: none !important;
        }

        #dtKeberangkatan_wrapper .dataTables_paginate .paginate_button.current,
        #dtKeberangkatan_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #E39A1B !important;
            color: #fff !important;
        }

        #dtKeberangkatan_wrapper .dataTables_paginate .paginate_button:hover {
            background: #FBF6F1 !important;
            color: #9A640D !important;
        }

        #dtKeberangkatan_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: .45;
            cursor: not-allowed !important;
        }

        @media (max-width: 767.98px) {
            #dtKeberangkatan_wrapper .dataTables_filter {
                margin-top: 12px;
                text-align: left;
            }

            #dtKeberangkatan_wrapper .dataTables_filter input {
                width: 100%;
                margin-left: 0;
            }

            #dtKeberangkatan_wrapper .dataTables_info,
            #dtKeberangkatan_wrapper .dataTables_paginate {
                float: none;
                text-align: left;
            }

            #dtKeberangkatan_wrapper .dataTables_paginate {
                margin-top: 10px;
            }

            #dtKeberangkatan thead th,
            #dtKeberangkatan tbody td {
                padding: 12px;
                font-size: 12px;
            }
        }
    </style>

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
                    { data: "kuota_terisi", orderable: false, searchable: false },
                    { data: "pengajuan_reschedule", orderable: false, searchable: false },
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
