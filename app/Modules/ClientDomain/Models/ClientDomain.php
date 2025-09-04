<?php

namespace App\Modules\ClientDomain\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\ClientDomain\Observers\ClientDomainObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([ClientDomainObserver::class])]
class ClientDomain extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

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
