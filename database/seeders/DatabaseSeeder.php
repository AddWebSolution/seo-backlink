<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{   
     use WithoutModelEvents;
     
    public function run(): void
    {
        $this->call(class: [
            RolePermissionSeeder::class,
            AdminSeeder::class,
            ClientSeeder::class,
        ]);
    }
}   
