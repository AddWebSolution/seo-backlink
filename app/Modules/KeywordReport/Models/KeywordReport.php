<?php

namespace App\Modules\KeywordReport\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\KeywordReport\Observers\KeywordReportObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([KeywordReportObserver::class])]
class KeywordReport extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
}
