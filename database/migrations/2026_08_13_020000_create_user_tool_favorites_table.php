<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tool_favorites', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tool_slug', 120);
            $table->timestamps();

            $table->unique(['user_id', 'tool_slug'], 'user_tool_favorites_owner_tool_unique');
            $table->index(['user_id', 'created_at'], 'user_tool_favorites_owner_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tool_favorites');
    }
};
