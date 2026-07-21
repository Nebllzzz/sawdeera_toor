@extends('layouts.main')
@section('title', 'Tour Leader')

@section('content')

    <div class="content-wrapper">

        <section class="content">

            <div class="container-fluid">

                <x-page-heading
                    title="Data Tour Leader"
                    description="Kelola data pembimbing yang bertugas pada perjalanan umrah."
                    section="Master Data"
                    current="Tour Leader"
                />

                <div class="card mt-3">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <h3>Data Tour Leader</h3>

                        <button type="button" class="btn btn-sawdeera1" id="btnAddLeader">

                            <i class="fas fa-plus mx-2 text-white"></i>

                            Tambah Tour Leader

                        </button>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover align-middle" id="dt">

                                <thead>

                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>No Telepon</th>
                                        <th>Email</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Aksi</th>
                                    </tr>

                                </thead>

                                <tbody></tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>

    <div class="modal fade" id="modalLeader">

        <div class="modal-dialog">

            <div class="modal-content">

                <form id="formLeader">

                    @csrf

                    <div class="modal-header">
                        <h4 id="modalTitle">Tambah Tour Leader</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>No Telepon</label>
                            <input type="text" name="no_telepon" id="no_telepon" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Jenis Kelamin</label>

                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">

                                <option value="" selected disabled>Pilih Jenis Kelamin</option>
                                <option value="laki_laki">Laki-laki</option>
                                <option value="perempuan">Perempuan</option>

                            </select>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-sawdeera1">
                            Simpan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <style>
        /* =========================
        TABEL KEBERANGKATAN
        ========================= */
        #dt {
            width: 100% !important;
            margin-bottom: 0 !important;
            border: 1px solid #E8DED5;
            border-collapse: separate !important;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        #dt thead {
            background-color: #FBF6F1;
        }

        #dt thead th {
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #1F2937;
            white-space: nowrap;
            vertical-align: middle;
            border-top: 0 !important;
            border-bottom: 1px solid #E8DED5 !important;
        }

        #dt tbody td {
            padding: 14px 16px;
            font-size: 13px;
            color: #1F2937;
            vertical-align: middle;
            border-top: 0 !important;
            border-bottom: 1px solid #E8DED5 !important;
        }

        #dt tbody tr:last-child td {
            border-bottom: 0 !important;
        }

        #dt tbody tr:hover {
            background-color: #FFFCF9;
        }

        table.dataTable#dt,
        table.dataTable#dt.no-footer {
            border-bottom: 1px solid #E8DED5 !important;
        }

        /* Jarak kontrol DataTables dengan tabel */
        #dt_wrapper .row:first-child {
            margin-bottom: 16px;
            align-items: flex-end;
        }

        #dt_wrapper .row:last-child {
            margin-top: 16px;
            align-items: center;
        }

        /* Dropdown jumlah data */
        #dt_wrapper .dataTables_length label {
            margin-bottom: 0;
            font-size: 13px;
            font-weight: 400;
            color: #475467;
        }

        #dt_wrapper .dataTables_length select {
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
        #dt_wrapper .dataTables_filter {
            text-align: right;
        }

        #dt_wrapper .dataTables_filter label {
            margin-bottom: 0;
            font-size: 13px;
            font-weight: 400;
            color: #475467;
        }

        #dt_wrapper .dataTables_filter input {
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

        #dt_wrapper .dataTables_filter input:focus,
        #dt_wrapper .dataTables_length select:focus {
            border-color: #E39A1B;
            box-shadow: 0 0 0 3px rgba(227, 154, 27, 0.12);
        }

        /* Informasi tabel */
        #dt_wrapper .dataTables_info {
            padding-top: 0;
            font-size: 13px;
            color: #667085;
        }

        /* Pagination */
        #dt_wrapper .dataTables_paginate {
            padding-top: 0;
        }

        #dt_wrapper .dataTables_paginate .paginate_button {
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

        #dt_wrapper .dataTables_paginate .paginate_button.current,
        #dt_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #E39A1B !important;
            color: #fff !important;
        }

        #dt_wrapper .dataTables_paginate .paginate_button:hover {
            background: #FBF6F1 !important;
            color: #9A640D !important;
        }

        #dt_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: .45;
            cursor: not-allowed !important;
        }

        @media (max-width: 767.98px) {
            #dt_wrapper .dataTables_filter {
                margin-top: 12px;
                text-align: left;
            }

            #dt_wrapper .dataTables_filter input {
                width: 100%;
                margin-left: 0;
            }

            #dt_wrapper .dataTables_info,
            #dt_wrapper .dataTables_paginate {
                float: none;
                text-align: left;
            }

            #dt_wrapper .dataTables_paginate {
                margin-top: 10px;
            }

            #dt thead th,
            #dt tbody td {
                padding: 12px;
                font-size: 12px;
            }
        }
    </style>

    @push('scripts')
        <script>
            var datatable = $("#dt").DataTable({

                dom:
                    "<'row'" +
                        "<'col-sm-6 d-flex align-items-center justify-content-start'l>" +
                        "<'col-sm-6 d-flex align-items-center justify-content-end'f>" +
                    ">" +

                    "<'table-responsive'tr>" +

                    "<'row'" +
                        "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
                        "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                    ">",

                lengthMenu: [10, 25, 50, 100],

                processing: true,
                serverSide: false,

                ajax: {
                    url: "/tour-leader/data",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    }
                },

                columns: [

                    {
                        data: "DT_RowIndex",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "nama"
                    },
                    {
                        data: "no_telepon"
                    },
                    {
                        data: "email"
                    },
                    {
                        data: "jenisKelaminLabel"
                    },
                    {
                        data: "action",
                        orderable: false,
                        searchable: false
                    }

                ]

            });


            function createLeader() {

                $("#modalTitle").text("Tambah Tour Leader");

                $("#formLeader").attr("action", "/tour-leader/store");

                $("#formLeader")[0].reset();

                showAppModal("modalLeader");

            }

            $("#btnAddLeader").on("click", createLeader);


            $(document).on("click", ".editLeader", function() {

                let btn = $(this);

                $("#modalTitle").text("Edit Tour Leader");

                $("#formLeader").attr("action", "/tour-leader/update/" + btn.data("id"));

                $("#nama").val(btn.data("nama"));
                $("#no_telepon").val(btn.data("telepon"));
                $("#email").val(btn.data("email"));
                $("#alamat").val(btn.data("alamat"));
                $("#jenis_kelamin").val(btn.data("jk"));

                showAppModal("modalLeader");

            });


            $("#formLeader").submit(function(e) {

                e.preventDefault();

                $.post($(this).attr("action"), $(this).serialize(), function(res) {

                    Swal.fire("Success", res.message, "success");

                    hideAppModal("modalLeader");

                    $("#dt").DataTable().ajax.reload();

                });

            });


            $(document).on("click", ".deleteLeader", function() {

                let id = $(this).data("id");

                Swal.fire({
                        title: "Yakin?",
                        icon: "warning",
                        showCancelButton: true
                    })

                    .then((result) => {

                        if (result.isConfirmed) {

                            $.ajax({

                                url: "/tour-leader/delete/" + id,
                                method: "DELETE",

                                data: {
                                    _token: $('meta[name="csrf-token"]').attr("content")
                                },

                                success: function(res) {

                                    Swal.fire("Success", res.message, "success");

                                    $("#dt").DataTable().ajax.reload();

                                }

                            });

                        }

                    });

            });
        </script>
    @endpush

@endsection
