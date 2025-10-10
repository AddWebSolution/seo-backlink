<?php

namespace App\Modules\Dashboard\Http\Controllers;
use App\Modules\Dashboard\Services\DashboardService;
use App\Modules\Dashboard\Http\Resources\DashboardResource;
use App\Modules\Dashboard\Http\Requests\StoreDashboardRequest;
use App\Modules\Dashboard\Http\Requests\UpdateDashboardRequest;

use Addweb\Base\Controller\BaseController;

class DashboardController extends BaseController
{
    public function __construct(protected DashboardService $service)
    {
        $this->classes = [
            'request' => [
                'store' => StoreDashboardRequest::class,
                'update' => UpdateDashboardRequest::class,
            ],
            'resource' => DashboardResource::class,
        ];
    }

    public function dashboardStats()
    {
       return  $this->service->dashboardStats();
    }

    public function monthlyStats()
    {
       return  $this->service->monthlystats();
    }
}
