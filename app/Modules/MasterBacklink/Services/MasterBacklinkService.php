<?php

namespace App\Modules\MasterBacklink\Services;

use Addweb\Base\Services\BaseService;
use App\Modules\MasterBacklink\Models\MasterBacklink;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\{
    Fill, Border, Alignment
};

class MasterBacklinkService extends BaseService
{
    public array $searchFields = [
        'website_name' => [],
        'domain_url' => [],
    ];

    public array $filters = [
        'platform_type' => ['filter' => 'default'],
        'country' => ['filter' => 'default'],
        'categories' => ['filter' => 'json_contains'],
        'dofollow' => ['filter' => 'default'],
        'indexed' => ['filter' => 'default'],
    ];

    public array $relationFilters = ['categories'];

    /**
     * Override to process filter data for JSON contains
     */
    public function processFilterData($key, $value)
    {
        $jsonFilters = [
            'categories',
        ];

        if (in_array($key, $jsonFilters, true)) {
            if (!empty($value)) {
                return [
                    'filter' => 'json_contains',
                    'value' => $value
                ];
            }
        }

        return parent::processFilterData($key, $value);
    }

    public function extraSearchConditions()
    {
        $jsonFilters = [
            'categories',
        ];

        foreach ($jsonFilters as $field) {
            if (!empty($this->filters[$field]['value'])) {
                $value = $this->filters[$field]['value'];

                if (is_string($value)) {
                    $this->query->whereJsonContains($field, $value);
                }

                elseif (is_array($value)) {
                    $this->query->where(function($q) use ($field, $value) {
                        foreach ($value as $category) {
                            $q->orWhereJsonContains($field, $category);
                        }
                    });
                }
            }
        }
    }

    public function __construct()
    {
        $this->object = new MasterBacklink();
    }

    public function processImport($file)
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // SANITIZE HEADERS
        $header = array_map(
            fn($h) => strtolower(str_replace(' ', '_', trim($h))),
            $rows[0]
        );
        unset($rows[0]);

        // EXISTING DB URLs
        $existingUrls = MasterBacklink::pluck('profile_url')
            ->map(fn($u) => strtolower(rtrim($u, "/")))
            ->filter()
            ->toArray();

        $insertData = [];
        $seenUrls = [];
        $duplicateUrls = [];
        $skippedRows = [];

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            $urlRaw = $data['profile_url'] ?? null;
            $url = $this->normalizeUrl($urlRaw);

            // Skip empty profile_url
            if (!$url) {
                $skippedRows[] = $data;
                continue;
            }

            // DB duplicate
            if (in_array($url, $existingUrls)) {
                $duplicateUrls[] = $urlRaw;
                continue;
            }

            // Sheet duplicate
            if (isset($seenUrls[$url])) {
                $duplicateUrls[] = $urlRaw;
                continue;
            }

            $seenUrls[$url] = true;

            $toBool = fn($v) =>
            $v === 'Yes' ? 1 :
                ($v === 'No' ? 0 : null);

            $insertData[] = [
                'website_name' => $data['website_name'] ?? null,
                'domain_url'   => $this->normalizeUrl($data['domain_url'] ?? null),
                'da'           => $data['da'] ?? null,
                'profile_url'  => $url,
                'platform_type'  =>  $data['platform_type'] ?? null,
                'country'  =>  $data['country'] ?? null,
                'categories' => $data['categories']
                    ? json_encode($this->parseCategories($data['categories']))
                    : null,
                'dofollow' => $toBool($data['dofollow'] ?? null),
                'indexed'  => $toBool($data['indexed'] ?? null),
                'last_active'  =>  $data['last_active'] ?? null,
                'avg_traffic'  =>  $data['avg_traffic'] ?? null,
                'domain_age'  =>  $data['domain_age'] ?? null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        // INSERT
        if (!empty($insertData)) {
            MasterBacklink::insert($insertData);
        }

        return response()->json([
            'message'         => 'Import completed',
            'inserted_rows'   => count($insertData),
            'duplicate_rows'  => count($duplicateUrls),
            'duplicate_urls'  => $duplicateUrls,
            'skipped_rows'    => count($skippedRows),
            'success'         => true,
        ]);
    }

