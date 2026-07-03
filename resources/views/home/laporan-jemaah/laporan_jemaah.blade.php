@extends('layouts.main')
@section('title', 'Laporan Data Jemaah')
@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Laporan Dan Ekspor Data Jemaah</h3>
                    </div>
                    <div class="card-body">

                        {{-- FILTER --}}
                        <div class="row mb-3">

                            <div class="col-md-4">
                                <label>Program Keberangkatan</label>

                                <select id="filterProgram" class="form-control">

                                    <option value="all">
                                        Semua Program
                                    </option>

                                    @foreach ($paket as $p)

                                        <option value="{{ $p->id }}">
                                            {{ $p->nama_paket }}
                                        </option>

                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Status Pembayaran</label>

                                <select id="filterPembayaran" class="form-control">

                                    <option value="all">
                                        Semua
                                    </option>

                                    <option value="Lunas">
                                        Lunas
                                    </option>

                                    <option value="Belum">
                                        Belum
                                    </option>

                                    <option value="Ditolak">
                                        Ditolak
                                    </option>

                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Periode</label>

                                <input
                                    type="text"
                                    id="filterPeriod"
                                    class="form-control"
                                    placeholder="Pilih tanggal">
                            </div>

                        </div>

                        {{-- BUTTON --}}
                        <div class="row mb-4 align-items-center">

                            {{-- LEFT --}}
                            <div class="col-md-6">

                                <button id="btnExportExcel" class="btn btn-success mr-2">
                                    <i class="fas fa-file-excel mr-1"></i>
                                    Export Excel
                                </button>

                                <button id="btnExportPdf" class="btn btn-danger">
                                    <i class="fas fa-file-pdf mr-1"></i>
                                    Export PDF
                                </button>

                            </div>

                            {{-- RIGHT --}}
                            <div class="col-md-6 text-md-right mt-3 mt-md-0">

                                <button id="btnTampilkan" class="btn btn-warning">
                                    <i class="fas fa-search mr-1"></i>
                                    Tampilkan Laporan
                                </button>

                            </div>

                        </div>

                        <hr>

                        <div id="reportContainer" class="table-responsive">
                            <table class="table table-bordered" id="reportTable">
                                <thead>
                                    <tr class="text-center font-weight-bold">
                                        <th>No</th>
                                        <th>Nama Jemaah</th>
                                        <th>NIK</th>
                                        <th>Paket</th>
                                        <th>Keberangkatan</th>
                                        <th>Status Pembayaran</th>
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

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <style>
            /* small tweaks to mimic existing blades */
            #reportTable thead th {
                font-weight: 700;
                text-align: center;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script>
            $(function() {
                $('#filterPeriod').daterangepicker({
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });

                var table = $('#reportTable').DataTable({
                    processing: true,
                    serverSide: true,

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

                    ajax: {
                        url: '/laporan/jemaah/data',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: function(d) {
                            d.program = $('#filterProgram').val();
                            d.period = $('#filterPeriod').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'kode',
                            name: 'kode'
                        },
                        {
                            data: 'paket',
                            name: 'paket'
                        },
                        {
                            data: 'keberangkatan',
                            name: 'keberangkatan'
                        },
                        {
                            data: 'status_pembayaran',
                            name: 'status_pembayaran'
                        }
                    ],
                    lengthMenu: [10, 25, 50, 100]
                });

                $('#btnTampilkan').click(function() {
                    table.ajax.reload();
                });

                $('#btnExportExcel').click(function() {
                    let program = $('#filterProgram').val();
                    let period = $('#filterPeriod').val();
                    let url =
                        `/laporan/jemaah/export/excel?program=${program}&period=${encodeURIComponent(period)}`;
                    window.location = url;
                });

                $('#btnExportPdf').click(function() {
                    let program = $('#filterProgram').val();
                    let period = $('#filterPeriod').val();
                    let url =
                        `/laporan/jemaah/export/pdf?program=${program}&period=${encodeURIComponent(period)}`;
                    window.open(url, '_blank');
                });
            });
        </script>
    @endpush

@endsection
