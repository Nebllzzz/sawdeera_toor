<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        @page {
            margin: 24px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #241a10;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        .invoice {
            border: 1px solid #3d3023;
            padding: 0 14px 18px;
        }

        .header {
            margin: 0 -14px;
            padding: 15px 18px 13px;
            background: #f5d77b;
            border-bottom: 8px solid #bc7f1b;
        }

        .header-table,
        .identity-table,
        .items,
        .totals,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .company {
            width: 56%;
            line-height: 1.55;
            vertical-align: middle;
        }

        .company b {
            font-size: 12px;
        }

        .logo-cell {
            width: 44%;
            text-align: right;
            vertical-align: middle;
        }

        .logo-cell img {
            width: 250px;
            max-height: 72px;
            object-fit: contain;
        }

        h1 {
            margin: 22px 0 20px;
            text-align: center;
            color: #17120d;
            font-size: 31px;
            text-decoration: underline;
            text-decoration-color: #c18a2d;
        }

        .identity-table {
            margin-bottom: 21px;
        }

        .identity-table th {
            width: 92px;
            padding: 3px 0;
            text-align: left;
            vertical-align: top;
            font-size: 11px;
        }

        .identity-table td {
            padding: 3px 8px;
            vertical-align: top;
        }

        .identity-table .gap {
            width: 38px;
        }

        .items th {
            padding: 8px 7px;
            color: #fff;
            background: #58420f;
            border: 1px solid #58420f;
            font-size: 10px;
        }

        .items td {
            padding: 9px 7px;
            border: 1px solid #ddd5c8;
            vertical-align: top;
        }

        .items tbody tr:nth-child(even) {
            background: #f3f3f3;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .payment-details {
            margin-top: 12px;
            padding: 10px 12px;
            background: #fbf7ec;
            border: 1px solid #eadfca;
        }

        .payment-details b {
            display: block;
            margin-bottom: 6px;
            color: #6f4a13;
        }

        .payment-details span {
            display: inline-block;
            margin-right: 14px;
            margin-bottom: 4px;
            font-size: 9px;
        }

        .totals {
            width: 48%;
            margin: 16px 0 0 auto;
        }

        .totals th,
        .totals td {
            padding: 5px 8px;
            border-bottom: 1px solid #ded8cf;
        }

        .totals th {
            width: 55%;
            text-align: left;
            font-style: italic;
        }

        .totals td {
            text-align: right;
        }

        .totals .grand-total th,
        .totals .grand-total td {
            color: #17120d;
            font-size: 13px;
            font-weight: bold;
            border-top: 2px solid #58420f;
            border-bottom: 0;
        }

        .paid-stamp {
            width: 230px;
            margin: 8px 0 0 auto;
            padding: 6px 12px;
            text-align: center;
            color: #0e614a;
            background: #bcecd9;
            border: 2px solid #198765;
            font-size: 20px;
            font-weight: bold;
            font-style: italic;
            letter-spacing: 1px;
        }

        .footer-table {
            margin-top: 25px;
        }

        .footer-table td {
            width: 50%;
            vertical-align: bottom;
        }

        .note {
            color: #6f675e;
            font-size: 9px;
            line-height: 1.5;
        }

        .signature {
            text-align: center;
            line-height: 1.5;
        }

        .signature-space {
            height: 43px;
        }
    </style>
</head>

<body>
    @php
        $jemaah = $pembayaran->jemaah;
        $paket = $pembayaran->pengajuan?->paketUmrah;
        $jadwal = $pembayaran->pengajuan?->keberangkatan;
        $paidTotal = (float) $pembayaran->tahapan->sum('nominal');
    @endphp

    <main class="invoice">
        <header class="header">
            <table class="header-table">
                <tr>
                    <td class="company">
                        <b>Kantor Pusat | PT. Sawdeera Berkah Utama</b><br>
                        Jl. Bojongrenget, Teluknaga, Kab. Tangerang, Banten 15510<br>
                        0812-8723-4572 | 0896-2246-2777<br>
                        info@sawdeeratour.com | www.sawdeeratour.com
                    </td>
                    <td class="logo-cell">
                        <img src="{{ public_path('img/logo.png') }}" alt="Sawdeera Tour">
                    </td>
                </tr>
            </table>
        </header>

        <h1>INVOICE / KWITANSI</h1>

        <table class="identity-table">
            <tr>
                <th>No. Invoice</th>
                <td>{{ $invoiceNumber }}</td>
                <td class="gap"></td>
                <th>Tanggal Lunas</th>
                <td>{{ $paidAt?->translatedFormat('d F Y') ?? '-' }}</td>
            </tr>
            <tr>
                <th>Yth. Bapak/Ibu</th>
                <td>{{ $jemaah?->user?->name ?? '-' }}</td>
                <td class="gap"></td>
                <th>No. Telepon</th>
                <td>{{ $jemaah?->no_telepon ?? '-' }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td colspan="4">{{ $jemaah?->alamat ?? '-' }}</td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 9%">Jumlah</th>
                    <th style="width: 39%">Paket</th>
                    <th style="width: 20%">Keberangkatan</th>
                    <th style="width: 16%">Harga</th>
                    <th style="width: 16%">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">1 Jemaah</td>
                    <td>
                        <b>{{ $paket?->nama_paket ?? '-' }}</b><br>
                        <span style="color: #756c61">Durasi {{ $paket?->durasi ?? '-' }} hari</span>
                    </td>
                    <td class="center">{{ $jadwal?->tanggal_keberangkatan?->translatedFormat('d F Y') ?? '-' }}</td>
                    <td class="right">Rp {{ number_format((float) $pembayaran->total_tagihan, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format((float) $pembayaran->total_tagihan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="payment-details">
            <b>Rincian Pembayaran Terverifikasi</b>
            @foreach ($pembayaran->tahapan as $tahap)
                <span>
                    {{ $tahap->nama_tahap }}: Rp {{ number_format((float) $tahap->nominal, 0, ',', '.') }}
                    ({{ $tahap->verified_at?->format('d/m/Y') ?? '-' }})
                </span>
            @endforeach
        </div>

        <table class="totals">
            <tr>
                <th>Sub Total</th>
                <td>Rp {{ number_format((float) $pembayaran->total_tagihan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Diskon / Potongan</th>
                <td>Rp 0</td>
            </tr>
            <tr class="grand-total">
                <th>Total Lunas</th>
                <td>Rp {{ number_format($paidTotal, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="paid-stamp">LUNAS FULL</div>

        <table class="footer-table">
            <tr>
                <td class="note">
                    Dokumen ini diterbitkan secara elektronik oleh sistem Sawdeera Tour<br>
                    setelah seluruh tahapan pembayaran diverifikasi. Simpan invoice ini<br>
                    sebagai bukti pembayaran yang sah.
                </td>
                <td class="signature">
                    Tangerang, {{ $paidAt?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}<br>
                    <div class="signature-space"></div>
                    <b>PT. Sawdeera Berkah Utama</b><br>
                    Finance Sawdeera Tour
                </td>
            </tr>
        </table>
    </main>
</body>

</html>
