<?php

namespace App\Modules\ClientDomain\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientDomainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'rival_domain_count' => $this->rivalDomains()->count(),
            'client_name' => $this->client->name ?? null,
        ]);
    }
}
