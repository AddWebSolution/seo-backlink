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

        $query = $report->keywords()->with('domain','keyword');
        $report->success = $report->getSuccessCount();
        $report->fail = $report->getFailCount();
        $report->total_keywords = $report->getKeywordsCount();
        $report->domain_count = $report->getDomainsCount();

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('llm_type', 'like', "%{$search}%")
                    ->orWhere('llm_response', 'like', "%{$search}%")
                    ->orWhereHas('domain', function ($q2) use ($search) {
                        $q2->where('title', 'like', "%{$search}%")
                            ->orWhere('target_url', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['domain']) && $filters['domain'] !== null) {
            $query->where('client_domain_id', $filters['domain']);
        }

        $keywords = $query->paginate($perPage);

        $domains = $report->getDomains();

        return [
            'report'    => $report,
            'keywords' => $keywords,
            'domains'   => $domains
        ];
    }

}
