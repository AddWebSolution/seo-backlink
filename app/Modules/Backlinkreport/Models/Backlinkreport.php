<?php

namespace App\Modules\Backlinkreport\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\Report\Models\Report;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Modules\Backlinkreport\Observers\BacklinkreportObserver;
use Illuminate\Database\Eloquent\Factories\Factory;


#[ObservedBy([BacklinkreportObserver::class])]
class Backlinkreport extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'backlink_reports';

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
        protected static function newFactory(): Factory
    {
        $factoryClass = "\\Database\\Factories\\" . class_basename(static::class) . "Factory";
        return $factoryClass::new();
    }


      protected $guarded = [];
}
