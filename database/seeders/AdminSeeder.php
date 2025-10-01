<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;
use App\Enums\UserStatus;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $superAdmins = [
            ['name' => 'Admin One', 'email' => 'admin1@example.com'],
        ];

        foreach ($superAdmins as $admin) {
            $user = User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'phone' => '9999999999',
                    'password' => Hash::make('adminpassword'),
                    'role' => UserRole::SUPERADMIN->value,
                    'status' => UserStatus::ACTIVE->value,
                ]
            );

            $user->assignRole('super_admin');
        }
    }
}
