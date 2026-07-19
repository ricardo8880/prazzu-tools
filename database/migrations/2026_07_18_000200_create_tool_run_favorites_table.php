<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_run_favorites', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tool_run_id')->constrained('tool_runs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tool_run_id', 'user_id'], 'tool_run_favorites_owner_unique');
            $table->index(['user_id', 'created_at'], 'tool_run_favorites_owner_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_run_favorites');
    }
};
