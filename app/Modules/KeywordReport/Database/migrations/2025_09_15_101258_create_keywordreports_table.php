<?php

use App\Enums\LlmType;
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
       Schema::create('keyword_reports', function (Blueprint $table) {
            $table->id();
            $table->string('run_id')->index();
            $table->integer('domain_count')->nullable();
            $table->integer('total_keywords')->nullable();
            $table->integer('success')->nullable();
            $table->integer('fail')->nullable();
            $table->timestamp('run_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keyword_reports');
    }
};
