<?php

namespace App\Modules\Backlinkreport\Http\Controllers;
use App\Modules\Backlinkreport\Services\BacklinkreportService;
use App\Modules\Backlinkreport\Http\Resources\BacklinkreportResource;
use App\Modules\Backlinkreport\Http\Requests\StoreBacklinkreportRequest;
use App\Modules\Backlinkreport\Http\Requests\UpdateBacklinkreportRequest;

use Addweb\Base\Controller\BaseController;

class BacklinkreportController extends BaseController
{
    public function __construct(protected BacklinkreportService $service)
        {
            $this->classes = [
                'request' => [
                    'store' => StoreBacklinkreportRequest::class,
                    'update' => UpdateBacklinkreportRequest::class,
                ],
                'resource' => BacklinkreportResource::class,
            ];
        }
}
