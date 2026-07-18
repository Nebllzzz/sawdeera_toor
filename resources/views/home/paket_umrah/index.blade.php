@extends('layouts.main')
@section('title', 'Paket Umrah')

@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">

                <x-page-heading
                    title="Paket Umrah"
                    description="Kelola paket, harga, hotel, fasilitas, dan program perjalanan umrah."
                    section="Master Data"
                    current="Paket Umrah"
                />

                <div class="card mt-3">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <h3>Data Paket Umrah</h3>

                        <button type="button" class="btn btn-sawdeera1" id="btnAddPaket">

                            <i class="fas fa-plus mr-2 text-white"></i>

                            Tambah Paket

                        </button>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover align-middle" id="dt">

                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Paket</th>
                                        <th>Durasi</th>
                                        <th>Hotel Makkah</th>
                                        <th>Hotel Madinah</th>
                                        <th>Harga</th>
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

    <div class="modal fade" id="modalPaket">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formPaket" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 id="modalTitle">Tambah Paket</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Nama Paket</label>
                            <input type="text" name="nama_paket" id="nama_paket" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Durasi</label>
                            <input type="number" name="durasi" id="durasi" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Hotel Makkah</label>
                            <select name="hotel_makkah_id" id="hotel_makkah_id" class="form-control">

                                <option value="" selected disabled>Pilih Hotel</option>

                                @foreach ($hotelMakkah as $h)
                                    <option value="{{ $h->id }}">
                                        {{ $h->nama }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Hotel Madinah</label>
                            <select name="hotel_madinah_id" id="hotel_madinah_id" class="form-control">

                                <option value="" selected disabled>Pilih Hotel</option>

                                @foreach ($hotelMadinah as $h)
                                    <option value="{{ $h->id }}">
                                        {{ $h->nama }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Harga</label>
                            <input type="text" name="harga" id="harga" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Status</label>
                            <select name="is_active" id="is_active" class="form-control">
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

    <div class="modal fade" id="modalFasilitas">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formFasilitas">
                    @csrf

                    <input type="hidden" id="paket_id_fasilitas" name="paket_id">

                    <div class="modal-header">
                        <h4>Lengkapi Fasilitas</h4>
                    </div>

                    <div class="modal-body">

                        <div id="fasilitasContainer">

                            <div class="row fasilitasRow mb-2">

                                <div class="col-10">
                                    <input type="text" name="nama[]" class="form-control" placeholder="Nama Fasilitas">
                                </div>

                                <div class="col-2">
                                    <button type="button" class="btn btn-danger removeFasilitas">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                            </div>

                        </div>

                        <button type="button" class="btn btn-success" id="addFasilitas">
                            +
                        </button>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-sawdeera1">Simpan</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalProgram">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formProgram">
                    @csrf

                    <input type="hidden" id="paket_id_program" name="paket_id">

                    <div class="modal-header">
                        <h4>Lengkapi Program</h4>
                    </div>

                    <div class="modal-body">

                        <div id="programContainer">

                            <div class="programRow mb-3 border p-2">

                                <div class="row">

                                    <div class="col-md-4">
                                        <input type="number" name="hari[]" class="form-control" placeholder="Hari">
                                    </div>

                                    <div class="col-md-6">
                                        <textarea name="deskripsi[]" class="form-control" placeholder="Deskripsi"></textarea>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger removeProgram">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <button type="button" class="btn btn-success" id="addProgram">
                            +
                        </button>

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
                    url: "/paket-umrah/data",
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
                        data: "nama_paket"
                    },
                    {
                        data: "durasiLabel"
                    },
                    {
                        data: "hotelMakkah"
                    },
                    {
                        data: "hotelMadinah"
                    },
                    {
                        data: "hargaLabel"
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

            new AutoNumeric('#harga', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 0
            });

            function createPaket() {

                $("#modalTitle").text("Tambah Paket");

                $("#formPaket").attr("action", "/paket-umrah/store");

                $("#formPaket")[0].reset();

                showAppModal("modalPaket");

            }

            $("#btnAddPaket").on("click", createPaket);

            $(document).on("click", ".editPaket", function() {

                let btn = $(this);

                $("#modalTitle").text("Edit Paket");

                $("#formPaket").attr("action", "/paket-umrah/update/" + btn.data("id"));

                $("#nama_paket").val(btn.data("nama"));
                $("#durasi").val(btn.data("durasi"));
                $("#hotel_makkah_id").val(btn.data("makkah"));
                $("#hotel_madinah_id").val(btn.data("madinah"));
                $("#harga").val(btn.data("harga"));
                $("#deskripsi").val(btn.data("deskripsi"));
                $("#is_active").val(btn.data("status"));

                showAppModal("modalPaket");

            });

            $("#formPaket").submit(function(e) {

                e.preventDefault();

                let formData = new FormData(this);

                formData.set('harga', AutoNumeric.getNumber('#harga'));

                $.ajax({
                    url: $("#formPaket").attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(res) {

                        hideAppModal("modalPaket");

                        Swal.fire("Success", res.message, "success");

                        $("#dt").DataTable().ajax.reload();
                    }
                });

            });

            $(document).on("click", ".deletePaket", function() {

                let id = $(this).data("id");

                Swal.fire({
                    title: "Yakin?",
                    icon: "warning",
                    showCancelButton: true
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({

                            url: "/paket-umrah/delete/" + id,
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

            $(document).on("click", ".fasilitasPaket", function() {

                let id = $(this).data("id");

                $("#paket_id_fasilitas").val(id);

                loadFasilitas(id);

                showAppModal("modalFasilitas");

            });

            function loadFasilitas(id) {

                $("#fasilitasContainer").html('');

                $.get("/paket-umrah/fasilitas/" + id, function(data) {

                    if (data.length == 0) {

                        addFasilitasRow('');

                    } else {

                        data.forEach(function(item) {

                            addFasilitasRow(item.nama);

                        });

                    }

                });

            }

            function addFasilitasRow(nama = '') {

                let html = `

            <div class="row fasilitasRow mb-2">

            <div class="col-10">
            <input type="text" name="nama[]" class="form-control" value="${nama}">
            </div>

            <div class="col-2">
            <button type="button" class="btn btn-danger removeFasilitas">
            <i class="bi bi-trash"></i>
            </button>
            </div>

            </div>

            `;

                $("#fasilitasContainer").append(html);

            }

            $(document).on("click", ".programPaket", function() {

                let id = $(this).data("id");

                $("#paket_id_program").val(id);

                loadProgram(id);

                showAppModal("modalProgram");

            });

            function loadProgram(id) {

                $("#programContainer").html('');

                $.get("/paket-umrah/program/" + id, function(data) {

                    if (data.length == 0) {

                        addProgramRow('', '');

                    } else {

                        data.forEach(function(item) {

                            addProgramRow(item.hari, item.deskripsi);

                        });

                    }

                });

            }

            function addProgramRow(hari = '', deskripsi = '') {

                let html = `

<div class="programRow mb-3 border p-2">

<div class="row">

<div class="col-md-4">
<input type="number" name="hari[]" class="form-control" value="${hari}">
</div>

<div class="col-md-6">
<textarea name="deskripsi[]" class="form-control">${deskripsi}</textarea>
</div>

<div class="col-md-2">
<button type="button" class="btn btn-danger removeProgram">
<i class="bi bi-trash"></i>
</button>
</div>

</div>

</div>

`;

                $("#programContainer").append(html);

            }

            $("#addFasilitas").click(function() {

                let html = `

                    <div class="row fasilitasRow mb-2">

                    <div class="col-10">
                    <input type="text" name="nama[]" class="form-control">
                    </div>

                    <div class="col-2">
                    <button type="button" class="btn btn-danger removeFasilitas">
                    <i class="bi bi-trash"></i>
                    </button>
                    </div>

                    </div>

                `;

                $("#fasilitasContainer").append(html);

            });

            $(document).on("click", ".removeFasilitas", function() {
                $(this).closest(".fasilitasRow").remove();
            });

            $("#addProgram").click(function() {

                let html = `

                    <div class="programRow mb-3 border p-2">

                    <div class="row">

                    <div class="col-md-4">
                    <input type="number" name="hari[]" class="form-control">
                    </div>

                    <div class="col-md-6">
                    <textarea name="deskripsi[]" class="form-control"></textarea>
                    </div>

                    <div class="col-md-2">
                    <button type="button" class="btn btn-danger removeProgram">
                    <i class="bi bi-trash"></i>
                    </button>
                    </div>

                    </div>

                    </div>

                `;

                $("#programContainer").append(html);

            });

            $(document).on("click", ".removeProgram", function() {
                $(this).closest(".programRow").remove();
            });

            $("#formFasilitas").submit(function(e) {

                e.preventDefault();

                $.post("/paket-umrah/fasilitas/store", $(this).serialize(), function(res) {

                    Swal.fire("Success", res.message, "success");

                    hideAppModal("modalFasilitas");

                });

            });

            $("#formProgram").submit(function(e) {

                e.preventDefault();

                $.post("/paket-umrah/program/store", $(this).serialize(), function(res) {

                    Swal.fire("Success", res.message, "success");

                    hideAppModal("modalProgram");

                });

            });
        </script>
    @endpush

@endsection
