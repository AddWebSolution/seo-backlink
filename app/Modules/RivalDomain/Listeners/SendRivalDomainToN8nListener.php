<?php

namespace App\Modules\RivalDomain\Listeners;

use Illuminate\Support\Facades\Http;
use App\Modules\RivalDomain\Events\AfterRivalDomainStoreEvent;
use App\Modules\User\Models\User;

class SendRivalDomainToN8nListener
{
    public function handle(AfterRivalDomainStoreEvent $event)
    {

        $rivalDomain = $event->rivalDomain->load('clientDomain');

        $clientDomain = $rivalDomain->clientDomain;
        $client = User::find($clientDomain->client_id);

        if (!$client) {
            return;
        }

        $newRivalDomainPayload = [
            'id' => $rivalDomain->id,
            'client_domain_id' => $rivalDomain->client_domain_id,
            'title' => trim($rivalDomain->title),
            'target_url' => $rivalDomain->target_url,
        ];

        $payload = [
            'id' => $client->id,
            'name' => $client->name,
            'client_domains' => [
                [
                    'id' => $clientDomain->id,
                    'client_id' => $clientDomain->client_id,
                    'title' => trim($clientDomain->title),
                    'target_url' => $clientDomain->target_url,
                    'rival_domains' => [$newRivalDomainPayload],
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
