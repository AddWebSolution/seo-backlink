<?php

namespace App\Modules\ClientDomain\Services;

use Illuminate\Support\Facades\DB;
use Addweb\Base\Services\BaseService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Modules\ClientDomain\Models\ClientDomain;

class ClientDomainService extends BaseService
{
    public array $searchFields = [
        'title'       => [],
        'source_url'  => [],
        'target_url'  => [],
        'country'     => [],
    ];

    public array $filters = [
        'title'   => ['filter' => 'contain'],
        'status'  => ['filter' => 'exact'],     // 1 = Available, 2 = Unavailable
        'country' => ['filter' => 'contain'],   // partial match
    ];

    public function __construct()
    {
        $this->object = new ClientDomain();
    }

    public function domainImport($file)
    {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $header = array_map('strtolower', $rows[0]);

        unset($rows[0]);

        $importedCount = 0;
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $data = array_combine($header, $row);
                if (empty($data['title']) || empty($data['target_url'])) {
                    continue;
                }
                ClientDomain::create($data);
                $importedCount++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $importedCount;
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Domain Import Template');

        $headersConfig = [
            ['name' => 'title', 'required' => true],
            ['name' => 'target_url', 'required' => true],
            ['name' => 'source_url', 'required' => true],
            ['name' => 'client_id', 'required' => true],
            ['name' => 'domain_authority', 'required' => false],
            ['name' => 'domain_rating', 'required' => false],
            ['name' => 'organic_traffic', 'required' => false],
            ['name' => 'price_ne', 'required' => false],
            ['name' => 'price_gp', 'required' => false],
            ['name' => 'total_price', 'required' => true],
            ['name' => 'turnaround_time', 'required' => false],
            ['name' => 'status', 'required' => true],
            ['name' => 'approval_status', 'required' => false],
            ['name' => 'country', 'required' => false],
            ['name' => 'anchor_text', 'required' => false],
            ['name' => 'special_requirements', 'required' => false],
            ['name' => 'price', 'required' => true],
        ];

        foreach ($headersConfig as $index => $headerConfig) {
            $columnLetter = Coordinate::stringFromColumnIndex($index + 1);
            $cellCoordinate = $columnLetter . '1';
            $headerName = $headerConfig['name'];
            $isRequired = $headerConfig['required'];

            $displayName = $isRequired ? $headerName . ' *' : $headerName;
            $sheet->setCellValue($cellCoordinate, $displayName);

            if ($isRequired) {
                // Required fields - Red theme
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                        'size' => 11
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE53E3E'] // Red background
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFD32F2F']
                        ]
                    ]
                ]);
            } else {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                        'size' => 11
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF2196F3']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF1976D2']
                        ]
                    ]
                ]);
            }

            $sheet->getColumnDimension($columnLetter)->setWidth(15);
        }

        $sheet->getRowDimension(1)->setRowHeight(25);

        $explanationStartRow = 3;

        $sheet->setCellValue('A' . $explanationStartRow, 'Field Descriptions:');
        $sheet->getStyle('A' . $explanationStartRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['argb' => 'FF2C3E50']
            ]
        ]);

        $sheet->mergeCells('A' . $explanationStartRow . ':D' . $explanationStartRow);

        $explanations = [
            'title *' => 'The title or name of the domain/website (Required)',
            'target_url *' => 'The URL where the backlink should point to (Required)',
            'source_url *' => 'The URL of the domain providing the backlink (Required)',
            'client_id *' => 'Unique identifier for the client (Required)',
            'domain_authority' => 'Moz Domain Authority score (0-100, Optional)',
            'domain_rating' => 'Ahrefs Domain Rating score (0-100, Optional)',
            'organic_traffic' => 'Monthly organic traffic estimate (Optional)',
            'price_ne' => 'Price excluding negotiations (Optional)',
            'price_gp' => 'Guest post price (Optional)',
            'total_price *' => 'Final total price for the service (Required)',
            'turnaround_time' => 'Expected completion time in days (Optional)',
            'status *' => 'Current status (e.g., pending, active, completed) (Required)',
            'approval_status' => 'Approval status (e.g., approved, rejected, pending, Optional)',
            'country' => 'Country/region of the domain (Optional)',
            'anchor_text' => 'Specific anchor text to be used (Optional)',
            'special_requirements' => 'Any special instructions or requirements (Optional)',
            'price *' => 'Base price for the service (Required)'
        ];

        $currentRow = $explanationStartRow + 2;

        $sheet->setCellValue('A' . $currentRow, 'Field Name');
        $sheet->setCellValue('B' . $currentRow, 'Description');

        $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF34495E']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF2C3E50']
                ]
            ]
        ]);

        $currentRow++;

        foreach ($explanations as $field => $description) {
            $sheet->setCellValue('A' . $currentRow, $field);
            $sheet->setCellValue('B' . $currentRow, $description);

            $isRequiredField = strpos($field, '*') !== false;
            $rowColor = $isRequiredField ? 'FFFFEAEA' : 'FFF0F8FF';

            $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $rowColor]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFBDC3C7']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true
                ]
            ]);

            if ($isRequiredField) {
                $sheet->getStyle('A' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFE53E3E']]
                ]);
            }

            $currentRow++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(60);

        $noteRow = $currentRow + 1;
        $sheet->setCellValue('A' . $noteRow, 'Note: Fields marked with * are required and must be filled.');
        $sheet->getStyle('A' . $noteRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFE53E3E'],
                'italic' => true
            ]
        ]);
        $sheet->mergeCells('A' . $noteRow . ':B' . $noteRow);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'domain_import_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
