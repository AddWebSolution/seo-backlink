<?php

namespace App\Modules\ClientDomain\Http\Controllers;

use App\Modules\ClientDomain\Services\ClientDomainService;
use App\Modules\ClientDomain\Http\Resources\ClientDomainResource;
use App\Modules\ClientDomain\Http\Requests\StoreClientDomainRequest;
use App\Modules\ClientDomain\Http\Requests\UpdateClientDomainRequest;

use Addweb\Base\Controller\BaseController;

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
}
