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
        Schema::create('backlink_history', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('client_domain_id')->nullable();
            $table->unsignedBigInteger('rival_domain_id')->nullable();
            $table->string('target')->nullable(); 
            $table->string('date_from')->nullable(); 
            $table->string('date_to')->nullable(); 
            $table->date('history_date')->nullable();    

            // Core backlink metrics
            $table->integer('rank')->nullable();
            $table->integer('backlinks')->nullable();
            $table->integer('new_backlinks')->nullable();
            $table->integer('lost_backlinks')->nullable();
            $table->integer('referring_domains')->nullable();
            $table->integer('new_referring_domains')->nullable();
            $table->integer('lost_referring_domains')->nullable();
            $table->integer('crawled_pages')->nullable();
            $table->integer('internal_links_count')->nullable();
            $table->integer('external_links_count')->nullable();
            $table->integer('broken_backlinks')->nullable();
            $table->integer('broken_pages')->nullable();
            $table->integer('referring_domains_nofollow')->nullable();
            $table->integer('referring_main_domains')->nullable();
            $table->integer('referring_main_domains_nofollow')->nullable();
            $table->integer('referring_ips')->nullable();
            $table->integer('referring_subnets')->nullable();
            $table->integer('referring_pages')->nullable();
            $table->integer('referring_pages_nofollow')->nullable();

            // Nested JSON metrics
            $table->json('information')->nullable();
            $table->json('referring_links_tld')->nullable();
            $table->json('referring_links_types')->nullable();
            $table->json('referring_links_attributes')->nullable();
            $table->json('referring_links_platform_types')->nullable();
            $table->json('referring_links_semantic_locations')->nullable();
            $table->json('referring_links_countries')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backlink_history');
    }
};
