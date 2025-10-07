<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Reports table
        Schema::create('backlink_reports', function (Blueprint $table) {
            $table->id();
            $table->string('run_id')->index();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->integer('domain_count')->nullable();
            $table->integer('total_backlink')->nullable();
            $table->integer('accepted_backlinks')->nullable();
            $table->integer('rejected_backlinks')->nullable();
            $table->timestamp('run_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backlink_reports');
    }
};
