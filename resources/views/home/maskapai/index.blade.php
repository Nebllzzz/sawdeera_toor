@extends('layouts.main')
@section('title', 'Maskapai')

@section('content')

    <div class="content-wrapper">

        <section class="content">

            <div class="container-fluid">

                <x-page-heading
                    title="Data Maskapai"
                    description="Kelola informasi maskapai untuk jadwal keberangkatan dan kepulangan."
                    section="Master Data"
                    current="Maskapai"
                />

                <div class="card mt-3">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <h3>Data Maskapai</h3>

                        <button type="button" class="btn btn-sawdeera1" id="btnAddMaskapai">

                            <i class="fas fa-plus mr-2 text-white"></i>

                            Tambah Maskapai

                        </button>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover align-middle" id="dt">

                                <thead>

                                    <tr>
                                        <th>No</th>
                                        <th>Airline Code</th>
                                        <th>ICAO Code</th>
                                        <th>Nama</th>
                                        <th>Asal Negara</th>
                                        <th>Status</th>
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

    <div class="modal fade" id="modalMaskapai">

        <div class="modal-dialog">

            <div class="modal-content">

                <form id="formMaskapai">

                    @csrf

                    <div class="modal-header">

                        <h4 id="modalTitle">Tambah Maskapai</h4>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Airline Code</label>
                            <input type="text" name="airline_code" id="airline_code" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>ICAO Code</label>
                            <input type="text" name="airline_icao_code" id="airline_icao_code" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Nama Maskapai</label>
                            <input type="text" name="nama" id="nama" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Asal Negara</label>
                            <input type="text" name="asal_negara" id="asal_negara" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Status</label>

                            <select name="is_active" id="is_active" class="form-control">

                                <option value="" selected disabled>Pilih Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>

                            </select>

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
                    url: "/maskapai/data",
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
                        data: "airline_code"
                    },
                    {
                        data: "airline_icao_code"
                    },
                    {
                        data: "nama"
                    },
                    {
                        data: "asal_negara"
                    },
                    {
                        data: "statusLabel"
                    },
                    {
                        data: "action",
                        orderable: false,
                        searchable: false
                    }

                ]

            });


            function createMaskapai() {

                $("#modalTitle").text("Tambah Maskapai");

                $("#formMaskapai").attr("action", "/maskapai/store");

                $("#formMaskapai")[0].reset();

                showAppModal("modalMaskapai");

            }

            $("#btnAddMaskapai").on("click", createMaskapai);


            $(document).on("click", ".editMaskapai", function() {

                let btn = $(this);

                $("#modalTitle").text("Edit Maskapai");

                $("#formMaskapai").attr("action", "/maskapai/update/" + btn.data("id"));

                $("#airline_code").val(btn.data("code"));
                $("#airline_icao_code").val(btn.data("icao"));
                $("#nama").val(btn.data("nama"));
                $("#asal_negara").val(btn.data("negara"));
                $("#is_active").val(btn.data("status"));

                showAppModal("modalMaskapai");

            });


            $("#formMaskapai").submit(function(e) {

                e.preventDefault();

                $.post($(this).attr("action"), $(this).serialize(), function(res) {

                    Swal.fire("Success", res.message, "success");

                    hideAppModal("modalMaskapai");

                    $("#dt").DataTable().ajax.reload();

                });

            });


            $(document).on("click", ".deleteMaskapai", function() {

                let id = $(this).data("id");

                Swal.fire({

                    title: "Yakin?",
                    icon: "warning",
                    showCancelButton: true

                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({

                            url: "/maskapai/delete/" + id,
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
