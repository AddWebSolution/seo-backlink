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
            $table->enum('status', ['1', '2'])
                ->default('1')
                ->comment('1 = available, 2 = unavailable')
                ->change();

            $table->tinyInteger('approval_status')
                ->unsigned()
                ->default(1)
                ->comment('1 = Pending, 2 = Rejected, 3 = Approved')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_domains', function (Blueprint $table) {
            // Revert back to nullable (if needed)
            $table->enum('status', ['1', '2'])
                ->nullable()
                ->default(null)
                ->comment('1 = available, 2 = unavailable')
                ->change();

            $table->tinyInteger('approval_status')
                ->unsigned()
                ->nullable()
                ->default(null)
                ->comment('1 = Pending, 2 = Rejected, 3 = Approved')
                ->change();
        });
    }
};
