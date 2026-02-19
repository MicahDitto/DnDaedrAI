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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('genre')->nullable(); // dark_fantasy, high_fantasy, comedic, etc.
            $table->string('rule_system')->default('5e'); // 5e, pathfinder_2e, homebrew, etc.
            $table->json('tone_settings')->nullable(); // strictness dials per category
            $table->unsignedTinyInteger('player_count')->nullable();
            $table->string('status')->default('setup'); // setup, active, paused, completed
            $table->json('settings')->nullable(); // misc campaign settings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
