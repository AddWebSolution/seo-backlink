<?php

namespace App\Modules\Report\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'domain_count'   => $this->getDomainsCount() ? $this->getDomainsCount() : $this->domain_count,
            'total_backlink'   => $this->getBacklinkCount() ? $this->getBacklinkCount() : $this->total_backlink,
            'accepted_backlinks'   => $this->getAcceptedCount() ? $this->getAcceptedCount() : $this->accepted_backlinks,
            'rejected_backlinks'   => $this->getRejectedCount() ? $this->getRejectedCount() : $this->rejected_backlinks,
            'backlinks' => $this->backlinks,
        ];
    }
}
