<?php

namespace App\Modules\RivalDomain\Http\Controllers;
use App\Modules\RivalDomain\Services\RivalDomainService;
use App\Modules\RivalDomain\Http\Resources\RivalDomainResource;
use App\Modules\RivalDomain\Http\Requests\StoreRivalDomainRequest;
use App\Modules\RivalDomain\Http\Requests\UpdateRivalDomainRequest;

use Addweb\Base\Controller\BaseController;
use App\Modules\RivalDomain\Models\RivalDomain;
use Illuminate\Http\Request;

class RivalDomainController extends BaseController
{
    public function __construct(protected RivalDomainService $service)
    {
        $this->classes = [
            'request' => [
                'store' => StoreRivalDomainRequest::class,
                'update' => UpdateRivalDomainRequest::class,
            ],
            'resource' => RivalDomainResource::class,
        ];
    }


    public function rivalDomainList()
    {
        $domains = RivalDomain::select('id', 'title', 'target_url')
            ->where('status', '1')
            ->orderBy('title')
            ->get();

        return response()->json([
            'success' => true,
            'rivaldomains' => $domains,
        ]);
    }

    public function rivalDomainImport(Request $request)
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
                'data'    => [
                    'imported' => $result['success'],
                    'failed'  => $result['failed']
                ]
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
}
