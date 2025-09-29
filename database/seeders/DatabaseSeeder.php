<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ClientDomainSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{   
     use WithoutModelEvents;
     
    public function run(): void
    {
        $this->call(class: [
            RoleSeeder::class,
        ]);
    }
}   
