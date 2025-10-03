<?php

namespace App\Modules\User\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Modules\User\Observers\UserObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\ClientDomain\Models\ClientDomain;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Example accessor: cast role to enum
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role'   => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    public function clientDomains()
    {
        return $this->hasMany(ClientDomain::class, 'client_id', 'id');
    }
}
