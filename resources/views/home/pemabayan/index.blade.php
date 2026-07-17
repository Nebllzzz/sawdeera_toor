@extends('layouts.main')
@section('title', 'Pembayaran')
@section('content')
    @php
        $paid = $pembayaran?->tahapan->where('status', 'diverifikasi')->sum('nominal') ?? 0;
        $total = (float) ($pembayaran?->total_tagihan ?? 0);
        $remaining = max(0, $total - $paid);
        $progress = $total > 0 ? round(($paid / $total) * 100) : 0;
        $current = $pembayaran?->tahapan->first(fn($t) => $t->status !== 'diverifikasi');
        $schemeLabels = [
            'sekali_bayar' => 'Satu Kali Bayar',
            'cicilan_3_bulan' => '3 Kali Cicilan',
            'cicilan_6_bulan' => '6 Kali Cicilan',
            'cicilan_12_bulan' => '12 Kali Cicilan',
        ];
    @endphp
    <div class="content-wrapper payment-page px-3">
        <section class="content py-4">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="font-weight-bold mb-1">Pembayaran</h2>
                        <small class="text-muted">Dashboard &nbsp;›&nbsp; Pembayaran</small>
                    </div>
                    <a href="/dashboard" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mx-2"></i>Kembali ke
                        Dashboard</a>
                </div>
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                @if (!$pembayaran)
                    <div class="empty-payment"><i class="fas fa-receipt"></i>
                        <h4>Belum ada rencana pembayaran</h4>
                        <p>Pilih paket dan ajukan keberangkatan terlebih dahulu.</p><a href="/paket-umrah-jemaah"
                            class="btn-gold">Pilih Paket Umrah</a>
                    </div>
                @else
                    @php
                        $trip = $pembayaran->pengajuan;
                    @endphp
                    <div class="row">
                        <div class="col-xl-9">
                            <div class="package-selected mb-3">
                                <img src="{{ asset('img/thumb1.jpg') }}">
                                <div class="flex-grow-1"><small>Paket yang Dipilih</small>
                                    <h4>{{ $trip->paketUmrah->nama_paket }}</h4>
                                    <div class="package-facts">
                                        <span><i
                                                class="far fa-calendar"></i><small>Keberangkatan</small><b>{{ $trip->keberangkatan->tanggal_keberangkatan->translatedFormat('d F Y') }}</b></span>
                                        <span><i class="far fa-clock"></i><small>Durasi</small><b>{{ $trip->paketUmrah->durasi }}
                                                Hari</b></span>
                                        <span><i
                                                class="fas fa-plane"></i><small>Maskapai</small><b>{{ $trip->keberangkatan->maskapaiBerangkat->nama ?? '-' }}</b></span>
                                        <span><i
                                                class="far fa-building"></i><small>Hotel</small><b>{{ $trip->paketUmrah->hotelMakkah->nama ?? '-' }}</b></span>
                                    </div>
                                </div>
                                <a href="/keberangkatan-jemaah" class="btn-outline-gold">Lihat Detail Paket <i
                                        class="fas fa-chevron-right"></i></a>
                            </div>

                            <div class="payment-card mb-3">
                                <h6>Informasi Pembayaran</h6>
                                <div class="info-grid">
                                    <div><small>Total Harga Paket</small><b>Rp {{ number_format($total, 0, ',', '.') }}</b>
                                    </div>
                                    <div><small>Skema
                                            Cicilan</small><b>{{ $schemeLabels[$pembayaran->jenis_pembayaran] ?? $pembayaran->jenis_pembayaran }}</b>
                                    </div>
                                    <div><small>DP (Pembayaran
                                            1)</small><b>{{ $pembayaran->dp_persen ? $pembayaran->dp_persen . '% dari total harga' : 'Pembayaran penuh' }}</b>
                                    </div>
                                </div>
                                <p class="deadline-note"><i class="far fa-calendar-alt"></i> Pembayaran terakhir harus lunas
                                    paling lambat 30 hari sebelum keberangkatan.</p>
                            </div>

                            <div class="payment-card mb-3">
                                <h6>Rencana Pembayaran ({{ $pembayaran->jumlah_tahap }} Tahap)</h6>
                                <div class="payment-steps">
                                    @foreach ($pembayaran->tahapan as $t)
                                        @php
                                            $state =
                                                $t->status === 'diverifikasi'
                                                    ? 'done'
                                                    : ($t->status === 'diproses'
                                                        ? 'process'
                                                        : ($t->status === 'ditolak'
                                                            ? 'rejected'
                                                            : 'waiting'));
                                        @endphp
                                        <div class="step {{ $state }}">
                                            <span>{{ $t->status === 'diverifikasi' ? '✓' : $t->urutan }}</span><small>{{ $t->nama_tahap }}
                                                ({{ number_format($t->persentase, 2, ',', '.') }}%)
                                            </small><b>{{ ucfirst(str_replace('_', ' ', $t->status)) }}</b>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="table-responsive">
                                    <table class="table installment-table">
                                        <thead>
                                            <tr>
                                                <th>Tahap Pembayaran</th>
                                                <th>Persentase</th>
                                                <th>Jatuh Tempo</th>
                                                <th>Nominal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pembayaran->tahapan as $t)
                                                <tr>
                                                    <td><b>{{ $t->urutan }}. {{ $t->nama_tahap }}</b></td>
                                                    <td>{{ number_format($t->persentase, 2, ',', '.') }}%</td>
                                                    <td
                                                        class="{{ $t->jatuh_tempo->isPast() && $t->status !== 'diverifikasi' ? 'text-danger' : '' }}">
                                                        <b>{{ $t->jatuh_tempo->translatedFormat('d F Y') }}</b>
                                                    </td>
                                                    <td>Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                                                    <td><span
                                                            class="status-pill status-{{ $t->status }}">{{ ['belum_bayar' => 'Belum Dibayar', 'diproses' => 'Diverifikasi Admin', 'diverifikasi' => 'Lunas', 'ditolak' => 'Ditolak'][$t->status] ?? $t->status }}</span>
                                                    </td>
                                                </tr>
                                                @if ($t->status === 'ditolak')
                                                    <tr>
                                                        <td colspan="5">
                                                            <div class="reject-note"><b>Catatan Admin:</b>
                                                                {{ $t->keterangan_penolakan }}</div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="important-note"><i class="far fa-bell"></i>
                                    <div><b>Catatan Penting</b><br>Pastikan pembayaran dilakukan sesuai jadwal agar
                                        pendaftaran tetap aktif.</div>
                                </div>
                            </div>

                            <div class="payment-card mb-3">
                                <h6>Upload Bukti Pembayaran</h6>
                                @if (!$current)
                                    <div class="alert alert-success mb-0"><i class="fas fa-check-circle mx-2"></i>Semua
                                        tahap pembayaran sudah lunas dan diverifikasi.</div>
                                @elseif($current->status === 'diproses')
                                    <div class="alert alert-warning mb-0"><i class="fas fa-clock mx-2"></i>Bukti
                                        {{ $current->nama_tahap }} sedang diverifikasi admin. Tahap berikutnya akan terbuka
                                        setelah disetujui.</div>
                                @else
                                    <form method="POST" action="/pemabayan/upload" enctype="multipart/form-data">
                                        @csrf<input type="hidden" name="tahap_id" value="{{ $current->id }}">
                                        <div class="current-bill">
                                            <div><small>Tahap yang harus dibayar</small>
                                                <h5>{{ $current->nama_tahap }}</h5>
                                            </div>
                                            <div class="text-right"><small>Nominal tepat</small>
                                                <h4>Rp {{ number_format($current->nominal, 0, ',', '.') }}</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-7 form-group"><label>Bukti Pembayaran</label><label
                                                    class="upload-zone" id="paymentUploadZone">
                                                    <div id="uploadPlaceholder"><i
                                                            class="fas fa-cloud-upload-alt"></i><b>Klik untuk
                                                            memilih bukti pembayaran</b><small>JPG, JPEG, PNG, PDF · Maks.
                                                            5MB</small></div>
                                                    <div id="uploadPreview" class="d-none"></div>
                                                    <input type="file" id="paymentProofInput" name="bukti_pembayaran"
                                                        accept=".jpg,.jpeg,.png,.pdf" required>
                                                </label></div>
                                            <div class="col-md-5">
                                                <div class="form-group"><label>Metode Pembayaran</label><select
                                                        name="metode_pembayaran" class="form-control" required>
                                                        <option value="">Pilih metode</option>
                                                        <option>Transfer BCA</option>
                                                        <option>Transfer Mandiri</option>
                                                        <option>Transfer BRI</option>
                                                        <option>Transfer BNI</option>
                                                        <option>QRIS</option>
                                                    </select></div>
                                                <div class="form-group"><label>Catatan (Opsional)</label>
                                                    <textarea name="catatan_jemaah" class="form-control" placeholder="Contoh: Pembayaran cicilan kedua"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="justify-content-end">
                                            <button class="btn-gold mt-2 w-100">
                                                <i class="fas fa-cloud-upload-alt mx-2"></i>
                                                {{ $current->status === 'ditolak' ? 'Kirim Ulang Bukti' : 'Kirim Bukti Pembayaran' }}
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>

                            <div class="payment-card payment-info-row">
                                <h6>Informasi Rekening Pembayaran</h6>
                                <div class="account-grid">
                                    <div><i class="fas fa-university"></i><span><small>Bank
                                                BCA</small><b>1234567890</b><em>a.n Sawdeera Tour</em></span></div>
                                    <div><i class="fas fa-university"></i><span><small>Bank
                                                Mandiri</small><b>9876543210</b><em>a.n Sawdeera Tour</em></span></div>
                                    <div><i class="fas fa-qrcode"></i><span><small>Pembayaran
                                                Digital</small><b>QRIS</b><em>Tersedia 24 jam</em></span></div>
                                </div>
                                <div class="safe-info"><i class="fas fa-shield-alt"></i> Pastikan nama penerima adalah
                                    <b>Sawdeera Tour</b> dan nominal sesuai tagihan aktif.
                                </div>
                            </div>
                        </div>
                        <aside class="col-xl-3">
                            <div class="side-card">
                                <h6>Ringkasan Pembayaran</h6>
                                <div class="summary-row"><span>Total Harga Paket</span><b>Rp
                                        {{ number_format($total, 0, ',', '.') }}</b></div>
                                <div class="summary-row paid"><span>Sudah Dibayar</span><b>Rp
                                        {{ number_format($paid, 0, ',', '.') }}</b></div>
                                <div class="summary-row remaining"><span>Sisa Tagihan</span><b>Rp
                                        {{ number_format($remaining, 0, ',', '.') }}</b></div>
                                <div class="summary-row"><span>Persentase Terbayar</span><b>{{ $progress }}%</b></div>
                                <div class="progress">
                                    <div style="width:{{ $progress }}%"></div>
                                </div>
                            </div>
                            <div class="side-card">
                                <h6>Jadwal Pembayaran</h6>
                                @foreach ($pembayaran->tahapan as $t)
                                    <div class="mini-timeline {{ $t->status }}">
                                        <span>{{ $t->status === 'diverifikasi' ? '✓' : $t->urutan }}</span>
                                        <div>
                                            <b>{{ $t->nama_tahap }}</b><small>{{ $t->jatuh_tempo->translatedFormat('d M Y') }}</small><em>{{ ['diverifikasi' => 'Lunas', 'diproses' => 'Menunggu Verifikasi', 'ditolak' => 'Perlu Perbaikan', 'belum_bayar' => 'Belum Dibayar'][$t->status] }}</em>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="side-card">
                                <h6>Informasi Penting</h6>
                                <ul class="important-list">
                                    <li>DP merupakan pembayaran pertama.</li>
                                    <li>Tahap selanjutnya terbuka setelah pembayaran diverifikasi.</li>
                                    <li>Verifikasi maksimal 1×24 jam kerja.</li>
                                    <li>Pelunasan paling lambat H-30.</li>
                                </ul>
                            </div>
                            <div class="side-card">
                                <h6><i class="fas fa-headset"></i> Butuh Bantuan?</h6>
                                <p>Jika ada kendala dalam pengunggahan dokumen, hubungi tim kami.</p>
                                <b><i class="fab fa-whatsapp mx-2"></i>0895-6007-91616</b>
                                <br>
                                <b><i class="fas fa-envelope mx-2 mt-2"></i>info@sawdeeratour.com</b>
                            </div>
                        </aside>
                    </div>
                @endif
            </div>
        </section>
    </div>
    <style>
        .payment-page {
            background: #faf9f7 !important
        }

        .package-selected,
        .payment-card,
        .side-card,
        .empty-payment {
            background: #fff;
            border: 1px solid #ece9e4;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(60, 42, 20, .05)
        }

        .package-selected {
            padding: 16px;
            display: flex;
            gap: 20px;
            align-items: center
        }

        .package-selected>img {
            width: 145px;
            height: 105px;
            border-radius: 8px;
            object-fit: cover
        }

        .package-selected h4 {
            font-weight: 750
        }

        .package-facts {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px
        }

        .package-facts span {
            position: relative;
            padding-left: 25px
        }

        .package-facts i {
            position: absolute;
            left: 0;
            top: 5px;
            color: #93631c
        }

        .package-facts small,
        .package-facts b {
            display: block;
            font-size: 11px
        }

        .btn-outline-gold,
        .btn-gold {
            border: 1px solid #c68b2c;
            color: #a66d12;
            background: #fff;
            padding: 10px 15px;
            border-radius: 7px;
            font-weight: 600
        }

        .btn-gold {
            background: #bd8120;
            color: #fff
        }

        .payment-card {
            padding: 18px
        }

        .payment-card>h6,
        .side-card>h6 {
            font-weight: 750;
            margin-bottom: 16px
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px
        }

        .info-grid>div {
            background: #fcf8f1;
            border: 1px solid #f0e5d3;
            padding: 14px;
            text-align: center;
            border-radius: 7px
        }

        .info-grid small,
        .info-grid b {
            display: block
        }

        .deadline-note {
            font-size: 12px;
            margin: 13px 0 0
        }

        .payment-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 28px 20px
        }

        .payment-steps:before {
            content: "";
            position: absolute;
            top: 14px;
            left: 3%;
            right: 3%;
            height: 3px;
            background: #e5e5e5
        }

        .step {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 120px
        }

        .step>span {
            display: flex;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #ddd;
            align-items: center;
            justify-content: center;
            margin: auto
        }

        .step>small,
        .step>b {
            display: block;
            font-size: 10px
        }

        .step.done>span {
            background: #4fba75;
            color: white
        }

        .step.process>span {
            background: #dfa33e;
            color: white
        }

        .step.rejected>span {
            background: #d85c5c;
            color: white
        }

        .installment-table {
            font-size: 12px
        }

        .status-pill {
            border-radius: 15px;
            padding: 5px 9px;
            white-space: nowrap
        }

        .status-diverifikasi {
            background: #e6f6eb;
            color: #278245
        }

        .status-diproses {
            background: #fff2d8;
            color: #966315
        }

        .status-ditolak {
            background: #fde7e7;
            color: #ae3030
        }

        .status-belum_bayar {
            background: #eee;
            color: #666
        }

        .reject-note {
            background: #fff0f0;
            color: #a32c2c;
            padding: 10px;
            border-radius: 6px
        }

        .important-note,
        .safe-info {
            background: #fff8eb;
            border: 1px solid #f0dfbd;
            padding: 11px;
            border-radius: 7px;
            display: flex;
            gap: 10px;
            color: #7c581d
        }

        .current-bill {
            background: #fbf7ef;
            padding: 13px;
            display: flex;
            justify-content: space-between;
            border-radius: 7px;
            margin-bottom: 14px
        }

        .upload-zone {
            min-height: 180px;
            border: 1px dashed #cb9031;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer
        }

        #uploadPlaceholder {
            text-align: center
        }

        .upload-zone i {
            font-size: 25px
        }

        .upload-zone small {
            display: block
        }

        .upload-zone input {
            display: none
        }

        #uploadPreview {
            width: 100%;
            height: 100%;
            min-height: 170px;
            position: relative;
            text-align: center
        }

        #uploadPreview img {
            width: 100%;
            height: 170px;
            object-fit: contain;
            border-radius: 7px;
            background: #f5f5f5
        }

        #uploadPreview object {
            width: 100%;
            height: 170px;
            border: 0;
            border-radius: 7px
        }

        .preview-caption {
            position: absolute;
            left: 7px;
            right: 7px;
            bottom: 7px;
            padding: 7px 10px;
            background: rgba(35, 28, 20, .82);
            color: #fff;
            border-radius: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .account-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 12px
        }

        .account-grid>div {
            display: flex;
            gap: 12px;
            border: 1px solid #eee;
            padding: 13px;
            border-radius: 8px
        }

        .account-grid i {
            color: #bb7f1e;
            font-size: 22px
        }

        .account-grid small,
        .account-grid b,
        .account-grid em {
            display: block
        }

        .account-grid em {
            font-size: 11px;
            color: #777
        }

        .side-card {
            padding: 18px;
            margin-bottom: 16px
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 9px 0;
            border-bottom: 1px solid #eee
        }

        .summary-row.paid b {
            color: #27904b
        }

        .summary-row.remaining b {
            color: #c03c3c
        }

        .progress {
            height: 7px;
            background: #eee;
            margin-top: 10px
        }

        .progress>div {
            background: #bb7f1e
        }

        .mini-timeline {
            display: flex;
            gap: 10px;
            padding-bottom: 17px
        }

        .mini-timeline>span {
            display: flex;
            flex: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: #ddd;
            align-items: center;
            justify-content: center;
            font-size: 11px
        }

        .mini-timeline.diverifikasi>span {
            background: #4fba75;
            color: #fff
        }

        .mini-timeline.diproses>span {
            background: #dfa33e;
            color: #fff
        }

        .mini-timeline b,
        .mini-timeline small,
        .mini-timeline em {
            display: block;
            font-size: 10px
        }

        .mini-timeline em {
            color: #a36b16
        }

        .important-list {
            padding-left: 18px;
            font-size: 11px
        }

        .important-list li {
            margin-bottom: 10px
        }

        .empty-payment {
            text-align: center;
            padding: 70px
        }

        .empty-payment i {
            font-size: 45px;
            color: #bd8120;
            margin-bottom: 15px
        }

        @media(max-width:768px) {
            .package-selected {
                display: block
            }

            .package-selected>img {
                width: 100%;
                height: 170px;
                margin-bottom: 12px
            }

            .package-facts,
            .info-grid,
            .account-grid {
                grid-template-columns: 1fr
            }

            .payment-steps {
                overflow: auto;
                gap: 30px
            }

            .step {
                min-width: 90px
            }
        }
    </style>
    @push('scripts')
        <script>
            (() => {
                const input = document.getElementById('paymentProofInput');
                const placeholder = document.getElementById('uploadPlaceholder');
                const preview = document.getElementById('uploadPreview');
                if (!input || !placeholder || !preview) return;

                let objectUrl = null;
                input.addEventListener('change', function() {
                    const file = this.files && this.files[0];
                    if (!file) return;

                    if (objectUrl) URL.revokeObjectURL(objectUrl);
                    objectUrl = URL.createObjectURL(file);
                    const escapedName = $('<div>').text(file.name).html();
                    const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');

                    preview.innerHTML = isPdf ?
                        `<object data="${objectUrl}" type="application/pdf">
                               <div class="py-5"><i class="fas fa-file-pdf text-danger fa-3x"></i><p>Preview PDF tidak didukung browser ini.</p></div>
                           </object><div class="preview-caption"><i class="fas fa-file-pdf mx-2"></i>${escapedName} · Klik untuk mengganti</div>` :
                        `<img src="${objectUrl}" alt="Preview bukti pembayaran">
                           <div class="preview-caption"><i class="fas fa-image mx-2"></i>${escapedName} · Klik untuk mengganti</div>`;

                    placeholder.classList.add('d-none');
                    preview.classList.remove('d-none');
                });

                window.addEventListener('beforeunload', () => {
                    if (objectUrl) URL.revokeObjectURL(objectUrl);
                });
            })();
        </script>
    @endpush
@endsection
