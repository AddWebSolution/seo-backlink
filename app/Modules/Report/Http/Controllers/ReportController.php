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
            'rival_domain' => $request->get('rival_domain'),
            'status'          => $request->get('status'),
            'sort_by'        => $request->get('sort_by', 'id'),
            'sort_order'     => $request->get('sort_order', 'desc'),
        ];

        $result = $this->service->getReportWithBacklinks($id, $perPage, $filters);

        return response()->json([
            'success'   => true,
            'report'    => $result['report'],
            'backlinks' => $result['backlinks'],
            'domains'       => $result['domains'],
            'rival_domains' => $result['rival_domains']
        ]);
    }


       public function domainbacklinkshow(Request $request, $id)
    {
        $perPage = $request->get('per_page', 10);

        $filters = [
            'search'          => $request->get('search'),
            'domain'          => $request->get('domain'),
            'rival_domain' => $request->get('rival_domain'),
            'status'          => $request->get('status'),
            'sort_by'        => $request->get('sort_by', 'id'),
            'sort_order'     => $request->get('sort_order', 'desc'),
        ];

        $result = $this->service->getReportDomainBacklinks($id, $perPage, $filters);

        return response()->json([
            'success'   => true,
            'report'    => $result['report'],
            'backlinks' => $result['backlinks'],
            'domains'       => $result['domains'],
            'rival_domains' => $result['rival_domains']
        ]);
    }

    public function reportExport(Request $request)
    {
        $reportIds = $request->input('report_ids');
        return $this->service->exportReport($reportIds);
    }
}
