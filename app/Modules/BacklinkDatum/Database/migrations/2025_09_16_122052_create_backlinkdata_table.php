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
        Schema::create('backlink_data', function (Blueprint $table) {
            $table->id();
            $table->string('report_id')->constrained('reports')->cascadeOnDelete();
            $table->string('target_url');
            $table->string('domain')->nullable();
            $table->string('from_domain')->nullable();
            $table->integer('rank')->nullable();
            $table->integer('domain_rank')->nullable();
            $table->integer('spam_score')->nullable();
            $table->tinyInteger('do_follow')->nullable()->default(false);
            $table->integer('tier')->default(0);
            $table->integer('score')->default(0);
            $table->tinyInteger('is_broken')->nullable()->default(false);
            $table->string('url')->nullable();
            $table->string('from_url')->nullable();
            $table->string('anchor')->nullable();
            $table->enum('status', ['accepted', 'rejected'])->default('rejected');
            $table->string('reason')->nullable();
            $table->json('key_highlights')->nullable();
            $table->timestamp('first_seen')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->string('link_type')->nullable();
            $table->string('page_title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlink_data');
    }
};
