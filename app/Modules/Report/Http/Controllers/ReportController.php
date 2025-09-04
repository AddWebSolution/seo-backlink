<?php

namespace App\Modules\Report\Http\Controllers;

use App\Modules\Report\Services\ReportService;
use App\Modules\Report\Http\Resources\ReportResource;
use App\Modules\Report\Http\Requests\StoreReportRequest;
use App\Modules\Report\Http\Requests\UpdateReportRequest;

use Addweb\Base\Controller\BaseController;

class ReportController extends BaseController
{
    public function __construct(protected ReportService $service)
    {
        $this->classes = [
            'request' => [
                'store' => StoreReportRequest::class,
                'update' => UpdateReportRequest::class,
            ],
            'resource' => ReportResource::class,
        ];
    }

    public function backlinkshow($id)
    {
        $perPage = request()->get('per_page', 10);

        $result = $this->service->getReportWithBacklinks($id, $perPage);

        return response()->json([
            'success'   => true,
            'report'    => $result['report'],
            'backlinks' => $result['backlinks'],
        ]);
    }
}
