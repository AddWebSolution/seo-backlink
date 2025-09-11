<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\ClientDomain\Models\ClientDomain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClientDomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       ClientDomain::newFactory()->count(9)->create();
    }
}
