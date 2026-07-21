@extends('layouts.main')
@section('title', 'Monitoring Data Jemaah')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <x-page-heading
                    title="Monitoring Data Jemaah"
                    description="Pantau perkembangan proses seluruh jemaah umrah"
                    section="Laporan"
                    current="Monitoring Data Jemaah"
                    breadcrumb-id="breadcrumbReport"
                >
                    <x-slot:actions>
                        <button type="button" class="btn-export excel" id="btnExportExcel"><i class="fas fa-file-excel"></i> Export Excel</button>
                        <button type="button" class="btn-export pdf" id="btnExportPdf"><i class="fas fa-file-pdf"></i> Cetak PDF</button>
                    </x-slot:actions>
                </x-page-heading>
                <div class="monitor-stats mb-4">
                    <div><i class="fas fa-users"></i><b>{{ $stats['total'] }}</b><span>Total Jemaah</span></div>
                    <div><i class="fas fa-paper-plane"></i><b>{{ $stats['siap'] }}</b><span>Siap Berangkat</span></div>
                    <div><i class="fas fa-hourglass-half"></i><b>{{ $stats['kelengkapan'] }}</b><span>Menunggu
                            Kelengkapan</span></div>
                    <div><i class="fas fa-sync"></i><b>{{ $stats['verifikasi'] }}</b><span>Proses Verifikasi</span></div>
                    <div><i class="fas fa-plane"></i><b>{{ $stats['berangkat'] }}</b><span>Sudah Berangkat</span></div>
                    <div><i class="fas fa-check-circle"></i><b>{{ $stats['selesai'] }}</b><span>Selesai Umrah</span></div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3 mb-2"><input id="searchBox" class="form-control"
                                    placeholder="Cari nama / no. pendaftaran..."></div>
                            <div class="col-md-3 mb-2"><select id="filterProgram" class="form-control">
                                    <option value="all">Semua Paket</option>
                                    @foreach ($paket as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_paket }}</option>
                                    @endforeach
                                </select></div>
                            <div class="col-md-3 mb-2"><input type="text" id="filterPeriod" class="form-control"
                                    placeholder="Tanggal Berangkat"></div>
                            <div class="col-md-3 mb-2"><button id="btnReset" class="btn btn-sawdeera1 btn-block w-100"><i
                                        class="fas fa-redo mx-2"></i>Reset Filter</button></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="reportTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No. Daftar</th>
                                        <th>Nama Jemaah</th>
                                        <th>Paket Umrah</th>
                                        <th>Keberangkatan</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="detailModal">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Detail Jemaah</h5><button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="detailBody"></div>
            </div>
        </div>
    </div>
    <style>
        .monitor-stats {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px
        }

        .monitor-stats>div {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 18px;
            box-shadow: 0 3px 14px rgba(0, 0, 0, .04)
        }

        .monitor-stats i {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #f6ead8;
            color: #9a640d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px
        }

        .monitor-stats b {
            font-size: 24px;
            display: inline-block
        }

        .monitor-stats span {
            display: block;
            font-size: 11px;
            color: #555;
            margin-top: 4px
        }

        .detail-head {
            background: #fff8e8;
            margin: -1rem -1rem 1rem;
            padding: 18px;
            display: flex;
            gap: 14px
        }

        .detail-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #e8efff;
            color: #4770c4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800
        }

        .detail-row {
            display: grid;
            grid-template-columns: 130px 1fr;
            margin-bottom: 7px
        }

        .detail-row span {
            color: #777
        }

        .timeline-mini {
            margin-top: 18px
        }

        .timeline-mini-item {
            border-left: 3px solid #d7be91;
            padding: 0 0 12px 14px
        }

        .timeline-mini-item b,
        .timeline-mini-item small {
            display: block
        }

        .timeline-mini-item small {
            color: #777
        }

        .export-detail {
            background: #f7d796;
            color: #5c3b0b;
            border: 0;
            border-radius: 6px;
            padding: 10px;
            width: 100%;
            font-weight: 700
        }

        .btn-export {
            height: 40px;
            border-radius: 9px;
            padding: 0 15px;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #e1ddd6;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: .15s
        }

        .btn-export.excel {
            color: #178547
        }

        .btn-export.pdf {
            color: #c83c36
        }

        .btn-export:hover {
            box-shadow: 0 5px 14px rgba(57, 40, 18, .1);
            transform: translateY(-1px)
        }

        .btn-export:disabled {
            opacity: .55;
            transform: none;
            cursor: not-allowed
        }

        /* =========================
        MONITORING TABLE
        ========================= */
        #reportTable {
            width: 100% !important;
            margin-bottom: 0 !important;
            border: 1px solid #E8DED5;
            border-collapse: separate !important;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        #reportTable thead {
            background-color: #FBF6F1;
        }

        #reportTable thead th {
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #1F2937;
            white-space: nowrap;
            vertical-align: middle;
            border-top: 0 !important;
            border-bottom: 1px solid #E8DED5 !important;
        }

        #reportTable tbody td {
            padding: 14px 16px;
            font-size: 13px;
            color: #1F2937;
            vertical-align: middle;
            border-top: 0 !important;
            border-bottom: 1px solid #E8DED5 !important;
        }

        #reportTable tbody tr:last-child td {
            border-bottom: 0 !important;
        }

        #reportTable tbody tr:hover {
            background-color: #FFFCF9;
        }

        /* Hilangkan garis bawaan DataTables yang bertumpuk */
        table.dataTable#reportTable {
            border-bottom: 1px solid #E8DED5 !important;
        }

        table.dataTable#reportTable.no-footer {
            border-bottom: 1px solid #E8DED5 !important;
        }

        /* Jarak antara tabel dan info/pagination */
        #reportTable_wrapper .dataTables_info {
            padding-top: 18px;
            font-size: 13px;
            color: #667085;
        }

        #reportTable_wrapper .dataTables_paginate {
            padding-top: 12px;
        }

        /* Pagination */
        #reportTable_wrapper .dataTables_paginate .paginate_button {
            min-width: 36px;
            height: 36px;
            margin-left: 4px;
            padding: 7px 11px !important;
            border: 0 !important;
            border-radius: 8px !important;
            background: transparent !important;
            color: #667085 !important;
            box-shadow: none !important;
        }

        #reportTable_wrapper .dataTables_paginate .paginate_button.current,
        #reportTable_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #E39A1B !important;
            color: #fff !important;
        }

        #reportTable_wrapper .dataTables_paginate .paginate_button:hover {
            background: #FBF6F1 !important;
            color: #9A640D !important;
        }

        /* Hilangkan search dan dropdown bawaan DataTables
        karena sudah memakai filter custom */
        #reportTable_wrapper .dataTables_filter,
        #reportTable_wrapper .dataTables_length {
            display: none;
        }

        @media (max-width: 767.98px) {
            #reportTable thead th,
            #reportTable tbody td {
                padding: 12px;
                font-size: 12px;
            }

            #reportTable_wrapper .dataTables_info,
            #reportTable_wrapper .dataTables_paginate {
                float: none;
                text-align: left;
            }

            #reportTable_wrapper .dataTables_paginate {
                margin-top: 8px;
            }
        }

        @media(max-width:1000px) {
            .monitor-stats {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media(max-width:600px) {
            .monitor-stats {
                grid-template-columns: 1fr
            }
        }
    </style>
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function() {
            $('#filterPeriod').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                autoUpdateInput: false
            });
            $('#filterPeriod').on('apply.daterangepicker', function(ev, p) {
                $(this).val(p.startDate.format('YYYY-MM-DD') + ' - ' + p.endDate.format('YYYY-MM-DD'));
                table.ajax.reload();
            });
            const table = $('#reportTable').DataTable({
                dom: "<'row'" +
                    "<'col-sm-6 d-flex align-items-center justify-content-start'l>" +
                    "<'col-sm-6 mb-3 d-flex align-items-center justify-content-end'f>" +
                    ">" +

                    "<'table-responsive'tr>" +

                    "<'row'" +
                    "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
                    "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                    ">",

                processing: true,
                serverSide: false,
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
                    orderable: false,
                    searchable: false
                }, {
                    data: 'no_daftar'
                }, {
                    data: 'nama'
                }, {
                    data: 'paket'
                }, {
                    data: 'keberangkatan'
                }, {
                    data: 'progress'
                }, {
                    data: 'status_monitoring'
                }, {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }]
            });
            $('#searchBox').on('keyup', function() {
                table.search(this.value).draw();
            });
            $('#filterProgram').change(() => table.ajax.reload());
            $('#btnReset').click(function() {
                $('#filterProgram').val('all');
                $('#filterPeriod').val('');
                $('#searchBox').val('');
                table.search('').draw();
                table.ajax.reload();
            });
            $('#btnExportExcel').click(function() {
                window.location =
                    `/laporan/jemaah/export/excel?program=${$('#filterProgram').val()}&period=${encodeURIComponent($('#filterPeriod').val())}`;
            });
            $('#btnExportPdf').click(function() {
                window.open(
                    `/laporan/jemaah/export/pdf?program=${$('#filterProgram').val()}&period=${encodeURIComponent($('#filterPeriod').val())}`,
                    '_blank');
            });
            $(document).on('click', '.btn-detail', function() {
                $.get('/laporan/jemaah/detail/' + $(this).data('id'), function(d) {
                    const initial = (d.nama || 'J').split(' ').slice(0, 2).map(x => x[0]).join('')
                        .toUpperCase();
                    $('#detailBody').html(`
                <div class="detail-head"><div class="detail-avatar">${initial}</div><div><h5>${d.nama}</h5><small>No. Pendaftaran: ${d.no_pendaftaran}</small><br><small>No. HP: ${d.telepon}</small><br><span class="badge badge-warning mt-2">${d.status}</span></div></div>
                <h6>Informasi Paket & Keberangkatan</h6>
                ${row('Paket Umrah',d.paket)}${row('Keberangkatan',d.keberangkatan)}${row('Maskapai',d.maskapai)}${row('Hotel Makkah',d.hotel_makkah)}${row('Hotel Madinah',d.hotel_madinah)}${row('Tour Guide',d.tour_guide)}${row('Kuota Paket',d.kuota)}
                <div class="mt-2"><small>Progress</small><div class="progress" style="height:8px"><div class="progress-bar bg-success" style="width:${d.progress}%"></div></div></div>
                <h6 class="mt-4">Progress Pendaftaran</h6><div class="timeline-mini">${d.timeline.map(t=>`<div class="timeline-mini-item"><b>${t.label}</b><small>${t.status}${t.date?' · '+t.date:''}</small></div>`).join('')}</div>
                <button class="export-detail mt-3" onclick="window.location='/laporan/jemaah/export/excel'"><i class="far fa-file-excel mx-2"></i>Unduh Excel</button>
            `);
                    $('#detailModal').modal('show');
                });
            });

            function row(a, b) {
                return `<div class="detail-row"><span>${a}</span><b>: ${b||'-'}</b></div>`
            }
        });
    </script>
@endpush
@endsection
