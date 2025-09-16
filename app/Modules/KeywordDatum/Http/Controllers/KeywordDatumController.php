<?php

namespace App\Modules\KeywordDatum\Http\Controllers;
use App\Modules\KeywordDatum\Services\KeywordDatumService;
use App\Modules\KeywordDatum\Http\Resources\KeywordDatumResource;
use App\Modules\KeywordDatum\Http\Requests\StoreKeywordDatumRequest;
use App\Modules\KeywordDatum\Http\Requests\UpdateKeywordDatumRequest;

use Addweb\Base\Controller\BaseController;

class KeywordDatumController extends BaseController
{
    public function __construct(protected KeywordDatumService $service)
        {
            $this->classes = [
                'request' => [
                    'store' => StoreKeywordDatumRequest::class,
                    'update' => UpdateKeywordDatumRequest::class,
                ],
                'resource' => KeywordDatumResource::class,
            ];
        }
}
