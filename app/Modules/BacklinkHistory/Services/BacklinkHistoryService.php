<?php

namespace App\Modules\BacklinkHistory\Services;

use Addweb\Base\Services\BaseService;
use App\Modules\BacklinkHistory\Events\AfterBacklinkHistoryStoreEvent;
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
        $clientDomain = preg_replace('/\s+/', '', strtolower($client->title));
        $clientHistory = BacklinkHistory::where('client_domain_id', $clientDomainId)
            ->where('target', 'like', "%{$clientDomain}%")
            ->orderBy('history_date', 'asc')
            ->get()
            ->keyBy('history_date');

        $rivalIds = BacklinkHistory::where('client_domain_id', $clientDomainId)
            ->pluck('rival_domain_id')
            ->unique();

        $rivalHistory = BacklinkHistory::whereIn('rival_domain_id', $rivalIds)
            ->where('client_domain_id', $clientDomainId)
            ->where('target', 'not like', "%{$clientDomain}%")
            ->orderBy('history_date', 'asc')
            ->get();

        return [
            'client_history' => $clientHistory,
            'client_id' => $client->client_id,
            'rival_history' => $rivalHistory,
        ];
    }
    public function updateOrCreateHistory(array $data): BacklinkHistory
    {

        $backlinkHistory = BacklinkHistory::updateOrCreate(
            [
                'client_domain_id' => $data['client_domain_id'],
                'rival_domain_id' => $data['rival_domain_id'],
                'history_date' => $data['history_date'],
                'target' => $data['target'],
            ],
            $data
        );

       event(new AfterBacklinkHistoryStoreEvent($backlinkHistory));

        return $backlinkHistory;
    }
}
