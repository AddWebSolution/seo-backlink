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
            'domain_count'   => $this->getDomainsCount() ?  $this->getDomainsCount() : $this->domain_count,
            'total_keywords'   => $this->getKeywordsCount() ? $this->getKeywordsCount() : $this->total_keywords,
            'accepted_keywords'   => $this->getSuccessCount() ? $this->getSuccessCount() : $this->success,
            'rejected_keywords'   => $this->getFailCount() ? $this->getFailCount() : $this->fail,
            'keywords' => $this->keywords,
        ];
    }
}
