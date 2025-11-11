<?php

namespace App\Modules\ClientDomain\Models;

use App\Modules\User\Models\User;

trait Relation
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'client_domain_user');
    }
}
