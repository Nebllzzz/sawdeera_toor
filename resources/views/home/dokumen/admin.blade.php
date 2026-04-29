@extends('layouts.main')
@section('title', 'Dokumen Jemaah')

@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">

                <div class="card mt-3">
                    <div class="card-header">
                        <h3>Verifikasi Dokumen Jemaah</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table-striped text-center" id="dt">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>KTP</th>
                                        <th>Paspor</th>
                                        <th>Visa</th>
                                        <th>Vaksin</th>
                                    </tr>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="modalDokumen">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4>Detail Dokumen</h4>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body text-center">

                    <div id="previewBox">
                        <a id="fileLink" target="_blank">
                            <img id="previewImg" style="max-height:400px;">
                        </a>
                    </div>

                    <div id="rejectBox" style="display:none;">
                        <label>Alasan Penolakan</label>
                        <textarea id="alasan" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer" id="actionButtons"></div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentId = null;
            let currentStatus = null;

            var datatable = $("#dt").DataTable({

                processing: true,
                serverSide: true,

                ajax: {
                    url: '/admin/dokumen/data',
                    type: 'POST',
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'nik'
                    },
                    {
                        data: 'ktp'
                    },
                    {
                        data: 'paspor'
                    },
                    {
                        data: 'visa'
                    },
                    {
                        data: 'vaksin'
                    },
                ]

            });


            function openModal(id) {
                $.get('/admin/dokumen/' + id, function(res) {

                    currentId = res.id;
                    currentStatus = res.status;

                    $('#modalDokumen').modal('show');

                    if (res.file_path) {
                        $('#fileLink').attr('href', '/storage/' + res.file_path);
                        $('#previewImg').attr('src', '/storage/' + res.file_path);
                    }

                    $('#previewBox').show();
                    $('#rejectBox').hide();

                    renderButtons();
                });
            }


            function renderButtons() {

                let btn = '';

                if (currentStatus === 'diproses') {
                    btn = `
                    <button class="btn btn-danger" onclick="showReject()">Tolak</button>
                    <button class="btn btn-success" onclick="approve()">Setujui</button>`
                } else if (currentStatus === 'diverifikasi') {
                    btn = `<button class="btn btn-danger" onclick="showReject()">Tolak</button>`;
                } else {
                    btn = `<button class="btn btn-success" onclick="approve()">Setujui</button>`;
                }

                $('#actionButtons').html(btn);
            }


            function showReject() {
                $('#previewBox').hide();
                $('#rejectBox').show();

                $('#actionButtons').html(`
                    <button class="btn btn-danger" onclick="reject()">Submit</button>
                `);
            }


            function approve() {
                $.post('/admin/dokumen/' + currentId + '/approve', {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function() {
                    $('#modalDokumen').modal('hide');
                    Swal.fire('Success', 'Disetujui', 'success');
                    $('#dt').DataTable().ajax.reload();
                });
            }


            function reject() {
                $.post('/admin/dokumen/' + currentId + '/reject', {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    alasan: $('#alasan').val()
                }, function() {
                    $('#modalDokumen').modal('hide');
                    Swal.fire('Success', 'Ditolak', 'success');
                    $('#dt').DataTable().ajax.reload();
                });
            }
        </script>
    @endpush

@endsection
