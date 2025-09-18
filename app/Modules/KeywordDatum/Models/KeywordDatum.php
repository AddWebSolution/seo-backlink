<?php

namespace App\Modules\KeywordDatum\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\ClientDomain\Models\ClientDomain;
use App\Modules\Keyword\Models\Keyword;
use App\Modules\KeywordDatum\Observers\KeywordDatumObserver;
use App\Modules\KeywordReport\Models\KeywordReport;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([KeywordDatumObserver::class])]
class KeywordDatum extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

     public function domain()
    {
        return $this->belongsTo(ClientDomain::class, 'client_domain_id');
    }

    public function keyword()
    {
        return $this->belongsTo(Keyword::class, 'keyword_id');
    }

     public function report()
    {
        return $this->belongsTo(KeywordReport::class, 'report_id');
    }
}
