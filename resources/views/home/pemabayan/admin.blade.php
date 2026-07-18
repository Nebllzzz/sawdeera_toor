@extends('layouts.main')
@section('title', 'Verifikasi Pembayaran')
@section('content')
    <div class="content-wrapper px-3">
        <section class="content py-4">
            <div class="container-fluid">
                <x-page-heading
                    title="Verifikasi Pembayaran"
                    description="Pantau rencana pembayaran dan verifikasi setiap bukti per tahap."
                    section="Verifikasi Jemaah"
                    current="Pembayaran"
                />
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="dt">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jemaah</th>
                                        <th>Paket</th>
                                        <th>Skema</th>
                                        <th>Progress</th>
                                        <th>Menunggu</th>
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
    @push('scripts')
        <script>
            $("#dt").DataTable({
                language: {
                    lengthMenu: "Show _MENU_"
                },

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
                    url: '/admin/pemabayan/data',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'nama'
                }, {
                    data: 'paket'
                }, {
                    data: 'skema'
                }, {
                    data: 'progress'
                }, {
                    data: 'menunggu'
                }, {
                    data: 'status_view',
                    name: 'status'
                }, {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }]
            });
        </script>
    @endpush
@endsection
