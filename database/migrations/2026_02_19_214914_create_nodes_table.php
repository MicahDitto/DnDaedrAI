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
        Schema::create('nodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // character, place, item, faction, plot, event, lore
            $table->string('subtype')->nullable(); // e.g., pc, npc, villain for characters
            $table->string('name');
            $table->string('slug');
            $table->text('summary')->nullable();
            $table->json('content')->nullable(); // type-specific detailed data
            $table->json('metadata')->nullable(); // flexible additional data
            $table->string('confidence')->default('canon'); // canon, likely, rumor, unknown
            $table->boolean('is_secret')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['campaign_id', 'slug']);
            $table->index(['campaign_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
