<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KeberangkatanJemaah;
use App\Models\PaketUmrah;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index()
    {
        $paket = PaketUmrah::where('is_active', true)->get();
        return view('home.laporan-jemaah.laporan_jemaah', compact('paket'));
    }

    public function data(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('[ReportController@data] called', [
            'program' => $request->input('program'),
            'period' => $request->input('period'),
            // user id/role debug (avoid auth()->user() type hints issues)
            'user_id' => null,
            'user_role' => null,
        ]);

        $q = KeberangkatanJemaah::with(['jemaah.user', 'paketUmrah', 'keberangkatan']);

        if ($request->filled('program') && $request->program !== 'all') {
            $q->where('paket_umrah_id', $request->program);
        }

        if ($request->filled('period')) {
            $period = $request->period;
            if (strpos($period, '|') !== false) {
                [$from, $to] = explode('|', $period);
            } elseif (strpos($period, ' - ') !== false) {
                [$from, $to] = explode(' - ', $period);
            } else {
                $from = $to = $period;
            }

            try {
                $from = trim($from);
                $to = trim($to);

                $from = date('Y-m-d', strtotime($from));
                $to = date('Y-m-d', strtotime($to));


                // date range filter (UTC vs Asia/Jakarta)
                // tanggal_keberangkatan adalah DATETIME (tersimpan UTC),
                // sementara UI mengirim tanggal tanpa jam.
                // Gunakan range mulai 00:00 sampai 23:59:59 pada UTC.
                $q->whereHas('keberangkatan', function ($s) use ($from, $to) {
                    $s->whereBetween(
                        'tanggal_keberangkatan',
                        [
                            $from . ' 00:00:00',
                            $to . ' 23:59:59'
                        ]
                    );
                });
            } catch (\Exception $e) {

                // ignore invalid period
            }
        }

        return DataTables::of($q)


            ->addIndexColumn()
            ->addColumn('nama', function ($it) {
                return $it->jemaah->user->name ?? '-';
            })
            ->addColumn('paket', function ($it) {
                return $it->paketUmrah->nama_paket ?? '-';
            })
            ->addColumn('keberangkatan', function ($it) {
                return $it->keberangkatan && $it->keberangkatan->tanggal_keberangkatan ? $it->keberangkatan->tanggal_keberangkatan->format('d/m/Y') : '-';
            })
            ->addColumn('kode', function ($it) {
                return $it->jemaah->nik ?? '-';
            })
            ->addColumn('status_pembayaran', function ($it) {
                $p = DB::table('pembayaran')
                    ->where('jemaah_id', $it->jemaah_id)
                    ->where('keberangkatan_id', $it->keberangkatan_id)
                    ->orderByDesc('id')
                    ->first();

                if ($p && $p->status == 'diverifikasi') return 'Lunas';
                if ($p && $p->status == 'ditolak') return 'Ditolak';
                return 'Belum';
            })
            ->rawColumns([])
            ->make(true);
    }

    public function exportExcel(Request $request)
    {
        $q = KeberangkatanJemaah::with([
            'jemaah.user',
            'paketUmrah',
            'keberangkatan'
        ]);

        if ($request->filled('program') && $request->program !== 'all') {
            $q->where('paket_umrah_id', $request->program);
        }

        if ($request->filled('period')) {

            $period = $request->period;

            if (strpos($period, '|') !== false) {
                [$from, $to] = explode('|', $period);
            } elseif (strpos($period, ' - ') !== false) {
                [$from, $to] = explode(' - ', $period);
            } else {
                $from = $to = $period;
            }

            try {

                $from = date('Y-m-d', strtotime($from));
                $to   = date('Y-m-d', strtotime($to));

                $q->whereHas('keberangkatan', function ($s) use ($from, $to) {
                    $s->whereBetween('tanggal_keberangkatan', [$from, $to]);
                });

            } catch (\Exception $e) {
                //
            }
        }

        $items = $q->get();

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        // HEADER
        $sheet->setCellValue('A1', 'Laporan Data Jemaah Sawdeera Toor');
        $sheet->mergeCells('A1:F1');

        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Nama Jemaah');
        $sheet->setCellValue('C3', 'NIK');
        $sheet->setCellValue('D3', 'Paket');
        $sheet->setCellValue('E3', 'Keberangkatan');
        $sheet->setCellValue('F3', 'Status Pembayaran');

        // STYLE HEADER
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A3:F3')->getFont()->setBold(true);

        $row = 4;
        $no  = 1;

        foreach ($items as $it) {

            $p = DB::table('pembayaran')
                ->where('jemaah_id', $it->jemaah_id)
                ->where('keberangkatan_id', $it->keberangkatan_id)
                ->orderByDesc('id')
                ->first();

            $statusPembayaran = 'Belum';

            if ($p && $p->status == 'diverifikasi') {
                $statusPembayaran = 'Lunas';
            }

            if ($p && $p->status == 'ditolak') {
                $statusPembayaran = 'Ditolak';
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $it->jemaah->user->name ?? '-');
            $sheet->setCellValue('C' . $row, $it->jemaah->nik ?? '-');
            $sheet->setCellValue('D' . $row, $it->paketUmrah->nama_paket ?? '-');
            $sheet->setCellValue(
                'E' . $row,
                optional($it->keberangkatan->tanggal_keberangkatan)->format('d/m/Y')
            );
            $sheet->setCellValue('F' . $row, $statusPembayaran);

            $row++;
        }

        // AUTO SIZE
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // BORDER
        $sheet->getStyle('A3:F' . ($row - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        $fileName = 'laporan_jemaah_' . date('Ymd_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' =>
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $q = KeberangkatanJemaah::with([
            'jemaah.user',
            'paketUmrah',
            'keberangkatan'
        ]);

        if ($request->filled('program') && $request->program !== 'all') {
            $q->where('paket_umrah_id', $request->program);
        }

        if ($request->filled('period')) {

            $period = $request->period;

            if (strpos($period, '|') !== false) {
                [$from, $to] = explode('|', $period);
            } elseif (strpos($period, ' - ') !== false) {
                [$from, $to] = explode(' - ', $period);
            } else {
                $from = $to = $period;
            }

            try {

                $from = date('Y-m-d', strtotime($from));
                $to = date('Y-m-d', strtotime($to));

                $q->whereHas('keberangkatan', function ($s) use ($from, $to) {

                    $s->whereBetween('tanggal_keberangkatan', [$from, $to]);

                });

            } catch (\Exception $e) {
                //
            }
        }

        $items = $q->get();

        $rows = $items->map(function ($it) {

            $p = DB::table('pembayaran')
                ->where('jemaah_id', $it->jemaah_id)
                ->where('keberangkatan_id', $it->keberangkatan_id)
                ->latest()
                ->first();

            $statusPembayaran = 'Belum Bayar';

            if ($p && $p->status == 'diverifikasi') {
                $statusPembayaran = 'Lunas';
            }

            if ($p && $p->status == 'ditolak') {
                $statusPembayaran = 'Ditolak';
            }

            return (object) [

                'nama' => $it->jemaah->user->name ?? '-',

                'nik' => $it->jemaah->nik ?? '-',

                'paket' => $it->paketUmrah->nama_paket ?? '-',

                'keberangkatan' => optional(
                    $it->keberangkatan->tanggal_keberangkatan
                )->format('d F Y'),

                'status_pembayaran' => $statusPembayaran,

            ];
        });

        $pdf = Pdf::loadView('reports.jemaah_print', [
            'rows' => $rows
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan-jemaah-' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }
}
