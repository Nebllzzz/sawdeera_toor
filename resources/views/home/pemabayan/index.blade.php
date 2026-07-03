@extends('layouts.main')
@section('title', 'Pembayaran')

@section('content')

    <div class="content-wrapper" style="min-height:100vh; padding:30px;">

        <div class="container-fluid">

            {{-- HEADER --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:18px; overflow:hidden;">

                <div class="card-header text-white py-4"
                    style="background:linear-gradient(135deg,#5c3317,#8c4d24); border:none;">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h3 class="mb-1 fw-bold">
                                Upload Pembayaran Umrah
                            </h3>

                            <p class="mb-0 text-light">
                                Silakan lakukan pembayaran sesuai metode yang tersedia dan upload bukti pembayaran
                                untuk proses verifikasi admin.
                            </p>
                        </div>
                        {{-- BUTTON INFO --}}
                        <div class="d-flex justify-content-end gap-2 mt-3">

                            <button type="button" class="btn btn-sawdeera1 text-white" id="btnShowInfo">

                                ℹ️ Informasi Lengkap Terkait Pembayaran

                            </button>

                            <button type="button" class="btn btn-sawdeera1 d-none text-white" id="btnHideInfo">

                                ✖ Hide Informasi Terkait Pembayaran

                            </button>

                        </div>
                    </div>

                </div>

                <div class="card-body bg-white">
                    {{-- INFO PEMBAYARAN --}}
                    <div class="row d-none" id="paymentInfoRow">

                        <div class="col-12">

                            <div class="card border-0 shadow-sm" style="border-radius:18px; overflow:hidden;">

                                <div class="card-header text-white py-3"
                                    style="background:linear-gradient(135deg,#6b3f1e,#8c4d24); border:none;">

                                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                                        <div>
                                            <h5 class="mb-1 fw-bold">
                                                💳 Informasi Pembayaran
                                            </h5>

                                            <small class="text-light">
                                                Pilih metode pembayaran sesuai kebutuhan Anda.
                                            </small>
                                        </div>

                                    </div>

                                </div>

                                <div class="card-body p-4">

                                    <div class="row">

                                        {{-- TRANSFER BANK --}}
                                        <div class="col-lg-4 mb-4">

                                            <div class="border rounded p-3 h-100">

                                                <h5 class="fw-bold mb-4">
                                                    🏦 Transfer Bank
                                                </h5>

                                                <div class="mb-4">
                                                    <small class="text-muted">
                                                        BANK BCA
                                                    </small>

                                                    <h6 class="fw-bold mb-1">
                                                        1234567890
                                                    </h6>

                                                    <small>
                                                        a.n Sawdeera Toor
                                                    </small>
                                                </div>

                                                <div class="mb-4">
                                                    <small class="text-muted">
                                                        BANK MANDIRI
                                                    </small>

                                                    <h6 class="fw-bold mb-1">
                                                        9876543210
                                                    </h6>

                                                    <small>
                                                        a.n Sawdeera Toor
                                                    </small>
                                                </div>

                                                <div class="mb-4">
                                                    <small class="text-muted">
                                                        BANK BRI
                                                    </small>

                                                    <h6 class="fw-bold mb-1">
                                                        1122334455
                                                    </h6>

                                                    <small>
                                                        a.n Sawdeera Toor
                                                    </small>
                                                </div>

                                                <div class="mb-4">
                                                    <small class="text-muted">
                                                        BANK BNI
                                                    </small>

                                                    <h6 class="fw-bold mb-1">
                                                        6677889900
                                                    </h6>

                                                    <small>
                                                        a.n Sawdeera Toor
                                                    </small>
                                                </div>

                                                <div>
                                                    <small class="text-muted">
                                                        BANK CIMB NIAGA
                                                    </small>

                                                    <h6 class="fw-bold mb-1">
                                                        5544332211
                                                    </h6>

                                                    <small>
                                                        a.n Sawdeera Toor
                                                    </small>
                                                </div>

                                            </div>

                                        </div>

                                        {{-- DIGITAL PAYMENT --}}
                                        <div class="col-lg-4 mb-4">

                                            <div class="border rounded p-3 h-100">

                                                <h5 class="fw-bold mb-4">
                                                    💳 Digital Payment
                                                </h5>

                                                <div class="mb-4">
                                                    <small class="text-muted">
                                                        OVO
                                                    </small>

                                                    <h6 class="fw-bold mb-1">
                                                        081234567890
                                                    </h6>

                                                    <small>
                                                        a.n Sawdeera Toor
                                                    </small>
                                                </div>

                                                <div class="mb-4">
                                                    <small class="text-muted">
                                                        GoPay
                                                    </small>

                                                    <h6 class="fw-bold mb-1">
                                                        081298765432
                                                    </h6>

                                                    <small>
                                                        a.n Sawdeera Toor
                                                    </small>
                                                </div>

                                                <div class="mb-4">
                                                    <small class="text-muted">
                                                        DANA
                                                    </small>

                                                    <h6 class="fw-bold mb-1">
                                                        081355667788
                                                    </h6>

                                                    <small>
                                                        a.n Sawdeera Toor
                                                    </small>
                                                </div>

                                                <div class="mb-4">
                                                    <small class="text-muted">
                                                        ShopeePay
                                                    </small>

                                                    <h6 class="fw-bold mb-1">
                                                        081377889900
                                                    </h6>

                                                    <small>
                                                        a.n Sawdeera Toor
                                                    </small>
                                                </div>

                                                <div class="mb-4">
                                                    <small class="text-muted">
                                                        LinkAja
                                                    </small>

                                                    <h6 class="fw-bold mb-1">
                                                        081344556677
                                                    </h6>

                                                    <small>
                                                        a.n Sawdeera Toor
                                                    </small>
                                                </div>

                                                <button class="btn btn-outline-dark btn-sm mt-2" data-toggle="modal"
                                                    data-target="#modalQris">

                                                    🔳 Lihat QRIS

                                                </button>

                                            </div>

                                        </div>

                                        {{-- INFORMASI --}}
                                        <div class="col-lg-4 mb-4">

                                            <div class="border rounded p-3 h-100" style="background:#fffaf2;">

                                                <h5 class="fw-bold mb-4">
                                                    ℹ️ Informasi Pembayaran
                                                </h5>

                                                <div class="mb-3">
                                                    ✅ Pastikan nominal transfer sesuai tagihan.
                                                </div>

                                                <div class="mb-3">
                                                    ✅ Upload bukti pembayaran yang jelas dan valid.
                                                </div>

                                                <div class="mb-3">
                                                    ✅ Verifikasi pembayaran dilakukan maksimal 1x24 jam.
                                                </div>

                                                <div class="mb-3">
                                                    ✅ Simpan bukti transfer hingga status diverifikasi.
                                                </div>

                                                <div class="mb-0">
                                                    ✅ Hubungi admin apabila pembayaran belum diverifikasi lebih dari 24 jam.
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            @if (!$keberangkatanJemaah)

                <div class="alert alert-warning shadow-sm">
                    Anda <b>belum memiliki jadwal keberangkatan</b>.
                    Silakan daftar terlebih dahulu pada menu
                    <a href="/keberangkatan-jemaah">Keberangkatan Saya</a>.
                </div>
            @else
                @php
                    $keberangkatanId = $keberangkatanJemaah->keberangkatan_id;
                    $bukti = $pembayaran?->bukti_pembayaran;
                @endphp

                <div class="row align-items-stretch">

                    {{-- FORM --}}
                    <div class="col-lg-7 mb-4 d-flex">

                        <div class="card border-0 shadow-sm w-100" style="border-radius:18px; overflow:hidden;">

                            <div class="card-header text-white py-3"
                                style="background:linear-gradient(135deg,#6b3f1e,#8c4d24); border:none;">

                                <h5 class="mb-0 fw-bold">
                                    📤 Form Upload Pembayaran
                                </h5>

                            </div>

                            <div class="card-body p-4">

                                <form method="POST" action="/pemabayan/upload" enctype="multipart/form-data">

                                    @csrf

                                    <input type="hidden" name="keberangkatan_id" value="{{ $keberangkatanId }}">

                                    {{-- JENIS --}}
                                    <div class="form-group mb-3">
                                        <label class="fw-semibold">
                                            Jenis Pembayaran
                                        </label>

                                        <select name="jenis_pembayaran" class="form-control" required>

                                            <option value="dp"
                                                {{ optional($pembayaran)->jenis_pembayaran === 'dp' ? 'selected' : '' }}>
                                                DP
                                            </option>

                                            <option value="cicilan"
                                                {{ optional($pembayaran)->jenis_pembayaran === 'cicilan' ? 'selected' : '' }}>
                                                Cicilan
                                            </option>

                                            <option value="pelunasan"
                                                {{ optional($pembayaran)->jenis_pembayaran === 'pelunasan' ? 'selected' : '' }}>
                                                Pelunasan
                                            </option>

                                        </select>
                                    </div>

                                    {{-- JUMLAH --}}
                                    <div class="form-group mb-3">

                                        <label class="fw-semibold">
                                            Jumlah Pembayaran
                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">
                                                Rp
                                            </span>

                                            <input type="text" id="jumlah_display" class="form-control"
                                                inputmode="numeric" autocomplete="off" placeholder="Contoh: 33.000.000">

                                            <input type="hidden" name="jumlah" id="jumlah">

                                        </div>

                                        <small class="text-muted">
                                            Masukkan nominal tanpa simbol rupiah.
                                        </small>

                                    </div>

                                    {{-- METODE --}}
                                    <div class="form-group mb-3">

                                        <label class="fw-semibold">
                                            Metode Pembayaran
                                        </label>

                                        <select name="metode_pembayaran" class="form-control" required>

                                            <optgroup label="Transfer Bank">
                                                <option value="Transfer via BCA">
                                                    Transfer via BCA
                                                </option>

                                                <option value="Transfer via Mandiri">
                                                    Transfer via Mandiri
                                                </option>

                                                <option value="Transfer via BRI">
                                                    Transfer via BRI
                                                </option>

                                                <option value="Transfer via BNI">
                                                    Transfer via BNI
                                                </option>

                                                <option value="Transfer via CIMB NIAGA">
                                                    Transfer via CIMB NIAGA
                                                </option>
                                            </optgroup>

                                            <optgroup label="Digital Payment">
                                                <option value="Visa Card">
                                                    Visa Card
                                                </option>

                                                <option value="Mastercard">
                                                    Mastercard
                                                </option>

                                                <option value="QRIS">
                                                    QRIS
                                                </option>

                                                <option value="OVO">
                                                    OVO
                                                </option>

                                                <option value="GoPay">
                                                    GoPay
                                                </option>

                                                <option value="DANA">
                                                    DANA
                                                </option>

                                                <option value="LinkAja">
                                                    LinkAja
                                                </option>

                                                <option value="ShopeePay">
                                                    ShopeePay
                                                </option>
                                            </optgroup>

                                        </select>

                                    </div>

                                    @php
                                        $status = optional($pembayaran)->status;
                                        $canUpload = !in_array($status, ['diproses', 'ditolak']);
                                        $canEdit = in_array($status, ['diproses', 'ditolak']);
                                        $isVerified = $status === 'diverifikasi';
                                    @endphp

                                    {{-- FILE --}}
                                    <div class="form-group mb-4">

                                        <label class="fw-semibold">
                                            Bukti Pembayaran
                                        </label>

                                        @if ($isVerified)
                                            <div class="text-muted d-block mb-2">
                                                Pembayaran sudah <b>diverifikasi</b>. Upload/edit dinonaktifkan.
                                            </div>
                                        @elseif($canUpload)
                                            <small class="text-muted d-block mb-2">
                                                Upload bukti pembayaran untuk memulai verifikasi.
                                            </small>
                                        @else
                                            <small class="text-muted d-block mb-2">
                                                Status saat ini: <b>{{ $status }}</b>. Silakan <b>edit/update</b>
                                                bukti pembayaran jika ingin ada perubahan.
                                            </small>
                                        @endif

                                        <input type="file" name="bukti_pembayaran" class="form-control"
                                            {{ $isVerified ? 'disabled' : '' }} {{ $canUpload ? 'required' : '' }}>

                                        <small class="text-muted">
                                            Format yang didukung: JPG, PNG, PDF (maksimal 2MB)
                                        </small>

                                    </div>

                                    <button class="btn btn-dark w-100 py-2" {{ $isVerified ? 'disabled' : '' }}>
                                        🚀 Upload Pembayaran
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                    {{-- STATUS --}}
                    <div class="col-lg-5 mb-4 d-flex">

                        <div class="card border-0 shadow-sm h-100 w-100" style="border-radius:18px; overflow:hidden;">

                            <div class="card-header text-white py-3"
                                style="background:linear-gradient(135deg,#6b3f1e,#8c4d24); border:none;">

                                <h5 class="mb-0 fw-bold">
                                    📋 Status Pembayaran
                                </h5>

                            </div>

                            <div class="card-body p-4 d-flex flex-column">

                                @php
                                    $status = optional($pembayaran)->status;

                                    $badge = match ($status) {
                                        'diverifikasi' => 'bg-success',
                                        'ditolak' => 'bg-danger',
                                        'diproses' => 'bg-warning',
                                        default => 'bg-secondary',
                                    };
                                @endphp

                                <div class="mb-3">
                                    <span class="badge {{ $badge }} py-2 w-100 d-block text-center">
                                        {{ ucfirst($status) ?? 'belum upload' }}
                                    </span>
                                </div>

                                @if ($pembayaran && $pembayaran->keterangan_penolakan)
                                    <div class="mb-3">
                                        <b>Alasan Penolakan:</b><br>
                                        {{ $pembayaran->keterangan_penolakan }}
                                    </div>
                                @endif

                                @if ($pembayaran && $pembayaran->bukti_pembayaran)

                                    <div class="border rounded p-3 d-flex align-items-center justify-content-center flex-grow-1"
                                        style="min-height:350px;">
                                        @if (Str::contains($pembayaran->bukti_pembayaran, ['.pdf']))
                                            <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}"
                                                target="_blank" class="btn btn-outline-dark">

                                                📄 Lihat PDF

                                            </a>
                                        @else
                                            <img src="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}"
                                                class="rounded shadow-sm"
                                                style="max-height:300px; max-width:100%; object-fit:contain;">
                                        @endif

                                    </div>
                                @else
                                    <div class="text-muted">
                                        Bukti pembayaran belum tersedia.
                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            @endif

        </div>

    </div>

    <div class="modal fade" id="modalQris" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content border-0">

                <div class="modal-header">
                    <h5 class="modal-title">
                        QRIS Pembayaran
                    </h5>

                    <button type="button" class="close" data-dismiss="modal">

                        <span>&times;</span>

                    </button>
                </div>

                <div class="modal-body text-center">

                    <img src="{{ asset('img/dummy-qris.png') }}" class="img-fluid rounded shadow-sm">

                    <small class="d-block text-muted mt-3">
                        Scan QRIS di atas untuk melakukan pembayaran.
                    </small>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <script>
        const jumlahDisplay = document.getElementById('jumlah_display');
        const jumlahHidden = document.getElementById('jumlah');

        jumlahDisplay.addEventListener('keyup', function() {

            // ambil angka saja
            let angka = this.value.replace(/[^,\d]/g, '');

            // simpan raw value ke hidden input
            jumlahHidden.value = angka;

            // format ribuan
            let split = angka.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;

            this.value = rupiah;
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // FORMAT RUPIAH
            const jumlahDisplay = document.getElementById('jumlah_display');
            const jumlahHidden = document.getElementById('jumlah');

            jumlahDisplay.addEventListener('keyup', function() {

                let angka = this.value.replace(/[^,\d]/g, '');

                jumlahHidden.value = angka;

                let split = angka.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;

                this.value = rupiah;

            });

            // SHOW HIDE PAYMENT INFO
            const paymentInfoRow = document.getElementById('paymentInfoRow');
            const btnShowInfo = document.getElementById('btnShowInfo');
            const btnHideInfo = document.getElementById('btnHideInfo');

            btnShowInfo.addEventListener('click', function() {

                paymentInfoRow.classList.remove('d-none');

                btnShowInfo.classList.add('d-none');

                btnHideInfo.classList.remove('d-none');

            });

            btnHideInfo.addEventListener('click', function() {

                paymentInfoRow.classList.add('d-none');

                btnHideInfo.classList.add('d-none');

                btnShowInfo.classList.remove('d-none');

            });

        });
    </script>
@endpush
