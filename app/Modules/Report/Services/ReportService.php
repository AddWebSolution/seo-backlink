<?php

namespace App\Modules\Report\Services;

use Addweb\Base\Services\BaseService;
use App\Modules\Report\Models\Report;

class ReportService extends BaseService
{
    public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
    ];

    public function __construct()
    {
        $this->object = new Report();
    }

    public function getReportWithBacklinks(int $id, int $perPage = 10)
    {
        $report = Report::findOrFail($id);

        $backlinks = $report->backlinks()->paginate($perPage);

        return [
            'report'    => $report,
            'backlinks' => $backlinks,
        ];
    }
}
