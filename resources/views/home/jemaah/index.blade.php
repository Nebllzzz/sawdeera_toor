@extends('layouts.main')
@section('title', 'Data Jemaah')
@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">

                        <div class="card mt-3">
                            <div class="card-header">
                                <h2>Data Jemaah</h2>
                                <button class="btn btn-sawdeera1" onclick="createJemaah()" data-toggle="modal"
                                    data-target="#modalJemaah">
                                    Tambah Data
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped text-center align-middle" id="dt">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>NIK</th>
                                                <th>No HP</th>
                                                <th>Status</th>
                                                <th>Terakhir Ditangani</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- SWEET ALERT --}}
        @if (session('berhasil'))
            <script>
                Swal.fire("Success", "{{ session('berhasil') }}", "success");
            </script>
        @endif

        @if (session('gagal'))
            <script>
                Swal.fire("Error", "{{ session('gagal') }}", "error");
            </script>
        @endif

    </div>

    {{-- ================= MODAL FORM ================= --}}
    <div class="modal fade" id="modalJemaah">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form id="formJemaah">
                    @csrf

                    <div class="modal-header">
                        <h4 id="modalTitle">Tambah Jemaah</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6">
                                <label>Nama</label>
                                <input type="text" id="name" name="name" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" id="email" name="email" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Confirm</label>
                                <input type="password" name="password_confirmation" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>NIK</label>
                                <input type="text" id="nik" name="nik" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Jenis Kelamin</label>
                                <select id="jk" name="jenis_kelamin" class="form-control mb-2">
                                    <option value="laki_laki">Laki-laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>No Telepon</label>
                                <input type="text" id="telp" name="no_telepon" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Tempat Lahir</label>
                                <input type="text" id="tempat" name="tempat_lahir" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Tanggal Lahir</label>
                                <input type="date" id="tgl" name="tanggal_lahir" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Status Pernikahan</label>
                                <select id="nikah" name="status_pernikahan" class="form-control mb-2">
                                    <option value="menikah">Menikah</option>
                                    <option value="belum_menikah">Belum</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Pekerjaan</label>
                                <input type="text" id="pekerjaan" name="pekerjaan" class="form-control mb-2">
                            </div>

                            <div class="col-md-6">
                                <label>Alamat</label>
                                <textarea id="alamat" name="alamat" class="form-control"></textarea>
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

    {{-- ================= MODAL DETAIL ================= --}}
    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog">
            <div class="modal-content p-3">

                <p><b>Nama:</b> <span id="dNama"></span></p>
                <p><b>Email:</b> <span id="dEmail"></span></p>
                <p><b>NIK:</b> <span id="dNik"></span></p>
                <p><b>No HP:</b> <span id="dTelp"></span></p>
                <p><b>TTL:</b> <span id="dTTL"></span></p>
                <p><b>Alamat:</b> <span id="dAlamat"></span></p>
                <p><b>Pekerjaan:</b> <span id="dPekerjaan"></span></p>
                <p><b>Status Nikah:</b> <span id="dNikah"></span></p>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let dt = $("#dt").DataTable({
                dom: "<'row'" +
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
                    url: '/jemaah/data',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'users.name' // 🔥 penting
                    },
                    {
                        data: 'email',
                        name: 'users.email'
                    },
                    {
                        data: 'nik',
                        name: 'data_jemaah.nik'
                    },
                    {
                        data: 'telepon',
                        name: 'data_jemaah.no_telepon'
                    },
                    {
                        data: 'statusActivity',
                        name: 'users.status'
                    },
                    {
                        data: 'operator',
                        name: 'operator.name'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // CREATE
            function createJemaah() {
                $("#modalTitle").text("Tambah Jemaah");
                $("#formJemaah").attr("action", "/jemaah/store");
                $("#formJemaah")[0].reset();
            }

            // EDIT
            $(document).on("click", ".editJemaah", function () {

                let id = $(this).data("id");

                $.get('/jemaah/detail/' + id, res => {

                    let j = res.jemaah || {};

                    $("#modalTitle").text("Edit Jemaah");
                    $("#formJemaah").attr("action", "/jemaah/update/" + res.id);

                    $("#name").val(res.name);
                    $("#email").val(res.email);

                    $("#nik").val(j.nik || '');
                    $("#jk").val(j.jenis_kelamin || '');
                    $("#telp").val(j.no_telepon || '');
                    $("#tempat").val(j.tempat_lahir || '');
                    $("#tgl").val(j.tanggal_lahir || '');
                    $("#alamat").val(j.alamat || '');
                    $("#pekerjaan").val(j.pekerjaan || '');
                    $("#nikah").val(j.status_pernikahan || '');

                    $("#modalJemaah").modal("show");
                });

            });

            // SUBMIT
            $("#formJemaah").submit(function(e) {
                e.preventDefault();

                $.post($(this).attr("action"), $(this).serialize(), res => {
                    $("#modalJemaah").modal("hide");
                    Swal.fire("Success", res.message, "success");
                    dt.ajax.reload();
                });
            });

            // DELETE
            $(document).on("click", ".deleteJemaah", function() {

                let id = $(this).data("id");

                Swal.fire({
                        title: "Yakin?",
                        icon: "warning",
                        showCancelButton: true
                    })
                    .then(r => {
                        if (r.isConfirmed) {
                            $.ajax({
                                url: '/jemaah/delete/' + id,
                                type: 'DELETE',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: (res) => {
                                    Swal.fire("Success", res.message, "success");
                                    dt.ajax.reload();
                                }
                            });
                        }
                    });
            });

            // TOGGLE
            $(document).on("click", ".toggleStatus", function() {

                let id = $(this).data("id");

                $.post('/jemaah/toggle/' + id, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, res => {
                    Swal.fire("Success", res.message, "success");
                    dt.ajax.reload();
                });

            });

            // DETAIL
            $(document).on("click", ".detailJemaah", function() {

                let id = $(this).data("id");

                $.get('/jemaah/detail/' + id, res => {

                    let j = res.jemaah;

                    $("#dNama").text(res.name);
                    $("#dEmail").text(res.email);
                    $("#dNik").text(j.nik);
                    $("#dTelp").text(j.no_telepon);
                    $("#dTTL").text(j.tempat_lahir + ', ' + j.tanggal_lahir);
                    $("#dAlamat").text(j.alamat);
                    $("#dPekerjaan").text(j.pekerjaan);
                    $("#dNikah").text(j.status_pernikahan);

                    $("#modalDetail").modal("show");
                });

            });
        </script>
    @endpush

@endsection
