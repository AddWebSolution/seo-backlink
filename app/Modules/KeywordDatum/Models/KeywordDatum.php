<?php

namespace App\Modules\KeywordDatum\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\KeywordDatum\Observers\KeywordDatumObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([KeywordDatumObserver::class])]
class KeywordDatum extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
}