    private function normalizeUrl($url)
    {
        if (!$url) return null;

        $url = trim(strtolower($url));
        return rtrim($url, "/");
    }

    private function parseCategories($value)
    {
        if (!$value || trim($value) === '') {
            return null;
        }

        $items = explode(',', $value);

        $items = array_map(fn($i) => trim($i), $items);

        $items = array_filter($items);

        return array_values($items);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Master Backlinks Template');

        // HEADER CONFIG
        $headersConfig = [
            ['name' => 'Website Name', 'required' => true],
            ['name' => 'Domain URL', 'required' => true],
            ['name' => 'DA', 'required' => false],
            ['name' => 'dofollow', 'required' => false],
            ['name' => 'Indexed', 'required' => false],
            ['name' => 'Last Active', 'required' => false],
            ['name' => 'Avg Traffic', 'required' => false],
            ['name' => 'Domain Age', 'required' => false],
            ['name' => 'Profile URL', 'required' => true],
            ['name' => 'Platform Type', 'required' => false],
            ['name' => 'Country', 'required' => false],
            ['name' => 'Categories', 'required' => false],
        ];

        foreach ($headersConfig as $index => $headerConfig) {
            $columnLetter = Coordinate::stringFromColumnIndex($index + 1);
            $cellCoordinate = $columnLetter . '1';
            $headerName = $headerConfig['name'];
            $isRequired = $headerConfig['required'];

            $sheet->setCellValue($cellCoordinate, $headerName);

            $style = [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $isRequired ? 'FFE53E3E' : 'FF2196F3']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => $isRequired ? 'FFD32F2F' : 'FF1976D2']
                    ]
                ]
            ];

            $sheet->getStyle($cellCoordinate)->applyFromArray($style);
            $sheet->getColumnDimension($columnLetter)->setWidth(20);
        }

        $sheet->getRowDimension(1)->setRowHeight(25);

        // SECTION TITLE
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

        // FIELD
        $explanations = [
            'Website Name *' => 'Name of the website/blog (Required)',
            'Domain URL *' => 'Main domain URL (Required)',
            'DA' => 'Domain Authority (Optional)',
            'dofollow' => 'dofollow (Optional)',
            'Indexed' =>'Indexed (Optional)',
            'Last Active' =>'Last Active (Optional)',
            'Avg Traffic' =>'Avg Traffic (Optional)',
            'Domain Age' =>'Domain Age (Optional)',
            'Profile URL *' => 'Exact backlink profile URL (Required)',
            'Platform Type' => 'Platform Type (Optional)',
            'Country' => 'Country (Optional)',
            'Categories' => 'Add Categories like Real Estate,Health etc. (Optional)',
        ];

        $currentRow = $explanationStartRow + 2;

        // TABLE TITLE
        $sheet->setCellValue('A' . $currentRow, 'Field Name');
        $sheet->setCellValue('B' . $currentRow, 'Description');

        $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF34495E']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF2C3E50']]
            ]
        ]);

        $currentRow++;

        // DESCRIPTION ROWS
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
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBDC3C7']]
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true]
            ]);

            if ($isRequiredField) {
                $sheet->getStyle('A' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFE53E3E']]
                ]);
            }

            $currentRow++;
        }

        // COLUMN WIDTHS
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(60);

        // NOTE
        $noteRow = $currentRow + 1;
        $sheet->setCellValue('A' . $noteRow, 'Note: Fields marked with * are required.');
        $sheet->getStyle('A' . $noteRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFE53E3E'],
                'italic' => true
            ]
        ]);
        $sheet->mergeCells('A' . $noteRow . ':B' . $noteRow);

        // DOWNLOAD
        $writer = new Xlsx($spreadsheet);
        $fileName = 'master_backlinks_import_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
