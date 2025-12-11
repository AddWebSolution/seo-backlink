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
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            ];
    }
}
