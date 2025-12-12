<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('masterbacklinks', function (Blueprint $table) {
            $table->boolean('dofollow')->nullable()->after('categories');
            $table->boolean('indexed')->nullable()->after('dofollow');
            $table->dateTime('last_active')->nullable()->after('indexed');
            $table->integer('avg_traffic')->nullable()->after('last_active');
            $table->integer('domain_age')->nullable()->after('avg_traffic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('masterbacklinks', function (Blueprint $table) {
            $table->dropColumn([
                'dofollow',
                'indexed',
                'last_active',
                'avg_traffic',
                'domain_age',
            ]);
        });
    }
};
