<?php

namespace App\Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Addweb\Base\Controller\BaseController;
use App\Modules\Report\Services\ReportService;

use App\Modules\Report\Http\Resources\ReportResource;
use App\Modules\Report\Http\Requests\StoreReportRequest;
use App\Modules\Report\Http\Requests\UpdateReportRequest;

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

    public function backlinkshow(Request $request, $id)
    {
        $perPage = $request->get('per_page', 10);

        $filters = [
            'search'          => $request->get('search'),
            'domain'          => $request->get('domain'),
            'status'          => $request->get('status'),
        ];

        $result = $this->service->getReportWithBacklinks($id, $perPage, $filters);

        return response()->json([
            'success'   => true,
            'report'    => $result['report'],
            'backlinks' => $result['backlinks'],
            'accepted_count' => $result['accepted_count'],
            'rejected_count' => $result['rejected_count'],
            'domains'       => $result['domains']
        ]);
    }
}
