<?php

namespace App\Modules\KeywordReport\Models;

use App\Enums\LlmType;
use Addweb\Base\Model\BaseModel;
use App\Modules\ClientDomain\Models\ClientDomain;
use App\Modules\KeywordDatum\Models\KeywordDatum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Modules\KeywordReport\Observers\KeywordReportObserver;

#[ObservedBy([KeywordReportObserver::class])]
class KeywordReport extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "keyword_reports";

    protected $casts = [
        'llm_type' => LlmType::class,
    ];

    public function keywords()
    {
        return $this->hasMany(KeywordDatum::class, 'report_id', 'id');
    }

    public function getSuccessCount(): int
    {
        return $this->keywords()->where('domain_found_in_response',1)->count();
    }

    public function getDomains()
    {
        $keywords = $this->keywords()->get();

        $domainIds = $keywords->pluck('client_domain_id')->unique()->toArray();

        $domains = ClientDomain::whereIn('id', $domainIds)->get();

        return $domains->map(function ($domain) {
            return [
                'id'         => $domain->id,
                'title'      => $domain->title,
                'target_url' => $domain->target_url,
            ];
        })->toArray();
    }

    public function getFailCount(): int
    {
        return $this->keywords()->where('domain_found_in_response', 0)->count();
    }

    public function getDomainsCount(): int
    {
        return $this->keywords()
            ->whereNotNull('client_domain_id')
            ->distinct('client_domain_id')
            ->count('client_domain_id');
    }

    public function getKeywordsCount(): int
    {
        return $this->keywords()
            ->count('id');
    }
}
