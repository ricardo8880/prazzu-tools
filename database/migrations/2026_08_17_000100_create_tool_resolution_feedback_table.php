<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_resolution_feedback', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('tool_slug', 120)->index();
            $table->string('tool_name');
            $table->string('tool_version', 32);
            $table->string('resolution', 20)->index();
            $table->string('reason', 40)->nullable()->index();
            $table->text('comment')->nullable();
            $table->string('path', 512);
            $table->text('url');
            $table->string('user_agent', 1024)->nullable();
            $table->timestamps();

            $table->index(['tool_slug', 'resolution', 'created_at']);
            $table->index(['tool_slug', 'reason', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_resolution_feedback');
    }
};
