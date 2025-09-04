<?php

namespace App\Modules\Report\Models;

use Addweb\Base\Model\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Report\Observers\ReportObserver;
use App\Modules\Backlinkreport\Models\Backlinkreport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([ReportObserver::class])]
class Report extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reports';

    protected $guarded = [];

    public function backlinks()
    {
        return $this->hasMany(Backlinkreport::class, 'report_id', 'id');
    }
}
