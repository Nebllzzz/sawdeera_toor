@extends('layouts.main')
@section('title', 'Tour Leader')

@section('content')

    <div class="content-wrapper">

        <section class="content">

            <div class="container-fluid">

                <div class="card mt-3">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <h3>Data Tour Leader</h3>

                        <button class="btn btn-sawdeera1" data-toggle="modal" data-target="#modalLeader"
                            onclick="createLeader()">

                            <i class="fas fa-plus mr-2 text-white"></i>

                            Tambah Tour Leader

                        </button>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-striped text-center" id="dt">

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
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                serverSide: true,

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

            }


            $(document).on("click", ".editLeader", function() {

                let btn = $(this);

                $("#modalTitle").text("Edit Tour Leader");

                $("#formLeader").attr("action", "/tour-leader/update/" + btn.data("id"));

                $("#nama").val(btn.data("nama"));
                $("#no_telepon").val(btn.data("telepon"));
                $("#email").val(btn.data("email"));
                $("#alamat").val(btn.data("alamat"));
                $("#jenis_kelamin").val(btn.data("jk"));

                $("#modalLeader").modal("show");

            });


            $("#formLeader").submit(function(e) {

                e.preventDefault();

                $.post($(this).attr("action"), $(this).serialize(), function(res) {

                    Swal.fire("Success", res.message, "success");

                    $("#modalLeader").modal("hide");

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
