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
        Schema::create('questionnaire_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_session_id')->nullable()->constrained('game_sessions')->cascadeOnDelete();
            $table->string('type'); // campaign_setup, session_planning
            $table->string('question_key');
            $table->json('response');
            $table->timestamps();

            $table->index(['campaign_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_responses');
    }
};
