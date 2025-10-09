<?php

namespace App\Modules\Report\Services;

use Addweb\Base\Services\BaseService;
use App\Enums\UserRole;
use App\Modules\BacklinkDatum\Models\BacklinkDatum;
use App\Modules\Report\Models\Report;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService extends BaseService
{
    public array $searchFields = [
        'run_id' => [],
    ];

    public array $filters = [
        'client_id' => ['filter' => ''],
    ];

    protected function loadRelations(): void
    {
        $authUser = auth()->user();
        if ($authUser->role === UserRole::CLIENT) {
            $this->query->where('client_id', auth()->id());
        }
        $this->loadExtraRelation();
    }

    public function __construct()
    {
        $this->object = new Report();
    }

    public function getReportWithBacklinks(int $id, int $perPage = 10, array $filters = [])
    {
        $report = Report::findOrFail($id);

        $query = $report->backlinks();
        $report->accepted_backlinks = $report->getAcceptedCount();
        $report->rejected_backlinks = $report->getRejectedCount();
        $report->domain_count = $report->getDomainsCount();

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                    ->orWhere('from_url', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%")
                    ->orWhere('domain_url', 'like', "%{$search}%")
                    ->orWhere('target_domain', 'like', "%{$search}%")
                    ->orWhere('target_url', 'like', "%{$search}%")
                    ->orWhere('anchor', 'like', "%{$search}%")
                    ->orWhere('page_title', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['domain'])) {
            $query->where(function ($q) use ($filters) {
                $q->orwhere('domain_url', $filters['domain'])
                    ->orWhere('domain', $filters['domain']);
            });
        }

        $backlinks = $query->paginate($perPage);

        $domains = $report->getDomains();

        return [
            'report'    => $report,
            'backlinks' => $backlinks,
            'domains'   => $domains
        ];
    }

    public function exportReport($reportIds)
    {
        if (empty($reportIds)) {
            return response()->json(['message' => 'No report IDs provided'], 400);
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($reportIds as $id) {
            $report = Report::findOrFail($id);
            $backlinks = $report->backlinks()->get();
            $client = $report->client ? $report->client->only(['id', 'name', 'email']) : null;

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle("Report-{$report->id}");

            $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

            $summaryData = [
                ['Report ID', $report->id],
                ['Run ID', $report->run_id],
                ['Total Domains', $report->getDomainsCount()],
                ['Total Backlinks', $report->backlinks()->count()],
                ['Accepted Backlinks', $report->getAcceptedCount()],
                ['Rejected Backlinks', $report->getRejectedCount()]
            ];

            // Add summary data
            $sheet->fromArray($summaryData, null, 'A1');

            // Style summary section
            $sheet->getStyle('A1:B6')->applyFromArray([
                'font' => [
                    'size' => 12,
                    'bold' => true
                ],
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

            // Style summary labels (column A)
            $sheet->getStyle('A1:A6')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '2B5797']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D4E6F1']
                ]
            ]);

            // Headers for backlinks table
            $headers = [
                'ID',
                'Target URL',
                'Domain',
                'Client',
                'From Domain',
                'Rank',
                'Domain Rank',
                'Spam Score',
                'Do Follow',
                'Tier',
                'Score',
                'Is Broken',
                'URL',
                'From URL',
                'Anchor',
                'Status',
                'Reason',
                'Key Highlights',
                'First Seen',
                'Last Seen',
                'Link Type',
                'Page Title'
            ];

            // Add headers starting from row 8
            $sheet->fromArray($headers, null, 'A8');

            // Style headers
            $headerRange = 'A8:' . chr(64 + count($headers)) . '8';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
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

            // Fill backlinks data
            $row = 9;
            foreach ($backlinks as $bl) {
                $rowData = [
                    $bl->id,
                    $bl->target_url,
                    $bl->domain,
                    $bl->client_id ? $client['name'] : 'N/A',
                    $bl->from_domain,
                    $bl->rank,
                    $bl->domain_rank,
                    $bl->spam_score,
                    $bl->do_follow ? 'Yes' : 'No',
                    $bl->tier,
                    $bl->score,
                    $bl->is_broken ? 'Yes' : 'No',
                    $bl->url,
                    $bl->from_url,
                    $bl->anchor,
                    $bl->status,
                    $bl->reason,
                    implode(", ", json_decode($bl->key_highlights, true) ?? []),
                    $bl->first_seen,
                    $bl->last_seen,
                    $bl->link_type,
                    $bl->page_title,
                ];

                $sheet->fromArray($rowData, null, "A{$row}");

                // Alternate row colors for better readability
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

            // Add borders to data area
            if ($row > 9) {
                $dataRange = 'A8:' . chr(64 + count($headers)) . ($row - 1);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
            }

            // Auto-fit columns
            foreach (range('A', chr(64 + count($headers))) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Set minimum column widths for better appearance
            $sheet->getColumnDimension('A')->setWidth(8);  // ID
            $sheet->getColumnDimension('B')->setWidth(25); // Target URL
            $sheet->getColumnDimension('C')->setWidth(20); // Domain
            $sheet->getColumnDimension('D')->setWidth(20); // From Domain
            $sheet->getColumnDimension('N')->setWidth(15); // Anchor
            $sheet->getColumnDimension('O')->setWidth(12); // Status
            $sheet->getColumnDimension('P')->setWidth(20); // Reason

            // Freeze panes to keep headers visible
            $sheet->freezePane('A9');

            // Set row height for header
            $sheet->getRowDimension(8)->setRowHeight(25);

            // Set row heights for summary section
            for ($i = 1; $i <= 6; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(22);
            }

            // Add a title above the summary
            $sheet->insertNewRowBefore(1, 1);
            $sheet->setCellValue('A1', 'BACKLINK REPORT SUMMARY');
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => '1F4E79']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'B4C6E7']
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => ['rgb' => '1F4E79']
                    ]
                ]
            ]);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Add spacing between summary and data
            $sheet->insertNewRowBefore(9, 1);
            $sheet->setCellValue('A9', 'BACKLINKS DATA');
            $sheet->mergeCells('A9:' . chr(64 + count($headers)) . '9');
            $sheet->getStyle('A9')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['rgb' => '1F4E79']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D4E6F1']
                ]
            ]);
            $sheet->getRowDimension(9)->setRowHeight(25);
        }

        // Remove any extra sheets
        if ($spreadsheet->getSheetCount() > count($reportIds)) {
            $spreadsheet->removeSheetByIndex(0);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'BacklinkReport_' . now()->format('Ymd_His') . '.xlsx';

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
