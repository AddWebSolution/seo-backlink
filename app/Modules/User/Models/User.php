<?php

namespace App\Modules\User\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Modules\User\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Enums\UserRole;
use App\Enums\UserStatus;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

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
            'role'   => UserRole::class,
            'status' => UserStatus::class,
        ];
    }
}
