<?php

namespace App\Modules\KeywordReport\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KeywordReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
      public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'run_id'   => $this->run_id,
            'domain_count'   => $this->getDomainsCount(),
            'total_keywords'   => $this->getKeywordsCount(),
            'accepted_keywords'   => $this->getSuccessCount(),
            'rejected_keywords'   => $this->getFailCount(),
            'backlinks' => $this->keywords,
        ];
    }
}
