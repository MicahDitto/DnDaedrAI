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
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number')->default(0); // session 0, 1, 2, etc.
            $table->string('title')->nullable();
            $table->string('status')->default('planned'); // planned, in_progress, completed
            $table->date('planned_date')->nullable();
            $table->date('actual_date')->nullable();
            $table->json('plan')->nullable(); // structured session plan
            $table->text('notes')->nullable(); // DM notes
            $table->text('recap')->nullable(); // "previously on..."
            $table->json('outcomes')->nullable(); // decisions, new entities, plot progression
            $table->timestamps();

            $table->unique(['campaign_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
