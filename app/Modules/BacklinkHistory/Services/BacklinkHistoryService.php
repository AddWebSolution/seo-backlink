<?php

namespace App\Modules\BacklinkHistory\Services;

use Addweb\Base\Services\BaseService;
use App\Modules\BacklinkHistory\Models\BacklinkHistory;
use App\Modules\ClientDomain\Models\ClientDomain;

class BacklinkHistoryService extends BaseService
{
    public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
    ];

    public function __construct()
    {
        $this->object = new BacklinkHistory();
    }

    public function fetchHistoryData($clientDomainId)
    {
        $client = ClientDomain::find($clientDomainId);
        if (!$client) {
            return null;
        }
        $clientHistory = BacklinkHistory::where('client_domain_id', $clientDomainId)
            ->where('target', 'like', '%' . $client->title . '%')
            ->orderBy('history_date', 'asc')
            ->get()
            ->keyBy('history_date');

        $rivalIds = BacklinkHistory::where('client_domain_id', $clientDomainId)
            ->pluck('rival_domain_id')
            ->unique();

        $rivalHistory = BacklinkHistory::whereIn('rival_domain_id', $rivalIds)
            ->where('client_domain_id', $clientDomainId)
            ->where('target', 'not like', '%' . $client->title . '%')
            ->orderBy('history_date', 'asc')
            ->get();

        return [
            'client_history' => $clientHistory,
            'rival_history' => $rivalHistory,
        ];
    }
}
