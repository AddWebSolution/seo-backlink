<?php

namespace App\Modules\KeywordReport\Http\Controllers;
use App\Modules\KeywordReport\Services\KeywordReportService;
use App\Modules\KeywordReport\Http\Resources\KeywordReportResource;
use App\Modules\KeywordReport\Http\Requests\StoreKeywordReportRequest;
use App\Modules\KeywordReport\Http\Requests\UpdateKeywordReportRequest;

use Addweb\Base\Controller\BaseController;

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
}
