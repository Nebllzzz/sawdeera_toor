@extends('layouts.main')
@section('title', 'Data Jemaah')
@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">

                        <div class="card mt-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h2 class="mb-0">Data Jemaah</h2>
                                <button class="btn btn-sawdeera1" onclick="createJemaah()" data-toggle="modal"
                                    data-target="#modalJemaah">
                                    <i class="fas fa-plus me-2 text-white"></i>Tambah Data
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="dt">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>NIK</th>
                                                <th>No HP</th>
                                                <th>Status</th>
                                                <th>Status Data</th>
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

                <form id="formJemaah" enctype="multipart/form-data">
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

                            <div class="col-12"><hr><h5>Kontak, Paspor & Kesehatan</h5></div>
                            <div class="col-md-6"><label>Kontak Darurat</label><input id="kontak_darurat" name="kontak_darurat" class="form-control mb-2"></div>
                            <div class="col-md-6"><label>Hubungan Kontak Darurat</label><input id="hubungan_kontak_darurat" name="hubungan_kontak_darurat" class="form-control mb-2"></div>
                            <div class="col-md-6"><label>Nomor Paspor</label><input id="nomor_paspor" name="nomor_paspor" class="form-control mb-2"></div>
                            <div class="col-md-6"><label>Tempat Penerbitan</label><input id="tempat_penerbitan_paspor" name="tempat_penerbitan_paspor" class="form-control mb-2"></div>
                            <div class="col-md-6"><label>Tanggal Terbit Paspor</label><input type="date" id="tanggal_terbit_paspor" name="tanggal_terbit_paspor" class="form-control mb-2"></div>
                            <div class="col-md-6"><label>Tanggal Kedaluwarsa Paspor</label><input type="date" id="tanggal_kedaluwarsa_paspor" name="tanggal_kedaluwarsa_paspor" class="form-control mb-2"></div>
                            <div class="col-md-6"><label>Golongan Darah</label><select id="golongan_darah" name="golongan_darah" class="form-control mb-2"><option value="">Pilih</option><option>A</option><option>B</option><option>AB</option><option>O</option></select></div>
                            <div class="col-md-6"><label>Scan Paspor</label><input type="file" name="scan_paspor" class="form-control-file mb-2"></div>
                            <div class="col-md-6"><label>Riwayat Penyakit</label><textarea id="riwayat_penyakit" name="riwayat_penyakit" class="form-control"></textarea></div>
                            <div class="col-md-6"><label>Alergi</label><textarea id="alergi" name="alergi" class="form-control"></textarea></div>
                            <div class="col-md-6 mt-2"><label>Foto Profil</label><input type="file" name="foto_profil" class="form-control-file"></div>

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
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-white">
                    <div>
                        <h4 class="mb-0 font-weight-bold">
                            Detail Data Jemaah
                        </h4>
                        <small class="text-muted">
                            Informasi lengkap jemaah
                        </small>
                    </div>

                    <button class="close" data-dismiss="modal">
                        &times;
                    </button>
                </div>

                <div class="modal-body">

                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-6">
                                    <label class="text-muted mb-1">Nama</label>
                                    <h5 id="dNama"></h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="text-muted mb-1">Email</label>
                                    <h5 id="dEmail"></h5>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label class="text-muted">Status Data</label>
                                    <div id="dStatus"></div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div id="dMore"></div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalStatusData">
        <div class="modal-dialog"><div class="modal-content">
            <form id="formStatusData">@csrf
                <div class="modal-header"><h5>Verifikasi Data Jemaah</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <label>Status Data</label>
                    <select name="status_data" class="form-control mb-3" required>
                        <option value="terverifikasi">Terverifikasi</option>
                        <option value="perlu_perbaikan">Perlu Perbaikan</option>
                    </select>
                    <label>Catatan Admin <small>(wajib jika perlu perbaikan)</small></label>
                    <textarea name="catatan_admin" class="form-control" rows="4"></textarea>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Simpan Status Data</button></div>
            </form>
        </div></div>
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
                serverSide: false,
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
                        data: 'statusData',
                        name: 'data_jemaah.status_data'
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
                    ['kontak_darurat','hubungan_kontak_darurat','nomor_paspor','tempat_penerbitan_paspor','tanggal_terbit_paspor','tanggal_kedaluwarsa_paspor','golongan_darah','riwayat_penyakit','alergi'].forEach(k => $("#" + k).val(j[k] || ''));

                    $("#modalJemaah").modal("show");
                });

            });

            // SUBMIT
            $("#formJemaah").submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr("action"), method: "POST",
                    data: new FormData(this), processData: false, contentType: false,
                    success: res => {
                        $("#modalJemaah").modal("hide");
                        Swal.fire("Success", res.message, "success");
                        dt.ajax.reload();
                    },
                    error: xhr => Swal.fire("Data belum valid", xhr.responseJSON?.message || "Periksa kembali form.", "error")
                });
            });

            let statusDataId;
            $(document).on("click", ".toggleData", function() {
                statusDataId = $(this).data("id");
                $.get('/jemaah/detail/' + statusDataId, res => {
                    $('#formStatusData [name=status_data]').val(res.jemaah?.status_data || 'menunggu_verifikasi');
                    $('#formStatusData [name=catatan_admin]').val(res.jemaah?.catatan_admin || '');
                    $('#modalStatusData').modal('show');
                });
            });
            $('#formStatusData').submit(function(e) {
                e.preventDefault();
                $.post('/jemaah/toggle-data/' + statusDataId, $(this).serialize())
                    .done(res => { $('#modalStatusData').modal('hide'); Swal.fire("Success", res.message, "success"); dt.ajax.reload(); })
                    .fail(xhr => Swal.fire("Data belum valid", xhr.responseJSON?.message || "Periksa catatan admin.", "error"));
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

                    let j = res.jemaah || {};

                    $("#dNama").text(res.name);
                    $("#dEmail").text(res.email);
                    $("#dStatus").html(`
                        <span class="badge badge-${j.status_data == 'terverifikasi' ? 'success' : (j.status_data == 'perlu_perbaikan' ? 'danger' : 'warning')}">
                            ${j.status_data.replaceAll('_', ' ').toUpperCase()}
                        </span>
                    `);
                    $("#dNik").text(j.nik);
                    $("#dTelp").text(j.no_telepon);
                    $("#dTTL").text((j.tempat_lahir + ', ' + j.tanggal_lahir) ?? '-');
                    $("#dAlamat").text(j.alamat);
                    $("#dPekerjaan").text(j.pekerjaan);
                    $("#dNikah").text(j.status_pernikahan);
                    const labels = {
                        jenis_kelamin:'Jenis Kelamin', kontak_darurat:'Kontak Darurat',
                        hubungan_kontak_darurat:'Hubungan Kontak', nomor_paspor:'Nomor Paspor',
                        tanggal_terbit_paspor:'Tanggal Terbit', tanggal_kedaluwarsa_paspor:'Tanggal Kedaluwarsa',
                        tempat_penerbitan_paspor:'Tempat Penerbitan', golongan_darah:'Golongan Darah',
                        riwayat_penyakit:'Riwayat Penyakit', alergi:'Alergi', status_data:'Status Data',
                        catatan_admin:'Catatan Admin'
                    };

                    $("#dMore").html(`

                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-white">
                                <b>Data Pribadi</b>
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    ${item("NIK", j.nik)}
                                    ${item("Jenis Kelamin", j.jenis_kelamin)}
                                    ${item("Tempat, Tanggal Lahir", (j.tempat_lahir + ', ' + j.tanggal_lahir) ?? '-')}
                                    ${item("No HP", j.no_telepon)}
                                    ${item("Status Pernikahan", j.status_pernikahan)}
                                    ${item("Pekerjaan", j.pekerjaan)}

                                </div>

                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-3">

                            <div class="card-header bg-white">
                                <b>Alamat</b>
                            </div>

                            <div class="card-body">
                                ${j.alamat ?? '-'}
                            </div>

                        </div>

                        <div class="card shadow-sm border-0 mb-3">

                            <div class="card-header bg-white">
                                <b>Informasi Paspor</b>
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    ${item("Nomor Paspor", j.nomor_paspor)}
                                    ${item("Tempat Terbit", j.tempat_penerbitan_paspor)}
                                    ${item("Tanggal Terbit", j.tanggal_terbit_paspor)}
                                    ${item("Tanggal Kadaluarsa", j.tanggal_kedaluwarsa_paspor)}

                                    <div class="col-md-6 mb-3">
                                        <small class="text-muted d-block">
                                            Scan Paspor
                                        </small>

                                        ${
                                            j.scan_paspor
                                            ? `<a target="_blank"
                                                class="btn btn-outline-primary btn-sm mt-2"
                                                href="/storage/${j.scan_paspor}">
                                                Lihat Dokumen
                                            </a>`
                                            : '-'
                                        }

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="card shadow-sm border-0 mb-3">

                            <div class="card-header bg-white">
                                <b>Informasi Kesehatan</b>
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    ${item("Golongan Darah", j.golongan_darah)}
                                    ${item("Riwayat Penyakit", j.riwayat_penyakit)}
                                    ${item("Alergi", j.alergi)}

                                </div>

                            </div>

                        </div>

                        <div class="card shadow-sm border-0">

                            <div class="card-header bg-white">
                                <b>Kontak Darurat</b>
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    ${item("Nama", j.kontak_darurat)}
                                    ${item("Hubungan", j.hubungan_kontak_darurat)}

                                </div>

                            </div>

                        </div>

                        <div class="card shadow-sm border-0">

                            <div class="card-header bg-white">
                                <b>Catatan Admin</b>
                            </div>

                            <div class="card-body">

                                <div class="border rounded p-3 bg-light">
                                    ${j.catatan_admin ?? '-'}
                                </div>

                            </div>

                        </div>

                    `);

                    $("#modalDetail").modal("show");
                });

            });

            function item(label, value){
                return `
                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">${label}</small>
                    <strong>${value || '-'}</strong>
                </div>
                `;
            }

            function item(label, value){
                return `
                <div class="col-md-6 mb-3">
                    <small class="text-muted d-block">${label}</small>
                    <strong>${value || '-'}</strong>
                </div>
                `;
            }
        </script>
    @endpush

@endsection
