<?php

namespace App\Modules\Keyword\Models;

use Carbon\Carbon;
use App\Enums\KeywordStatus;
use Addweb\Base\Model\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\ClientDomain\Models\ClientDomain;
use App\Modules\KeywordDatum\Models\KeywordDatum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Modules\Keyword\Observers\KeywordObserver;
use App\Modules\KeywordReport\Models\KeywordReport;
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

    public function keywordHistory()
    {
        return $this->hasMany(KeywordDatum::class, 'keyword_id','id');
    }

    protected $casts = [
        'status' => KeywordStatus::class,
    ];

    protected $appends = ['status_label'];


    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            KeywordStatus::ACTIVE => 'active',
            KeywordStatus::INACTIVE => 'inactive',
        };
    }

}
