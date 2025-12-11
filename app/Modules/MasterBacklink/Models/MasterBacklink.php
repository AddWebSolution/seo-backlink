<?php

namespace App\Modules\MasterBacklink\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\MasterBacklink\Observers\MasterBacklinkObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([MasterBacklinkObserver::class])]
class MasterBacklink extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'masterbacklinks';

    protected $fillable = [
        'website_name',
        'domain_url',
        'da',
        'profile_url',
        'platform_type',
        'country',
        'categories',
        'dofollow',
        'indexed',
        'last_active',
        'avg_traffic',
        'domain_age',
    ];

    protected $casts = [
        'categories' => 'array',
        'dofollow' => 'integer',
        'indexed' => 'integer',
        'last_active' => 'datetime',
        'avg_traffic' => 'integer',
        'domain_age' => 'integer',
    ];
}
