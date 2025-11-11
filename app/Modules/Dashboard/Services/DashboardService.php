<?php

namespace App\Modules\Dashboard\Services;

use Addweb\Base\Services\BaseService;
use App\Enums\UserRole;
use App\Modules\BacklinkDatum\Models\BacklinkDatum;
use App\Modules\ClientDomain\Models\ClientDomain;
use App\Modules\Dashboard\Models\Dashboard;
use App\Modules\Keyword\Models\Keyword;
use App\Modules\KeywordReport\Models\KeywordReport;
use App\Modules\Report\Models\Report;
use App\Modules\RivalDomain\Models\RivalDomain;
use App\Modules\User\Models\User;

class DashboardService extends BaseService
{
    protected $user;
    protected $isAdmin;
    protected $userId;
    public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
    ];

    public function __construct()
    {
        $this->object = new Dashboard();

        $this->user = auth()->user();
        $this->isAdmin = $this->user && $this->user->role === UserRole::SUPERADMIN->value;
        $this->userId = $this->user?->id;
    }


    private function getMonthlyCounts($model, $userId = null, $filterColumn = null)
    {
        $query = $model::query();

        if (!is_null($userId) && $filterColumn) {
            $query->where($filterColumn, $userId);
        }

        return $query
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();
    }

    private function getRivalDomainMonthlyCounts()
    {
        if ($this->isAdmin) {
            $query = RivalDomain::query();
        } else {
            $query = $this->user->rivalDomains(); 
        }

        return $query
            ->selectRaw('MONTH(rival_domains.created_at) as month, COUNT(*) as total')
            ->whereYear('rival_domains.created_at', date('Y'))
            ->groupByRaw('MONTH(rival_domains.created_at)')
            ->pluck('total', 'month')
            ->toArray();
    }

    public function monthlystats()
    {   
        $stats = [];

        $stats = [
            'domains' => $this->getMonthlyCounts(ClientDomain::class, $this->isAdmin ? null : $this->userId, 'client_id'),
            'rivalDomains' => $this->getRivalDomainMonthlyCounts(),
            'backlinks' => $this->getMonthlyCounts(BacklinkDatum::class, $this->isAdmin ? null : $this->userId, 'client_id'),
            'keywords' => $this->getMonthlyCounts(Keyword::class),
            'reports' => $this->getMonthlyCounts(Report::class, $this->isAdmin ? null : $this->userId, 'client_id'),
            'keywordReports' => $this->getMonthlyCounts(KeywordReport::class)
        ];

        foreach ($stats as &$data) {
            $data = collect(range(1, 12))
                ->mapWithKeys(fn($m) => [$m => $data[$m] ?? 0])
                ->toArray();
        }

        return $stats;
    }

    public function dashboardStats()
    {  
        return [
            'total_users' => User::count(),
            'total_clients' => User::where('role', 3)->count(),
            'total_admins' => User::where('role', 1)->count(),
            'Domains' => [
                'total' => $this->isAdmin ? ClientDomain::count() : ClientDomain::where('client_id', $this->userId)->count(),
                'active' => $this->isAdmin ? ClientDomain::where('status', 1)->count() : ClientDomain::where('client_id', $this->userId)->where('status', 1)->count(),
                'inactive' => $this->isAdmin ? ClientDomain::where('status', 2)->count() : ClientDomain::where('client_id', $this->userId)->where('status', 2)->count(),
            ],
            'rivalDomains' => [
                'total' => $this->isAdmin
                    ? RivalDomain::count()
                    : $this->user->rivalDomains()->count(),
                'active' => $this->isAdmin
                    ? RivalDomain::where('rival_domains.status', 1)->count()
                    : $this->user->rivalDomains()->where('rival_domains.status', 1)->count(),
                'inactive' => $this->isAdmin
                    ? RivalDomain::where('rival_domains.status', 2)->count()
                    : $this->user->rivalDomains()->where('rival_domains.status', 2)->count(),
            ],
            'backlink' => [
                'total' => $this->isAdmin ? Report::sum('total_backlink') : Report::where('client_id', $this->userId)->sum('total_backlink'),
                'accepted_backlink' => $this->isAdmin ? Report::sum('accepted_backlinks') : Report::where('client_id', $this->userId)->sum('accepted_backlinks'),
                'rejected_backlink' => $this->isAdmin ? Report::sum('rejected_backlinks') : Report::where('client_id', $this->userId)->sum('rejected_backlinks'),
                'total_backlink_report' => $this->isAdmin ? Report::count() : Report::where('client_id', $this->userId)->count(),
            ],
            'keyword' => [
                'total' => Keyword::sum('keyword'),
                'accepted_keyword' => KeywordReport::select('success')->count(),
                'rejected_keyword' => KeywordReport::select('failed')->count(),
                'total_keyword_report' => KeywordReport::count(),
            ]
        ];
    }

    public function backlinkStats()
    {
        return [
            'total_users' => User::count(),
            'total_clients' => User::where('role', 3)->count(),
            'total_admins' => User::where('role', 1)->count(),
            'total_Domains' => ClientDomain::count(),
            'backlink' => [
                'total' => Report::sum('backlink'),
                'today' => Report::whereDate('created_at', now()->format('Y-m-d'))->sum('backlink'),
                'yesterday' => Report::whereDate('created_at', now()->subDay()->format('Y-m-d'))->sum('backlink'),
                'last_7_days' => Report::whereDate('created_at', '>=', now()->subDays(7)->format('Y-m-d'))->sum('backlink'),
                'last_30_days' => Report::whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'))->sum('backlink'),
                'last_90_days' => Report::whereDate('created_at', '>=', now()->subDays(90)->format('Y-m-d'))->sum('backlink'),
                'data' => [
                    ''
                ]
            ],
            // 'completed_tasks' => \App\Modules\Task\Models\Task::where('status', 'completed')->count(),
            // 'pending_tasks' => \App\Modules\Task\Models\Task::where('status', 'pending')->count(),
        ];
    }
}
