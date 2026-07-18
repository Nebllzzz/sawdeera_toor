@extends('layouts.main')
@section('title', 'Verifikasi Registrasi Akun')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <x-page-heading
                    title="Verifikasi Registrasi Akun Jemaah"
                    description="Periksa dan tentukan status akun calon jemaah yang baru mendaftar."
                    section="Verifikasi Jemaah"
                    current="Registrasi Akun"
                />

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="verify-stat"><span class="green"><i class="fas fa-users"></i></span>
                            <div><b>{{ $stats['menunggu'] }}</b>
                                <h5>Menunggu Verifikasi</h5><small>Akun baru yang perlu diverifikasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="verify-stat"><span class="orange"><i class="far fa-clock"></i></span>
                            <div><b>{{ $stats['aktif'] }}</b>
                                <h5>Diverifikasi</h5><small>Akun aktif saat ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="verify-stat"><span class="red"><i class="fas fa-times"></i></span>
                            <div><b>{{ $stats['tidak_aktif'] }}</b>
                                <h5>Ditolak / Nonaktif</h5><small>Akun tidak aktif</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="dt">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Jemaah</th>
                                        <th>Email</th>
                                        <th>No. Telepon</th>
                                        <th>Tanggal Registrasi</th>
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
    <style>
        .verify-stat {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 22px;
            display: flex;
            gap: 18px;
            align-items: center;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .04)
        }

        .verify-stat span {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px
        }

        .verify-stat .green {
            background: #e8f5df;
            color: #6b9b45
        }

        .verify-stat .orange {
            background: #fff1d9;
            color: #e69a19
        }

        .verify-stat .red {
            background: #fde4e4;
            color: #d94a4a
        }

        .verify-stat b {
            font-size: 30px;
            color: #188245
        }

        .verify-stat h5 {
            margin: 0;
            font-weight: 800
        }

        .verify-stat small {
            color: #777
        }


        /* =========================
        TABEL KEBERANGKATAN
        ========================= */
        #dt {
            width: 100% !important;
            margin-bottom: 0 !important;
            border: 1px solid #E8DED5;
            border-collapse: separate !important;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        #dt thead {
            background-color: #FBF6F1;
        }

        #dt thead th {
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #1F2937;
            white-space: nowrap;
            vertical-align: middle;
            border-top: 0 !important;
            border-bottom: 1px solid #E8DED5 !important;
        }

        #dt tbody td {
            padding: 14px 16px;
            font-size: 13px;
            color: #1F2937;
            vertical-align: middle;
            border-top: 0 !important;
            border-bottom: 1px solid #E8DED5 !important;
        }

        #dt tbody tr:last-child td {
            border-bottom: 0 !important;
        }

        #dt tbody tr:hover {
            background-color: #FFFCF9;
        }

        table.dataTable#dt,
        table.dataTable#dt.no-footer {
            border-bottom: 1px solid #E8DED5 !important;
        }

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
            font-size: 13px;
            font-weight: 400;
            color: #475467;
        }

        #dt_wrapper .dataTables_length select {
            min-width: 76px;
            height: 40px;
            margin: 6px 6px 0;
            padding: 6px 28px 6px 12px;
            border: 1px solid #E4E7EC;
            border-radius: 8px;
            background-color: #F8F9FB;
            outline: none;
        }

        /* Search */
        #dt_wrapper .dataTables_filter {
            text-align: right;
        }

        #dt_wrapper .dataTables_filter label {
            margin-bottom: 0;
            font-size: 13px;
            font-weight: 400;
            color: #475467;
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

        #dt_wrapper .dataTables_filter input:focus,
        #dt_wrapper .dataTables_length select:focus {
            border-color: #E39A1B;
            box-shadow: 0 0 0 3px rgba(227, 154, 27, 0.12);
        }

        /* Informasi tabel */
        #dt_wrapper .dataTables_info {
            padding-top: 0;
            font-size: 13px;
            color: #667085;
        }

        /* Pagination */
        #dt_wrapper .dataTables_paginate {
            padding-top: 0;
        }

        #dt_wrapper .dataTables_paginate .paginate_button {
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

        #dt_wrapper .dataTables_paginate .paginate_button.current,
        #dt_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #E39A1B !important;
            color: #fff !important;
        }

        #dt_wrapper .dataTables_paginate .paginate_button:hover {
            background: #FBF6F1 !important;
            color: #9A640D !important;
        }

        #dt_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: .45;
            cursor: not-allowed !important;
        }

        @media (max-width: 767.98px) {
            #dt_wrapper .dataTables_filter {
                margin-top: 12px;
                text-align: left;
            }

            #dt_wrapper .dataTables_filter input {
                width: 100%;
                margin-left: 0;
            }

            #dt_wrapper .dataTables_info,
            #dt_wrapper .dataTables_paginate {
                float: none;
                text-align: left;
            }

            #dt_wrapper .dataTables_paginate {
                margin-top: 10px;
            }

            #dt thead th,
            #dt tbody td {
                padding: 12px;
                font-size: 12px;
            }
        }
    </style>
    @push('scripts')
        <script>
            $('#dt').DataTable({
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
                    url: '/jemaah/registrasi/data',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'telepon',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_registrasi',
                        name: 'created_at'
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        </script>
    @endpush
@endsection
