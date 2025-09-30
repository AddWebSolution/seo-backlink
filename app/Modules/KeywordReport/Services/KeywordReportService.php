<?php

namespace App\Modules\KeywordReport\Services;

use Addweb\Base\Services\BaseService;
use App\Modules\KeywordReport\Models\KeywordReport;
use App\Modules\Report\Models\Report;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KeywordReportService extends BaseService
{
    public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
    ];

    public function __construct()
    {
        $this->object = new KeywordReport();
    }

    public function getReportWithKeywords(int $id, int $perPage = 10, array $filters = [])
    {
        $report = KeywordReport::findOrFail($id);

        $query = $report->keywords()->with('domain', 'keyword');
        $report->success = $report->getSuccessCount();
        $report->fail = $report->getFailCount();
        $report->total_keywords = $report->getKeywordsCount();
        $report->domain_count = $report->getDomainsCount();

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('llm_type', 'like', "%{$search}%")
                    ->orWhere('llm_response', 'like', "%{$search}%")
                    ->orWhereHas('domain', function ($q2) use ($search) {
                        $q2->where('title', 'like', "%{$search}%")
                            ->orWhere('target_url', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['domain']) && $filters['domain'] !== null) {
            $query->where('client_domain_id', $filters['domain']);
        }

        $keywords = $query->paginate($perPage);

        $domains = $report->getDomains();

        return [
            'report'    => $report,
            'keywords' => $keywords,
            'domains'   => $domains
        ];
    }

    public function exportKeywordReport($reportIds)
    {
        if (empty($reportIds)) {
            return response()->json(['message' => 'No report IDs provided'], 400);
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($reportIds as $id) {
            $report = KeywordReport::findOrFail($id);
            $keywords = $report->keywords()->with('domain', 'keyword')->get();

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle("Report-{$report->id}");
            $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

            $summaryData = [
                ['Report ID', $report->id],
                ['Run ID', $report->run_id],
                ['Run At', $report->run_at],
                ['Total Domains', $report->getDomainsCount()],
                ['Total Keywords', $report->getKeywordsCount()],
                ['Success', $report->getSuccessCount()],
                ['Fail', $report->getFailCount()],
            ];

            $sheet->fromArray($summaryData, null, 'A2');

            $sheet->getStyle('A2:B8')->applyFromArray([
                'font' => ['size' => 12, 'bold' => true],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F4FD']
                ]
            ]);
            $sheet->getStyle('A2:A8')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '2B5797']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D4E6F1']
                ]
            ]);

            $headers = [
                'ID',
                'Keyword ID',
                'Keyword',
                'Client Domain',
                'Target URL',
                'LLM Type',
                'Found in Response',
                'Highlights',
                'Status',
                'Processed At',
                'LLM Response'
            ];
            $startRow = 10;
            $sheet->fromArray($headers, null, "A{$startRow}");

            $headerRange = 'A' . $startRow . ':' . chr(64 + count($headers)) . $startRow;
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2B5797']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ]);

            $row = $startRow + 1;
            foreach ($keywords as $kw) {
                $rowData = [
                    $kw->id,
                    $kw->keyword_id,
                    optional($kw->keyword)->keyword ?? '',
                    optional($kw->domain)->title ?? '',
                    optional($kw->domain)->target_url ?? '',
                    $kw->llm_type,
                    $kw->domain_found_in_response == 1 ? 'Yes' : 'No',
                    $kw->highlights,
                    $kw->status,
                    $kw->processed_at,
                    $kw->llm_response,
                ];
                $sheet->fromArray($rowData, null, "A{$row}");

                if ($row % 2 == 0) {
                    $dataRange = 'A' . $row . ':' . chr(64 + count($headers)) . $row;
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8F9FA']
                        ]
                    ]);
                }

                $row++;
            }

            if ($row > $startRow + 1) {
                $dataRange = 'A' . $startRow . ':' . chr(64 + count($headers)) . ($row - 1);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
            }

            foreach (range('A', chr(64 + count($headers))) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $sheet->freezePane('A' . ($startRow + 1));
            $sheet->getRowDimension($startRow)->setRowHeight(25);

            $sheet->setCellValue('A1', 'KEYWORD REPORT SUMMARY');
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1F4E79']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'B4C6E7']
                ]
            ]);
            $sheet->getRowDimension(1)->setRowHeight(30);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'KeywordReport_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0'
        ]);
    }
}
