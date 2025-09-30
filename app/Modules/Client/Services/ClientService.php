<?php

namespace App\Modules\Client\Services;

use Addweb\Base\Services\BaseService;
use Carbon\Carbon;
use App\Modules\Client\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Modules\Keyword\Models\Keyword;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Modules\ClientDomain\Models\ClientDomain;


class ClientService extends BaseService
{
    public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
    ];

    public function __construct()
    {
        $this->object = new Client();
    }
     public function clientImportTemplateDownload()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Client Import Template');

        $headersConfig = [
            ['name' => 'name', 'required' => true],
            ['name' => 'email', 'required' => true],
            ['name' => 'phone', 'required' => true],
            ['name' => 'industry', 'required' => false],
            ['name' => 'company_name', 'required' => true],
            ['name' => 'website', 'required' => false],
            ['name' => 'address', 'required' => false],
            ['name' => 'city', 'required' => false],
            ['name' => 'state', 'required' => false],
            ['name' => 'country', 'required' => false],
            ['name' => 'zip_code', 'required' => false],
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
            'name *' => 'Name of the Client (Required)',
            'email *' => 'Email of the Client (Required)',
            'phone *' => 'Phone of the Client (Required)',
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
        $fileName = 'client_import_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function clientImport($file)
    {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $header = array_map('strtolower', $rows[0]);
        unset($rows[0]);

        $imported = [];
        $failed = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $data = array_combine($header, $row);


                // dd($data);
                if (empty($data['name']) || empty($data['email']) || empty($data['phone'])) {
                    continue;
                }

                $exists = Client::where('name', $data['name'])->exists();

                if ($exists) {
                    $failed[] = [
                        'reason' => "Client already exists",
                        'name'    => $data['name'] ?? null,
                    ];
                    continue;
                }

                Client::create($data);
                $imported[] = [
                    'reason' => "Client imported",
                   'name' => $data['name'] ?? null,
                ];
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'success' => $imported,
            'failed'  => $failed,
            'message' => sprintf(
                "Import completed: %d succeeded, %d failed",
                count($imported),
                count($failed)
            ),
        ];
    }

}
