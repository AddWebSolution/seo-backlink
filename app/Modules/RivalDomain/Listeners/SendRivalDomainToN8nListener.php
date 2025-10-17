<?php

namespace App\Modules\RivalDomain\Listeners;

use Illuminate\Support\Facades\Http;
use App\Modules\RivalDomain\Events\AfterRivalDomainStoreEvent;
use App\Modules\User\Models\User;

class SendRivalDomainToN8nListener
{
    public function handle(AfterRivalDomainStoreEvent $event)
    {
        $rivalDomain = $event->rivalDomain->load([
            'clientDomain.client',
            'clientDomain.rivalDomains' => function ($q) {
                $q->where('status', 1)
                    ->select('id', 'client_domain_id', 'title', 'target_url');
            }
        ]);

        $clientDomain = $rivalDomain->clientDomain;
        $client = User::find($clientDomain->client_id);

        if (!$client) {
            return;
        }

        $rivalDomains = $clientDomain->rivalDomains->map(function ($rd) {
            return [
                'id' => $rd->id,
                'client_domain_id' => $rd->client_domain_id,
                'title' => trim($rd->title),
                'target_url' => $rd->target_url,
            ];
        })->toArray();

        $payload = [
            'id' => $client->id,
            'name' => $client->name,
            'client_domains' => [
                [
                    'id' => $clientDomain->id,
                    'client_id' => $clientDomain->client_id,
                    'title' => trim($clientDomain->title),
                    'target_url' => $clientDomain->target_url,
                    'rival_domains' => $rivalDomains,
                ]
            ]
        ];

        Http::post(
            env("Six_MONTH_BACKLINK_WEBHOOK"), // six month data flow
            $payload
        );

        Http::post(
            env('CURRENT_MONTH_BACKLINK_WEBHOOK'), // current month data flow
            $payload
        );
    }
}
