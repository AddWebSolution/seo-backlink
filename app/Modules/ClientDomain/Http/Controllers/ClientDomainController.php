<?php

namespace App\Modules\ClientDomain\Http\Controllers;

use Addweb\Base\Controller\BaseController;
use App\Modules\ClientDomain\Models\ClientDomain;
use App\Modules\ClientDomain\Services\ClientDomainService;
use App\Modules\ClientDomain\Http\Resources\ClientDomainResource;

use App\Modules\ClientDomain\Http\Requests\StoreClientDomainRequest;
use App\Modules\ClientDomain\Http\Requests\UpdateClientDomainRequest;

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
}
