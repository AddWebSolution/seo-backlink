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
            'domain_count'   => $this->getDomainsCount(),
            'total_backlink'   => $this->getBacklinkCount(),
            'accepted_backlinks'   => $this->getAcceptedCount(),
            'rejected_backlinks'   => $this->getRejectedCount(),
            'backlinks' => $this->backlinks,
        ];
    }
}
