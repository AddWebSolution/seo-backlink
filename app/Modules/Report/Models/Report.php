<?php

namespace App\Modules\Report\Models;

use Addweb\Base\Model\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Report\Observers\ReportObserver;
use App\Modules\Backlinkreport\Models\Backlinkreport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\Factory;


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

    protected static function newFactory(): Factory
    {
        $factoryClass = "\\Database\\Factories\\" . class_basename(static::class) . "Factory";
        return $factoryClass::new();
    }

    public function getAcceptedCount(): int
    {
        return $this->backlinks()->where('status', 'accepted')->count();
    }

    public function getRejectedCount(): int
    {
        return $this->backlinks()->where('status', 'rejected')->count();
    }

    public function getDomains(): array
    {
        return $this->backlinks()
            ->whereNotNull('domain')
            ->distinct()
            ->pluck('target_url', 'domain')
            ->toArray();
    }

    public function getDomainsCount(): int
    {
        return $this->backlinks()
            ->whereNotNull('domain')
            ->distinct('domain')
            ->count('domain');
    }

    public function getBacklinkCount(): int
    {
        return $this->backlinks()
            ->count('domain');
    }
}
