<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backlink_data', function (Blueprint $table) {
            if (!Schema::hasColumn('backlink_data', 'domain_id')) {
                $table->unsignedBigInteger('domain_id')->nullable()->after('report_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('backlink_data', function (Blueprint $table) {
            $table->dropColumn('domain_id');
        });
    }
};
