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
        Schema::create('client_domains', function (Blueprint $table) {
            $table->bigIncrements('id'); // primary key
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('source_url')->nullable();
            $table->string('title');
            $table->string('target_url');
            $table->integer('domain_authority')->nullable();
            $table->integer('domain_rating')->nullable();
            $table->unsignedBigInteger('organic_traffic')->nullable();
            $table->decimal('price_ne', 10, 2)->unsigned()->nullable();
            $table->decimal('price_gp', 10, 2)->unsigned()->nullable();
            $table->decimal('total_price', 10, 2)->unsigned()->nullable();
            $table->integer('turnaround_time')->nullable();
            $table->enum('status', ['1', '2'])->nullable()->default('1')->comment('1 = available, 2 = unavailable');
            $table->tinyInteger('approval_status')->unsigned()->default(1)
                ->comment('1 = Pending, 2 = Rejected, 3 = Approved')->nullable();
            $table->string('country')->nullable();
            $table->string('anchor_text')->nullable();
            $table->text('special_requirements')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // If you want FK:
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_domains');
    }
};
