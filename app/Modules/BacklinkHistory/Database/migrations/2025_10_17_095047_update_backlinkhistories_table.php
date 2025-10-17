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
        Schema::table('backlink_history', function (Blueprint $table) {
            // Add unique constraint
            $table->unique([
                'client_domain_id', 
                'rival_domain_id',
                'target',
                'history_date'
            ], 'unique_backlink_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backlink_history', function (Blueprint $table) {
            $table->dropUnique('unique_backlink_history');
        });
    }
};