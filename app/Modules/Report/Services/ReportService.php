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

    public function getReportWithBacklinks(int $id, int $perPage = 10, array $filters = [])
    {
        $report = Report::findOrFail($id);

        $query = $report->backlinks();
        $report->accepted_backlinks = $report->getAcceptedCount();
        $report->rejected_backlinks = $report->getRejectedCount();
        $report->domain_count = $report->getDomainsCount();

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                    ->orWhere('from_url', 'like', "%{$search}%")
                    ->orWhere('domain', 'like',"%{$search}%")
                    ->orWhere('target_url','like',"%{$search}%")
                    ->orWhere('anchor', 'like', "%{$search}%")
                    ->orWhere('page_title', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['domain'])) {
            $query->where(function ($q) use ($filters) {
                $q->orwhere('target_url', $filters['domain'])
                    ->orWhere('domain', $filters['domain']);
            });
        }

        $backlinks = $query->paginate($perPage);

        $domains = $report->getDomains();

        return [
            'report'    => $report,
            'backlinks' => $backlinks,
            'domains'   => $domains
        ];
    }
}
