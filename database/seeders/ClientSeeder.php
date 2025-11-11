<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;
use App\Enums\UserStatus;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            ['name' => 'Client One', 'email' => 'client1@example.com'],
            ['name' => 'Client Two', 'email' => 'client2@example.com'],
            ['name' => 'Client Three', 'email' => 'client3@example.com'],
        ];

        foreach ($clients as $client) {
            $user = User::firstOrCreate(
                ['email' => $client['email']],
                [
                    'name'     => $client['name'],
                    'phone'    => '8888888888',
                    'password' => Hash::make('password'),
                    'role'     => UserRole::CLIENT->value,
                    'status'   => UserStatus::ACTIVE->value,
                ]
            );

            if ($user->wasRecentlyCreated && !$user->hasRole('client')) {
                $user->assignRole('client');
            }
        }
    }
}
