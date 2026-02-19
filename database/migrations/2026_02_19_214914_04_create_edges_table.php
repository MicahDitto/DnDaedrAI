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
        Schema::create('edges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->uuid('source_node_id');
            $table->uuid('target_node_id');
            $table->string('type'); // located_in, member_of, knows, owns, involved_in, etc.
            $table->string('label')->nullable(); // optional custom label
            $table->unsignedTinyInteger('strength')->default(3); // 1-5 scale
            $table->json('metadata')->nullable(); // e.g., trust_level for "knows"
            $table->boolean('is_secret')->default(false);
            $table->timestamps();

            $table->foreign('source_node_id')->references('id')->on('nodes')->cascadeOnDelete();
            $table->foreign('target_node_id')->references('id')->on('nodes')->cascadeOnDelete();
            $table->index(['campaign_id', 'type']);
            $table->index(['source_node_id']);
            $table->index(['target_node_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edges');
    }
};
