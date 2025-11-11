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
        if (!$this->resource) {
            return [];
        }

        $array = parent::toArray($request);

        unset($array['roles']);

        $domainCount = 0;

        if (method_exists($this->resource, 'clientDomains')) {
            $domainCount = $this->resource->clientDomains()->count();
        }

        $array['domain_count'] = $domainCount;
        $array['role'] = $this->resource->roles->first()?->only(['id', 'name']);

        return $array;
    }
}
