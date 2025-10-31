<?php

namespace App\Modules\User\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return array_merge(parent::toArray($request), [
            'domain_count' => $this->clientDomains()->count(),
            'role' => $this->roles->first()?->only(['id', 'name']),
        ]);
    }
}
