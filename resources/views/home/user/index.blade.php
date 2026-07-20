@extends('layouts.main')
@section('title', 'Admin & Operator')
@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <x-page-heading title="Data Admin" description="Kelola akun admin dan operator yang memiliki akses ke sistem."
                    section="Pengaturan" current="Data Admin" />

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mt-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h2 class="mb-0">Data Admin</h2>
                                <button class="btn btn-sawdeera1" id="btnTambahUser">
                                    <i class="fas fa-plus mr-2 text-white"></i>Tambah Data
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0" id="dt"
                                        style="
                                            width:100%;
                                            border-collapse:separate;
                                            border-spacing:0;
                                            border:1px solid #E8DED5;
                                            border-radius:10px;
                                            overflow:hidden;
                                        ">
                                        <thead style="background-color:#FBF6F1;">
                                            <tr>
                                                <th
                                                    style="padding:14px 16px; font-weight:600; border-bottom:1px solid #E8DED5;">
                                                    No</th>
                                                <th
                                                    style="padding:14px 16px; font-weight:600; border-bottom:1px solid #E8DED5;">
                                                    Nama</th>
                                                <th
                                                    style="padding:14px 16px; font-weight:600; border-bottom:1px solid #E8DED5;">
                                                    Email</th>
                                                <th
                                                    style="padding:14px 16px; font-weight:600; border-bottom:1px solid #E8DED5;">
                                                    Role</th>
                                                <th
                                                    style="padding:14px 16px; font-weight:600; border-bottom:1px solid #E8DED5;">
                                                    Status</th>
                                                <th
                                                    style="padding:14px 16px; font-weight:600; border-bottom:1px solid #E8DED5;">
                                                    Aksi</th>
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

        @if (session('berhasil'))
            <script>
                Swal.fire({
                    title: "Good job!",
                    text: "{{ session('berhasil') }}",
                    icon: "success"
                });
            </script>
        @elseif (session('gagal'))
            <script>
                Swal.fire({
                    title: "Error!",
                    text: "{{ session('gagal') }}",
                    icon: "error"
                });
            </script>
        @endif

    </div>

    <div class="modal fade" id="modalUser" tabindex="-1" role="dialog"
        aria-labelledby="modalTitle" aria-hidden="true">

        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <form id="formUser" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title" id="modalTitle">Tambah Admin</h4>

                        <button type="button"
                            class="close btnCloseModal"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" minlength="3" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password <small class="text-muted" id="passwordHint"></small></label>
                            <input type="password" id="password" name="password" class="form-control" minlength="6">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" minlength="6">
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="operator" @selected(old('role', 'operator') === 'operator')>Admin</option>
                                <option value="admin" @selected(old('role') === 'admin')>Pimpinan</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif</option>
                                <option value="tidak_aktif" @selected(old('status') === 'tidak_aktif')>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-secondary btnCloseModal" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-sawdeera1">
                            Simpan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <style>
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
            font-weight: 400;
        }

        #dt_wrapper .dataTables_length select {
            min-width: 78px;
            height: 40px;
            margin: 6px 6px 0 6px;
            padding: 6px 28px 6px 12px;
            border: 1px solid #E4E7EC;
            border-radius: 8px;
            background-color: #F8F9FB;
        }

        /* Search */
        #dt_wrapper .dataTables_filter {
            text-align: right;
        }

        #dt_wrapper .dataTables_filter label {
            margin-bottom: 0;
            font-weight: 400;
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

        #dt_wrapper .dataTables_filter input:focus {
            border-color: #E39A1B;
            box-shadow: 0 0 0 3px rgba(227, 154, 27, 0.12);
        }

        /* Isi tabel */
        #dt tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-top: none;
            border-bottom: 1px solid #E8DED5;
        }

        #dt tbody tr:last-child td {
            border-bottom: none;
        }

        #dt tbody tr:hover {
            background-color: #FFFCF9;
        }

        #dt thead th {
            white-space: nowrap;
            border-top: none;
        }

        /* Hilangkan border bawaan Bootstrap/DataTables yang bertumpuk */
        #dt.table {
            border-bottom: 1px solid #E8DED5 !important;
        }

        /* Mobile */
        @media (max-width: 767.98px) {
            #dt_wrapper .dataTables_filter {
                margin-top: 12px;
                text-align: left;
            }

            #dt_wrapper .dataTables_filter input {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>

    @push('scripts')
        <script>
            var datatable = $("#dt").DataTable({
                language: {
                    lengthMenu: "Show _MENU_"
                },

                dom: "<'row'" +
                    "<'col-sm-6 d-flex align-items-center justify-content-start'l>" +
                    "<'col-sm-6 d-flex align-items-center justify-content-end'f>" +
                    ">" +

                    "<'table-responsive'tr>" +

                    "<'row'" +
                    "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
                    "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                    ">",

                processing: true,
                serverSide: false,

                ajax: {
                    url: "/user/data",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    }
                },

                lengthMenu: [10, 25, 50, 100],

                columns: [{
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

            $(document).on("click", ".btnCloseModal", function (e) {
                e.preventDefault();
                e.stopPropagation();

                $("#modalUser").modal("hide");
            });

            $("#modalUser").on("hidden.bs.modal", function () {
                $("body").removeClass("modal-open");
                $(".modal-backdrop").remove();
            });

            $(document).on("click", ".deleteUser", function() {
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

                            success: function(res) {
                                Swal.fire("Success", res.message, "success");

                                $("#dt").DataTable().ajax.reload();
                            },
                        });
                    }
                });
            });

            $(document).on("click", "#btnTambahUser", function() {
                $("#modalTitle").text("Tambah Admin");

                $("#formUser").attr("action", "/user/store");

                $("#name").val("");
                $("#email").val("");
                $("#password").val("");
                $("#password_confirmation").val("");
                $("#password").prop("required", true);
                $("#password_confirmation").prop("required", true);
                $("#passwordHint").text("(wajib diisi)");
                $("#role").val("operator");
                $("#status").val("aktif");

                // TAMBAHKAN BARIS INI UNTUK MEMUNCULKAN MODAL
                $("#modalUser").modal("show");
            });

            $(document).on("click", ".editUser", function() {
                let btn = $(this);

                $("#modalTitle").text("Edit User");

                $("#formUser").attr("action", "/user/update/" + btn.data("id"));

                $("#name").val(btn.data("name"));
                $("#email").val(btn.data("email"));
                $("#role").val(btn.data("role"));
                $("#status").val(btn.data("status"));
                $("#password, #password_confirmation").val("").prop("required", false);
                $("#passwordHint").text("(kosongkan jika tidak diubah)");

                $("#modalUser").modal("show");
            });

            $("#formUser").submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: $("#formUser").attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(res) {
                        $("#modalUser").modal("hide");

                        Swal.fire({
                            icon: "success",
                            text: res.message,
                        });

                        $("#dt").DataTable().ajax.reload();
                    },

                    error: function(err) {
                        const errors = err.responseJSON?.errors;
                        const message = errors ? Object.values(errors).flat()[0] : (err.responseJSON?.message || "Validasi gagal");
                        Swal.fire("Error", message, "error");
                    },
                });
            });
        </script>
    @endpush

@endsection
