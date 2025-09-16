<?php

namespace App\Modules\KeywordDatum\Services;

use Addweb\Base\Services\BaseService;
use App\Modules\KeywordDatum\Models\KeywordDatum;

class KeywordDatumService extends BaseService
{
    public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
    ];

    public function __construct()
    {
        $this->object = new KeywordDatum();
    }
}
