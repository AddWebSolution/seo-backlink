<?php

namespace App\Modules\RivalDomain\Models;

use Addweb\Base\Model\BaseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\RivalDomain\Observers\RivalDomainObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([RivalDomainObserver::class])]
class RivalDomain extends BaseModel
{
     /** @use HasFactory<\Database\Factories\ClientDomainFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $table = 'rival_domains';

    protected $guarded = [];

    protected static function newFactory(): Factory
    {
        $factoryClass = "\\Database\\Factories\\" . class_basename(static::class) . "Factory";
        return $factoryClass::new();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'int',
        ];
    }
}
