<?php

namespace App\Modules\User\Models;

use App\Modules\ClientDomain\Models\ClientDomain;

trait Relation
{
    public function clientDomains()
    {
        return $this->belongsToMany(ClientDomain::class, 'client_domain_user');
    }
}
