@extends('layouts.main')
@section('title', 'Hotel')

@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">

                <x-page-heading
                    title="Data Hotel"
                    description="Kelola hotel Makkah dan Madinah yang digunakan pada paket umrah."
                    section="Master Data"
                    current="Hotel"
                />

                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">

                        <h3>Data Hotel</h3>

                        <button type="button" class="btn btn-sawdeera1" id="btnAddHotel">
                            <i class="fas fa-plus mr-2 text-white"></i>
                            Tambah Hotel
                        </button>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover align-middle" id="dt">

                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Lokasi</th>
                                        <th>Bintang</th>
                                        <th>Tipe Kamar</th>
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

    <div class="modal fade" id="modalHotel">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formHotel" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 id="modalTitle">Tambah Hotel</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Nama Hotel</label>
                            <input type="text" name="nama" id="nama" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Lokasi</label>
                            <select name="lokasi" id="lokasi" class="form-control">
                                <option value="mekkah">Mekkah</option>
                                <option value="madinah">Madinah</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Bintang</label>
                            <input type="number" name="bintang" id="bintang" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Tipe Kamar</label>
                            <select name="tipe_kamar" id="tipe_kamar" class="form-control">
                                <option value="" selected disabled>Pilih Tipe Kamar</option>
                                <option value="double">Double</option>
                                <option value="triple">Triple</option>
                                <option value="quad">Quad</option>
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
                    url: "/hotel/data",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    }
                },

                columns: [{
                        data: "DT_RowIndex",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "nama"
                    },
                    {
                        data: "lokasiLabel"
                    },
                    {
                        data: "bintangLabel"
                    },
                    {
                        data: "tipeLabel"
                    },
                    {
                        data: "action",
                        orderable: false,
                        searchable: false
                    }
                ]

            });


            function createHotel() {

                $("#modalTitle").text("Tambah Hotel");

                $("#formHotel").attr("action", "/hotel/store");

                $("#formHotel")[0].reset();

                showAppModal("modalHotel");

            }

            $("#btnAddHotel").on("click", createHotel);


            $(document).on("click", ".editHotel", function() {

                let btn = $(this);

                $("#modalTitle").text("Edit Hotel");

                $("#formHotel").attr("action", "/hotel/update/" + btn.data("id"));

                $("#nama").val(btn.data("nama"));
                $("#lokasi").val(btn.data("lokasi"));
                $("#bintang").val(btn.data("bintang"));
                $("#tipe_kamar").val(btn.data("tipe"));

                showAppModal("modalHotel");

            });


            $("#formHotel").submit(function(e) {

                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({

                    url: $("#formHotel").attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(res) {

                        hideAppModal("modalHotel");

                        Swal.fire("Success", res.message, "success");

                        $("#dt").DataTable().ajax.reload();

                    }

                });

            });


            $(document).on("click", ".deleteHotel", function() {

                let id = $(this).data("id");

                Swal.fire({
                    title: "Yakin?",
                    icon: "warning",
                    showCancelButton: true
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({

                            url: "/hotel/delete/" + id,
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
