<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backlink_data', function (Blueprint $table) {
            if (!Schema::hasColumn('backlink_data', 'target_domain')) {
                $table->string('target_domain')->nullable()->after('target_url');
            }

            if (!Schema::hasColumn('backlink_data', 'domain_url')) {
                $table->string('domain_url')->nullable()->after('domain');
            }
        });
    }

    public function down(): void
    {
        Schema::table('backlink_data', function (Blueprint $table) {
            $table->dropColumn(['target_domain', 'domain_url']);
        });
    }
};
