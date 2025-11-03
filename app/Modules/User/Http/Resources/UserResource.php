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

        $domainCount = method_exists($this, 'clientDomains')
            ? $this->clientDomains()->count()
            : 0;

        $array['domain_count'] = $domainCount;
        $array['role'] = $this->roles->first()?->only(['id', 'name']);

        return $array;
    }


}
