<?php

namespace App\Http\Controllers;

use App\Models\PaketUmrah;
use App\Services\JemaahRecapService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JemaahRecapController extends Controller
{
    public function __construct(private readonly JemaahRecapService $recapService) {}

    public function index()
    {

        return view('home.rekapitulasi-jemaah.index', [
            'reportTypes' => JemaahRecapService::reportTypes(),
            'packages' => PaketUmrah::orderBy('nama_paket')->get(),
            'defaultStart' => now()->startOfYear()->toDateString(),
            'defaultEnd' => now()->endOfYear()->toDateString(),
        ]);
    }

    public function data(Request $request)
    {
        [$type, $packageId, $start, $end] = $this->filters($request);

        return response()->json($this->recapService->build($type, $packageId, $start, $end));
    }

    public function exportExcel(Request $request)
    {
        [$type, $packageId, $start, $end] = $this->filters($request);
        $report = $this->recapService->build($type, $packageId, $start, $end);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr(str_replace('Rekapitulasi ', '', $report['title']), 0, 31));
        $lastColumn = Coordinate::stringFromColumnIndex(count($report['columns']));

        $sheet->setCellValue('A1', $report['title'].' Jemaah');
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A2', 'Sawdeera Tour & Travel');
        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->setCellValue('A4', 'Periode');
        $sheet->setCellValue('B4', $report['period_label']);
        $sheet->setCellValue('A5', 'Paket Umrah');
        $sheet->setCellValue('B5', $report['package_label']);

        foreach ($report['columns'] as $index => $column) {
            $sheet->setCellValue([$index + 1, 7], $column['label']);
        }

        $rowNumber = 8;
        foreach ($report['rows'] as $row) {
            foreach ($report['columns'] as $index => $column) {
                $sheet->setCellValue([$index + 1, $rowNumber], $row[$column['key']] ?? 0);
                $this->applyExcelNumberFormat($sheet, $index + 1, $rowNumber, $column['format']);
            }
            $rowNumber++;
        }

        foreach ($report['columns'] as $index => $column) {
            $value = match ($column['key']) {
                'package_name' => 'TOTAL',
                'month_label' => '',
                default => $report['totals'][$column['key']] ?? 0,
            };
            $sheet->setCellValue([$index + 1, $rowNumber], $value);
            $this->applyExcelNumberFormat($sheet, $index + 1, $rowNumber, $column['format']);
        }

        $sheet->getStyle("A1:{$lastColumn}1")->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF7A4B13');
        $sheet->getStyle("A2:{$lastColumn}2")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A7:{$lastColumn}7")
            ->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle("A7:{$lastColumn}7")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF8A5B19');
        $sheet->getStyle("A7:{$lastColumn}{$rowNumber}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFD7D9DF');
        $sheet->getStyle("A{$rowNumber}:{$lastColumn}{$rowNumber}")->getFont()->setBold(true);
        $sheet->getStyle("A{$rowNumber}:{$lastColumn}{$rowNumber}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF4DE');
        $sheet->getStyle("A7:{$lastColumn}{$rowNumber}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->freezePane('C8');
        $sheet->setAutoFilter("A7:{$lastColumn}".max(7, $rowNumber - 1));
        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(20);
        for ($column = 3; $column <= count($report['columns']); $column++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setWidth(20);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'rekapitulasi_'.$type.'_'.$start->format('Ymd').'_'.$end->format('Ymd').'.xlsx';

        return response()->streamDownload(fn () => $writer->save('php://output'), $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(Request $request)
    {
        [$type, $packageId, $start, $end] = $this->filters($request);
        $report = $this->recapService->build($type, $packageId, $start, $end);
        $report['generated_at'] = now()->format('d/m/Y H:i').' WIB';

        return Pdf::loadView('home.rekapitulasi-jemaah.pdf', [
            'report' => $report,
            'recapService' => $this->recapService,
        ])->setPaper('a4', 'landscape')->download(
            'rekapitulasi_'.$type.'_'.$start->format('Ymd').'_'.$end->format('Ymd').'.pdf'
        );
    }

    private function filters(Request $request): array
    {
        $input = $request->all();
        $input['package_id'] = in_array($request->input('package_id'), [null, '', 'all'], true)
            ? null
            : $request->input('package_id');

        $validated = validator($input, [
            'type' => ['required', Rule::in(array_keys(JemaahRecapService::reportTypes()))],
            'package_id' => ['nullable', 'integer', 'exists:paket_umrah,id'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ], [
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ])->validate();

        return [
            $validated['type'],
            isset($validated['package_id']) ? (int) $validated['package_id'] : null,
            Carbon::createFromFormat('Y-m-d', $validated['start_date'])->startOfDay(),
            Carbon::createFromFormat('Y-m-d', $validated['end_date'])->endOfDay(),
        ];
    }

    private function applyExcelNumberFormat($sheet, int $column, int $row, string $format): void
    {
        $coordinate = Coordinate::stringFromColumnIndex($column).$row;
        $numberFormat = match ($format) {
            'currency' => '[$Rp-421] #,##0',
            'percent' => '0.0"%"',
            default => null,
        };
        if ($numberFormat) {
            $sheet->getStyle($coordinate)->getNumberFormat()->setFormatCode($numberFormat);
        }
    }
}
