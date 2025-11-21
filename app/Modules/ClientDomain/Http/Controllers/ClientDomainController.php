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
use Illuminate\Http\Request;

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
}
