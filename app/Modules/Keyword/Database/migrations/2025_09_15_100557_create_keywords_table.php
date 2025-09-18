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
       Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_domain_id');
            $table->string('keyword');
            $table->enum('status', [1, 2])->nullable()->comment('1 = active, 2 = deactivate')->default(1);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};
