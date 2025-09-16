<?php

namespace App\Modules\KeywordReport\Models;

use App\Enums\LlmType;
use Addweb\Base\Model\BaseModel;
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
        return $this->keywords()->where('status', 'approved')->count();
    }

    public function getDomains(): array
    {
        return [];
    }

    public function getFailCount(): int
    {
        return $this->keywords()->where('status', 'rejected')->count();
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
            ->count('llm_type');
    }
}
