<?php

namespace App\Modules\Client\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\ClientDomain\Models\ClientDomain;
use App\Modules\Client\Observers\ClientObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([ClientObserver::class])]
class Client extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'clients';

    protected $guarded = [];

    // public function clientDomains()
    // {
    //     return $this->hasMany(ClientDomain::class, 'client_id', 'id');
    // }
}
