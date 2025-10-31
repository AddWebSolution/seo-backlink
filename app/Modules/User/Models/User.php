<?php

namespace App\Modules\User\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\User\Observers\UserObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\RivalDomain\Models\RivalDomain;
use App\Modules\ClientDomain\Models\ClientDomain;
use App\Modules\BacklinkDatum\Models\BacklinkDatum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, HasRoles;

    // protected $fillable = [
    //     'name',
    //     'email',
    //     'phone',
    //     'password',
    //     'role',
    //     'status',
    // ];

    protected $guarded = [''];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => Role::class,
            'status' => UserStatus::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::SUPERADMIN;
    }

    public function backlinks()
    {
        return $this->hasMany(BacklinkDatum::class, 'client_id', 'id');
    }

    public function rivalDomains()
    {
        return $this->hasManyThrough(
            RivalDomain::class,    
            ClientDomain::class,    
            'client_id',            
            'client_domain_id',     
            'id',                  
            'id'                   
        );
    }

    public function clientDomains()
    {
        return $this->hasMany(ClientDomain::class, 'client_id', 'id');
    }
}
