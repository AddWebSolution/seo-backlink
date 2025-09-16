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

    public function getReportWithKeywords(int $id, int $perPage = 10, array $filters = [])
    {
        $report = KeywordReport::findOrFail($id);

        $query = $report->keywords();
        $report->success = $report->getSuccessCount();
        $report->fail = $report->getFailCount();
        $report->domain_count = $report->getDomainsCount();

        // Search
        // if (!empty($filters['search'])) {
        //     $search = $filters['search'];
        //     $query->where(function ($q) use ($search) {
        //         $q->where('url', 'like', "%{$search}%")
        //             ->orWhere('from_url', 'like', "%{$search}%")
        //             ->orWhere('domain', 'like', "%{$search}%")
        //             ->orWhere('target_url', 'like', "%{$search}%")
        //             ->orWhere('anchor', 'like', "%{$search}%")
        //             ->orWhere('page_title', 'like', "%{$search}%");
        //     });
        // }

        // if (!empty($filters['status'])) {
        //     $query->where('status', $filters['status']);
        // }

        // if (!empty($filters['domain'])) {
        //     $query->where(function ($q) use ($filters) {
        //         $q->orwhere('target_url', $filters['domain'])
        //             ->orWhere('domain', $filters['domain']);
        //     });
        // }

        $keywords = $query->paginate($perPage);

        $domains = $report->getDomains();

        return [
            'report'    => $report,
            'keywords' => $keywords,
            'domains'   => $domains
        ];
    }

}
