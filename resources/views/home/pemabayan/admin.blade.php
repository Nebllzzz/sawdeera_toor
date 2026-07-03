@extends('layouts.main')
@section('title', 'Verifikasi Pemabayan')

@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">

                <div class="card mt-3">
                    <div class="card-header">
                        <h3>Verifikasi Pembayaran</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped text-center" id="dt">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>Jumlah</th>
                                        <th>Jenis</th>
                                        <th>Status</th>
                                        <th>Bukti</th>
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
    <div class="modal fade" id="modalPembayaran">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4>Detail Pembayaran</h4>
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

                    <div class="mt-3 text-left">
                        <p><b>Jumlah:</b> <span id="jumlahText"></span></p>
                        <p><b>Metode:</b> <span id="metodeText"></span></p>
                        <p><b>Jenis:</b> <span id="jenisText"></span></p>
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
                dom: "<'row'" +
                    "<'col-sm-6 d-flex align-items-center justify-content-start'l>" +
                    "<'col-sm-6 d-flex align-items-center justify-content-end'f>" +
                    ">" +

                    "<'table-responsive'tr>" +

                    "<'row'" +
                    "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
                    "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                    ">",

                ajax: {
                    url: '/admin/pemabayan/data',
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
                        data: 'jumlah'
                    },
                    {
                        data: 'jenis_pembayaran'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'bukti'
                    }
                ]
            });

            function openModal(id) {
                $.get('/admin/pemabayan/' + id, function(res) {
                    currentId = res.id;
                    currentStatus = res.status;

                    $('#modalPembayaran').modal('show');

                    $('#jumlahText').text(res.jumlah);
                    $('#metodeText').text(res.metode_pembayaran);
                    $('#jenisText').text(res.jenis_pembayaran);

                    if (res.bukti_pembayaran) {
                        $('#fileLink').attr('href', '/storage/' + res.bukti_pembayaran);
                        $('#previewImg').attr('src', '/storage/' + res.bukti_pembayaran);
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
                <button class="btn btn-success" onclick="approve()">Setujui</button>
            `;
                } else if (currentStatus === 'diverifikasi') {
                    btn = `<button class="btn btn-secondary" disabled>Sudah diverifikasi</button>`;
                } else {
                    btn = `<button class="btn btn-secondary" disabled>Ditolak</button>`;
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
                $.post('/admin/pemabayan/' + currentId + '/approve', {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function() {
                    $('#modalPembayaran').modal('hide');
                    Swal.fire('Success', 'Pembayaran disetujui', 'success');
                    $('#dt').DataTable().ajax.reload();
                });
            }

            function reject() {
                $.post('/admin/pemabayan/' + currentId + '/reject', {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    alasan: $('#alasan').val()
                }, function() {
                    $('#modalPembayaran').modal('hide');
                    Swal.fire('Success', 'Pembayaran ditolak', 'success');
                    $('#dt').DataTable().ajax.reload();
                });
            }
        </script>
    @endpush
@endsection
