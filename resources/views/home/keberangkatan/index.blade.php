@extends('layouts.main')
@section('title', 'Keberangkatan')

@section('content')
    <div class="content-wrapper">

        <section class="content">

            <div class="container-fluid">

                <div class="card mt-3">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <h3>Data Keberangkatan Jemaah</h3>

                        <button class="btn btn-sawdeera1" onclick="createKeberangkatan()">

                            <i class="fas fa-plus mr-2 text-white"></i>

                            Tambah Jadwal

                        </button>

                    </div>

                    <div class="card-body">

                        <input type="text" id="search" class="form-control mb-4" placeholder="Cari keberangkatan">

                        <div id="listKeberangkatan"></div>

                    </div>

                </div>

            </div>

        </section>

    </div>

    <div class="modal fade" id="modalKeberangkatan">

        <div class="modal-dialog">

            <div class="modal-content">

                <form id="formKeberangkatan">

                    @csrf

                    <div class="modal-header">

                        <h4>Tambah Keberangkatan</h4>

                        <button type="button" class="close" data-dismiss="modal">
                            &times;
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label>Maskapai Berangkat</label>

                            <select name="maskapai_berangkat_id" id="maskapai_berangkat_id" class="form-control">

                            </select>

                        </div>


                        <div class="mb-3">

                            <label>Maskapai Pulang</label>

                            <select name="maskapai_pulang_id" id="maskapai_pulang_id" class="form-control">

                            </select>

                        </div>


                        <div class="mb-3">

                            <label>Tour Leader</label>

                            <select name="tour_leader_id" id="tour_leader_id" class="form-control">

                            </select>

                        </div>


                        <hr>


                        <div class="mb-3">

                            <label>Tanggal Keberangkatan</label>

                            <input type="date" name="tanggal_keberangkatan" class="form-control">

                        </div>


                        <div class="mb-3">

                            <label>Jam Berangkat</label>

                            <input type="time" name="jam_berangkat" class="form-control">

                        </div>


                        <div class="mb-3">

                            <label>Jam Tiba</label>

                            <input type="time" name="jam_tiba" class="form-control">

                        </div>


                        <hr>


                        <div class="mb-3">

                            <label>Tanggal Pulang</label>

                            <input type="date" name="tanggal_pulang" class="form-control">

                        </div>


                        <div class="mb-3">

                            <label>Jam Pulang</label>

                            <input type="time" name="jam_pulang" class="form-control">

                        </div>


                        <div class="mb-3">

                            <label>Jam Tiba Pulang</label>

                            <input type="time" name="jam_tiba_pulang" class="form-control">

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

    {{-- Modal Edit Status --}}
    <div class="modal fade" id="modalStatus">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Status Keberangkatan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select id="modalStatusSelect" class="form-control">
                            <option value="pendaftaran">pendaftaran</option>
                            <option value="persiapan">persiapan</option>
                            <option value="berangkat">berangkat</option>
                            <option value="pulang">pulang</option>
                            <option value="selesai">selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="modalSaveStatus" data-id="">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function renderCard(data) {

                let html = '';

                data.forEach(function (row) {

                    let progress = (row.total_jemaah / 40) * 100;

                    let berangkat = moment(row.tanggal_keberangkatan);
                    let pulang = moment(row.tanggal_pulang);

                    let hari = pulang.diff(berangkat, 'days') + 1;

                    html += `

                    <div class="card mb-3 p-3">

                        <div class="row align-items-center">

                            <div class="col-md-9">

                                <h3>
                                    ${berangkat.format('D MMMM YYYY')} - ${pulang.format('D MMMM YYYY')}

                                    <span style="font-size:14px;color:#777">
                                        (${hari} Hari)
                                    </span>
                                </h3>

                                <p class="mb-2">

                                    ✈️ ${row.maskapai_berangkat.nama}
                                    (Keberangkatan)

                                    →

                                    ${row.maskapai_pulang.nama}
                                    (Perpulangan)

                                </p>

                                <p class="mb-2">

                                    Status :
                                    <span class="badge badge-light status-badge">
                                        ${row.status}
                                    </span>

                                </p>

                                <p class="mb-3">

                                    👤 ${row.total_jemaah} / 40 Jemaah

                                </p>

                                <div class="progress">

                                    <div
                                        class="progress-bar"
                                        style="width:${progress}%">

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-3 d-flex flex-column justify-content-center align-items-center mt-3 mt-md-0">

                                <div class="d-flex w-100 mb-2" style="gap:10px;">

                                    <button
                                        class="btn btn-sawdeera1 flex-fill open-status-modal"
                                        data-id="${row.id}">

                                        Edit Status

                                    </button>

                                    <button
                                        class="btn btn-danger flex-fill btnDelete"
                                        data-id="${row.id}">

                                        Delete

                                    </button>

                                </div>

                                <a
                                    href="/keberangkatan/detail/${row.id}"
                                    class="btn btn-info w-100">

                                    Detail

                                </a>

                            </div>

                        </div>

                    </div>

                    `;
                });

                $("#listKeberangkatan").html(html);
            }

            function loadKeberangkatan() {

                $.ajax({

                    url: '/keberangkatan/list',

                    type: 'GET',

                    data: {
                        search: $("#search").val()
                    },

                    success: function(res) {

                        renderCard(res)

                    }

                })

            }

            loadKeberangkatan()

            $("#search").keyup(function() {

                loadKeberangkatan()

            })

            function createKeberangkatan() {

                $("#formKeberangkatan")[0].reset()

                $.get('/keberangkatan/form-data', function(res) {

                    let maskapai = ''

                    res.maskapai.forEach(function(row) {

                        maskapai += `
                            <option value="${row.id}">
                            ${row.nama}
                            </option>
                        `

                    })

                    $("#maskapai_berangkat_id").html(maskapai)
                    $("#maskapai_pulang_id").html(maskapai)

                    let leader = '<option value="">-</option>'

                    res.leader.forEach(function(row) {

                        leader += `
                            <option value="${row.id}">
                            ${row.nama}
                            </option>
                        `

                    })

                    $("#tour_leader_id").html(leader)

                    $("#modalKeberangkatan").modal("show")

                })

            }

            $("#formKeberangkatan").submit(function(e) {

                e.preventDefault()

                $.ajax({

                    url: '/keberangkatan/store',

                    type: 'POST',

                    data: $(this).serialize(),

                    success: function(res) {

                        Swal.fire(
                            "Success",
                            res.message,
                            "success"
                        )

                        $("#modalKeberangkatan").modal("hide")

                        loadKeberangkatan()

                    }

                })

            })

            $(document).on("click", ".btnDelete", function() {

                let id = $(this).data("id")

                if (!confirm("Hapus keberangkatan?")) return

                $.ajax({

                    url: '/keberangkatan/delete/' + id,

                    type: 'DELETE',

                    data: {
                        _token: csrf
                    },

                    success: function() {

                        loadKeberangkatan()

                    }

                })

            })

            // Open edit status modal
            $(document).on('click', '.open-status-modal', function() {
                let id = $(this).data('id');
                // find associated status badge
                let mainCard = $(this).closest('.col-md-3').prev();
                let status = mainCard.find('.status-badge').text().trim();
                $('#modalStatusSelect').val(status);
                $('#modalSaveStatus').data('id', id);
                $('#modalStatus').modal('show');
            });

            // Save status from modal
            $(document).on('click', '#modalSaveStatus', function() {
                let id = $(this).data('id');
                let status = $('#modalStatusSelect').val();

                $.ajax({
                    url: '/keberangkatan/update-status',
                    type: 'POST',
                    data: {
                        id: id,
                        status: status,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire('Sukses', res.message, 'success');
                        // update badge in card
                        $(`.open-status-modal[data-id="${id}"]`).closest('.col-md-3').prev().find(
                            '.status-badge').text(res.status);
                        $('#modalStatus').modal('hide');
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengupdate status', 'error');
                    }
                });
            });
        </script>
    @endpush
@endsection
