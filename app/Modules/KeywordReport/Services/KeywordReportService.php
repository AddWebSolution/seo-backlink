<?php

namespace App\Modules\KeywordReport\Services;

use Addweb\Base\Services\BaseService;
use App\Modules\KeywordReport\Models\KeywordReport;

class KeywordReportService extends BaseService
{
    public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
    ];

    public function __construct()
    {
        $this->object = new KeywordReport();
    }
}
