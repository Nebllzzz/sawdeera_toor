<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KeberangkatanJemaah;
use App\Models\PaketUmrah;
use App\Models\DokumenJemaah;
use App\Models\Pembayaran;
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
        $base = KeberangkatanJemaah::query();
        $stats = [
            'total' => (clone $base)->count(),
            'siap' => (clone $base)->where('status', 'setuju')->count(),
            'kelengkapan' => (clone $base)->where('status', 'pendaftaran')->count(),
            'verifikasi' => DokumenJemaah::where('status', 'diproses')->distinct('jemaah_id')->count('jemaah_id'),
            'berangkat' => (clone $base)->whereHas('keberangkatan', fn($q) => $q->whereIn('status', ['berangkat', 'berlangsung', 'pulang']))->count(),
            'selesai' => (clone $base)->whereHas('keberangkatan', fn($q) => $q->where('status', 'selesai'))->count(),
        ];
        return view('home.laporan-jemaah.laporan_jemaah', compact('paket', 'stats'));
    }

    public function data(Request $request)
    {
        $q = KeberangkatanJemaah::with(['jemaah.user', 'paketUmrah', 'keberangkatan.maskapaiBerangkat', 'pembayaran.tahapan'])->orderBy('created_at', 'desc');

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
            ->addColumn('no_daftar', fn ($it) => $this->registrationNumber($it))
            ->addColumn('nama', fn ($it) => '<b>'.e($it->jemaah->user->name ?? '-').'</b><br><small>'.e($it->jemaah->no_telepon ?? '-').'</small>')
            ->addColumn('paket', fn ($it) => e($it->paketUmrah->nama_paket ?? '-'))
            ->addColumn('keberangkatan', fn ($it) => $it->keberangkatan?->tanggal_keberangkatan?->translatedFormat('d M Y').'<br><small>'.($it->keberangkatan?->jam_berangkat ?? '-').' WIB</small>')
            ->addColumn('progress', fn ($it) => $this->progressHtml($it))
            ->addColumn('status_monitoring', fn ($it) => '<span class="badge badge-'.($this->monitoringStatus($it)[1]).'">'.$this->monitoringStatus($it)[0].'</span>')
            ->addColumn('action', fn ($it) => '<button class="btn btn-sm btn-light-primary btn-detail" data-id="'.$it->id.'"><i class="far fa-eye mr-1"></i> Detail</button>')
            ->addColumn('kode', fn ($it) => $it->jemaah->nik ?? '-')
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
            ->rawColumns(['nama', 'keberangkatan', 'progress', 'status_monitoring', 'action'])
            ->make(true);
    }

    public function detail($id)
    {
        $it = KeberangkatanJemaah::with([
            'jemaah.user', 'paketUmrah.hotelMakkah', 'paketUmrah.hotelMadinah',
            'keberangkatan.maskapaiBerangkat', 'keberangkatan.maskapaiPulang', 'keberangkatan.leader',
            'pembayaran.tahapan',
        ])->findOrFail($id);
        $docs = DokumenJemaah::where('jemaah_id', $it->jemaah_id)->get();

        return response()->json([
            'id' => $it->id,
            'no_pendaftaran' => $this->registrationNumber($it),
            'nama' => $it->jemaah->user->name ?? '-',
            'telepon' => $it->jemaah->no_telepon ?? '-',
            'paket' => $it->paketUmrah->nama_paket ?? '-',
            'keberangkatan' => $it->keberangkatan?->tanggal_keberangkatan?->translatedFormat('d F Y') ?? '-',
            'maskapai' => $it->keberangkatan?->maskapaiBerangkat?->nama ?? '-',
            'hotel_makkah' => $it->paketUmrah?->hotelMakkah?->nama ?? '-',
            'hotel_madinah' => $it->paketUmrah?->hotelMadinah?->nama ?? '-',
            'tour_guide' => $it->keberangkatan?->leader?->nama ?? '-',
            'kuota' => ($it->keberangkatan?->terisi ?? 0).' / '.($it->keberangkatan?->kuota ?? 0).' Jemaah',
            'progress' => $this->progressValue($it, $docs),
            'status' => $this->monitoringStatus($it)[0],
            'timeline' => $this->timeline($it, $docs),
        ]);
    }

    private function registrationNumber(KeberangkatanJemaah $item): string
    {
        return 'UMR'.now()->format('ym').str_pad(strtoupper(base_convert((string) $item->id, 10, 36)), 5, '0', STR_PAD_LEFT);
    }

    private function progressValue(KeberangkatanJemaah $item, $docs = null): int
    {
        $docs ??= DokumenJemaah::where('jemaah_id', $item->jemaah_id)->get();
        $payment = $item->pembayaran;
        $done = 0;
        $done += $item->jemaah?->user ? 1 : 0;
        $done += $item->jemaah?->user?->status === 'aktif' ? 1 : 0;
        $done += $item->paketUmrah ? 1 : 0;
        $done += $item->jemaah?->status_data === 'terverifikasi' ? 1 : 0;
        $requiredDocumentCount = $item->jemaah?->status_pernikahan === 'menikah' ? 7 : 6;
        $uploadedDocumentCount = $docs->whereIn('status', ['diproses', 'diverifikasi', 'ditolak'])->count();
        $done += $uploadedDocumentCount >= $requiredDocumentCount ? 1 : 0;
        $done += $payment && in_array($payment->status, ['diproses', 'diverifikasi'], true) ? 1 : 0;
        $done += $payment?->status === 'diverifikasi' ? 1 : 0;
        $done += $item->keberangkatan ? 1 : 0;
        $done += in_array($item->keberangkatan?->status, ['berangkat','berlangsung','pulang','selesai'], true) ? 1 : 0;
        $done += $item->keberangkatan?->status === 'selesai' ? 1 : 0;
        return (int) round(($done / 10) * 100);
    }

    private function progressHtml(KeberangkatanJemaah $item): string
    {
        $value = $this->progressValue($item);
        $color = $value < 40 ? '#e74c3c' : ($value < 70 ? '#f39c12' : '#43a047');
        return "<b>{$value}%</b><div class=\"progress\" style=\"height:6px\"><div class=\"progress-bar\" style=\"width:{$value}%;background:{$color}\"></div></div>";
    }

    private function monitoringStatus(KeberangkatanJemaah $item): array
    {
        if ($item->keberangkatan?->status === 'selesai') return ['Selesai Umrah', 'success'];
        if (in_array($item->keberangkatan?->status, ['berangkat','berlangsung','pulang'], true)) return ['Sudah Berangkat', 'primary'];
        if ($item->status === 'setuju') return ['Siap Berangkat', 'info'];
        if ($item->pembayaran?->status === 'diverifikasi') return ['Menunggu Keberangkatan', 'warning'];
        if ($item->pembayaran?->status === 'diproses') return ['Proses Verifikasi', 'warning'];
        if ($item->jemaah?->status_data !== 'terverifikasi') return ['Lengkapi Dokumen', 'danger'];
        return ['Menunggu Pembayaran', 'warning'];
    }

    private function timeline(KeberangkatanJemaah $item, $docs): array
    {
        $payment = $item->pembayaran;
        $latestDoc = $docs->sortByDesc('updated_at')->first();

        return [
            ['label' => 'Registrasi Akun', 'status' => $item->jemaah?->user ? 'Selesai' : 'Belum Diproses', 'date' => $item->jemaah?->user?->created_at?->translatedFormat('d M Y')],
            ['label' => 'Verifikasi Akun', 'status' => $item->jemaah?->user?->status === 'aktif' ? 'Selesai' : 'Belum Diproses', 'date' => $item->jemaah?->user?->updated_at?->translatedFormat('d M Y')],
            ['label' => 'Pilih Paket Umrah', 'status' => $item->paketUmrah ? 'Selesai' : 'Belum Diproses', 'date' => $item->created_at?->translatedFormat('d M Y')],
            ['label' => 'Lengkapi Data Diri', 'status' => $item->jemaah?->status_data === 'terverifikasi' ? 'Selesai' : 'Belum Diproses', 'date' => $item->jemaah?->updated_at?->translatedFormat('d M Y')],
            ['label' => 'Upload Dokumen Pendukung', 'status' => $docs->whereIn('status', ['diproses', 'diverifikasi', 'ditolak'])->count() >= ($item->jemaah?->status_pernikahan === 'menikah' ? 7 : 6) ? 'Selesai' : 'Menunggu Upload', 'date' => $latestDoc?->updated_at?->translatedFormat('d M Y')],
            ['label' => 'Upload Bukti Pembayaran', 'status' => $payment ? 'Menunggu Verifikasi' : 'Belum Diproses', 'date' => $payment?->updated_at?->translatedFormat('d M Y')],
            ['label' => 'Verifikasi Pendaftaran', 'status' => $payment?->status === 'diverifikasi' ? 'Selesai' : 'Belum Diproses', 'date' => $payment?->updated_at?->translatedFormat('d M Y')],
            ['label' => 'Masuk Jadwal Keberangkatan', 'status' => $item->keberangkatan ? 'Selesai' : 'Belum Diproses', 'date' => $item->keberangkatan?->tanggal_keberangkatan?->translatedFormat('d M Y')],
            ['label' => 'Sudah Berangkat', 'status' => in_array($item->keberangkatan?->status, ['berangkat','berlangsung','pulang','selesai'], true) ? 'Selesai' : 'Belum Diproses', 'date' => null],
            ['label' => 'Selesai Umrah', 'status' => $item->keberangkatan?->status === 'selesai' ? 'Selesai' : 'Belum Diproses', 'date' => null],
        ];
    }

    public function exportExcel(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

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
