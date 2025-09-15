<?php

namespace App\Modules\Keyword\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\Keyword\Observers\KeywordObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([KeywordObserver::class])]
class Keyword extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
}
