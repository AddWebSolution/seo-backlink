<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Adding new fields
            $table->string('website')->nullable()->after('company_name');
            $table->string('industry')->nullable()->after('website');
            $table->string('city')->nullable()->after('industry');
            $table->string('state')->nullable()->after('city');
            $table->string('zip_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('zip_code');
            $table->tinyInteger('status')->default(1)->nullable()
                ->comment('1 = Active, 2 = Inactive')
                ->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'website',
                'industry',
                'city',
                'state',
                'zip_code',
                'country',
                'status'
            ]);
        });
    }
};
