@extends('layouts.main')
@section('title', 'Detail Verifikasi Pembayaran')
@section('content')
    @php
        $trip = $pembayaran->pengajuan;
    @endphp
    <div class="content-wrapper admin-payment-detail">
        <section class="content py-4">
            <div class="container-fluid">
                <x-page-heading
                    title="Detail Pembayaran"
                    :description="$pembayaran->jemaah->user->name . ' · ' . $trip->paketUmrah->nama_paket"
                    section="Verifikasi Pembayaran"
                    current="Detail"
                >
                    <x-slot:actions>
                        <a href="/admin/pemabayan-admin" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                        <span class="badge badge-light p-2">{{ $pembayaran->jumlah_tahap }} Tahap</span>
                    </x-slot:actions>
                </x-page-heading>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="detail-card mb-3">
                            <div class="detail-grid">
                                <div>
                                    <small>Jemaah</small><b>{{ $pembayaran->jemaah->user->name }}</b><span>{{ $pembayaran->jemaah->nik }}</span>
                                </div>
                                <div>
                                    <small>Jadwal</small><b>{{ $trip->keberangkatan->tanggal_keberangkatan->translatedFormat('d F Y') }}</b><span>{{ $trip->keberangkatan->maskapaiBerangkat->nama ?? '-' }}</span>
                                </div>
                                <div><small>Total Tagihan</small><b>Rp
                                        {{ number_format($pembayaran->total_tagihan, 0, ',', '.') }}</b><span>DP
                                        {{ $pembayaran->dp_persen ? $pembayaran->dp_persen . '%' : 'Pembayaran penuh' }}</span>
                                </div>
                            </div>
                        </div>
                        @foreach ($pembayaran->tahapan as $t)
                            <div class="detail-card installment mb-3" id="tahap-{{ $t->id }}">
                                <div class="installment-head">
                                    <div><span class="number">{{ $t->urutan }}</span><span><small>TAHAP
                                                {{ $t->urutan }}</small><b>{{ $t->nama_tahap }}</b></span></div><span
                                        class="status status-{{ $t->status }}">{{ ['belum_bayar' => 'Belum Dibayar', 'diproses' => 'Menunggu Verifikasi', 'diverifikasi' => 'Diverifikasi', 'ditolak' => 'Ditolak'][$t->status] }}</span>
                                </div>
                                <div class="installment-info">
                                    <div><small>Persentase</small><b>{{ number_format($t->persentase, 2, ',', '.') }}%</b>
                                    </div>
                                    <div><small>Nominal</small><b>Rp {{ number_format($t->nominal, 0, ',', '.') }}</b></div>
                                    <div><small>Jatuh Tempo</small><b>{{ $t->jatuh_tempo->translatedFormat('d F Y') }}</b>
                                    </div>
                                    <div><small>Metode</small><b>{{ $t->metode_pembayaran ?? '-' }}</b></div>
                                </div>
                                @if ($t->bukti_pembayaran)
                                    <div class="proof-box">
                                        <div><i class="fas fa-file-invoice"></i><span><b>Bukti pembayaran</b><small>Diunggah
                                                    {{ $t->uploaded_at?->translatedFormat('d M Y H:i') }}</small></span>
                                        </div><a href="{{ asset('storage/' . $t->bukti_pembayaran) }}" target="_blank">Lihat
                                            Bukti <i class="fas fa-external-link-alt"></i></a>
                                    </div>
                                @endif
                                @if ($t->catatan_jemaah)
                                    <p class="note"><b>Catatan jemaah:</b> {{ $t->catatan_jemaah }}</p>
                                @endif
                                @if ($t->keterangan_penolakan)
                                    <p class="reject"><b>Alasan penolakan:</b> {{ $t->keterangan_penolakan }}</p>
                                @endif
                                @if ($t->status === 'diproses')
                                    <div class="verify-actions gap-3">
                                        <button class="btn btn-danger reject-btn"
                                            data-id="{{ $t->id }}">Tolak</button>
                                        <button class="btn btn-success approve-btn" data-id="{{ $t->id }}">Verifikasi
                                            Pembayaran
                                        </button>
                                    </div>
                                @endif
                                @if ($t->verified_at)
                                    <small class="text-muted">Diperiksa
                                        {{ $t->verified_at->translatedFormat('d M Y H:i') }} oleh
                                        {{ $t->verifier->name ?? '-' }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="col-lg-4">
                        <div class="detail-card sticky-card">
                            <h6>Ringkasan Progress</h6>
                            @php
                                $verified = $pembayaran->tahapan->where('status', 'diverifikasi');
                                $sum = $verified->sum('nominal');
                                $pct = $pembayaran->total_tagihan
                                    ? round(($sum / $pembayaran->total_tagihan) * 100)
                                    : 0;
                            @endphp
                            <div class="big-progress">{{ $pct }}%</div>
                            <div class="progress">
                                <div style="width:{{ $pct }}%"></div>
                            </div>
                            <div class="summary"><span>Sudah diverifikasi <b>Rp
                                        {{ number_format($sum, 0, ',', '.') }}</b></span><span>Sisa tagihan <b>Rp
                                        {{ number_format($pembayaran->total_tagihan - $sum, 0, ',', '.') }}</b></span><span>Tahap
                                    lunas <b>{{ $verified->count() }}/{{ $pembayaran->jumlah_tahap }}</b></span></div>
                            <div class="admin-tip"><i class="fas fa-shield-alt"></i> Cocokkan nama penerima, nominal,
                                tanggal, dan keaslian bukti sebelum memverifikasi.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <style>
        .admin-payment-detail {
            background: #faf9f7 !important
        }

        .detail-card {
            background: #fff;
            border: 1px solid #ece8e1;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 14px rgba(60, 40, 15, .05)
        }

        .detail-grid,
        .installment-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px
        }

        .detail-grid small,
        .detail-grid b,
        .detail-grid span,
        .installment-info small,
        .installment-info b {
            display: block
        }

        .installment-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 13px
        }

        .installment-head>div {
            display: flex;
            gap: 10px;
            align-items: center
        }

        .installment-head small,
        .installment-head b {
            display: block
        }

        .number {
            display: flex;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #bd8120;
            color: #fff;
            align-items: center;
            justify-content: center
        }

        .status {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px
        }

        .status-diproses {
            background: #fff1d3;
            color: #946012
        }

        .status-diverifikasi {
            background: #e4f6e9;
            color: #277d42
        }

        .status-ditolak {
            background: #fbe5e5;
            color: #a62f2f
        }

        .status-belum_bayar {
            background: #eee;
            color: #666
        }

        .installment-info {
            grid-template-columns: repeat(4, 1fr);
            padding: 17px 0
        }

        .proof-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f6f2;
            padding: 13px;
            border-radius: 8px
        }

        .proof-box>div {
            display: flex;
            gap: 10px
        }

        .proof-box i {
            font-size: 22px;
            color: #b4791b
        }

        .proof-box small,
        .proof-box b {
            display: block
        }

        .verify-actions {
            text-align: right;
            margin-top: 15px
        }

        .note,
        .reject {
            padding: 10px;
            margin-top: 10px;
            background: #f8f6f2
        }

        .reject {
            background: #fff0f0;
            color: #a72e2e
        }

        .sticky-card {
            position: sticky;
            top: 15px
        }

        .big-progress {
            text-align: center;
            font-size: 38px;
            font-weight: 800;
            color: #b4791b
        }

        .progress>div {
            background: #b4791b
        }

        .summary span {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee
        }

        .admin-tip {
            background: #fff7e8;
            padding: 12px;
            margin-top: 15px;
            color: #795319
        }

        @media(max-width:700px) {

            .detail-grid,
            .installment-info {
                grid-template-columns: 1fr 1fr
            }
        }
    </style>
    @push('scripts')
        <script>
            $('.approve-btn').click(function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Verifikasi pembayaran ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, verifikasi'
                }).then(r => {
                    if (r.isConfirmed) $.post(`/admin/pemabayan/${id}/approve`, {
                            _token: $('meta[name=csrf-token]').attr('content')
                        }).done(x => Swal.fire('Berhasil', x.message, 'success').then(() => location.reload()))
                        .fail(x => Swal.fire('Gagal', x.responseJSON?.message || 'Terjadi kesalahan', 'error'));
                });
            });
            $('.reject-btn').click(function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Tolak pembayaran',
                    input: 'textarea',
                    inputLabel: 'Alasan penolakan',
                    inputValidator: v => !v && 'Alasan wajib diisi',
                    showCancelButton: true,
                    confirmButtonText: 'Tolak',
                    confirmButtonColor: '#dc3545'
                }).then(r => {
                    if (r.isConfirmed) $.post(`/admin/pemabayan/${id}/reject`, {
                            _token: $('meta[name=csrf-token]').attr('content'),
                            alasan: r.value
                        }).done(x => Swal.fire('Terkirim', x.message, 'success').then(() => location.reload()))
                        .fail(x => Swal.fire('Gagal', x.responseJSON?.message || 'Terjadi kesalahan', 'error'));
                });
            });
        </script>
    @endpush
@endsection
