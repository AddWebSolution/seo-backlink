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
        Schema::create('keyword_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('keyword_reports')->onDelete('cascade');
            $table->foreignId('keyword_id')->constrained('keywords')->onDelete('cascade');
            $table->foreignId('client_domain_id')->constrained('client_domains')->onDelete('cascade');
            $table->enum('llm_type', ['gpt', 'gemini', 'cohere'])
                ->comment('The type of LLM used to generate the report');
            $table->boolean('domain_found_in_response')->nullable();
            $table->longText('llm_response')->nullable();
            $table->text('highlights')->nullable();
            $table->tinyInteger('status')->unsigned()->default(1)
                ->comment('1 = Pending, 2 = Rejected, 3 = Approved');
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
        Schema::dropIfExists('keyword_data');
    }
};
