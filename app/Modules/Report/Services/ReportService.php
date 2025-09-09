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

        // Count accepted and rejected backlinks (ignoring pagination)
        $acceptedCount = $report->backlinks()->where('status', 'accepted')->count();
        $rejectedCount = $report->backlinks()->where('status', 'rejected')->count();
        $domains = $report->backlinks()
            ->whereNotNull('domain')
            ->distinct()
            ->pluck('target_url','domain')
            ->toArray();

        return [
            'report'    => $report,
            'backlinks' => $backlinks,
            'accepted_count'    => $acceptedCount,
            'rejected_count'    => $rejectedCount,
            'domains'           => $domains
        ];
    }
}
