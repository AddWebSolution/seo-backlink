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
        Schema::create('masterbacklinks', function (Blueprint $table) {
            $table->id();
            $table->string('website_name')->nullable();
            $table->string('domain_url')->nullable()->index();
            $table->string('da')->nullable();
            $table->string('profile_url')->nullable()->index();
            $table->string('platform_type')->nullable()->index();
            $table->string('country')->nullable()->index();
            $table->json('categories')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masterbacklinks');
    }
};
