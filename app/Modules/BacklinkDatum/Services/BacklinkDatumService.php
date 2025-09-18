<?php

namespace App\Modules\BacklinkDatum\Services;

use Addweb\Base\Services\BaseService;
use App\Modules\BacklinkDatum\Models\BacklinkDatum;

class BacklinkDatumService extends BaseService
{
   public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
    ];

    public function __construct()
    {
        $this->object = new BacklinkDatum();
    }

    /**
     * Get all backlinks by report id (with report data)
     */
    public function getByReportId(int $reportId)
    {
        return $this->object
            ->with('report') // eager load parent report
            ->where('report_id', $reportId)
            ->get();
    }
}
