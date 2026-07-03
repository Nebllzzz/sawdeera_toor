<!doctype html>
<html>

<head>
    <meta charset="utf-8">

    <title>
        Laporan Data Jemaah
    </title>

    <style>
        @page {
            size: A4;
            margin: 18mm;
        }

        body {
            font-family: Arial, sans-serif;
            color: #222;
            font-size: 12px;
            position: relative;
            min-height: 100%;
        }

        .kop-wrapper {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 14px;
            margin-bottom: 25px;
        }

        .kop-table {
            width: 100%;
            border: none;
        }

        .kop-table td {
            border: none;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .alamat {
            font-size: 12px;
            line-height: 1.5;
        }

        .generated {
            margin-top: 8px;
            font-size: 11px;
            color: #666;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            text-transform: uppercase;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            font-size: 11px;
        }

        th {
            background: #f1f1f1;
            text-align: center;
            font-weight: bold;
        }

        td {
            vertical-align: middle;
        }

        /* TANDA TANGAN FIX BAWAH KANAN */
        .signature-wrapper {
            position: fixed;
            right: 0;
            bottom: 10px;
            width: 260px;
            text-align: center;
        }

        .ttd-space {
            height: 80px;
        }
    </style>

<body>

    {{-- KOP SURAT --}}
    <div class="kop-wrapper">

        <table class="kop-table">

            <tr>

                <td class="text-center">

                    <div class="company-name">
                        Sawdeera Toor
                    </div>

                    <div class="report-title">
                        LAPORAN DATA JEMAAH
                    </div>

                    <div class="alamat">

                        Jl. Kp. Tukang Kajang, RT.25/RW.12,
                        Bojong Renged, Kec. Teluknaga, Tangerang

                    </div>

                    <div class="generated">

                        Generated :
                        {{ date('d-m-Y H:i') }}

                    </div>

                </td>

            </tr>

        </table>

    </div>

    {{-- JUDUL --}}
    <div class="section-title">
        Data Jemaah Sawdeera Toor
    </div>

    {{-- TABLE --}}
    <table>

        <thead>

            <tr>

                <th width="40">
                    No
                </th>

                <th>
                    Nama Jemaah
                </th>

                <th>
                    NIK
                </th>

                <th>
                    Paket
                </th>

                <th width="110">
                    Keberangkatan
                </th>

                <th width="120">
                    Status Pembayaran
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse ($rows as $i => $r)

                <tr>

                    <td class="text-center">
                        {{ $i + 1 }}
                    </td>

                    <td>
                        {{ $r->nama }}
                    </td>

                    <td>
                        {{ $r->nik }}
                    </td>

                    <td>
                        {{ $r->paket }}
                    </td>

                    <td class="text-center">
                        {{ $r->keberangkatan }}
                    </td>

                    <td class="text-center">
                        {{ $r->status_pembayaran }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6" class="text-center">

                        Tidak ada data jemaah

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

    {{-- TANDA TANGAN --}}
    <div class="signature-wrapper">

        <div>
            Tangerang, {{ date('d F Y') }}
        </div>

        <br>

        <div>
            Pimpinan Sawdeera Toor
        </div>

        <div class="ttd-space"></div>

        <div>
            ____________________
        </div>

    </div>
</body>

</html>
