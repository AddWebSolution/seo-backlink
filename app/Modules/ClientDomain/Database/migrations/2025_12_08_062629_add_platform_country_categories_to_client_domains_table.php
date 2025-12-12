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
        Schema::table('client_domains', function (Blueprint $table) {
            $table->string('platform_type')->nullable()->after('country');
            $table->json('categories')->nullable()->after('platform_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_domains', function (Blueprint $table) {
            $table->dropColumn(['platform_type', 'categories']);
        });
    }
};
