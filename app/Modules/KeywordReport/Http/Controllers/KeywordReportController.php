<?php

namespace App\Modules\KeywordReport\Http\Controllers;

use Illuminate\Http\Request;
use Addweb\Base\Controller\BaseController;
use App\Modules\KeywordReport\Services\KeywordReportService;
use App\Modules\KeywordReport\Http\Resources\KeywordReportResource;

use App\Modules\KeywordReport\Http\Requests\StoreKeywordReportRequest;
use App\Modules\KeywordReport\Http\Requests\UpdateKeywordReportRequest;

class KeywordReportController extends BaseController
{
    public function __construct(protected KeywordReportService $service)
    {
        $this->classes = [
            'request' => [
                'store' => StoreKeywordReportRequest::class,
                'update' => UpdateKeywordReportRequest::class,
            ],
            'resource' => KeywordReportResource::class,
        ];
    }

    public function keywordsShow(Request $request, $id)
    {
        $perPage = $request->get('per_page', 10);

        $filters = [
            'search'          => $request->get('search'),
            'domain'          => $request->get('domain'),
            'status'          => $request->get('status'),
        ];

        $result = $this->service->getReportWithKeywords($id, $perPage, $filters);

        return response()->json([
            'success'   => true,
            'report'    => $result['report'],
            'keywords' => $result['keywords'],
            'domains'       => $result['domains']
        ]);
    }
}
