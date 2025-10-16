<?php

namespace App\Modules\ClientDomain\Listeners;

use App\Modules\ClientDomain\Events\AfterClientDomainStoreEvent;
use Illuminate\Support\Facades\Http;

class SendClientDomainToN8nListener
{
    public function handle(AfterClientDomainStoreEvent $event)
    {
        $domain = $event->clientDomain->load(['client', 'rivalDomains' => function ($q) {
            $q->where('status', 1)->select('id', 'client_domain_id', 'title', 'target_url');
        }]);

        $payload = [
            'client' => [
                'id' => $domain->client_id,
                'name' => optional($domain->client)->name,
                'clientDomains' => [
                    [
                        'id' => $domain->id,
                        'title' => $domain->title,
                        'target_url' => $domain->target_url,
                        'rivalDomains' => $domain->rivalDomains->toArray(),
                    ]
                ]
            ]
        ];

        Http::post('http://localhost:5678/webhook-test/bc2adfa4-1598-4b5b-ae7b-7e3f88456510', $payload);
    }
}
