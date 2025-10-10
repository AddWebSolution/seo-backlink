<?php

namespace App\Modules\Dashboard\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\Dashboard\Observers\DashboardObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([DashboardObserver::class])]
class Dashboard extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
}
