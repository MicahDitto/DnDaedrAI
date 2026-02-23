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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ai_provider')->default('openai'); // openai, anthropic
            $table->text('api_keys')->nullable(); // Encrypted JSON: { openai: "sk-...", anthropic: "sk-ant-..." }
            $table->json('ai_preferences')->nullable(); // { temperature: 0.7, max_tokens: 2000, etc. }
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
