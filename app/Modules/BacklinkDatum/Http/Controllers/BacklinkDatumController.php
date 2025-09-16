<?php

namespace App\Modules\BacklinkDatum\Http\Controllers;
use App\Modules\BacklinkDatum\Services\BacklinkDatumService;
use App\Modules\BacklinkDatum\Http\Resources\BacklinkDatumResource;
use App\Modules\BacklinkDatum\Http\Requests\StoreBacklinkDatumRequest;
use App\Modules\BacklinkDatum\Http\Requests\UpdateBacklinkDatumRequest;

use Addweb\Base\Controller\BaseController;

class BacklinkDatumController extends BaseController
{
    public function __construct(protected BacklinkDatumService $service)
        {
            $this->classes = [
                'request' => [
                    'store' => StoreBacklinkDatumRequest::class,
                    'update' => UpdateBacklinkDatumRequest::class,
                ],
                'resource' => BacklinkDatumResource::class,
            ];
        }
}
