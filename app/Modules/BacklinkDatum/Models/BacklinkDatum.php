<?php

namespace App\Modules\BacklinkDatum\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\Report\Models\Report;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Modules\BacklinkDatum\Observers\BacklinkDatumObserver;
use Illuminate\Database\Eloquent\Factories\Factory;

#[ObservedBy([BacklinkDatumObserver::class])]
class BacklinkDatum extends BaseModel
{

    use HasFactory;
    use SoftDeletes;
    protected $table = 'backlink_data';

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
        protected static function newFactory(): Factory
    {
        $factoryClass = "\\Database\\Factories\\" . class_basename(static::class) . "Factory";
        return $factoryClass::new();
    }

    public function client(){
        return $this->belongsTo(\App\Modules\User\Models\User::class, 'client_id');
    }


      protected $guarded = [];
}
