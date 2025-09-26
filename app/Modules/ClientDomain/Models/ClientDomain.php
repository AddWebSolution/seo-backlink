<?php

namespace App\Modules\ClientDomain\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\Client\Models\Client;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Modules\ClientDomain\Observers\ClientDomainObserver;
use Database\Factories\ClientDomainFactory;
use Illuminate\Database\Eloquent\Factories\Factory;


#[ObservedBy([ClientDomainObserver::class])]
class ClientDomain extends BaseModel
{   
    /** @use HasFactory<\Database\Factories\ClientDomainFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $table = 'client_domains';

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

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
