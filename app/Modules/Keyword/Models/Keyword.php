<?php

namespace App\Modules\Keyword\Models;

use Carbon\Carbon;
use Addweb\Base\Model\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\ClientDomain\Models\ClientDomain;
use App\Modules\Keyword\Observers\KeywordObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([KeywordObserver::class])]
class Keyword extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'keywords';

    public function getProcessedAtAttribute($value)
    {
        $timezone = env('USER_TIMEZONE') ?? "Asia/Kolkata";
        return Carbon::parse($value)->timezone($timezone)->format('Y-m-d H:i:s');
    }

     public function domain()
    {
        return $this->belongsTo(ClientDomain::class, 'client_domain_id');
    }
}
