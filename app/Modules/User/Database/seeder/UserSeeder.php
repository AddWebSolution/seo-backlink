<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Modules\User\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Prevent duplicate seeding
        if (!User::where('email', 'superadmin@example.com')->exists()) {
            User::create([
                'name'     => 'Super Admin',
                'email'    => 'superadmin@example.com',
                'phone'    => '9999999999',
                'password' => Hash::make('password'),
                'role'     => UserRole::SUPERADMIN,
            ]);
        }
    }
}
