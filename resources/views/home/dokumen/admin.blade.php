@extends('layouts.main')
@section('title', 'Verifikasi Dokumen')
@section('content')
    <div class="content-wrapper px-3">
        <section class="content py-4">
            <div class="container-fluid">
                <div class="mb-4">
                    <h2 class="font-weight-bold mb-1">Verifikasi Dokumen Jemaah</h2>
                    <p class="text-muted">Buka detail jemaah untuk memeriksa seluruh dokumen pendukung.</p>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="dt">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Email</th>
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
            $('#dt').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/admin/dokumen/data',
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
                    data: 'nama',
                    name: 'user.name'
                }, {
                    data: 'email',
                    name: 'user.email'
                }, {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }]
            });
        </script>
    @endpush
@endsection
