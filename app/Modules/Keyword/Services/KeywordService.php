<?php

namespace App\Modules\Keyword\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Addweb\Base\Services\BaseService;
use App\Modules\Keyword\Models\Keyword;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Modules\ClientDomain\Models\ClientDomain;


class KeywordService extends BaseService
{

    public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
        'status' => ['filter' => 'contain']
    ];

    public function __construct()
    {
        $this->object = new Keyword();
    }

    public function getKeywordHistory($id)
    {
        try {
            $keyword = Keyword::with('keywordHistory.domain')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'keyword' => $keyword,
                    'history' => $keyword->keywordHistory 
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.'
            ], 500);
        }
    }



    public function keywordImportTemplateDownload()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Keyword Import Template');

        $headersConfig = [
            ['name' => 'client_domain', 'required' => true],
            ['name' => 'keyword', 'required' => true],
            ['name' => 'status', 'required' => true],
        ];

        foreach ($headersConfig as $index => $headerConfig) {
            $columnLetter = Coordinate::stringFromColumnIndex($index + 1);
            $cellCoordinate = $columnLetter . '1';
            $headerName = $headerConfig['name'];
            $isRequired = $headerConfig['required'];

            $displayName = $isRequired ? $headerName : $headerName;
            $sheet->setCellValue($cellCoordinate, $displayName);

            $style = [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11
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
            ];

            if ($isRequired) {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE53E3E'] // Red background
                ];
            } else {
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2196F3'] // Blue background
                ];
            }

            $sheet->getStyle($cellCoordinate)->applyFromArray($style);

            $sheet->getColumnDimension($columnLetter)->setWidth(25);
        }

        $sheet->getRowDimension(1)->setRowHeight(25);

        // Explanation Section
        $sheet->setCellValue('A3', 'Field Descriptions:');
        $sheet->mergeCells('A3:C3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['argb' => 'FF2C3E50']
            ]
        ]);

        $explanations = [
            'client_domain_id *' => 'The ID of the domain the keyword belongs to (Required)',
            'keyword *' => 'The SEO keyword to target (Required)',
            'status *' => '1 = Active, 2 = Deactivated (Required)',
        ];

        $currentRow = 5;
        $sheet->setCellValue('A' . $currentRow, 'Field Name');
        $sheet->setCellValue('B' . $currentRow, 'Description');

        $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF34495E']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF2C3E50']]],
        ]);

        $currentRow++;

        foreach ($explanations as $field => $description) {
            $sheet->setCellValue('A' . $currentRow, $field);
            $sheet->setCellValue('B' . $currentRow, $description);

            $rowColor = strpos($field, '*') !== false ? 'FFFFEAEA' : 'FFF0F8FF';

            $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBDC3C7']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
            ]);

            if (strpos($field, '*') !== false) {
                $sheet->getStyle('A' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFE53E3E']],
                ]);
            }

            $currentRow++;
        }

        $noteRow = $currentRow + 1;
        $sheet->setCellValue('A' . $noteRow, 'Note: Fields marked with * are required.');
        $sheet->mergeCells('A' . $noteRow . ':C' . $noteRow);
        $sheet->getStyle('A' . $noteRow)->applyFromArray([
            'font' => ['bold' => true, 'italic' => true, 'color' => ['argb' => 'FFE53E3E']],
        ]);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'keyword_import_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function keywordImport($file)
    {
        try {
            $spreadsheet = IOFactory::load($file);
            $importedCount = 0;

            DB::beginTransaction();

            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $rows = $sheet->toArray();
                $header = array_map('strtolower', $rows[0] ?? []);
                unset($rows[0]);

                foreach ($rows as $row) {
                    $data = array_combine($header, $row);

                    if (empty($data['client_domain']) || empty($data['keyword']) || empty($data['status'])) {
                        continue;
                    }

                    $domain = ClientDomain::where('title', $data['client_domain'])->first();

                    if (!$domain && !empty($data['client_domain'])) {
                        $domain = ClientDomain::where('target_url', $data['client_domain'])->first();
                    }

                    if ($domain) {
                        $keywordData = [
                            'client_domain_id' => $domain->id,
                            'keyword'          => $data['keyword'],
                            'status'           => (int) $data['status'],
                            'processed_at'     => Carbon::now(),
                        ];

                        Keyword::create($keywordData);
                        $importedCount++;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $importedCount;
    }
}
