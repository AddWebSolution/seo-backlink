<?php

namespace App\Modules\MasterBacklink\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterBacklinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'website_name' => $this->website_name,
            'domain_url' => $this->domain_url,
            'da' => $this->da,
            'profile_url' => $this->profile_url,
            'platform_type' => $this->platform_type,
            'country'       => $this->country,
            'categories'    => $this->categories ?? [],
            'dofollow' => is_null($this->dofollow) ? null : (bool) $this->dofollow,
            'indexed' => is_null($this->indexed) ? null : (bool) $this->indexed,
            'last_active'       => $this->last_active,
            'avg_traffic'       => $this->avg_traffic,
            'domain_age'       => $this->domain_age,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            ];
    }
}
