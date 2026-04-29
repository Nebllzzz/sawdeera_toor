@extends('layouts.main')
@section('title', 'Admin & Operator')
@section('content')

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h2>Data Admin & Operator</h2>
                            <button class="btn btn-sawdeera1" data-toggle="modal" data-target="#modalUser" onclick="createUser()">Tambah Data</button>
                        </div>
                        <div class="card-body">
                            <div
                                class="table-responsive">
                                <table class="table table-striped text-center align-middle" id="dt">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Role</th>
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
            </div>
        </div>
    </section>

    @if(session('berhasil'))
    <script>
        Swal.fire({
        title: "Good job!",
        text: "{{session('berhasil')}}",
        icon: "success"
        });
    </script>
    @elseif (session('gagal'))
    <script>
        Swal.fire({
        title: "Error!",
        text: "{{session('gagal')}}",
        icon: "error"
        });
    </script>
    @endif

</div>

<div class="modal fade" id="modalUser">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formUser" method="POST">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title" id="modalTitle">Tambah User</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" id="role" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="operator">Operator</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-sawdeera1">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>


@push('scripts')
    <script>
        var datatable = $("#dt").DataTable({
            language: {
                lengthMenu: "Show _MENU_"
            },

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

            processing: true,
            serverSide: true,

            ajax: {
                url: "/user/data",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                }
            },

            lengthMenu: [10, 25, 50, 100],

            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "name",
                    name: "name"
                },
                {
                    data: "email",
                    name: "email"
                },
                {
                    data: "roles",
                    name: "roles"
                },
                {
                    data: "statusActivity",
                    name: "statusActivity"
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $(document).on("click", ".deleteUser", function () {
        let id = $(this).data("id");

        Swal.fire({
            title: "Yakin?",
            icon: "warning",
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
            $.ajax({
                url: "/user/delete/" + id,
                method: "DELETE",
                data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                },

                success: function (res) {
                Swal.fire("Success", res.message, "success");

                $("#dt").DataTable().ajax.reload();
                },
            });
            }
        });
        });

        function createUser() {
        $("#modalTitle").text("Tambah User");

        $("#formUser").attr("action", "/user/store");

        $("#formUser")[0].reset();
        }

        $(document).on("click", ".editUser", function () {
        let btn = $(this);

        $("#modalTitle").text("Edit User");

        $("#formUser").attr("action", "/user/update/" + btn.data("id"));

        $("#name").val(btn.data("name"));
        $("#email").val(btn.data("email"));
        $("#role").val(btn.data("role"));
        $("#status").val(btn.data("status"));

        $("#modalUser").modal("show");
        });

        $("#formUser").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: $("#formUser").attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function (res) {
            $("#modalUser").modal("hide");

            Swal.fire({
                icon: "success",
                text: res.message,
            });

            $("#dt").DataTable().ajax.reload();
            },

            error: function (err) {
            Swal.fire("Error", "Validasi gagal", "error");
            },
        });
        });
    </script>
@endpush

@endsection
