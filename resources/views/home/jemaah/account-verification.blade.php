@extends('layouts.main')
@section('title', 'Verifikasi Registrasi Akun')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <h2 class="font-weight-bold mb-1">Verifikasi Registrasi Akun Jemaah</h2>
                <small class="text-muted">Dashboard &nbsp;›&nbsp; Verifikasi Registrasi Akun</small>

                <div class="row mt-4">
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
