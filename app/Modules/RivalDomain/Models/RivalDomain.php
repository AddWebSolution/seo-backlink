<?php

namespace App\Modules\RivalDomain\Models;

use Addweb\Base\Model\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\ClientDomain\Models\ClientDomain;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Modules\RivalDomain\Observers\RivalDomainObserver;

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

    public function clientDomain()
    {
        return $this->belongsTo(ClientDomain::class, 'client_domain_id');
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
