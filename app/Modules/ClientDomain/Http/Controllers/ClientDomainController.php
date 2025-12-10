<?php

namespace App\Modules\ClientDomain\Http\Controllers;

use Addweb\Base\Controller\BaseController;
use Addweb\Base\Helpers\Response;
use App\Modules\BacklinkDatum\Models\BacklinkDatum;
use App\Modules\ClientDomain\Models\ClientDomain;
use App\Modules\ClientDomain\Services\ClientDomainService;
use App\Modules\ClientDomain\Http\Resources\ClientDomainResource;

use App\Modules\ClientDomain\Http\Requests\StoreClientDomainRequest;
use App\Modules\ClientDomain\Http\Requests\UpdateClientDomainRequest;
use App\Modules\MasterBacklink\Models\MasterBacklink;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientDomainController extends BaseController
{
    public function __construct(protected ClientDomainService $service)
    {
        $this->classes = [
            'request' => [
                'store' => StoreClientDomainRequest::class,
                'update' => UpdateClientDomainRequest::class,
            ],
            'resource' => ClientDomainResource::class,
        ];
    }

    public function domainList()
    {
        $domains = ClientDomain::select('id', 'title', 'target_url')
            ->where('status', '1')
            ->orderBy('title')
            ->get();

        return response()->json([
            'success' => true,
            'domains' => $domains,
        ]);
    }

    public function clientDomains($id)
    {
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Client ID is required',
                'data'    => null
            ], 400);
        }

        $searchTerm = request()->get('searchTerm');
        $filters = request()->get('filters', '{}'); 
        $filters = json_decode($filters, true); 
        $perPage = request()->get('perPage', 10);
        $sortField = request()->get('sortField', 'id');
        $sortOrder = request()->get('sortOrder', 'desc');
        

        $result = $this->service->getClientDomains($id, $perPage, $searchTerm,$filters ,$sortField, $sortOrder);

        return response()->json([
            'success' => true,
            'domains' => $result['domains']
        ]);
    }

    public function domainImport(Request $request)
    {
        try {
            $file = $request->file('file');

            if (!$file || !$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload',
                    'data'    => null
                ], 422);
            }

            $result = $this->service->domainImport($file->getPathname());

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'imported' => $result['success'],
                'failed'  => $result['failed']
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Domain import failed. Please check the file and try again.',
                'data'    => null,
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    public function domainImportTemplateDownload(Request $request){

        return $this->service->downloadTemplate();
    }

    public function assignUsersToDomain(Request $request, $domainId)
    {
        $request->validate([
            'user_ids' => 'array',
        ]);

        $domain = ClientDomain::findOrFail($domainId);

        $domain->users()->sync($request->user_ids ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Users assigned successfully.'
        ]);
    }

    public function rivalBacklinksClientwise($domainId)
    {
        $domain = ClientDomain::select('client_id','target_url')->findOrFail($domainId);

        $backlinks = BacklinkDatum::where('domain_id', $domainId)
            ->where('client_id', $domain->client_id)
            ->where('target_url', '!=', $domain->target_url)
            ->get(['url', 'target_domain']);

        $grouped = $backlinks->groupBy('target_domain')->map(function ($items) {
            return $items
                ->pluck('url')
                ->map(function ($url) {
                    return str_replace('www.', '', parse_url($url, PHP_URL_HOST));
                })
                ->filter()
                ->unique()
                ->values();
        });

        return Response::success($grouped, 'Grouped Rival Backlinks Fetched.');
    }

    public function referringDomainsExport($domainId)
    {
        $domain = ClientDomain::select('client_id','target_url')->findOrFail($domainId);

        $backlinks = BacklinkDatum::where('domain_id', $domainId)
            ->where('client_id', $domain->client_id)
            ->where('target_url', '!=', $domain->target_url)
            ->get(['url', 'target_domain']);

        if ($backlinks->isEmpty()) {
            return response()->json(['message' => 'No backlinks found'], 404);
        }

        $grouped = $backlinks->groupBy('target_domain')->map(function ($items) {
            return $items
                ->pluck('url')
                ->map(fn ($url) => str_replace('www.', '', parse_url($url, PHP_URL_HOST)))
                ->filter()
                ->unique()
                ->values();
        });

        $targetDomains = $grouped->keys()->values();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Referring Domains');

        $excelCol = fn($n) => $this->excelColumnName($n);

        $sheet->setCellValue("A1", "#");

        foreach ($targetDomains as $i => $domainName) {
            $col = $excelCol($i + 2);
            $sheet->setCellValue("{$col}1", $domainName);
        }

        $maxRows = 1;
        $rowIndex = 2;

        foreach ($targetDomains as $domainName) {
            $maxRows = max($maxRows, count($grouped[$domainName]) + 1);
        }

        for ($r = 2; $r <= $maxRows; $r++) {
            $sheet->setCellValue("A{$r}", $r - 1);
        }

        foreach ($targetDomains as $i => $domainName) {
            $col = $excelCol($i + 2);
            $r = 2;

            foreach ($grouped[$domainName] as $ref) {
                $sheet->setCellValue("{$col}{$r}", $ref);
                $r++;
            }
        }

        $lastCol = $excelCol(count($targetDomains) + 1);

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2B5797']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getStyle("A1:A{$maxRows}")->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->getStyle("A1:{$lastCol}{$maxRows}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // EXPORT
        $writer = new Xlsx($spreadsheet);
        $fileName = 'ReferringDomains_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function excelColumnName($index)
    {
        $result = '';

        while ($index > 0) {
            $index--;
            $result = chr(65 + ($index % 26)) . $result;
            $index = intval($index / 26);
        }

        return $result;
    }

    public function recommendedBacklinks($id)
    {
        $domain = ClientDomain::findOrFail($id);

        $categories = $domain->categories ?? [];
        $platformType = strtolower($domain->platform_type ?? '');
        $country = $domain->country;
        $da = (int) $domain->domain_authority;

        $query = MasterBacklink::query();

        $query->where(function ($q) use ($categories, $platformType, $country, $da) {

            // 1. Category
            if (!empty($categories)) {
                foreach ($categories as $cat) {
                    $cat = strtolower(trim($cat));

                    $q->orWhereRaw("JSON_SEARCH(LOWER(categories), 'one', ?) IS NOT NULL", [$cat])
                        ->orWhereRaw("LOWER(categories) LIKE ?", ["%{$cat}%"]);
                }
            }

            // 2. Platform type
            if ($platformType) {
                $q->orWhereRaw("LOWER(platform_type) LIKE ?", ["%{$platformType}%"]);
            }

            // 3. Country
            if ($country) {
                $q->orWhere('country', $country);
            }

            // 4. Domain Authority
            if ($da > 0) {
                $q->orWhereBetween('da', [$da - 10, $da + 10]);
            }

        });

        $results = $query->get()->map(function($mb) use ($domain) {
            $matches = [
                'platform_type' => stripos($mb->platform_type, $domain->platform_type) !== false ? 1 : 0,
                'country' => $mb->country === $domain->country ? 1 : 0,
                'da' => abs($mb->da - $domain->domain_authority) <= 10 ? 1 : 0,
                'categories' => 0,
            ];

            $clientCats = collect($domain->categories ?? [])->map(fn($c) => strtolower($c));
            $masterCats = collect($mb->categories ?? [])->map(fn($c) => strtolower($c));

            foreach ($clientCats as $cc) {
                foreach ($masterCats as $mc) {
                    if (stripos($mc, $cc) !== false) {
                        $matches['categories']++;
                    }
                }
            }

            $mb->match_summary = $matches;
            return $mb;
        });


        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }


}
