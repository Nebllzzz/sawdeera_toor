@extends('layouts.main')
@section('title', 'Detail Keberangkatan')

@section('content')
    <div class="content-wrapper">

        <section class="content">

            <div class="container-fluid">

                <div class="card mt-3">

                    <div class="card-header">

                        <h3>Detail Keberangkatan</h3>

                    </div>

                    <div class="card-body">

                        <div id="detailKeberangkatan"></div>

                    </div>

                </div>


                <div class="card">

                    <div class="card-header">

                        <h3>List Jemaah</h3>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-striped text-center" id="dtJemaah">

                                <thead>

                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>Paket</th>
                                        <th>Status</th>
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

    @push('scripts')
        <script>
            let id = "{{ $id }}";

            function loadDetail() {

                $.get('/keberangkatan/detail/data/' + id, function(res) {

                    let berangkat = moment(res.tanggal_keberangkatan)
                    let pulang = moment(res.tanggal_pulang)

                    let durasi = pulang.diff(berangkat, 'days') + 1

                    let tglBerangkat = berangkat.format('D MMMM YYYY')
                    let tglPulang = pulang.format('D MMMM YYYY')

                    let hijriBerangkat = moment(res.tanggal_keberangkatan).format('iD iMMMM iYYYY')
                    let hijriPulang = moment(res.tanggal_pulang).format('iD iMMMM iYYYY')

                    let jamBerangkatArab = moment(res.jam_berangkat, "HH:mm:ss").format("HH.mm.ss")
                    let jamTibaArab = moment(res.jam_tiba, "HH:mm:ss").format("HH.mm.ss")

                    let html = `

                    <div class="card shadow-sm p-4"
                    style="background:linear-gradient(135deg,#6b3f1e,#8c4d24);color:white;border-radius:12px">

                        <h4 style="font-weight:bold;margin-bottom:10px">
                            🛫 Jadwal Keberangkatan
                        </h4>

                        <hr style="border-color:rgba(255,255,255,0.2)">

                        <p>
                        📅 <b>Durasi :</b> ${durasi} Hari
                        </p>

                        <p>
                        🗓 <b>Tanggal Berangkat :</b> ${tglBerangkat}
                        <small>(${hijriBerangkat} H)</small>
                        </p>

                        <p>
                        🗓 <b>Tanggal Pulang :</b> ${tglPulang}
                        <small>(${hijriPulang} H)</small>
                        </p>

                        <p>
                        ✈️ <b>Maskapai Berangkat :</b> ${res.maskapai_berangkat?.nama ?? '-'}
                        </p>

                        <p>
                        🛬 <b>Maskapai Pulang :</b> ${res.maskapai_pulang?.nama ?? '-'}
                        </p>

                        <hr style="border-color:rgba(255,255,255,0.2)">

                        <p>
                        ⏰ <b>Jam Berangkat :</b><br>
                        ${res.jam_berangkat} (WIB) - ${jamBerangkatArab} (Arab)
                        </p>

                        <p>
                        ⏳ <b>Jam Tiba :</b><br>
                        ${res.jam_tiba} (WIB) - ${jamTibaArab} (Arab)
                        </p>

                        <p>
                        📌 <b>Status :</b>
                        <span class="badge badge-light">
                        ${res.status}
                        </span>
                        </p>

                    </div>

                    `;

                    $("#detailKeberangkatan").html(html)

                })

            }

            loadDetail()

            var datatable = $("#dtJemaah").DataTable({

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
                    url: "/keberangkatan/jemaah/data",
                    type: "POST",
                    data: function(d) {
                        d.keberangkatan_id = id
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
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
                        data: "nik"
                    },

                    {
                        data: "paket"
                    },

                    {
                        data: "status"
                    }

                ]

            });
        </script>
    @endpush
@endsection
