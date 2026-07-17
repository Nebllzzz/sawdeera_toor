@extends('layouts.main')
@section('title', 'Verifikasi Data Jemaah')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <h2 class="font-weight-bold mb-1">Verifikasi Data Jemaah</h2>
                <small class="text-muted">Dashboard &nbsp;›&nbsp; Verifikasi Data Jemaah</small>

                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="verify-stat"><span class="orange"><i class="far fa-clock"></i></span>
                            <div><b>{{ $stats['menunggu'] }}</b>
                                <h5>Menunggu Verifikasi</h5><small>Data perlu diperiksa</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="verify-stat"><span class="green"><i class="fas fa-check"></i></span>
                            <div><b>{{ $stats['terverifikasi'] }}</b>
                                <h5>Terverifikasi</h5><small>Data sudah disetujui</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="verify-stat"><span class="red"><i class="fas fa-edit"></i></span>
                            <div><b>{{ $stats['perlu_perbaikan'] }}</b>
                                <h5>Perlu Revisi</h5><small>Butuh perbaikan jemaah</small>
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
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>NIK</th>
                                        <th>No HP</th>
                                        <th>Status Akun</th>
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
            font-size: 30px
        }
    </style>
    @push('scripts')
        <script>
            $('#dt').DataTable({
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
                serverSide: false,
                ajax: {
                    url: '/jemaah/data',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama'
                    }, {
                        data: 'email'
                    }, {
                        data: 'nik'
                    }, {
                        data: 'telepon'
                    },
                    {
                        data: 'statusActivity'
                    }, {
                        data: 'statusData'
                    }, {
                        data: 'operator'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(row) {
                            return `<a href="/jemaah/data-verifikasi/${row.id}" class="btn btn-sm btn-primary">Lihat Detail</a>`;
                        }
                    }
                ]
            });
        </script>
    @endpush
@endsection
